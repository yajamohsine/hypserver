<?php 
session_start();
require_once 'config/db.php';

// Check if plan is selected
$plan_id = $_GET['plan'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    header("Location: index.php");
    exit();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "checkout.php?plan=" . $plan_id;
    header("Location: login.php");
    exit();
}

// Fetch user balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_balance = $stmt->fetchColumn();

require_once 'includes/header.php'; 
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold mb-2 dark:text-white text-slate-900">Complete Your Order</h1>
            <p class="text-gray-500">Secure your high-performance server in seconds.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card p-8">
                    <h3 class="text-xl font-bold mb-6 dark:text-white text-slate-800">1. Selected Service</h3>
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-neon-pink/10 rounded-lg flex items-center justify-center text-neon-pink text-xl">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h4 class="font-bold dark:text-white text-slate-800"><?php echo htmlspecialchars($plan['name']); ?></h4>
                                <p class="text-xs text-gray-500 uppercase tracking-widest"><?php echo $plan['type']; ?> Hosting</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold dark:text-white text-slate-900">€<?php echo number_format($plan['price'], 2); ?></div>
                            <div class="text-[10px] text-gray-500">per month</div>
                        </div>
                    </div>

                    <ul class="mt-6 grid grid-cols-2 gap-4 text-sm text-gray-500">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['cpu']); ?> CPU</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['ram']); ?> RAM</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['ssd']); ?> NVMe SSD</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> Instant Setup</li>
                    </ul>
                </div>

                <div class="glass-card p-8">
                    <h3 class="text-xl font-bold mb-6 dark:text-white text-slate-800">2. Payment Method</h3>
                    <div class="space-y-4">
                        <!-- Account Balance -->
                        <label class="relative flex items-center p-4 border border-light-border dark:border-dark-border rounded-xl cursor-pointer hover:border-neon-pink transition-all group">
                            <input type="radio" name="payment_method" value="balance" checked class="hidden peer">
                            <div class="w-5 h-5 border-2 border-gray-400 rounded-full flex items-center justify-center peer-checked:border-neon-pink peer-checked:after:content-[''] peer-checked:after:w-2.5 peer-checked:after:h-2.5 peer-checked:after:bg-neon-pink peer-checked:after:rounded-full transition-all"></div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold dark:text-white text-slate-800">Account Balance</span>
                                    <span class="text-sm font-bold text-neon-blue">€<?php echo number_format($user_balance, 2); ?></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pay instantly using your funds.</p>
                            </div>
                        </label>

                        <!-- Stripe -->
                        <label class="relative flex items-center p-4 border border-light-border dark:border-dark-border rounded-xl opacity-50 cursor-not-allowed">
                            <input type="radio" name="payment_method" value="stripe" disabled class="hidden">
                            <div class="w-5 h-5 border-2 border-gray-400 rounded-full"></div>
                            <div class="ml-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold dark:text-white text-slate-800">Stripe / Credit Card</span>
                                    <span class="text-[10px] bg-gray-100 dark:bg-white/5 px-2 py-0.5 rounded text-gray-500 uppercase tracking-widest font-bold">Coming Soon</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Visa, Mastercard, American Express.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Checkout Sidebar -->
            <div class="lg:col-span-1">
                <div class="glass-card p-8 sticky top-24">
                    <h3 class="text-lg font-bold mb-6 dark:text-white text-slate-800">Summary</h3>
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="dark:text-white text-slate-800 font-medium">€<?php echo number_format($plan['price'], 2); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tax / Fee</span>
                            <span class="dark:text-white text-slate-800 font-medium">€0.00</span>
                        </div>
                        <div class="pt-4 border-t border-light-border dark:border-dark-border flex justify-between">
                            <span class="font-bold dark:text-white text-slate-900">Total Due</span>
                            <span class="text-2xl font-bold text-neon-pink">€<?php echo number_format($plan['price'], 2); ?></span>
                        </div>
                    </div>

                    <form action="process_order.php" method="POST">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                        <button type="submit" class="btn-primary w-full py-4 text-center block">
                            Pay Now
                        </button>
                    </form>

                    <p class="mt-6 text-[10px] text-center text-gray-500 leading-relaxed uppercase tracking-widest font-bold">
                        Secure 256-bit SSL Encrypted Connection
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
