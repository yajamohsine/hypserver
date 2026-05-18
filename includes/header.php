<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'csrf.php';

// Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com;");
?>
<!DOCTYPE html>
<html lang="en" class="bg-white dark:bg-dark-base">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HyperServer — Premium Cloud Infrastructure</title>

    <!-- Theme Toggle Script (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

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
                        light: {
                            base: '#ffffff',
                            card: '#ffffff',
                            border: '#e2e8f0',
                        },
                        neon: {
                            pink: '#ff2a70',
                            blue: '#0ea5e9',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* HyperServer Component Library - Refactored to Standard CSS to fix IDE Errors */
        .glass-card {
            background-color: #ffffff;
            backdrop-filter: blur(24px);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .dark .glass-card {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        .btn-primary {
            background: linear-gradient(to right, #ff2a70, #b81755);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(255, 42, 112, 0.2);
            display: inline-block;
        }

        .btn-primary:hover {
            box-shadow: 0 20px 25px -5px rgba(255, 42, 112, 0.4);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .dark .btn-outline {
            border-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }

        .btn-outline:hover {
            border-color: #ff2a70;
            color: #ff2a70;
        }

        .neon-border {
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .dark .neon-border {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .neon-border:hover {
            border-color: #ff2a70;
        }

        .text-neon-gradient {
            background: linear-gradient(to right, #ff2a70, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="bg-white text-slate-900 dark:bg-dark-base dark:text-white antialiased overflow-x-hidden">
    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white dark:bg-dark-base backdrop-blur-lg border-b border-light-border dark:border-dark-border">
        <div class="px-3 py-4 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between container mx-auto">
                <div class="flex items-center justify-start shrink-0">
                    <a href="./" class="flex items-center gap-2 text-xl md:text-2xl font-bold tracking-tighter">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-neon-pink rounded-xl flex items-center justify-center shadow-lg shadow-neon-pink/20">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4C4.89543 4 4 4.89543 4 6V9C4 10.1046 4.89543 11 6 11H10V13H6C4.89543 13 4 13.8954 4 15V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V15C20 13.8954 19.1046 13 18 13H14V11H18C19.1046 11 20 10.1046 20 9V6C20 4.89543 19.1046 4 18 4H6ZM17 9C17.8284 9 18.5 8.32843 18.5 7.5C18.5 6.67157 17.8284 6 17 6C16.1716 6 15.5 6.67157 15.5 7.5C15.5 8.32843 16.1716 9 17 9ZM17 18C17.8284 18 18.5 17.3284 18.5 16.5C18.5 15.6716 17.8284 15 17 15C16.1716 15 15.5 15.6716 15.5 16.5C15.5 17.3284 16.1716 18 17 18Z" />
                            </svg>
                        </div>
                        <span class="dark:text-white">HyperServer</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <a href="./#features" class="hover:text-neon-pink dark:hover:text-white transition-colors">Features</a>
                    <a href="./#plans" class="hover:text-neon-pink dark:hover:text-white transition-colors">Plans</a>
                    <a href="./#faq" class="hover:text-neon-pink dark:hover:text-white transition-colors">FAQ</a>
                </div>
                <div class="flex items-center gap-1.5 md:gap-4">
                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-card focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 transition-colors">
                        <i id="theme-toggle-dark-icon" class="hidden fa-solid fa-moon text-lg"></i>
                        <i id="theme-toggle-light-icon" class="hidden fa-solid fa-sun text-lg"></i>
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="text-xs text-gray-500 mr-2 hidden lg:inline"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                        <a href="dashboard.php" class="btn-primary py-2 px-4 text-xs md:text-sm">Dashboard</a>
                    <?php else: ?>
                        <a href="login.php" class="hidden sm:inline text-sm font-semibold hover:text-neon-pink transition-colors dark:text-gray-300">Login</a>
                        <a href="signup.php" class="btn-primary py-2 px-3 md:px-4 text-xs md:text-sm">Get Started</a>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle -->
                    <button data-collapse-toggle="mobile-menu" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 dark:hover:bg-dark-card focus:outline-none focus:ring-2 focus:ring-light-border dark:focus:ring-dark-border" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fa-solid fa-bars-staggered text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu Drawer -->
        <div class="hidden md:hidden bg-light-base dark:bg-dark-base border-b border-light-border dark:border-dark-border transition-colors duration-300" id="mobile-menu">
            <ul class="flex flex-col p-4 font-medium space-y-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="orders.php" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-neon-pink">
                            <i class="fa-solid fa-server w-5"></i> My Orders</a>
                    </li>
                    <li><a href="billing.php" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-neon-pink">
                            <i class="fa-solid fa-file-invoice-dollar w-5"></i> Billing</a>
                    </li>
                    <li><a href="tickets.php" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-neon-pink">
                            <i class="fa-solid fa-ticket w-5"></i> Tickets</a>
                    </li>
                    <li><a href="settings.php" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-neon-pink">
                            <i class="fa-solid fa-gear w-5"></i> Settings</a>
                    </li>
                    <li class="pt-2 border-t border-light-border dark:border-dark-border">
                        <a href="logout.php" class="flex items-center gap-2 text-red-500">
                            <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Logout</a>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="block text-gray-500 dark:text-gray-400 hover:text-neon-pink">Login</a></li>
                    <li><a href="./#features" class="block text-gray-500 dark:text-gray-400 hover:text-neon-pink">Features</a></li>
                    <li><a href="./#plans" class="block text-gray-500 dark:text-gray-400 hover:text-neon-pink">Plans</a></li>
                    <li><a href="./#faq" class="block text-gray-500 dark:text-gray-400 hover:text-neon-pink">FAQ</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="pt-20">