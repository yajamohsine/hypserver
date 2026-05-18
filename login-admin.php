<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin/orders.php");
            exit();
        } else {
            $error = "Invalid admin credentials.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HyperServer.cloud Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05050a;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-admin {
            background: linear-gradient(to right, #8b5cf6, #6366f1);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.4);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-admin-primary bg-gradient-to-br from-[#8b5cf6] to-[#6366f1] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-purple-500/20">
                <i class="fa-solid fa-shield-halved text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Central Command</h1>
            <p class="text-gray-500 text-sm">Please authenticate to access the administration suite.</p>
        </div>

        <div class="glass-card p-8 rounded-2xl">
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Admin Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" required class="w-full bg-white/5 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-white focus:border-purple-500 focus:ring-0 outline-none transition-all" placeholder="admin@hyperserver.cloud">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-white focus:border-purple-500 focus:ring-0 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn-admin w-full py-4 rounded-xl font-bold text-lg">
                    Access Console
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-gray-600 uppercase tracking-widest font-bold">
            Authorized Personnel Only
        </p>
    </div>
</body>

</html>