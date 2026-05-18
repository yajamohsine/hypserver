<?php 
require_once 'includes/auth_check.php';

// Fetch real tickets from database
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();

require_once 'includes/header.php'; 
require_once 'includes/sidebar.php'; 
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'ticket_created'): ?>
            <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-8 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                Ticket created successfully! Our team will respond shortly.
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight dark:text-white text-slate-900">Support Tickets</h1>
            <button data-modal-target="new-ticket-modal" data-modal-toggle="new-ticket-modal" class="btn-primary py-2 px-5 text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> New Ticket
            </button>
        </div>
        
        <div class="glass-card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-slate-50 dark:bg-white/5 border-b border-light-border dark:border-white/5">
                    <tr>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-light-border dark:divide-white/5">
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fa-solid fa-ticket-simple text-5xl mb-4 opacity-20"></i>
                                <p class="text-lg font-medium">No active tickets</p>
                                <p class="text-sm">If you need help, please create a new support ticket.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr onclick="window.location='view_ticket.php?id=<?php echo $ticket['id']; ?>'" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer group">
                            <td class="px-6 py-6 font-bold dark:text-white text-slate-800 group-hover:text-neon-pink"><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td class="px-6 py-6 dark:text-gray-400 text-slate-600"><?php echo htmlspecialchars($ticket['department']); ?></td>
                            <td class="px-6 py-6">
                                <?php 
                                $statusColor = match($ticket['status']) {
                                    'Open' => 'bg-neon-pink/10 text-neon-pink border-neon-pink/20',
                                    'Answered' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                    'Closed' => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                                    default => 'bg-gray-500/10 text-gray-500'
                                };
                                ?>
                                <span class="px-2 py-1 <?php echo $statusColor; ?> rounded text-[10px] font-bold uppercase border">
                                    <?php echo $ticket['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-6 text-gray-500"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                            <td class="px-6 py-6 text-right"><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="text-neon-pink font-bold text-xs hover:underline">View Chat</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Ticket Modal -->
<div id="new-ticket-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white dark:bg-dark-base rounded-2xl shadow-2xl overflow-hidden border border-light-border dark:border-dark-border">
            <div class="p-6 border-b border-light-border dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                <h3 class="text-xl font-bold dark:text-white text-slate-900">Create New Ticket</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white" data-modal-toggle="new-ticket-modal">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="process_ticket.php" method="POST" class="p-8 space-y-6">
                <?php csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Department</label>
                        <select name="department" class="w-full bg-slate-50 dark:bg-white/5 border border-light-border dark:border-white/10 rounded-xl px-4 py-3 text-sm dark:text-white focus:ring-neon-pink focus:border-neon-pink">
                            <option value="Technical">Technical Support</option>
                            <option value="Billing">Billing & Payments</option>
                            <option value="General">General Inquiry</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Priority</label>
                        <select name="priority" class="w-full bg-slate-50 dark:bg-white/5 border border-light-border dark:border-white/10 rounded-xl px-4 py-3 text-sm dark:text-white focus:ring-neon-pink focus:border-neon-pink">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Subject</label>
                    <input type="text" name="subject" required placeholder="Brief summary of your issue" class="w-full bg-slate-50 dark:bg-white/5 border border-light-border dark:border-white/10 rounded-xl px-4 py-3 text-sm dark:text-white focus:ring-neon-pink focus:border-neon-pink">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Message</label>
                    <textarea name="message" rows="5" required placeholder="Describe your issue in detail..." class="w-full bg-slate-50 dark:bg-white/5 border border-light-border dark:border-white/10 rounded-xl px-4 py-3 text-sm dark:text-white focus:ring-neon-pink focus:border-neon-pink"></textarea>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" class="btn-primary py-3 px-10">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
