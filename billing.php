<?php 
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/header.php'; 
require_once 'includes/sidebar.php'; 

$user_id = $_SESSION['user_id'];

// Fetch current balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn();
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="mb-10">
            <h1 class="text-3xl font-bold mb-1 dark:text-white text-slate-900">Billing & Funds</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Top up your account balance to deploy more servers.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Deposit Form -->
            <div class="lg:col-span-2">
                <div class="glass-card p-8">
                    <h3 class="text-xl font-bold mb-6 dark:text-white text-slate-800">Add Funds</h3>
                    
                    <form action="payments/init_payment.php" method="POST" class="space-y-6" id="deposit-form">
                        <?php csrf_field(); ?>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Amount to Deposit (EUR)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-bold">€</span>
                                <input type="number" name="amount" min="5" max="5000" step="0.01" required class="w-full bg-slate-50 dark:bg-dark-base border border-light-border dark:border-dark-border rounded-xl pl-10 pr-4 py-4 dark:text-white text-slate-800 text-xl font-bold focus:border-neon-pink focus:ring-0 outline-none transition-all" placeholder="25.00">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-2 uppercase tracking-widest">Minimum deposit: €5.00 | Maximum: €5,000.00</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Stripe Option -->
                            <label id="label-stripe" class="relative flex items-center p-4 border border-neon-pink bg-neon-pink/5 rounded-xl cursor-pointer transition-all">
                                <input type="radio" name="method" value="stripe" checked class="hidden" onchange="togglePaymentMethod('stripe')">
                                <div class="w-10 h-10 bg-white dark:bg-white/10 rounded flex items-center justify-center text-neon-pink">
                                    <i class="fa-brands fa-stripe text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold dark:text-white text-slate-800">Credit Card</div>
                                    <div class="text-[10px] text-gray-500">Stripe Secure</div>
                                </div>
                                <div class="absolute top-4 right-4 text-neon-pink check-icon">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </label>

                            <!-- Cryptomus Option -->
                            <label id="label-cryptomus" class="relative flex items-center p-4 border border-light-border dark:border-dark-border rounded-xl cursor-pointer hover:border-neon-blue transition-all group">
                                <input type="radio" name="method" value="cryptomus" class="hidden" onchange="togglePaymentMethod('cryptomus')">
                                <div class="w-10 h-10 bg-white dark:bg-white/10 rounded flex items-center justify-center text-gray-400 group-hover:text-neon-blue transition-colors icon-container">
                                    <i class="fa-brands fa-bitcoin text-2xl"></i>
                                </div>
                                <div class="ml-4 text-container">
                                    <div class="font-bold dark:text-white text-slate-800 group-hover:text-neon-blue transition-colors method-title">Crypto</div>
                                    <div class="text-[10px] text-gray-500">Cryptomus (BTC, ETH, USDT)</div>
                                </div>
                                <div class="absolute top-4 right-4 text-neon-blue check-icon hidden">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary w-full py-4 text-center block text-lg">
                            Proceed to Payment
                        </button>
                    </form>

                    <script>
                    function togglePaymentMethod(method) {
                        const stripeLabel = document.getElementById('label-stripe');
                        const cryptomusLabel = document.getElementById('label-cryptomus');
                        
                        const stripeCheck = stripeLabel.querySelector('.check-icon');
                        const cryptomusCheck = cryptomusLabel.querySelector('.check-icon');
                        
                        const cryptomusIcon = cryptomusLabel.querySelector('.icon-container');
                        const cryptomusTitle = cryptomusLabel.querySelector('.method-title');

                        if (method === 'stripe') {
                            // Active Stripe
                            stripeLabel.className = "relative flex items-center p-4 border border-neon-pink bg-neon-pink/5 rounded-xl cursor-pointer transition-all";
                            stripeCheck.classList.remove('hidden');
                            
                            // Inactive Cryptomus
                            cryptomusLabel.className = "relative flex items-center p-4 border border-light-border dark:border-dark-border rounded-xl cursor-pointer hover:border-neon-blue transition-all group";
                            cryptomusCheck.classList.add('hidden');
                            cryptomusIcon.className = "w-10 h-10 bg-white dark:bg-white/10 rounded flex items-center justify-center text-gray-400 group-hover:text-neon-blue transition-colors icon-container";
                            cryptomusTitle.className = "font-bold dark:text-white text-slate-800 group-hover:text-neon-blue transition-colors method-title";
                        } else {
                            // Active Cryptomus
                            cryptomusLabel.className = "relative flex items-center p-4 border border-neon-blue bg-neon-blue/5 rounded-xl cursor-pointer transition-all";
                            cryptomusCheck.classList.remove('hidden');
                            cryptomusIcon.className = "w-10 h-10 bg-white dark:bg-white/10 rounded flex items-center justify-center text-neon-blue transition-colors icon-container";
                            cryptomusTitle.className = "font-bold dark:text-white text-slate-800 text-neon-blue transition-colors method-title";

                            // Inactive Stripe
                            stripeLabel.className = "relative flex items-center p-4 border border-light-border dark:border-dark-border rounded-xl cursor-pointer hover:border-neon-pink transition-all group";
                            stripeCheck.classList.add('hidden');
                        }
                    }
                    </script>
                </div>
            </div>

            <!-- Balance Summary -->
            <div class="lg:col-span-1">
                <div class="glass-card p-8 bg-gradient-to-br from-neon-blue/5 to-transparent border-neon-blue/20">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500 mb-6">Current Balance</h3>
                    <div class="text-5xl font-extrabold text-neon-blue mb-2">€<?php echo number_format($balance, 2); ?></div>
                    <p class="text-xs text-gray-500">Available for immediate server deployments.</p>
                    
                    <div class="mt-10 pt-10 border-t border-white/5 space-y-4">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <i class="fa-solid fa-shield-halved text-neon-pink"></i>
                            Secure Payments
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <i class="fa-solid fa-bolt text-neon-pink"></i>
                            Instant Credit
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
