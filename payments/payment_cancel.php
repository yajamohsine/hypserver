<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? '';

if (!empty($order_id)) {
    // Update local status to cancelled if it was still pending
    $stmt = $pdo->prepare("UPDATE payments SET status = 'cancelled' WHERE order_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    
    // Log the cancellation
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents(__DIR__ . '/../logs/cryptomus.log', "$timestamp Payment cancelled by user: $order_id\n", FILE_APPEND);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-[85vh] flex items-center justify-center container mx-auto px-4 py-12">
    <div class="glass-card p-10 w-full max-w-xl text-center relative overflow-hidden">
        <!-- Cancel Top Border Gradient -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-500 to-orange-500 animate-pulse"></div>

        <div class="w-24 h-24 bg-red-500/10 rounded-full flex items-center justify-center text-red-500 text-5xl mx-auto mb-8 shadow-[0_0_20px_rgba(239,68,68,0.2)]">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        
        <h1 class="text-3xl font-extrabold mb-4 dark:text-white text-slate-900 tracking-tight">Payment Cancelled</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto mb-10 leading-relaxed">
            Your transaction was cancelled and no funds were charged. If you ran into any issues, feel free to try again or reach out to support.
        </p>

        <!-- Payment Meta Details -->
        <?php if (!empty($order_id)): ?>
            <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-6 mb-8 text-left space-y-3.5 border border-light-border dark:border-white/5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 uppercase tracking-widest font-bold text-[10px]">Order ID</span>
                    <span class="font-mono dark:text-white text-slate-800 font-bold"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-light-border dark:border-white/5 pt-3.5">
                    <span class="text-gray-400 uppercase tracking-widest font-bold text-[10px]">Status</span>
                    <span class="px-3 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-widest bg-red-100 dark:bg-red-500/20 text-red-500">
                        CANCELLED
                    </span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="../billing.php" class="btn-primary py-4 text-center text-sm font-bold tracking-wide">
                <i class="fa-solid fa-wallet mr-2"></i> Try Again
            </a>
            <a href="../dashboard.php" class="btn-outline py-4 text-center text-sm font-bold tracking-wide">
                <i class="fa-solid fa-house mr-2"></i> Go to Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
