<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/csrf.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Simple logging helper
function log_event($file, $message) {
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents(__DIR__ . '/../logs/' . $file, "$timestamp $message\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        log_event('errors.log', "CSRF Token validation failed for user_id: " . $_SESSION['user_id']);
        die("CSRF token validation failed.");
    }

    // 2. Read and Validate Amount
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $method = $_POST['method'] ?? '';

    if ($method !== 'cryptomus') {
        // Fallback for non-cryptomus or mock stripe flow
        if ($method === 'stripe') {
            // Stripe stub logic
            $_SESSION['pending_deposit'] = [
                'amount' => $amount,
                'method' => 'stripe',
                'token' => bin2hex(random_bytes(16))
            ];
            header("Location: ../payment_complete.php");
            exit();
        }
        header("Location: ../billing.php?error=invalid_method");
        exit();
    }

    if ($amount === false || $amount < 5.00 || $amount > 5000.00) {
        log_event('errors.log', "Amount validation failed: " . var_export($_POST['amount'], true));
        header("Location: ../billing.php?error=invalid_amount");
        exit();
    }

    // Load Cryptomus Config
    $config = require_once __DIR__ . '/../config/cryptomus.php';
    $merchantUuid = $config['merchant_uuid'] ?? '';
    $paymentKey = $config['payment_key'] ?? '';

    if (empty($merchantUuid) || empty($paymentKey) || $merchantUuid === 'your_merchant_uuid_here') {
        log_event('errors.log', "Cryptomus credentials not configured.");
        header("Location: ../billing.php?error=gateway_not_configured");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    
    // 3. Generate a highly unique Order ID
    $order_id = 'DEP-' . strtoupper(bin2hex(random_bytes(5)));

    try {
        // 4. Create pending payment record in DB
        $stmt = $pdo->prepare("
            INSERT INTO payments (user_id, order_id, amount, currency, status, provider) 
            VALUES (?, ?, ?, 'EUR', 'pending', 'cryptomus')
        ");
        $stmt->execute([$user_id, $order_id, $amount]);
        
        log_event('cryptomus.log', "Created pending payment: $order_id for user_id: $user_id, amount: €$amount");

    } catch (PDOException $e) {
        log_event('errors.log', "Database insertion failed for payment: " . $e->getMessage());
        header("Location: ../billing.php?error=db_error");
        exit();
    }

    // 5. Dynamic Base URL Discovery
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $projectDir = rtrim(dirname($_SERVER['PHP_SELF'], 2), '/\\');
    $base_url = $protocol . $host . $projectDir;

    $url_return = $base_url . '/payments/payment_success.php?order_id=' . $order_id;
    $url_callback = $base_url . '/payments/cryptomus_webhook.php';

    // 6. Build Cryptomus API Payload
    $payload = [
        'amount' => number_format($amount, 2, '.', ''),
        'currency' => 'EUR',
        'order_id' => $order_id,
        'url_return' => $url_return,
        'url_callback' => $url_callback,
        'lifetime' => 3600 // 1 hour
    ];

    // Compute Cryptomus Sign: md5(base64(json) + apiKey)
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $sign = md5(base64_encode($payloadJson) . $paymentKey);

    log_event('cryptomus.log', "Initiating Cryptomus checkout for $order_id. Payload: $payloadJson");

    // 7. Fire cURL Request with Security Settings & Timeouts
    $ch = curl_init('https://api.cryptomus.com/v1/payment');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'merchant: ' . $merchantUuid,
        'sign: ' . $sign,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        log_event('errors.log', "cURL error initiating payment for $order_id: $curlErr");
        
        // Update payment status to failed
        $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        header("Location: ../billing.php?error=gateway_connection_failed");
        exit();
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['state']) && $result['state'] === 0 && isset($result['result']['url'])) {
        // Checkout link received!
        $uuid = $result['result']['uuid'] ?? null;
        
        // Store provider payment UUID in database
        $stmt = $pdo->prepare("UPDATE payments SET provider_payment_id = ? WHERE order_id = ?");
        $stmt->execute([$uuid, $order_id]);
        
        log_event('cryptomus.log', "Successfully created Cryptomus checkout for $order_id. Redirecting. UUID: $uuid");

        // Redirect to Cryptomus
        header("Location: " . $result['result']['url']);
        exit();
    } else {
        log_event('errors.log', "Cryptomus API response error for $order_id (HTTP $httpCode): " . $response);
        
        // Update payment status to failed
        $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        header("Location: ../billing.php?error=gateway_api_error");
        exit();
    }
}
