<?php 
require_once 'includes/header.php'; 

// Filtering Logic
$filter = $_GET['filter'] ?? 'all';
$sql = "SELECT o.*, u.email as client_email FROM orders o JOIN users u ON o.user_id = u.id";

if ($filter === 'pending') {
    $sql .= " WHERE o.status = 'Pending'";
} elseif ($filter === 'renewal') {
    $sql .= " WHERE o.status = 'Delivered' AND o.expiry_date < DATE_ADD(NOW(), INTERVAL 3 DAY)";
}

$sql .= " ORDER BY o.created_at DESC";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();

// Stats (Always fetch all for stats)
$stmt_stats = $pdo->query("SELECT o.* FROM orders o");
$all_orders = $stmt_stats->fetchAll();

$total_orders = count($all_orders);
$pending_orders = count(array_filter($all_orders, fn($o) => $o['status'] == 'Pending'));
$total_revenue = array_sum(array_column($all_orders, 'amount'));

$needs_renewal = count(array_filter($all_orders, function($o) {
    if ($o['status'] !== 'Delivered' || empty($o['expiry_date'])) return false;
    return strtotime($o['expiry_date']) < strtotime('+3 days');
}));
?>

<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold mb-1">Order Management</h1>
        <p class="text-gray-500 text-sm">Monitor incoming requests and deliver services.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <a href="?filter=all" class="glass-card p-6 border-l-4 border-l-admin-primary hover:bg-white/5 transition-colors <?php echo $filter === 'all' ? 'ring-1 ring-admin-primary/50' : ''; ?>">
        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Total Orders</div>
        <div class="text-3xl font-bold"><?php echo $total_orders; ?></div>
    </a>
    <a href="?filter=pending" class="glass-card p-6 border-l-4 border-l-neon-pink hover:bg-white/5 transition-colors <?php echo $filter === 'pending' ? 'ring-1 ring-neon-pink/50' : ''; ?>">
        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Pending Delivery</div>
        <div class="text-3xl font-bold"><?php echo $pending_orders; ?></div>
    </a>
    <a href="?filter=renewal" class="glass-card p-6 border-l-4 border-l-yellow-500 hover:bg-white/5 transition-colors <?php echo $filter === 'renewal' ? 'ring-1 ring-yellow-500/50' : ''; ?>">
        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Needs Renewal</div>
        <div class="text-3xl font-bold"><?php echo $needs_renewal; ?></div>
    </a>
    <div class="glass-card p-6 border-l-4 border-l-green-500">
        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Total Revenue</div>
        <div class="text-3xl font-bold">€<?php echo number_format($total_revenue, 2); ?></div>
    </div>
</div>

<?php if ($filter !== 'all'): ?>
    <div class="mb-4 flex items-center justify-between">
        <div class="text-sm text-gray-500 italic">
            Showing: <span class="font-bold text-slate-900 dark:text-white"><?php echo ucfirst($filter === 'renewal' ? 'Orders needing renewal' : $filter); ?></span>
        </div>
        <a href="orders.php" class="text-xs text-neon-pink hover:underline flex items-center gap-1">
            <i class="fa-solid fa-times"></i> Clear Filter
        </a>
    </div>
<?php endif; ?>

<!-- Orders Table -->
<div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-[10px] uppercase tracking-widest text-gray-500 bg-white/5 border-b border-white/5">
                <tr>
                    <th class="px-6 py-4 hidden lg:table-cell">ID</th>
                    <th class="px-6 py-4">Client</th>
                    <th class="px-6 py-4 hidden sm:table-cell">Plan</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 hidden md:table-cell">Expires</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs hidden lg:table-cell">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-xs sm:text-sm truncate max-w-[120px] sm:max-w-none"><?php echo htmlspecialchars($order['client_email']); ?></div>
                    </td>
                    <td class="px-6 py-4 hidden sm:table-cell">
                        <span class="px-2 py-1 bg-white/5 rounded text-[10px]"><?php echo htmlspecialchars($order['plan_name']); ?></span>
                    </td>
                    <td class="px-6 py-4 font-bold text-xs sm:text-sm">€<?php echo number_format($order['amount'], 2); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                        $statusColor = match($order['status']) {
                            'Pending' => 'text-yellow-500 bg-yellow-500/10',
                            'Paid' => 'text-green-500 bg-green-500/10',
                            'Processing' => 'text-blue-500 bg-blue-500/10',
                            'Delivered' => 'text-purple-500 bg-purple-500/10',
                            'Cancelled' => 'text-red-500 bg-red-500/10',
                            default => 'text-gray-500'
                        };
                        ?>
                        <span class="px-2 py-0.5 rounded text-[9px] sm:text-[10px] font-bold uppercase <?php echo $statusColor; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <?php if ($order['status'] == 'Delivered' && !empty($order['expiry_date'])): ?>
                            <?php 
                            $expiry = strtotime($order['expiry_date']);
                            $isExpired = $expiry < time();
                            $isExpiringSoon = $expiry < strtotime('+3 days');
                            ?>
                            <span class="<?php echo $isExpired ? 'text-red-500 font-bold' : ($isExpiringSoon ? 'text-yellow-500' : 'text-gray-400'); ?> text-xs">
                                <?php echo date('M d, Y', $expiry); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-600">---</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($order['status'] !== 'Delivered' && $order['status'] !== 'Cancelled'): ?>
                            <a href="deliver_order.php?id=<?php echo $order['id']; ?>" class="btn-admin text-[10px] sm:text-xs py-1 px-3">Deliver</a>
                        <?php elseif ($order['status'] == 'Delivered'): ?>
                            <button onclick="toggleAdminOrderDetails(<?php echo $order['id']; ?>)" class="text-admin-primary hover:underline text-[10px] sm:text-xs font-bold">Details</button>
                        <?php else: ?>
                            <button class="opacity-50 cursor-not-allowed text-[10px]">X</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($order['status'] == 'Delivered'): ?>
                <tr id="admin-order-details-<?php echo $order['id']; ?>" class="hidden bg-admin-primary/5">
                    <td colspan="7" class="px-6 py-6 border-l-4 border-admin-primary">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <div class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-2">IP Address</div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm"><?php echo htmlspecialchars($order['ip']); ?></span>
                                    <button onclick="navigator.clipboard.writeText('<?php echo $order['ip']; ?>')" class="text-gray-400 hover:text-slate-900 dark:hover:text-white"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-2">Username</div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm"><?php echo htmlspecialchars($order['username']); ?></span>
                                    <button onclick="navigator.clipboard.writeText('<?php echo $order['username']; ?>')" class="text-gray-400 hover:text-slate-900 dark:hover:text-white"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-2">Password</div>
                                <div class="flex items-center gap-3">
                                    <input type="password" id="admin-pass-<?php echo $order['id']; ?>" value="<?php echo htmlspecialchars($order['password']); ?>" readonly class="bg-transparent border-none p-0 text-sm font-mono focus:ring-0 w-32 text-slate-900 dark:text-white">
                                    <button onclick="toggleAdminPass(<?php echo $order['id']; ?>)" class="text-gray-400 hover:text-slate-900 dark:hover:text-white"><i class="fa-regular fa-eye" id="admin-eye-<?php echo $order['id']; ?>"></i></button>
                                    <button onclick="navigator.clipboard.writeText('<?php echo $order['password']; ?>')" class="text-gray-400 hover:text-slate-900 dark:hover:text-white"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        <i class="fa-solid fa-inbox text-4xl mb-4 block"></i>
                        No orders found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleAdminOrderDetails(id) {
    const el = document.getElementById('admin-order-details-' + id);
    el.classList.toggle('hidden');
}

function toggleAdminPass(id) {
    const input = document.getElementById('admin-pass-' + id);
    const eye = document.getElementById('admin-eye-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
