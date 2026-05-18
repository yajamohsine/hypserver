<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';
require_once '../includes/csrf.php';

// Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com;");

// Simple Admin Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login-admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HyperServer Admin — Central Command</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            base: '#05050a',
                            card: 'rgba(255, 255, 255, 0.03)',
                            border: 'rgba(255, 255, 255, 0.08)',
                        },
                        admin: {
                            primary: '#8b5cf6', // Purple for Admin
                            secondary: '#6366f1',
                        },
                        neon: {
                            pink: '#ff2a70',
                            blue: '#0ea5e9',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #05050a;
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        body.light-mode {
            background-color: #ffffff;
            color: #1e293b;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
        }

        .light-mode .glass-card {
            background: white;
            border-color: #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-admin {
            background: linear-gradient(to right, #8b5cf6, #6366f1);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            color: #94a3b8;
            transition: all 0.2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(139, 92, 246, 0.1);
            color: #a78bfa;
        }

        .light-mode .sidebar-link:hover,
        .light-mode .sidebar-link.active {
            background: #f1f5f9;
            color: #8b5cf6;
        }

        .light-mode #logo-sidebar {
            background-color: white;
            border-color: #e2e8f0;
        }
    </style>
    <script>
        // Theme Management
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            document.body.classList.toggle('light-mode', !isDark);
            localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('admin-theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.body.classList.add('light-mode');
            } else {
                document.documentElement.classList.add('dark');
                document.body.classList.remove('light-mode');
            }
        });
    </script>
</head>

<body class="antialiased">
    <!-- Mobile Top Nav -->
    <nav class="fixed top-0 z-50 w-full bg-white dark:bg-dark-base sm:hidden border-b border-light-border dark:border-dark-border py-4 px-4 flex justify-between items-center dark:text-white text-slate-800 transition-colors duration-300 shadow-sm sm:shadow-none">
        <a href="./" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-admin-primary rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4C4.89543 4 4 4.89543 4 6V9C4 10.1046 4.89543 11 6 11H10V13H6C4.89543 13 4 13.8954 4 15V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V15C20 13.8954 19.1046 13 18 13H14V11H18C19.1046 11 20 10.1046 20 9V6C20 4.89543 19.1046 4 18 4H6ZM17 9C17.8284 9 18.5 8.32843 18.5 7.5C18.5 6.67157 17.8284 6 17 6C16.1716 6 15.5 6.67157 15.5 7.5C15.5 8.32843 16.1716 9 17 9ZM17 18C17.8284 18 18.5 17.3284 18.5 16.5C18.5 15.6716 17.8284 15 17 15C16.1716 15 15.5 15.6716 15.5 16.5C15.5 17.3284 16.1716 18 17 18Z" />
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tighter">HyperServer</span>
        </a>
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="p-2 text-gray-500 rounded-lg sm:hidden hover:bg-white/5 focus:outline-none">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
    </nav>

    <!-- Admin Sidebar -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 border-r border-dark-border bg-dark-base shadow-2xl">
        <div class="h-full px-4 py-8 flex flex-col">
            <a href="./" class="flex items-center gap-2 mb-10 px-2">
                <div class="w-8 h-8 bg-admin-primary rounded flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4C4.89543 4 4 4.89543 4 6V9C4 10.1046 4.89543 11 6 11H10V13H6C4.89543 13 4 13.8954 4 15V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V15C20 13.8954 19.1046 13 18 13H14V11H18C19.1046 11 20 10.1046 20 9V6C20 4.89543 19.1046 4 18 4H6ZM17 9C17.8284 9 18.5 8.32843 18.5 7.5C18.5 6.67157 17.8284 6 17 6C16.1716 6 15.5 6.67157 15.5 7.5C15.5 8.32843 16.1716 9 17 9ZM17 18C17.8284 18 18.5 17.3284 18.5 16.5C18.5 15.6716 17.8284 15 17 15C16.1716 15 15.5 15.6716 15.5 16.5C15.5 17.3284 16.1716 18 17 18Z" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tighter dark:text-white text-slate-800">HyperServer</span>
            </a>

            <?php $cur = basename($_SERVER['PHP_SELF']); ?>
            <ul class="space-y-2 font-medium flex-1">
                <li>
                    <a href="./" class="sidebar-link <?php echo $cur == 'index.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="sidebar-link <?php echo $cur == 'orders.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li>
                    <a href="users.php" class="sidebar-link <?php echo $cur == 'users.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="tickets.php" class="sidebar-link <?php echo $cur == 'tickets.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Tickets</span>
                    </a>
                </li>
                <li>
                    <a href="plans.php" class="sidebar-link <?php echo $cur == 'plans.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Plans</span>
                    </a>
                </li>
            </ul>

            <div class="pt-10 space-y-4">
                <button onclick="toggleTheme()" class="w-full flex items-center gap-3 p-3 text-gray-400 rounded-xl hover:bg-white/5 transition-all">
                    <i class="fa-solid fa-circle-half-stroke text-admin-primary"></i>
                    <span class="text-sm font-medium">Switch Theme</span>
                </button>

                <div class="border-t border-white/5 pt-4">
                    <a href="../logout.php" class="sidebar-link text-red-400 hover:bg-red-400/10">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <div class="sm:ml-64 p-4 md:p-8 pt-20 sm:pt-8 transition-all">