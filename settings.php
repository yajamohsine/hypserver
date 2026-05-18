<?php 
require_once 'includes/auth_check.php';
require_once 'includes/csrf.php';

$success = '';
$error = '';

// Handle Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['full_name']);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $stmt->execute([$name, $user_id]);
        $success = "Profile updated successfully.";
        $user['full_name'] = $name;
    }

    if (isset($_POST['update_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $hashed = $stmt->fetchColumn();

        if (password_verify($current, $hashed)) {
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hashed, $user_id]);
            $success = "Password updated successfully.";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

require_once 'includes/header.php'; 
require_once 'includes/sidebar.php'; 
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <h1 class="text-3xl font-bold tracking-tight mb-4">Settings</h1>

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
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Account Info -->
            <div class="glass-card p-8">
                <h3 class="text-xl font-bold mb-6">Profile Information</h3>
                <form method="POST" class="space-y-4">
                    <?php csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
                        <input type="email" class="w-full bg-dark-base border border-dark-border rounded-xl px-4 py-3 text-white focus:border-neon-pink focus:ring-0 outline-none opacity-60" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                        <input type="text" name="full_name" class="w-full bg-dark-base border border-dark-border rounded-xl px-4 py-3 text-white focus:border-neon-pink focus:ring-0 outline-none" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn-primary py-2 px-6 text-sm">Save Changes</button>
                </form>
            </div>

            <!-- Security -->
            <div class="glass-card p-8">
                <h3 class="text-xl font-bold mb-6">Security</h3>
                <form method="POST" class="space-y-4">
                    <?php csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Current Password</label>
                        <input type="password" name="current_password" required class="w-full bg-dark-base border border-dark-border rounded-xl px-4 py-3 text-white focus:border-neon-pink focus:ring-0 outline-none" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">New Password</label>
                        <input type="password" name="new_password" required class="w-full bg-dark-base border border-dark-border rounded-xl px-4 py-3 text-white focus:border-neon-pink focus:ring-0 outline-none" placeholder="••••••••">
                    </div>
                    <button type="submit" name="update_password" class="btn-outline py-2 px-6 text-sm">Update Password</button>
                </form>
                

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
