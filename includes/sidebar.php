<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-light-base dark:bg-dark-base border-r border-light-border dark:border-dark-border sm:translate-x-0 transition-colors duration-300" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-light-base dark:bg-dark-base transition-colors duration-300">
      <ul class="space-y-2 font-medium">
         <li>
            <a href="dashboard.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-slate-100 dark:hover:bg-dark-card hover:text-slate-900 dark:hover:text-white group <?php echo ($current_page == 'dashboard.php') ? 'bg-slate-100 dark:bg-dark-card text-neon-pink' : ''; ?>">
               <i class="fa-solid fa-chart-pie w-5 h-5 transition duration-75 group-hover:text-neon-pink"></i>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="orders.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-slate-100 dark:hover:bg-dark-card hover:text-slate-900 dark:hover:text-white group <?php echo ($current_page == 'orders.php') ? 'bg-slate-100 dark:bg-dark-card text-neon-pink' : ''; ?>">
               <i class="fa-solid fa-server w-5 h-5 transition duration-75 group-hover:text-neon-pink"></i>
               <span class="ms-3">My Orders</span>
            </a>
         </li>
         <li>
            <a href="billing.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-slate-100 dark:hover:bg-dark-card hover:text-slate-900 dark:hover:text-white group <?php echo ($current_page == 'billing.php') ? 'bg-slate-100 dark:bg-dark-card text-neon-pink' : ''; ?>">
               <i class="fa-solid fa-file-invoice-dollar w-5 h-5 transition duration-75 group-hover:text-neon-pink"></i>
               <span class="ms-3">Billing</span>
            </a>
         </li>
         <li>
            <a href="tickets.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-slate-100 dark:hover:bg-dark-card hover:text-slate-900 dark:hover:text-white group <?php echo ($current_page == 'tickets.php') ? 'bg-slate-100 dark:bg-dark-card text-neon-pink' : ''; ?>">
               <i class="fa-solid fa-ticket w-5 h-5 transition duration-75 group-hover:text-neon-pink"></i>
               <span class="ms-3">Tickets</span>
            </a>
         </li>
         <li class="pt-4 mt-4 border-t border-light-border dark:border-dark-border">
            <a href="settings.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-slate-100 dark:hover:bg-dark-card hover:text-slate-900 dark:hover:text-white group <?php echo ($current_page == 'settings.php') ? 'bg-slate-100 dark:bg-dark-card text-neon-pink' : ''; ?>">
               <i class="fa-solid fa-gear w-5 h-5 transition duration-75 group-hover:text-neon-pink"></i>
               <span class="ms-ms-3">Settings</span>
            </a>
         </li>
         <li>
            <a href="logout.php" class="flex items-center p-3 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-600 dark:hover:text-red-500 group">
               <i class="fa-solid fa-arrow-right-from-bracket w-5 h-5 transition duration-75 group-hover:text-red-600 dark:group-hover:text-red-500"></i>
               <span class="ms-3">Logout</span>
            </a>
         </li>
      </ul>
   </div>
</aside>