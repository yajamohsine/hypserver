<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ticket_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ticket_id) {
    header("Location: tickets.php");
    exit();
}

// Fetch ticket details
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND user_id = ?");
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: tickets.php");
    exit();
}

// Fetch replies
$stmt = $pdo->prepare("SELECT * FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC");
$stmt->execute([$ticket_id]);
$replies = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14 max-w-4xl mx-auto">

        <a href="tickets.php" class="text-xs text-gray-500 hover:text-neon-pink flex items-center gap-2 mb-6">
            <i class="fa-solid fa-arrow-left"></i> Back to Tickets
        </a>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold dark:text-white text-slate-900"><?php echo htmlspecialchars($ticket['subject']); ?></h1>
                <div class="flex items-center gap-4 mt-2">
                    <span class="text-[10px] uppercase font-bold text-gray-500 tracking-widest"><?php echo $ticket['department']; ?></span>
                    <span class="text-gray-400">•</span>
                    <span class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">ID #<?php echo str_pad($ticket['id'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>
            <?php
            $statusColor = match ($ticket['status']) {
                'Open' => 'text-neon-pink bg-neon-pink/10 border-neon-pink/20',
                'Answered' => 'text-green-500 bg-green-500/10 border-green-500/20',
                'Closed' => 'text-gray-500 bg-gray-500/10 border-gray-500/20',
                default => 'text-gray-500'
            };
            ?>
            <span class="px-3 py-1 <?php echo $statusColor; ?> rounded-full text-[10px] font-bold uppercase border tracking-widest">
                <?php echo $ticket['status']; ?>
            </span>
        </div>

        <!-- Chat Container -->
        <div class="space-y-6 mb-10">
            <!-- Initial Message -->
            <div class="flex flex-col items-start">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-[10px] font-bold text-gray-500">U</div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">You • <?php echo date('M d, H:i', strtotime($ticket['created_at'])); ?></span>
                </div>
                <div class="glass-card p-5 max-w-[90%] rounded-tl-none border-l-2 border-l-neon-pink">
                    <p class="text-sm dark:text-gray-300 text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($ticket['message']); ?></p>
                </div>
            </div>

            <!-- Replies -->
            <?php foreach ($replies as $reply): ?>
                <?php if ($reply['is_admin']): ?>
                    <!-- Admin Reply -->
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-bold text-neon-pink uppercase tracking-tighter">HyperServer Support • <?php echo date('M d, H:i', strtotime($reply['created_at'])); ?></span>
                            <div class="w-6 h-6 rounded-full bg-neon-pink/20 flex items-center justify-center text-[10px] font-bold text-neon-pink">A</div>
                        </div>
                        <div class="bg-neon-pink/5 border border-neon-pink/20 p-5 max-w-[90%] rounded-tr-none text-right">
                            <p class="text-sm dark:text-gray-200 text-slate-800 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($reply['message']); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- User Reply -->
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-[10px] font-bold text-gray-500">U</div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">You • <?php echo date('M d, H:i', strtotime($reply['created_at'])); ?></span>
                        </div>
                        <div class="glass-card p-5 max-w-[90%] rounded-tl-none border-l-2 border-l-neon-pink">
                            <p class="text-sm dark:text-gray-300 text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($reply['message']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($ticket['status'] != 'Closed'): ?>
            <!-- Reply Form -->
            <div class="glass-card p-0 overflow-hidden border-t-2 border-t-neon-pink">
                <div class="p-4 bg-slate-50/50 dark:bg-white/5 border-b border-light-border dark:border-white/5">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Send a reply</h4>
                </div>
                <form action="send_ticket_reply.php" method="POST" class="p-6 space-y-4">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                    <textarea name="message" rows="4" required placeholder="Type your message here..." class="w-full bg-transparent border-none p-0 text-sm dark:text-white text-slate-800 focus:ring-0 resize-none min-h-[100px]"></textarea>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-primary py-2 px-8 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-gray-500/10 border border-gray-500/20 text-gray-500 p-6 rounded-2xl text-center text-sm">
                This ticket is closed. If you still need help, please open a new ticket.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>