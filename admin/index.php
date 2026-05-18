<?php
require_once 'includes/header.php';

// Fetch Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE payment_status = 'Paid'")->fetchColumn() ?? 0;
$pending_delivery = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Paid'")->fetchColumn();

// Recent Orders
$stmt = $pdo->query("SELECT o.*, u.email as client_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold mb-1">Admin Overview</h1>
        <p class="text-gray-500 text-sm">Welcome back to the HyperServer command center.</p>
    </div>
    <div class="text-[10px] md:text-xs font-bold text-gray-500 bg-white/5 px-4 py-2 rounded-lg border border-white/5">
        System Status: <span class="text-green-500 animate-pulse">Operational</span>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="glass-card p-6 border-b-2 border-b-admin-primary">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-admin-primary/10 flex items-center justify-center text-admin-primary">
                <i class="fa-solid fa-users text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Users</span>
        </div>
        <div class="text-3xl font-bold"><?php echo $total_users; ?></div>
        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Active Accounts</p>
    </div>

    <div class="glass-card p-6 border-b-2 border-b-neon-blue">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-neon-blue/10 flex items-center justify-center text-neon-blue">
                <i class="fa-solid fa-cart-shopping text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Orders</span>
        </div>
        <div class="text-3xl font-bold"><?php echo $total_orders; ?></div>
        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Total Volume</p>
    </div>

    <div class="glass-card p-6 border-b-2 border-b-green-500">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center text-green-500">
                <i class="fa-solid fa-euro-sign text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Revenue</span>
        </div>
        <div class="text-3xl font-bold">€<?php echo number_format($total_revenue, 2); ?></div>
        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Net Earnings</p>
    </div>

    <div class="glass-card p-6 border-b-2 border-b-neon-pink">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-neon-pink/10 flex items-center justify-center text-neon-pink">
                <i class="fa-solid fa-truck-fast text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pending</span>
        </div>
        <div class="text-3xl font-bold"><?php echo $pending_delivery; ?></div>
        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Awaiting Fulfillment</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2">
        <div class="glass-card p-6 h-full">
            <h3 class="font-bold mb-8 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-admin-primary"></i>
                Revenue Overview
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <div class="glass-card p-6 h-full">
            <h3 class="font-bold mb-6">Quick Actions</h3>
            <div class="space-y-4">
                <a href="plans.php" class="flex items-center gap-3 p-4 bg-white/5 rounded-xl hover:bg-admin-primary/10 transition-all border border-transparent hover:border-admin-primary/20 group">
                    <div class="w-10 h-10 rounded-lg bg-admin-primary/20 flex items-center justify-center text-admin-primary">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold">New Plan</div>
                        <div class="text-[10px] text-gray-500 uppercase">Create service</div>
                    </div>
                </a>
                <a href="users.php" class="flex items-center gap-3 p-4 bg-white/5 rounded-xl hover:bg-neon-blue/10 transition-all border border-transparent hover:border-neon-blue/20 group">
                    <div class="w-10 h-10 rounded-lg bg-neon-blue/20 flex items-center justify-center text-neon-blue">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold">Manage Users</div>
                        <div class="text-[10px] text-gray-500 uppercase">Edit balances</div>
                    </div>
                </a>
                <a href="orders.php" class="flex items-center gap-3 p-4 bg-white/5 rounded-xl hover:bg-neon-pink/10 transition-all border border-transparent hover:border-neon-pink/20 group">
                    <div class="w-10 h-10 rounded-lg bg-neon-pink/20 flex items-center justify-center text-neon-pink">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold">Pending Orders</div>
                        <div class="text-[10px] text-gray-500 uppercase">Fulfill services</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="mt-8">
    <div class="glass-card overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <h3 class="font-bold">Recent Activity</h3>
            <a href="orders.php" class="text-[10px] font-bold text-admin-primary uppercase tracking-widest hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3 hidden sm:table-cell">Service</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($recent_orders as $order): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($order['client_email']); ?></td>
                            <td class="px-6 py-4 text-gray-500 text-xs hidden sm:table-cell"><?php echo htmlspecialchars($order['plan_name']); ?></td>
                            <td class="px-6 py-4 font-bold">€<?php echo number_format($order['amount'], 2); ?></td>
                            <td class="px-6 py-4 text-right sm:text-left">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white/5 text-gray-400">
                                    <?php echo $order['status']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue (€)',
                data: [120, 190, 300, 250, 420, 380, 500],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#8b5cf6',
                pointRadius: 0,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
</script>
<?php require_once 'includes/footer.php'; ?>