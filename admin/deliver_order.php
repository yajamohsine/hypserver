<?php 
require_once 'includes/header.php'; 

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT o.*, u.email as client_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $ip = $_POST['ip'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $duration_months = $_POST['duration'] ?? 1;

    if (!empty($ip) && !empty($username) && !empty($password)) {
        $expiry_date = date('Y-m-d', strtotime("+$duration_months months"));
        
        $update = $pdo->prepare("UPDATE orders SET ip = ?, username = ?, password = ?, expiry_date = ?, status = 'Delivered', payment_status = 'Paid' WHERE id = ?");
        if ($update->execute([$ip, $username, $password, $expiry_date, $id])) {
            $success = "Order delivered successfully! The client can now see their credentials.";
            // Refresh order data
            $stmt->execute([$id]);
            $order = $stmt->fetch();
        } else {
            $error = "Failed to update order. Please try again.";
        }
    } else {
        $error = "Please fill in all server details.";
    }
}
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="orders.php" class="text-gray-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm flex items-center gap-2 mb-4">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>
        <h1 class="text-3xl font-bold mb-1">Deliver Service</h1>
        <p class="text-gray-500 text-sm">Providing credentials for Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></p>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="glass-card p-8">
        <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-white/5">
            <div>
                <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Client</div>
                <div class="font-medium"><?php echo htmlspecialchars($order['client_email']); ?></div>
            </div>
            <div>
                <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Service</div>
                <div class="font-medium"><?php echo htmlspecialchars($order['plan_name']); ?></div>
            </div>
        </div>

        <?php if ($order['status'] == 'Delivered'): ?>
            <div class="space-y-6">
                <div class="p-4 bg-purple-500/5 rounded-xl border border-purple-500/10">
                    <p class="text-sm text-purple-400 font-medium mb-4">This order has already been delivered.</p>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">IP Address:</span>
                            <span class="font-mono"><?php echo htmlspecialchars($order['ip']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Username:</span>
                            <span><?php echo htmlspecialchars($order['username']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Expiry Date:</span>
                            <span><?php echo $order['expiry_date']; ?></span>
                        </div>
                    </div>
                </div>
                <a href="orders.php" class="btn-admin w-full text-center block">Done</a>
            </div>
        <?php else: ?>
            <form method="POST" class="space-y-6">
                <?php csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Server IP Address</label>
                    <input type="text" name="ip" required class="w-full bg-slate-50 dark:bg-dark-base border border-slate-200 dark:border-dark-border rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:border-admin-primary focus:ring-0 outline-none transition-all placeholder-gray-400 dark:placeholder-gray-600" placeholder="192.168.1.1">
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Username</label>
                        <input type="text" name="username" required class="w-full bg-slate-50 dark:bg-dark-base border border-slate-200 dark:border-dark-border rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:border-admin-primary focus:ring-0 outline-none transition-all placeholder-gray-400 dark:placeholder-gray-600" placeholder="Administrator">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
                        <input type="text" name="password" required class="w-full bg-slate-50 dark:bg-dark-base border border-slate-200 dark:border-dark-border rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:border-admin-primary focus:ring-0 outline-none transition-all placeholder-gray-400 dark:placeholder-gray-600" placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Duration (Months)</label>
                    <select name="duration" class="w-full bg-slate-50 dark:bg-dark-base border border-slate-200 dark:border-dark-border rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:border-admin-primary focus:ring-0 outline-none transition-all">
                        <option value="1">1 Month</option>
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-admin w-full py-4">Confirm Delivery</button>
                    <p class="text-[10px] text-gray-500 text-center mt-4">This will mark the order as Delivered and notify the user.</p>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
