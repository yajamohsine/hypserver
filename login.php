<?php 
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

require_once 'includes/header.php'; 
?>

<div class="min-h-[80vh] flex items-center justify-center container mx-auto px-4">
    <div class="glass-card p-10 w-full max-w-md relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-neon-pink to-neon-blue"></div>
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold mb-2 dark:text-white text-slate-900">Welcome Back</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Enter your credentials to access your dashboard.</p>
        </div>

        <form method="POST" class="space-y-6">
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 text-xs p-3 rounded-lg text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl pl-12 pr-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="name@company.com">
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500">Password</label>
                    <a href="#" class="text-[10px] text-neon-pink font-bold hover:underline">Forgot password?</a>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl pl-12 pr-4 py-3 dark:text-white text-slate-800 focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="••••••••">
                </div>
            </div>
            
            <div class="flex items-center">
                <input id="remember" type="checkbox" class="w-4 h-4 border border-light-border dark:border-dark-border rounded bg-slate-50 dark:bg-dark-base focus:ring-0 text-neon-pink">
                <label for="remember" class="ml-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Remember this device</label>
            </div>

            <button type="submit" class="btn-primary w-full block text-center">Sign In</button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Don't have an account? <a href="signup.php" class="text-neon-pink font-bold hover:underline">Create one</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
