<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Enforce admin access only for extreme security
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Access Denied: Authorized Personnel Only.");
}

$success_message = '';
$error_message = '';

// Load Cryptomus Config
$config = require __DIR__ . '/../config/cryptomus.php';
$paymentKey = $config['payment_key'] ?? '';
$merchantUuid = $config['merchant_uuid'] ?? '';

// Handle payment simulation POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_order'])) {
    $order_id = $_POST['order_id'] ?? '';
    
    // Fetch payment record
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $payment = $stmt->fetch();
    
    if ($payment) {
        // Build mock success payload
        $payload = [
            'order_id' => $payment['order_id'],
            'amount' => number_format($payment['amount'], 2, '.', ''),
            'currency' => 'EUR',
            'status' => 'paid',
            'network' => 'TRON',
            'from' => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
            'uuid' => $payment['provider_payment_id'] ?? 'mock-uuid-' . bin2hex(random_bytes(8))
        ];
        
        // Compute Signature: md5(base64(json) + apiKey)
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $sign = md5(base64_encode($payloadJson) . $paymentKey);
        
        // Add signature to the body
        $payload['sign'] = $sign;
        $finalPayloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        
        // Fire direct HTTP POST request to the local webhook
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $projectDir = rtrim(dirname($_SERVER['PHP_SELF'], 2), '/\\');
        $webhook_url = $protocol . $host . $projectDir . '/payments/cryptomus_webhook';
        
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $finalPayloadJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            $error_message = "Webhook dispatch failed: " . $curlErr;
        } elseif ($httpCode !== 200 || trim($response) !== 'OK') {
            $error_message = "Webhook returned HTTP {$httpCode}. Response: " . htmlspecialchars($response);
        } else {
            $success_message = "Success! Payment webhook dispatched and verified for Order <strong>{$order_id}</strong>. Balance has been credited successfully.";
        }
    } else {
        $error_message = "Payment record not found for Order ID: " . htmlspecialchars($order_id);
    }
}

// Fetch all pending payments
$payments = $pdo->query("SELECT * FROM payments WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HyperServer — Cryptomus Sandbox Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05050a;
            color: white;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .neon-border {
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .neon-border:hover {
            border-color: #0ea5e9;
            box-shadow: 0 0 15px -3px rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body class="min-h-screen p-6 md:p-12 flex flex-col justify-start items-center">
    <div class="max-w-4xl w-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center shadow-lg shadow-sky-500/20">
                    <i class="fa-solid fa-flask text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Cryptomus Sandbox</h1>
                    <p class="text-gray-500 text-xs uppercase tracking-widest font-bold mt-1">Payment Simulation Command Center</p>
                </div>
            </div>
            <a href="../admin/orders" class="glass-card px-4 py-2 rounded-xl text-sm font-semibold hover:border-gray-500 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Admin Panel
            </a>
        </div>

        <!-- Alert messages -->
        <?php if ($success_message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-2xl mb-8 flex items-start gap-4">
                <i class="fa-solid fa-circle-check text-2xl mt-0.5 shrink-0"></i>
                <div>
                    <h4 class="font-bold mb-1">Simulation Success</h4>
                    <p class="text-sm"><?php echo $success_message; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-6 rounded-2xl mb-8 flex items-start gap-4">
                <i class="fa-solid fa-circle-exclamation text-2xl mt-0.5 shrink-0"></i>
                <div>
                    <h4 class="font-bold mb-1">Simulation Failure</h4>
                    <p class="text-sm"><?php echo $error_message; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Gateway Info Card -->
        <div class="glass-card p-8 rounded-3xl mb-8">
            <h3 class="text-xl font-bold mb-4 flex items-center gap-3 text-sky-400">
                <i class="fa-solid fa-circle-info"></i> How Sandbox Testing Works
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                Cryptomus does not offer a public sandbox with test currencies. However, this custom utility allows you to simulate a complete end-to-end payment workflow without sending actual cryptocurrency.
            </p>
            <ul class="space-y-2 text-sm text-gray-500">
                <li class="flex items-center gap-2"><i class="fa-solid fa-chevron-right text-sky-500 text-xs"></i> Visit the normal client <a href="../billing" class="text-sky-400 hover:underline">Billing Page</a> and deposit an amount using Cryptomus.</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-chevron-right text-sky-500 text-xs"></i> Instead of making the real payment, come here to complete the transaction instantly.</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-chevron-right text-sky-500 text-xs"></i> Click the "Simulate Success" button to trigger a secure webhook payload directly.</li>
            </ul>
        </div>

        <!-- Pending Payments List -->
        <div class="glass-card rounded-3xl p-8">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <i class="fa-solid fa-clock text-yellow-500"></i> Pending Deposits (<?php echo count($payments); ?>)
            </h3>

            <?php if (empty($payments)): ?>
                <div class="text-center py-16 text-gray-500">
                    <i class="fa-solid fa-inbox text-5xl mb-4 opacity-25"></i>
                    <p class="text-lg">No pending deposits found.</p>
                    <p class="text-sm mt-1">Initiate a deposit from the client dashboard first.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($payments as $pay): ?>
                        <div class="glass-card neon-border p-6 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-extrabold text-white"><?php echo htmlspecialchars($pay['order_id']); ?></span>
                                    <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-500 border border-yellow-500/25 rounded-md text-[10px] uppercase font-bold tracking-widest">Pending</span>
                                </div>
                                <div class="text-xs text-gray-500 flex flex-wrap gap-4">
                                    <span><i class="fa-solid fa-user mr-1.5"></i> User ID: <?php echo $pay['user_id']; ?></span>
                                    <span><i class="fa-solid fa-calendar mr-1.5"></i> <?php echo date('M d, Y H:i:s', strtotime($pay['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                <div class="text-right">
                                    <span class="text-xs text-gray-500 block uppercase tracking-wider font-bold">Amount</span>
                                    <span class="text-2xl font-extrabold text-sky-400">€<?php echo number_format($pay['amount'], 2); ?></span>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($pay['order_id']); ?>">
                                    <button type="submit" name="simulate_order" class="bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold px-5 py-3 rounded-xl transition-all shadow-lg shadow-sky-500/20 hover:scale-[1.02] flex items-center gap-2">
                                        <i class="fa-solid fa-circle-play"></i> Simulate Success
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
