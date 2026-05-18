<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';

// Fetch plans from database
try {
    $stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
    $plans = $stmt->fetchAll();
} catch (Exception $e) {
    $plans = []; // Fallback
}
?>

<!-- Hero Section -->
<section class="relative pt-12 pb-24 overflow-hidden bg-white dark:bg-dark-base">
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-neon-pink/10 border border-neon-pink/20 text-neon-pink text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-pink opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-neon-pink"></span>
                    </span>
                    Enterprise Cloud Infrastructure
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-8 leading-[1.1] dark:text-white text-slate-900">
                    Next Gen <br>
                    <span class="text-neon-gradient">Remote Desktop</span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-lg md:text-xl max-w-2xl mb-10 leading-relaxed mx-auto lg:mx-0">
                    Deploy high-performance Windows & Linux servers in under 60 seconds.
                    Dedicated resources, NVMe storage, and 10Gbps networking.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="signup.php" class="btn-primary text-lg px-8 py-4 flex items-center justify-center gap-2">
                        Get Started <i class="fa-solid fa-rocket"></i>
                    </a>
                    <a href="#plans" class="btn-outline text-lg px-8 py-4">View All Tiers</a>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="lg:w-1/2 relative">
                <div class="glass-card p-4 border-neon-pink/20 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-pink/10 to-transparent opacity-50"></div>
                    <div class="relative bg-dark-base rounded-xl border border-dark-border p-6 shadow-2xl">
                        <div class="flex items-center gap-2 mb-6 border-b border-dark-border pb-4">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                            </div>
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest ml-4">Terminal — HyperServer.cloud.vps.root</div>
                        </div>
                        <div class="font-mono text-sm space-y-2">
                            <p class="text-green-400">$ HyperServer.cloud deploy --plan pro --region eu-west</p>
                            <p class="text-gray-500">Initializing hypervisor...</p>
                            <p class="text-gray-500">Allocating 8GB RAM & 4 vCPUs...</p>
                            <p class="text-neon-blue">Server online at 185.20.144.102</p>
                            <p class="text-white">_</p>
                        </div>
                    </div>
                </div>
                <!-- Decorative Glows -->
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-neon-pink/20 blur-[100px] rounded-full"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-neon-blue/20 blur-[100px] rounded-full"></div>
            </div>
        </div>
    </div>
</section>

<!-- Stats / Trust -->
<section class="py-16 bg-white dark:bg-dark-base">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
            <div class="text-center group">
                <div class="text-neon-pink text-2xl mb-4 group-hover:scale-110 transition-transform"><i class="fa-solid fa-up-right-from-square"></i></div>
                <h3 class="text-3xl font-bold mb-1">99.99%</h3>
                <p class="text-xs text-gray-500 uppercase tracking-widest">Uptime SLA</p>
            </div>
            <div class="text-center group">
                <div class="text-neon-blue text-2xl mb-4 group-hover:scale-110 transition-transform"><i class="fa-solid fa-bolt-lightning"></i></div>
                <h3 class="text-3xl font-bold mb-1">
                    < 60s</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-widest">Instant Deploy</p>
            </div>
            <div class="text-center group">
                <div class="text-neon-pink text-2xl mb-4 group-hover:scale-110 transition-transform"><i class="fa-solid fa-network-wired"></i></div>
                <h3 class="text-3xl font-bold mb-1">10Gbps</h3>
                <p class="text-xs text-gray-500 uppercase tracking-widest">Network Uplink</p>
            </div>
            <div class="text-center group">
                <div class="text-neon-blue text-2xl mb-4 group-hover:scale-110 transition-transform"><i class="fa-solid fa-earth-americas"></i></div>
                <h3 class="text-3xl font-bold mb-1">Global</h3>
                <p class="text-xs text-gray-500 uppercase tracking-widest">5+ Locations</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4 tracking-tight dark:text-white text-slate-900">Engineered for Professionals</h2>
            <p class="text-gray-500 dark:text-gray-400">Everything you need to scale your remote operations.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-card p-8 hover:border-neon-pink transition-all group">
                <div class="w-12 h-12 rounded-xl bg-neon-pink/10 flex items-center justify-center text-neon-pink text-xl mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 dark:text-white text-slate-800">Instant Deployment</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Your server is online and ready within seconds of payment confirmation. No manual setup required.</p>
            </div>
            <div class="glass-card p-8 hover:border-neon-blue transition-all group">
                <div class="w-12 h-12 rounded-xl bg-neon-blue/10 flex items-center justify-center text-neon-blue text-xl mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-microchip"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 dark:text-white text-slate-800">Dedicated Resources</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">No overselling. Your CPU, RAM, and NVMe storage are dedicated exclusively to your workloads.</p>
            </div>
            <div class="glass-card p-8 hover:border-neon-pink transition-all group">
                <div class="w-12 h-12 rounded-xl bg-neon-pink/10 flex items-center justify-center text-neon-pink text-xl mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 dark:text-white text-slate-800">Secure Network</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Enterprise-grade DDoS protection and encrypted connections keep your data safe and available.</p>
            </div>
        </div>
    </div>
</section>

<!-- Use Cases Section -->
<section id="use-cases" class="py-24 bg-slate-50 dark:bg-white/5">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4 tracking-tight dark:text-white text-slate-900">Built for Professionals</h2>
            <p class="text-gray-500 dark:text-gray-400">Whatever your workload, we have the infrastructure to support it.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-6 hover:bg-neon-pink/5 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-neon-pink/10 flex items-center justify-center text-neon-pink mb-4">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h4 class="font-bold mb-2 dark:text-white text-slate-800">Remote Work</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Access your workspace from any device, anywhere. Stable and low latency.</p>
            </div>
            <div class="glass-card p-6 hover:bg-neon-blue/5 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-neon-blue/10 flex items-center justify-center text-neon-blue mb-4">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h4 class="font-bold mb-2 dark:text-white text-slate-800">Trading</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Run MT4/MT5 and algo-bots 24/7 with zero downtime and fast execution.</p>
            </div>
            <div class="glass-card p-6 hover:bg-neon-pink/5 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-neon-pink/10 flex items-center justify-center text-neon-pink mb-4">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <h4 class="font-bold mb-2 dark:text-white text-slate-800">Automation</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Perfect for scrapers, bots, and scheduled tasks that need reliable uptime.</p>
            </div>
            <div class="glass-card p-6 hover:bg-neon-blue/5 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-neon-blue/10 flex items-center justify-center text-neon-blue mb-4">
                    <i class="fa-solid fa-code"></i>
                </div>
                <h4 class="font-bold mb-2 dark:text-white text-slate-800">Development</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Spin up dev environments or staging servers in seconds with full root access.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-24">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2">
                <h2 class="text-4xl font-bold mb-8 tracking-tight dark:text-white text-slate-900">The HyperServer.cloud Difference</h2>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-full bg-neon-pink/10 flex items-center justify-center text-neon-pink">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <div>
                            <h4 class="font-bold mb-1 dark:text-white text-slate-800">Enterprise Hardware</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">We use only the latest generation AMD EPYC processors and NVMe storage for maximum performance.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-full bg-neon-blue/10 flex items-center justify-center text-neon-blue">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h4 class="font-bold mb-1 dark:text-white text-slate-800">24/7 Expert Support</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Our team of engineers is always on standby to assist you with any technical requirements.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2 grid grid-cols-2 gap-4">
                <div class="glass-card p-6 bg-gradient-to-br from-neon-pink/5 to-transparent">
                    <div class="text-3xl font-bold mb-2 dark:text-white text-slate-800">500+</div>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">Active Users</div>
                </div>
                <div class="glass-card p-6 bg-gradient-to-br from-neon-blue/5 to-transparent">
                    <div class="text-3xl font-bold mb-2 dark:text-white text-slate-800">15k+</div>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">Deployments</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Plans Section -->
<section id="plans" class="py-24 bg-slate-50 dark:bg-white/5">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4 tracking-tight dark:text-white text-slate-900">Transparent Pricing</h2>
            <p class="text-gray-500 dark:text-gray-400">Select the compute power you need.</p>
        </div>

        <?php if (empty($plans)): ?>
            <div class="glass-card p-12 text-center max-w-2xl mx-auto border-dashed border-slate-200 dark:border-gray-700">
                <div class="text-gray-400 dark:text-gray-500 text-5xl mb-6"><i class="fa-solid fa-box-open"></i></div>
                <h3 class="text-xl font-bold mb-2 dark:text-white text-slate-800">No Plans Available</h3>
                <p class="text-gray-500 dark:text-gray-500 mb-8">We are currently updating our pricing tiers. Please check back in a few minutes or contact support.</p>
                <a href="mailto:support@hyperserver.cloud" class="btn-outline px-6 py-2 text-sm">Contact Support</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <?php foreach ($plans as $index => $plan): ?>
                    <?php
                    $isPopular = ($index === 1);
                    $cardClass = $isPopular
                        ? "glass-card p-8 border-neon-pink shadow-2xl shadow-neon-pink/10 relative overflow-hidden scale-105 z-10"
                        : "glass-card p-8 relative overflow-hidden group hover:border-neon-pink/30 transition-all";
                    $btnClass = $isPopular ? "btn-primary" : "btn-outline";
                    $subText = ($plan['type'] == 'rdp') ? 'RDP Tier' : 'VPS Tier';
                    ?>
                    <div class="<?php echo $cardClass; ?>">
                        <?php if ($isPopular): ?>
                            <div class="absolute top-0 right-0 bg-neon-pink text-white text-[10px] font-bold uppercase px-4 py-1 rounded-bl-lg">Most Popular</div>
                        <?php endif; ?>

                        <h4 class="text-sm font-bold uppercase tracking-widest <?php echo $isPopular ? 'text-neon-pink' : 'text-gray-500'; ?> mb-2"><?php echo $subText; ?></h4>
                        <h3 class="text-2xl font-bold mb-6 dark:text-white text-slate-800"><?php echo htmlspecialchars($plan['name']); ?></h3>

                        <div class="flex items-baseline gap-1 mb-8">
                            <span class="text-4xl font-bold dark:text-white text-slate-900">€<?php echo number_format($plan['price'], 0); ?></span>
                            <span class="text-gray-500 text-sm">/mo</span>
                        </div>

                        <ul class="space-y-4 mb-8 text-sm text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['cpu']); ?> Cores</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['ram']); ?> RAM</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['ssd']); ?> SSD</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-neon-pink"></i> <?php echo htmlspecialchars($plan['uplink']); ?> Uplink</li>
                        </ul>

                        <a href="checkout.php?plan=<?php echo $plan['id']; ?>" class="<?php echo $btnClass; ?> block text-center w-full transition-all">
                            Configure Server
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Dashboard Preview -->
<section class="py-24 overflow-hidden">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-16 tracking-tight dark:text-white text-slate-900">Powerful Management Portal</h2>
        <div class="relative max-w-5xl mx-auto glass-card p-4 md:p-8 transform rotate-1 hover:rotate-0 transition-transform duration-700">
            <!-- Mock Dashboard Content -->
            <div class="flex gap-4 mb-8">
                <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="h-20 bg-slate-50 dark:bg-dark-base rounded-xl border border-light-border dark:border-dark-border"></div>
                <div class="h-20 bg-slate-50 dark:bg-dark-base rounded-xl border border-light-border dark:border-dark-border"></div>
                <div class="h-20 bg-slate-50 dark:bg-dark-base rounded-xl border border-light-border dark:border-dark-border"></div>
            </div>
            <div class="h-64 bg-slate-100 dark:bg-dark-base/60 rounded-xl border border-light-border dark:border-dark-border flex items-center justify-center">
                <div class="text-neon-pink/20 text-6xl"><i class="fa-solid fa-chart-line"></i></div>
            </div>
            <!-- Decorative Glow -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-neon-pink/20 blur-3xl rounded-full"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-neon-blue/20 blur-3xl rounded-full"></div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-24 border-t border-light-border dark:border-dark-border">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4 tracking-tight dark:text-white text-slate-900">Frequently Asked Questions</h2>
        </div>
        <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-slate-50 dark:bg-dark-base dark:text-white text-slate-900" data-inactive-classes="text-gray-500 dark:text-gray-400">
            <h2 id="accordion-flush-heading-1">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left border-b border-light-border dark:border-dark-border dark:text-white text-slate-700" data-accordion-target="#accordion-flush-body-1" aria-expanded="true" aria-controls="accordion-flush-body-1">
                    <span>How long does setup take?</span>
                    <i data-accordion-icon class="fa-solid fa-chevron-down shrink-0"></i>
                </button>
            </h2>
            <div id="accordion-flush-body-1" class="hidden" aria-labelledby="accordion-flush-heading-1">
                <div class="py-5 border-b border-light-border dark:border-dark-border">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Your server is automatically provisioned and ready within 60 seconds of payment confirmation. You will receive credentials directly in your client dashboard.</p>
                </div>
            </div>
            <h2 id="accordion-flush-heading-2">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left border-b border-light-border dark:border-dark-border dark:text-white text-slate-700" data-accordion-target="#accordion-flush-body-2" aria-expanded="false" aria-controls="accordion-flush-body-2">
                    <span>Which payment methods are accepted?</span>
                    <i data-accordion-icon class="fa-solid fa-chevron-down shrink-0"></i>
                </button>
            </h2>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="py-5 border-b border-light-border dark:border-dark-border">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">We accept Bitcoin, Ethereum, Monero, and Credit Cards via Stripe. All crypto payments are confirmed after 1-2 confirmations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>