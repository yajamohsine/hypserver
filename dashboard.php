<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$user_id = $_SESSION['user_id'];

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // If not in users table, maybe they are an admin who just migrated?
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$user_id]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin/index.php");
        exit();
    } else {
        // Truly not found anywhere
        session_destroy();
        header("Location: login.php?error=account_not_found");
        exit();
    }
}

// Fetch Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Fetch Counts
$count_servers = count(array_filter($orders, fn($o) => $o['status'] == 'Delivered'));
$count_pending = count(array_filter($orders, fn($o) => in_array($o['status'], ['Paid', 'Processing'])));
$count_tickets = 0; // Placeholder for now
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <!-- Dashboard Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight mb-1 dark:text-white text-slate-900">Dashboard</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Welcome back, <span class="dark:text-white text-slate-800 font-medium"><?php echo htmlspecialchars($user['email']); ?></span></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="glass-card px-4 py-2 flex items-center gap-3">
                    <span class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Balance</span>
                    <span class="text-lg font-bold text-neon-blue">€<?php echo number_format($user['balance'], 2); ?></span>
                </div>
                <a href="billing.php" class="btn-primary py-2 px-5 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Add Funds
                </a>
            </div>
        </div>

        <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
            <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-8 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                Order placed successfully! Our team will deliver your server shortly.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'insufficient_balance'): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-8 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                Insufficient balance. Please add funds to your account.
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 border-l-4 border-l-neon-pink">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-neon-pink/10 flex items-center justify-center text-neon-pink text-lg">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <span class="text-[10px] font-bold text-neon-pink uppercase tracking-widest">Active</span>
                </div>
                <h3 class="text-3xl font-bold mb-1 dark:text-white text-slate-800"><?php echo $count_servers; ?></h3>
                <p class="text-gray-500 text-sm">Active Servers</p>
            </div>

            <div class="glass-card p-6 border-l-4 border-l-yellow-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 text-lg">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <span class="text-[10px] font-bold text-yellow-500 uppercase tracking-widest">Processing</span>
                </div>
                <h3 class="text-3xl font-bold mb-1 dark:text-white text-slate-800"><?php echo $count_pending; ?></h3>
                <p class="text-gray-500 text-sm">Orders in Queue</p>
            </div>

            <div class="glass-card p-6 border-l-4 border-l-neon-blue">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-neon-blue/10 flex items-center justify-center text-neon-blue text-lg">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <span class="text-[10px] font-bold text-neon-blue uppercase tracking-widest">Spent</span>
                </div>
                <h3 class="text-3xl font-bold mb-1 dark:text-white text-slate-800">€<?php echo number_format(array_sum(array_column($orders, 'amount')), 2); ?></h3>
                <p class="text-gray-500 text-sm">Total Lifetime Spend</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-8">
            <!-- Active Services Table -->
            <div id="orders-list-view">
                <div class="glass-card overflow-hidden">
                    <div class="p-6 border-b border-light-border dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                        <h2 class="text-lg font-bold dark:text-white text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-neon-pink text-sm"></i>
                            My Active Orders
                        </h2>
                        <a href="orders.php" class="text-xs text-gray-500 hover:text-neon-pink">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] uppercase tracking-widest text-gray-500 bg-slate-50 dark:bg-white/5 border-b border-light-border dark:border-white/5">
                                <tr>
                                    <th class="px-6 py-4 min-w-[150px]">Service</th>
                                    <th class="px-6 py-4 min-w-[150px]">Details</th>
                                    <th class="px-6 py-4">Price</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-light-border dark:divide-white/5">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold dark:text-white text-slate-900"><?php echo htmlspecialchars($order['plan_name']); ?></div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-tighter">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <?php if ($order['status'] == 'Delivered'): ?>
                                                <div class="dark:text-white text-slate-900 font-mono"><?php echo htmlspecialchars($order['ip']); ?></div>
                                                <div class="text-gray-500">Exp: <?php echo $order['expiry_date']; ?></div>
                                            <?php else: ?>
                                                <span class="text-gray-400 italic">Pending provisioning...</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-bold dark:text-white text-slate-900">€<?php echo number_format($order['amount'], 2); ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $statusColor = match ($order['status']) {
                                                'Pending' => 'text-yellow-500 bg-yellow-500/10',
                                                'Paid' => 'text-green-500 bg-green-500/10',
                                                'Processing' => 'text-blue-500 bg-blue-500/10',
                                                'Delivered' => 'text-purple-500 bg-purple-500/10',
                                                'Cancelled' => 'text-red-500 bg-red-500/10',
                                                default => 'text-gray-500'
                                            };
                                            ?>
                                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?php echo $statusColor; ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <?php if ($order['status'] == 'Delivered'): ?>
                                                <a href="orders.php?id=<?php echo $order['id']; ?>" class="px-3 py-1.5 bg-neon-pink/10 text-neon-pink border border-neon-pink/20 rounded-lg text-[10px] font-bold uppercase hover:bg-neon-pink hover:text-white transition-all whitespace-nowrap inline-block">View Credentials</a>
                                            <?php else: ?>
                                                <button class="text-gray-400 cursor-not-allowed text-xs whitespace-nowrap inline-block">In Progress</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                            <i class="fa-solid fa-inbox text-4xl mb-4 block"></i>
                                            You haven't placed any orders yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Details Panels -->
                <?php foreach ($orders as $order): if ($order['status'] == 'Delivered'): ?>
                <div id="order-details-<?php echo $order['id']; ?>" class="hidden">
                    <div class="p-8">
                        <div class="flex justify-between items-center mb-8 pb-6 border-b border-light-border dark:border-white/5">
                            <div>
                                <button onclick="hideOrderDetails()" class="text-xs text-gray-500 hover:text-neon-pink flex items-center gap-2 mb-4">
                                    <i class="fa-solid fa-arrow-left"></i> Back to Orders
                                </button>
                                <h3 class="text-2xl font-bold dark:text-white text-slate-900"><?php echo htmlspecialchars($order['plan_name']); ?></h3>
                                <p class="text-xs text-gray-500">Credentials and server management</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-green-500/10 text-green-500 rounded-full text-[10px] font-bold uppercase tracking-widest">Active</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div class="space-y-8">
                                <h4 class="text-sm font-bold dark:text-white text-slate-800 flex items-center gap-2 uppercase tracking-widest">
                                    <i class="fa-solid fa-key text-neon-pink"></i> Access data
                                </h4>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between border-b border-light-border dark:border-white/5 pb-4">
                                        <div class="text-xs text-gray-500 uppercase tracking-widest">Host:</div>
                                        <div class="flex items-center gap-4">
                                            <span class="font-mono text-sm dark:text-white text-slate-800"><?php echo htmlspecialchars($order['ip']); ?></span>
                                            <button onclick="navigator.clipboard.writeText('<?php echo $order['ip']; ?>')" class="text-gray-400 hover:text-neon-pink"><i class="fa-regular fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-light-border dark:border-white/5 pb-4">
                                        <div class="text-xs text-gray-500 uppercase tracking-widest">Username:</div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm dark:text-white text-slate-800"><?php echo htmlspecialchars($order['username']); ?></span>
                                            <button onclick="navigator.clipboard.writeText('<?php echo $order['username']; ?>')" class="text-gray-400 hover:text-neon-pink"><i class="fa-regular fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-light-border dark:border-white/5 pb-4">
                                        <div class="text-xs text-gray-500 uppercase tracking-widest">Password:</div>
                                        <div class="flex items-center gap-4">
                                            <input type="password" id="inline-pass-<?php echo $order['id']; ?>" value="<?php echo htmlspecialchars($order['password']); ?>" readonly class="bg-transparent border-none p-0 text-sm dark:text-white text-slate-800 font-mono focus:ring-0 w-32 text-right">
                                            <button onclick="togglePassInline(<?php echo $order['id']; ?>)" class="text-gray-400 hover:text-neon-blue"><i class="fa-regular fa-eye" id="inline-eye-<?php echo $order['id']; ?>"></i></button>
                                            <button onclick="navigator.clipboard.writeText('<?php echo $order['password']; ?>')" class="text-gray-400 hover:text-neon-pink"><i class="fa-regular fa-copy"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-8">
                                <h4 class="text-sm font-bold dark:text-white text-slate-800 flex items-center gap-2 uppercase tracking-widest">
                                    <i class="fa-solid fa-server text-neon-blue"></i> Server details
                                </h4>
                                <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-6 space-y-4">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Operating System:</span>
                                        <span class="dark:text-white text-slate-800 font-medium">Windows Server 2022</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Expiry Date:</span>
                                        <span class="text-neon-pink font-bold"><?php echo $order['expiry_date']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>