<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            // Create user with hashed password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $full_name = trim($first_name . ' ' . $last_name);
            $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password, balance) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$email, $full_name, $hashed_password, 0.00])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['email'] = $email;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

require_once 'includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center container mx-auto px-4 py-12">
    <div class="glass-card p-10 w-full max-w-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-neon-pink to-neon-blue"></div>

        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold mb-2 dark:text-white text-slate-900">Create Account</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Join HyperServer and deploy your first server today.</p>
        </div>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if ($error): ?>
                <div class="md:col-span-2 bg-red-500/10 border border-red-500/20 text-red-500 text-xs p-3 rounded-lg text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="md:col-span-1">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">First Name</label>
                <input type="text" name="first_name" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl px-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="John">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Last Name</label>
                <input type="text" name="last_name" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl px-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="Doe">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl px-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="name@company.com">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl px-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="••••••••">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl px-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="••••••••">
            </div>

            <div class="md:col-span-2 flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" type="checkbox" class="w-4 h-4 border border-light-border dark:border-dark-border rounded bg-slate-50 dark:bg-dark-base focus:ring-0 text-neon-pink" required>
                </div>
                <label for="terms" class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    I agree to the <a href="terms" class="text-neon-pink hover:underline">Terms of Service</a> and <a href="privacy" class="text-neon-pink hover:underline">Privacy Policy</a>.
                </label>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="btn-primary w-full block text-center py-4">Create My Account</button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Already have an account? <a href="login.php" class="text-neon-pink font-bold hover:underline">Sign In</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>