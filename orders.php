<?php 
require_once 'includes/auth_check.php';
require_once 'includes/header.php'; 
require_once 'includes/sidebar.php'; 

// Fetch Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight mb-1 dark:text-white text-slate-900">My Orders</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Manage your active cloud instances and servers.</p>
            </div>
            <a href="./#plans" class="btn-primary py-2 px-5 text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> New Server
            </a>
        </div>

        <!-- Orders Table/Grid -->
        <div class="space-y-6">
            <?php if (empty($orders)): ?>
                <div class="glass-card p-12 text-center">
                    <div class="text-neon-pink/20 text-6xl mb-4"><i class="fa-solid fa-server"></i></div>
                    <p class="text-gray-400">No active servers found. Start by deploying one!</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php 
                    $isActive = ($order['status'] == 'Delivered');
                    $isPending = ($order['status'] == 'Pending' || $order['status'] == 'Processing' || $order['status'] == 'Paid');
                    $isCancelled = ($order['status'] == 'Cancelled');
                    $isExpired = (strtotime($order['expiry_date']) < time());
                    
                    $statusColor = 'text-green-500';
                    $statusBg = 'bg-green-500';
                    if ($isPending) {
                        $statusColor = 'text-yellow-500';
                        $statusBg = 'bg-yellow-500';
                    } elseif ($isCancelled || ($isActive && $isExpired)) {
                        $statusColor = 'text-red-500';
                        $statusBg = 'bg-red-500';
                    }
                    ?>
                    <!-- Order Card -->
                    <div class="glass-card overflow-hidden group hover:border-neon-pink/50 transition-all duration-500 <?php echo (!$isActive) ? 'opacity-60 grayscale-[0.5] hover:grayscale-0 hover:opacity-100' : ''; ?>">
                        <div class="p-6 md:p-8">
                            <div class="flex flex-col lg:flex-row gap-8">
                                <!-- Server Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-neon-pink/10 flex items-center justify-center text-neon-pink text-xl">
                                            <i class="fa-brands fa-windows"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold dark:text-white text-slate-900"><?php echo htmlspecialchars($order['plan_name']); ?></h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="flex h-2 w-2 rounded-full <?php echo $statusBg; ?>"></span>
                                                <span class="text-[10px] uppercase font-bold <?php echo $statusColor; ?> tracking-widest"><?php echo $order['status']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="creds-<?php echo $order['id']; ?>" class="<?php echo ($isActive) ? 'hidden' : ''; ?>">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-light-border dark:border-white/5 mt-6">
                                            <div>
                                                <p class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-1">IP Address</p>
                                                <p class="text-sm font-mono dark:text-white text-slate-900 flex items-center gap-2">
                                                    <?php echo ($isActive) ? $order['ip'] : '<span class="italic text-gray-400">Locked</span>'; ?>
                                                    <?php if ($isActive): ?>
                                                        <button onclick="navigator.clipboard.writeText('<?php echo $order['ip']; ?>')" class="text-gray-400 hover:text-neon-blue transition-colors"><i class="fa-regular fa-copy"></i></button>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-1">Username</p>
                                                <p class="text-sm dark:text-white text-slate-900"><?php echo ($isActive) ? htmlspecialchars($order['username']) : '---'; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-1">Password</p>
                                                <div class="flex items-center gap-2">
                                                    <input type="password" id="inline-pass-<?php echo $order['id']; ?>" value="<?php echo htmlspecialchars($order['password']); ?>" readonly class="bg-transparent border-none p-0 text-sm dark:text-white text-slate-900 font-mono focus:ring-0 w-24">
                                                    <?php if ($isActive): ?>
                                                        <button onclick="togglePassInline(<?php echo $order['id']; ?>)" class="text-gray-400 hover:text-neon-pink transition-colors"><i class="fa-regular fa-eye" id="inline-eye-<?php echo $order['id']; ?>"></i></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-[10px] uppercase text-gray-500 font-bold tracking-widest mb-1">Expiry Date</p>
                                                <p class="text-sm dark:text-white text-slate-900 <?php echo ($isExpired) ? 'text-red-500' : ''; ?>">
                                                    <?php echo date('M d, Y', strtotime($order['expiry_date'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col justify-center gap-3 min-w-[200px]">
                                    <?php if ($isActive): ?>
                                        <button onclick="toggleCredentials(<?php echo $order['id']; ?>)" class="btn-primary w-full py-2.5 text-xs flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-eye"></i> View Order
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-primary w-full py-2.5 text-xs">
                                            <i class="fa-solid fa-clock mr-2"></i> <?php echo $order['status']; ?>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-outline w-full py-2 text-xs flex items-center justify-center gap-2 text-gray-400">
                                        <i class="fa-solid fa-arrows-rotate"></i> Renew Subscription
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if ($isActive): ?>
                            <!-- Progress Bar (Expiry approximation) -->
                            <div class="w-full bg-dark-base h-1">
                                <div class="bg-neon-pink h-full w-[65%] shadow-[0_0_10px_rgba(255,42,112,0.5)]"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('id');
    if (orderId) {
        const credsDiv = document.getElementById('creds-' + orderId);
        if (credsDiv) {
            // Expand the credentials
            credsDiv.classList.remove('hidden');
            // Scroll to the order card
            credsDiv.closest('.glass-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Add a temporary highlight or border
            credsDiv.closest('.glass-card').classList.add('border-neon-pink');
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
