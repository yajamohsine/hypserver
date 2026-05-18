<?php 
require_once 'includes/header.php'; 

// Handle Balance Update
if (isset($_POST['update_balance'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $uid = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $new_bal = filter_var($_POST['balance'], FILTER_VALIDATE_FLOAT);

    if ($uid !== false && $new_bal !== false) {
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_bal, $uid]);
        $success = "Balance updated successfully.";
    } else {
        $error = "Invalid input data.";
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold mb-1">User Management</h1>
        <p class="text-gray-500 text-sm">Monitor user activity and manage account balances.</p>
    </div>
    <div class="glass-card px-4 py-2 text-[10px] md:text-xs font-bold uppercase tracking-widest text-gray-500 border border-white/5">
        Total Users: <?php echo count($users); ?>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="fa-solid fa-circle-check"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-[10px] uppercase tracking-widest text-gray-500 bg-white/5 border-b border-white/5">
                <tr>
                    <th class="px-6 py-4 hidden lg:table-cell">ID</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 hidden sm:table-cell">Role</th>
                    <th class="px-6 py-4">Balance</th>
                    <th class="px-6 py-4 hidden md:table-cell">Joined</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs hidden lg:table-cell">#<?php echo $u['id']; ?></td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-xs sm:text-sm truncate max-w-[150px] sm:max-w-none"><?php echo htmlspecialchars($u['email']); ?></div>
                    </td>
                    <td class="px-6 py-4 hidden sm:table-cell">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $u['role'] == 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400'; ?>">
                            <?php echo $u['role']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" class="flex items-center gap-2">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <span class="text-slate-900 dark:text-gray-400 font-bold">€</span>
                            <input type="number" name="balance" step="0.01" value="<?php echo $u['balance']; ?>" class="w-20 bg-transparent border-none p-0 focus:ring-0 font-bold" style="color: var(--text-main) !important;">
                            <button type="submit" name="update_balance" class="text-admin-primary hover:text-slate-900 dark:hover:text-white transition-colors">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-500 text-xs">
                        <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-gray-500 hover:text-red-500 transition-colors text-xs">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
