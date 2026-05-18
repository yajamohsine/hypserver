<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? '';
$payment = null;

if (!empty($order_id)) {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $payment = $stmt->fetch();
}

// Default to pending display if not found or pending/processing
$status = $payment['status'] ?? 'pending';
$amount = $payment['amount'] ?? 0.00;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-[85vh] flex items-center justify-center container mx-auto px-4 py-12">
    <div class="glass-card p-10 w-full max-w-xl text-center relative overflow-hidden">
        
        <?php if ($status === 'paid' || $status === 'paid_over'): ?>
            <!-- Success Top Border Gradient -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-500 via-emerald-400 to-teal-500 animate-pulse"></div>

            <div class="w-24 h-24 bg-green-500/10 rounded-full flex items-center justify-center text-green-500 text-5xl mx-auto mb-8 shadow-[0_0_20px_rgba(34,197,94,0.2)] animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            
            <h1 class="text-3xl font-extrabold mb-4 dark:text-white text-slate-900 tracking-tight">Payment Completed!</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto mb-10 leading-relaxed">
                Your cryptocurrency deposit of <span class="text-green-500 font-extrabold text-lg">€<?php echo number_format($amount, 2); ?></span> was successfully processed. Your account balance has been credited.
            </p>
        <?php else: ?>
            <!-- Pending Top Border Gradient -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-neon-blue via-indigo-500 to-purple-500 animate-pulse"></div>

            <div class="w-24 h-24 bg-neon-blue/10 rounded-full flex items-center justify-center text-neon-blue text-5xl mx-auto mb-8 shadow-[0_0_20px_rgba(0,242,254,0.2)]">
                <i class="fa-solid fa-arrows-spin fa-spin text-4xl"></i>
            </div>
            
            <h1 class="text-3xl font-extrabold mb-4 dark:text-white text-slate-900 tracking-tight">Payment Confirming...</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto mb-10 leading-relaxed">
                We are currently waiting for blockchain confirmation. Your balance of <span class="text-neon-blue font-extrabold text-lg">€<?php echo number_format($amount, 2); ?></span> will be added automatically once credited. You can safely navigate away from this page.
            </p>
        <?php endif; ?>

        <!-- Payment Meta Details -->
        <?php if ($payment): ?>
            <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-6 mb-8 text-left space-y-3.5 border border-light-border dark:border-white/5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 uppercase tracking-widest font-bold text-[10px]">Order ID</span>
                    <span class="font-mono dark:text-white text-slate-800 font-bold"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-light-border dark:border-white/5 pt-3.5">
                    <span class="text-gray-400 uppercase tracking-widest font-bold text-[10px]">Deposit Amount</span>
                    <span class="dark:text-white text-slate-800 font-extrabold text-sm">€<?php echo number_format($amount, 2); ?></span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-light-border dark:border-white/5 pt-3.5">
                    <span class="text-gray-400 uppercase tracking-widest font-bold text-[10px]">Status</span>
                    <span class="px-3 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-widest bg-slate-200 dark:bg-white/10 dark:text-white text-slate-700">
                        <?php echo htmlspecialchars($status); ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="../dashboard.php" class="btn-primary py-4 text-center text-sm font-bold tracking-wide">
                <i class="fa-solid fa-house mr-2"></i> Go to Dashboard
            </a>
            <a href="../billing.php" class="btn-outline py-4 text-center text-sm font-bold tracking-wide">
                <i class="fa-solid fa-wallet mr-2"></i> View Billing
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
