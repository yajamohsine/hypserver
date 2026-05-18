<?php
require_once 'includes/header.php';

$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header("Location: tickets.php");
    exit();
}

// Fetch ticket details with user info
$stmt = $pdo->prepare("SELECT t.*, u.email as client_email FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: tickets.php");
    exit();
}

// Fetch replies
$stmt = $pdo->prepare("SELECT * FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC");
$stmt->execute([$ticket_id]);
$replies = $stmt->fetchAll();
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <a href="tickets.php" class="text-xs text-gray-500 hover:text-admin-primary flex items-center gap-2 mb-4">
            <i class="fa-solid fa-arrow-left"></i> Back to Tickets
        </a>
        <h1 class="text-3xl font-bold tracking-tight dark:text-white text-slate-900"><?php echo htmlspecialchars($ticket['subject']); ?></h1>
        <p class="text-gray-500 text-sm mt-1">Ticket from <span class="dark:text-white text-slate-800 font-medium"><?php echo htmlspecialchars($ticket['client_email']); ?></span></p>
    </div>
    <div class="flex items-center gap-3">
        <?php 
        $statusColor = match($ticket['status']) {
            'Open' => 'text-neon-pink bg-neon-pink/10 border-neon-pink/20',
            'Answered' => 'text-green-500 bg-green-500/10 border-green-500/20',
            'Closed' => 'text-gray-500 bg-gray-500/10 border-gray-500/20',
            default => 'text-gray-500'
        };
        ?>
        <span class="px-4 py-1 <?php echo $statusColor; ?> rounded-full text-[10px] font-bold uppercase border tracking-widest">
            <?php echo $ticket['status']; ?>
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Chat History -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Client Initial Message -->
        <div class="flex flex-col items-start">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-xs font-bold text-gray-500">C</div>
                <span class="text-xs font-bold text-gray-500 uppercase">Client • <?php echo date('M d, H:i', strtotime($ticket['created_at'])); ?></span>
            </div>
            <div class="glass-card p-6 w-full rounded-tl-none border-l-4 border-l-neon-pink">
                <p class="text-sm dark:text-gray-300 text-slate-700 whitespace-pre-wrap"><?php echo htmlspecialchars($ticket['message']); ?></p>
            </div>
        </div>

        <!-- Replies -->
        <?php foreach ($replies as $reply): ?>
            <?php if ($reply['is_admin']): ?>
            <!-- Admin Reply -->
            <div class="flex flex-col items-end">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-bold text-admin-primary uppercase">Support (You) • <?php echo date('M d, H:i', strtotime($reply['created_at'])); ?></span>
                    <div class="w-8 h-8 rounded-full bg-admin-primary/20 flex items-center justify-center text-xs font-bold text-admin-primary">A</div>
                </div>
                <div class="bg-admin-primary/5 border border-admin-primary/20 p-6 w-full rounded-tr-none text-right">
                    <p class="text-sm dark:text-gray-200 text-slate-800 whitespace-pre-wrap"><?php echo htmlspecialchars($reply['message']); ?></p>
                </div>
            </div>
            <?php else: ?>
            <!-- Client Reply -->
            <div class="flex flex-col items-start">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-xs font-bold text-gray-500">C</div>
                    <span class="text-xs font-bold text-gray-500 uppercase">Client • <?php echo date('M d, H:i', strtotime($reply['created_at'])); ?></span>
                </div>
                <div class="glass-card p-6 w-full rounded-tl-none border-l-4 border-l-neon-pink">
                    <p class="text-sm dark:text-gray-300 text-slate-700 whitespace-pre-wrap"><?php echo htmlspecialchars($reply['message']); ?></p>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($ticket['status'] != 'Closed'): ?>
        <!-- Reply Form -->
        <div class="mt-10">
            <h4 class="text-sm font-bold dark:text-white text-slate-800 mb-4 uppercase tracking-widest">Send Reply</h4>
            <div class="glass-card p-6 border-t-4 border-t-admin-primary">
                <form action="send_admin_reply.php" method="POST">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                    <textarea name="message" rows="5" required placeholder="Type your response to the client..." class="w-full bg-transparent border-none p-0 text-sm dark:text-white text-slate-800 focus:ring-0 resize-none min-h-[120px]"></textarea>
                    <div class="flex justify-between items-center pt-4 border-t border-white/5">
                        <div class="flex gap-2">
                            <button type="submit" name="status" value="Answered" class="btn-admin py-2 px-6 text-xs">Send & Mark Answered</button>
                            <button type="submit" name="status" value="Closed" class="bg-gray-500/10 text-gray-500 border border-gray-500/20 rounded-xl px-6 py-2 text-xs font-bold hover:bg-gray-500/20 transition-all">Send & Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Ticket Info Sidebar -->
    <div class="space-y-6">
        <div class="glass-card p-6">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-6">Ticket Information</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">Department:</span>
                    <span class="text-slate-900 dark:text-white font-medium"><?php echo $ticket['department']; ?></span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">Priority:</span>
                    <span class="font-bold <?php echo $ticket['priority'] == 'High' ? 'text-red-500' : 'text-yellow-500'; ?>"><?php echo $ticket['priority']; ?></span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">Created:</span>
                    <span class="text-gray-400"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                </div>
            </div>
        </div>
        
        <?php if ($ticket['status'] != 'Closed'): ?>
        <form action="update_ticket_status.php" method="POST">
            <?php csrf_field(); ?>
            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
            <input type="hidden" name="status" value="Closed">
            <button type="submit" class="w-full bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl py-3 text-xs font-bold hover:bg-red-500/20 transition-all">
                Close Ticket
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
