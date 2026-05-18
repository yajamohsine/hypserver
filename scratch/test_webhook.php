<?php
// scratch/test_webhook.php
require_once __DIR__ . '/../config/db.php';

// Load Cryptomus Config
$config = require __DIR__ . '/../config/cryptomus.php';
$paymentKey = $config['payment_key'] ?? '';

// Grab the latest pending payment
$stmt = $pdo->query("SELECT * FROM payments WHERE status = 'pending' ORDER BY id DESC LIMIT 1");
$payment = $stmt->fetch();

if (!$payment) {
    die("No pending payment found to test.\n");
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

// Dispatch to cryptomus_webhook.php directly using a simulated POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// We can run the webhook file directly by mocking php://input!
// But wait, PHP's php://input cannot be mocked easily in the same process since it's read-only.
// So we will do a real cURL request to the local server or use local execution!
// Let's do a real local HTTP request!
$webhook_url = "http://localhost/rdp-reselling/payments/cryptomus_webhook.php";

$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $finalPayloadJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Webhook HTTP Status: $httpCode\n";
echo "Webhook Response: $response\n";

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
