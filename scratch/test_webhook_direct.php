<?php
// scratch/test_webhook_direct.php
require_once __DIR__ . '/../config/db.php';

// Load Cryptomus Config
$config = require __DIR__ . '/../config/cryptomus.php';
$paymentKey = $config['payment_key'] ?? '';

// Grab the latest pending payment
$stmt = $pdo->query("SELECT * FROM payments WHERE status = 'pending' ORDER BY id DESC LIMIT 1");
$payment = $stmt->fetch();

if (!$payment) {
    die("No pending payment found to test. Please create a payment first.\n");
}

$order_id = $payment['order_id'];
$amount = number_format($payment['amount'], 2, '.', '');
echo "Testing with Order ID: $order_id, Amount: €$amount\n";

// Fetch user's current balance before webhook
$userStmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$userStmt->execute([$payment['user_id']]);
$balanceBefore = floatval($userStmt->fetchColumn());
echo "User balance before: €$balanceBefore\n";

// Build mock success payload
$payload = [
    'order_id' => $order_id,
    'amount' => $amount,
    'currency' => 'EUR',
    'status' => 'paid',
    'network' => 'TRON',
    'from' => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
    'uuid' => 'mock-uuid-' . bin2hex(random_bytes(8))
];

// Compute Signature: md5(base64(json) + apiKey)
$payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
$sign = md5(base64_encode($payloadJson) . $paymentKey);

// Add signature to the body
$payload['sign'] = $sign;
$finalPayloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);

// Set the global mock variable
$GLOBALS['MOCK_WEBHOOK_PAYLOAD'] = $finalPayloadJson;

// Define headers/status stub so we don't output actual HTTP headers in CLI
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {
        static $currCode = 200;
        if ($code !== NULL) {
            $currCode = $code;
        }
        return $currCode;
    }
}

// Intercept exit in the script by registering a shutdown function,
// or we can just capture output using output buffering and run it!
ob_start();
try {
    include __DIR__ . '/../payments/cryptomus_webhook.php';
} catch (Exception $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
}
$output = ob_get_clean();

echo "Webhook Output: " . trim($output) . "\n";

// Check user's balance after webhook
$userStmt->execute([$payment['user_id']]);
$balanceAfter = floatval($userStmt->fetchColumn());
echo "User balance after: €$balanceAfter\n";

// Check payment status
$stmt_check = $pdo->prepare("SELECT status FROM payments WHERE order_id = ?");
$stmt_check->execute([$order_id]);
$newStatus = $stmt_check->fetchColumn();
echo "New payment status in DB: $newStatus\n";

if ($newStatus === 'paid' && $balanceAfter > $balanceBefore) {
    echo "SUCCESS: Balance successfully credited and transaction marked as paid!\n";
} else {
    echo "FAILURE: Status or balance was not updated.\n";
}
?>
