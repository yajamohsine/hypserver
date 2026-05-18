<?php 
require_once 'includes/header.php'; 

// Handle Plan Addition
if (isset($_POST['add_plan'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $ssd = $_POST['ssd'];
    $uplink = $_POST['uplink'];
    $type = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO plans (name, price, cpu, ram, ssd, uplink, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $cpu, $ram, $ssd, $uplink, $type]);
    $success = "Plan added successfully!";
}

// Handle Plan Update
if (isset($_POST['edit_plan'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $id = $_POST['plan_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $ssd = $_POST['ssd'];
    $uplink = $_POST['uplink'];
    $type = $_POST['type'];

    $stmt = $pdo->prepare("UPDATE plans SET name = ?, price = ?, cpu = ?, ram = ?, ssd = ?, uplink = ?, type = ? WHERE id = ?");
    $stmt->execute([$name, $price, $cpu, $ram, $ssd, $uplink, $type, $id]);
    $success = "Plan updated successfully!";
}

// Handle Plan Deletion
if (isset($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf_token($_GET['token'])) {
        die("CSRF token validation failed.");
    }
    $stmt = $pdo->prepare("DELETE FROM plans WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Plan deleted successfully.";
}

// Fetch all plans
$stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
$plans = $stmt->fetchAll();
?>

<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold mb-1 text-black dark:text-white">Service Plans</h1>
        <p class="text-slate-800 dark:text-gray-500 text-sm">Configure your RDP and VPS offerings.</p>
    </div>
    <button data-modal-target="add-plan-modal" data-modal-toggle="add-plan-modal" class="btn-admin flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Create Plan
    </button>
</div>

<?php if (isset($success)): ?>
    <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="fa-solid fa-circle-check"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($plans as $p): ?>
    <div class="glass-card p-6 relative group">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-bold text-black dark:text-white"><?php echo htmlspecialchars($p['name']); ?></h3>
                <p class="text-[10px] text-black dark:text-gray-500 uppercase tracking-widest font-bold"><?php echo $p['type']; ?> Hosting</p>
            </div>
            <div class="text-2xl font-bold text-black dark:text-neon-pink">€<?php echo number_format($p['price'], 2); ?></div>
        </div>

        <ul class="space-y-3 mb-8">
            <li class="flex justify-between text-xs text-black dark:text-gray-400 font-bold dark:font-normal">
                <span>CPU:</span> <span class="text-black dark:text-white font-medium"><?php echo htmlspecialchars($p['cpu']); ?></span>
            </li>
            <li class="flex justify-between text-xs text-black dark:text-gray-400 font-bold dark:font-normal">
                <span>RAM:</span> <span class="text-black dark:text-white font-medium"><?php echo htmlspecialchars($p['ram']); ?></span>
            </li>
            <li class="flex justify-between text-xs text-black dark:text-gray-400 font-bold dark:font-normal">
                <span>Storage:</span> <span class="text-black dark:text-white font-medium"><?php echo htmlspecialchars($p['ssd']); ?></span>
            </li>
            <li class="flex justify-between text-xs text-black dark:text-gray-400 font-bold dark:font-normal">
                <span>Network:</span> <span class="text-black dark:text-white font-medium"><?php echo htmlspecialchars($p['uplink']); ?></span>
            </li>
        </ul>

        <div class="flex gap-2">
            <button 
                data-modal-target="edit-plan-modal-<?php echo $p['id']; ?>" 
                data-modal-toggle="edit-plan-modal-<?php echo $p['id']; ?>" 
                class="flex-1 py-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg text-xs font-bold text-black dark:text-white transition-all">
                Edit
            </button>
            <a href="?delete=<?php echo $p['id']; ?>&token=<?php echo generate_csrf_token(); ?>" onclick="return confirm('Are you sure?')" class="w-10 h-10 bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-lg flex items-center justify-center transition-all">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </div>
    </div>

    <!-- Edit Plan Modal for each plan -->
    <div id="edit-plan-modal-<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative glass-card bg-dark-base shadow-2xl">
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-white/5 rounded-t">
                    <h3 class="text-lg font-bold text-black dark:text-white">Edit Plan: <?php echo htmlspecialchars($p['name']); ?></h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-slate-100 dark:hover:bg-white/5 hover:text-black dark:hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="edit-plan-modal-<?php echo $p['id']; ?>">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form class="p-4 md:p-5 space-y-4" method="POST">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="plan_id" value="<?php echo $p['id']; ?>">
                    <div>
                        <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Plan Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Price (€)</label>
                            <input type="number" name="price" step="0.01" value="<?php echo $p['price']; ?>" required class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Type</label>
                            <select name="type" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20">
                                <option value="rdp" <?php echo $p['type'] == 'rdp' ? 'selected' : ''; ?>>RDP</option>
                                <option value="vps" <?php echo $p['type'] == 'vps' ? 'selected' : ''; ?>>VPS</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="cpu" value="<?php echo htmlspecialchars($p['cpu']); ?>" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                        <input type="text" name="ram" value="<?php echo htmlspecialchars($p['ram']); ?>" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="ssd" value="<?php echo htmlspecialchars($p['ssd']); ?>" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                        <input type="text" name="uplink" value="<?php echo htmlspecialchars($p['uplink']); ?>" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    </div>
                    <button type="submit" name="edit_plan" class="w-full btn-admin py-3 mt-4 text-sm">Update Plan</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Plan Modal -->
<div id="add-plan-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative glass-card bg-dark-base shadow-2xl">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-white/5 rounded-t">
                <h3 class="text-lg font-bold text-black dark:text-white">Create New Plan</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-slate-100 dark:hover:bg-white/5 hover:text-black dark:hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="add-plan-modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form class="p-4 md:p-5 space-y-4" method="POST">
                <?php csrf_field(); ?>
                <div>
                    <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Plan Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Price (€)</label>
                        <input type="number" name="price" step="0.01" required class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Type</label>
                        <select name="type" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-3 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20">
                            <option value="rdp">RDP</option>
                            <option value="vps">VPS</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="cpu" placeholder="e.g. 4 vCPU" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    <input type="text" name="ram" placeholder="e.g. 8 GB DDR4" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="ssd" placeholder="e.g. 100 GB NVMe" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                    <input type="text" name="uplink" placeholder="e.g. 1 Gbps" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/20 rounded-xl px-4 py-2 text-black dark:text-white outline-none focus:border-admin-primary focus:ring-1 focus:ring-admin-primary/20 placeholder-gray-500">
                </div>
                <button type="submit" name="add_plan" class="w-full btn-admin py-3 mt-4">Save Plan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
