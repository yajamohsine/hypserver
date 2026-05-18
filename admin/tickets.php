<?php
require_once 'includes/header.php';

// Fetch all tickets with user emails
$stmt = $pdo->query("SELECT t.*, u.email as client_email FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$tickets = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight dark:text-white text-slate-900">Ticket Management</h1>
        <p class="text-gray-500 text-sm mt-1">Manage client support requests and inquiries.</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <div class="glass-card overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-slate-50 dark:bg-white/5 border-b border-light-border dark:border-white/5">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Client</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Priority</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-light-border dark:divide-white/5">
                <?php foreach ($tickets as $ticket): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 font-mono text-gray-500">#<?php echo str_pad($ticket['id'], 4, '0', STR_PAD_LEFT); ?></td>
                    <td class="px-6 py-4">
                        <div class="font-medium dark:text-white text-slate-800"><?php echo htmlspecialchars($ticket['client_email']); ?></div>
                        <div class="text-[10px] text-gray-500 uppercase"><?php echo htmlspecialchars($ticket['department']); ?></div>
                    </td>
                    <td class="px-6 py-4 dark:text-gray-300 text-slate-700"><?php echo htmlspecialchars($ticket['subject']); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                        $prioColor = match($ticket['priority']) {
                            'High' => 'text-red-500 bg-red-500/10',
                            'Medium' => 'text-yellow-500 bg-yellow-500/10',
                            'Low' => 'text-blue-500 bg-blue-500/10',
                            default => 'text-gray-500'
                        };
                        ?>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $prioColor; ?>">
                            <?php echo $ticket['priority']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $statusColor = match($ticket['status']) {
                            'Open' => 'text-neon-pink bg-neon-pink/10',
                            'Answered' => 'text-green-500 bg-green-500/10',
                            'Closed' => 'text-gray-500 bg-gray-500/10',
                            default => 'text-gray-500'
                        };
                        ?>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $statusColor; ?>">
                            <?php echo $ticket['status']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500"><?php echo date('M d, H:i', strtotime($ticket['created_at'])); ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="text-admin-primary hover:underline font-bold text-xs">Reply / View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
