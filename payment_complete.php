<?php 
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_deposit'])) {
    header("Location: billing.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$amount = $_SESSION['pending_deposit']['amount'];

// Start Transaction
$pdo->beginTransaction();

try {
    // 1. Fetch current balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$user_id]);
    $current_balance = $stmt->fetchColumn();

    // 2. Update balance
    $new_balance = $current_balance + $amount;
    $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->execute([$new_balance, $user_id]);

    // 3. Create an invoice record for the deposit
    $stmt = $pdo->prepare("INSERT INTO invoices (user_id, amount, status, due_date) VALUES (?, ?, 'paid', ?)");
    $stmt->execute([$user_id, $amount, date('Y-m-d')]);

    $pdo->commit();
    
    // Clear the pending session
    unset($_SESSION['pending_deposit']);
    
    // Success State
    $is_success = true;
} catch (Exception $e) {
    $pdo->rollBack();
    $is_success = false;
    $error_msg = $e->getMessage();
}

require_once 'includes/header.php'; 
?>

<div class="min-h-[80vh] flex items-center justify-center container mx-auto px-4">
    <div class="glass-card p-12 w-full max-w-lg text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-emerald-400"></div>
        
        <?php if ($is_success): ?>
            <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center text-green-500 text-4xl mx-auto mb-8 animate-bounce">
                <i class="fa-solid fa-check"></i>
            </div>
            
            <h1 class="text-3xl font-bold mb-4 dark:text-white text-slate-900">Payment Successful!</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-10">
                Your account has been credited with <span class="text-green-500 font-bold">€<?php echo number_format($amount, 2); ?></span>. 
                You can now deploy your servers immediately.
            </p>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="dashboard.php" class="btn-primary py-4">Dashboard</a>
                <a href="billing.php" class="btn-outline py-4">Billing History</a>
            </div>
        <?php else: ?>
            <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center text-red-500 text-4xl mx-auto mb-8">
                <i class="fa-solid fa-xmark"></i>
            </div>
            
            <h1 class="text-3xl font-bold mb-4 dark:text-white text-slate-900">Payment Failed</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-10">
                We encountered an error while processing your deposit. No funds were charged.
            </p>
            <a href="billing.php" class="btn-primary w-full py-4">Try Again</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
