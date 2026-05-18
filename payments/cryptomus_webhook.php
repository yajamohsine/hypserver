<?php
// payments/cryptomus_webhook.php
require_once __DIR__ . '/../config/db.php';

// Helper for audit logging
function log_webhook($file, $message) {
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents(__DIR__ . '/../logs/' . $file, "$timestamp $message\n", FILE_APPEND);
}

// 1. Retrieve Raw Webhook Payload
$rawContent = file_get_contents('php://input');

if (empty($rawContent)) {
    log_webhook('errors.log', "Webhook received empty body.");
    http_response_code(400);
    exit('Empty body');
}

// Log full raw payload for debugging and audit trail
log_webhook('webhook.log', "Raw payload: " . $rawContent);

$data = json_decode($rawContent, true);
if (!$data || !is_array($data)) {
    log_webhook('errors.log', "Failed to decode webhook JSON payload.");
    http_response_code(400);
    exit('Invalid JSON');
}

// 2. Extract and Extract Signature
$receivedSign = $data['sign'] ?? '';
if (empty($receivedSign)) {
    log_webhook('errors.log', "Webhook payload missing sign key.");
    http_response_code(400);
    exit('Missing signature');
}

unset($data['sign']); // Remove signature before computing our own

// Load Cryptomus Config
$config = require __DIR__ . '/../config/cryptomus.php';
$paymentKey = $config['payment_key'] ?? '';

// 3. Signature Verification (timing-safe check)
$payloadJson = json_encode($data, JSON_UNESCAPED_UNICODE);
$generatedSign = md5(base64_encode($payloadJson) . $paymentKey);

if (!hash_equals($generatedSign, $receivedSign)) {
    log_webhook('errors.log', "Signature mismatch! Received: $receivedSign, Generated: $generatedSign. Payload: $payloadJson");
    http_response_code(400);
    exit('Signature verification failed');
}

log_webhook('webhook.log', "Signature successfully verified.");

// 4. Fetch the Associated Payment Record
$order_id = $data['order_id'] ?? '';
if (empty($order_id)) {
    log_webhook('errors.log', "Webhook payload missing order_id.");
    http_response_code(400);
    exit('Missing order_id');
}

$stmt = $pdo->prepare("SELECT * FROM payments WHERE order_id = ?");
$stmt->execute([$order_id]);
$payment = $stmt->fetch();

if (!$payment) {
    log_webhook('errors.log', "Payment record not found in database for order_id: $order_id");
    http_response_code(404);
    exit('Payment not found');
}

$user_id = $payment['user_id'];
$expectedAmount = floatval($payment['amount']);
$receivedAmount = floatval($data['amount'] ?? 0);
$currency = strtoupper($data['currency'] ?? '');
$status = strtolower($data['status'] ?? '');
$network = $data['network'] ?? null;
$wallet = $data['from'] ?? null;

// 5. Idempotency Check
if ($payment['status'] === 'paid' || $payment['status'] === 'paid_over') {
    log_webhook('webhook.log', "Payment $order_id is already marked as paid. Skipping update.");
    http_response_code(200);
    exit('Already processed');
}

// 6. Currency & Amount Verification
if ($currency !== 'EUR') {
    log_webhook('errors.log', "Currency validation failed for $order_id: expected EUR, got $currency");
    http_response_code(400);
    exit('Currency validation failed');
}

if ($receivedAmount < $expectedAmount) {
    log_webhook('errors.log', "Amount validation failed for $order_id: expected >= €$expectedAmount, got €$receivedAmount");
    http_response_code(400);
    exit('Amount validation failed');
}

// Map Webhook Status
// Cryptomus standard statuses: pending, processing, paid, paid_over, fail, wrong_amount, cancel, system_fail
$dbStatus = 'pending';
if ($status === 'paid') {
    $dbStatus = 'paid';
} elseif ($status === 'paid_over') {
    $dbStatus = 'paid_over';
} elseif ($status === 'processing') {
    $dbStatus = 'processing';
} elseif ($status === 'cancel') {
    $dbStatus = 'cancelled';
} elseif (in_array($status, ['fail', 'system_fail', 'wrong_amount'])) {
    $dbStatus = 'failed';
}

log_webhook('webhook.log', "Processing state change for $order_id: {$payment['status']} -> $dbStatus");

// 7. DB Update inside secure Transaction block
if ($dbStatus === 'paid' || $dbStatus === 'paid_over') {
    try {
        $pdo->beginTransaction();

        // Lock user row for updates to avoid race conditions (Balance Increment)
        $userStmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
        $userStmt->execute([$user_id]);
        $currentBalance = floatval($userStmt->fetchColumn());

        // Calculate new balance
        // We use the amount requested in our database to credit their balance. 
        // If they paid over, we can credit the exact expectedAmount or the receivedAmount depending on business logic.
        // Let's credit the receivedAmount to be fair to the customer since it's a 'paid_over' state!
        $creditAmount = ($dbStatus === 'paid_over') ? $receivedAmount : $expectedAmount;
        $newBalance = $currentBalance + $creditAmount;

        // Update balance
        $updateUserStmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $updateUserStmt->execute([$newBalance, $user_id]);

        // Update Payment Record
        $updatePayStmt = $pdo->prepare("
            UPDATE payments 
            SET status = ?, 
                wallet_address = ?, 
                network = ?, 
                webhook_payload = ?, 
                paid_at = CURRENT_TIMESTAMP 
            WHERE order_id = ?
        ");
        $updatePayStmt->execute([$dbStatus, $wallet, $network, $rawContent, $order_id]);

        // Create Paid Invoice record
        $invoiceStmt = $pdo->prepare("
            INSERT INTO invoices (user_id, amount, status, due_date) 
            VALUES (?, ?, 'paid', ?)
        ");
        $invoiceStmt->execute([$user_id, $creditAmount, date('Y-m-d')]);

        $pdo->commit();
        
        log_webhook('webhook.log', "SUCCESS: Balance updated for user $user_id. Deposited €$creditAmount. New balance: €$newBalance.");
        http_response_code(200);
        exit('OK');

    } catch (Exception $e) {
        $pdo->rollBack();
        log_webhook('errors.log', "Database transaction failed for order $order_id: " . $e->getMessage());
        http_response_code(500);
        exit('Internal database error');
    }
} else {
    // If not paid (e.g. cancelled, failed, expired)
    try {
        $updatePayStmt = $pdo->prepare("
            UPDATE payments 
            SET status = ?, 
                webhook_payload = ? 
            WHERE order_id = ?
        ");
        $updatePayStmt->execute([$dbStatus, $rawContent, $order_id]);
        
        log_webhook('webhook.log', "Updated payment status to non-paid state: $dbStatus");
        http_response_code(200);
        exit('OK');
    } catch (PDOException $e) {
        log_webhook('errors.log', "Failed to update failed payment status for $order_id: " . $e->getMessage());
        http_response_code(500);
        exit('Database error');
    }
}
