<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
</div> <!-- End of pt-20 -->

<footer class="<?php echo ($current_page != 'index.php') ? 'sm:ml-64' : ''; ?> bg-light-base dark:bg-dark-base border-t border-light-border dark:border-dark-border mt-20 pt-16 pb-8 transition-colors duration-300">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div class="col-span-1 md:col-span-1">
                <a href="./" class="flex items-center gap-2 text-2xl font-bold tracking-tighter mb-6">
                    <div class="w-10 h-10 bg-neon-pink rounded-xl flex items-center justify-center shadow-lg shadow-neon-pink/20">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4C4.89543 4 4 4.89543 4 6V9C4 10.1046 4.89543 11 6 11H10V13H6C4.89543 13 4 13.8954 4 15V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V15C20 13.8954 19.1046 13 18 13H14V11H18C19.1046 11 20 10.1046 20 9V6C20 4.89543 19.1046 4 18 4H6ZM17 9C17.8284 9 18.5 8.32843 18.5 7.5C18.5 6.67157 17.8284 6 17 6C16.1716 6 15.5 6.67157 15.5 7.5C15.5 8.32843 16.1716 9 17 9ZM17 18C17.8284 18 18.5 17.3284 18.5 16.5C18.5 15.6716 17.8284 15 17 15C16.1716 15 15.5 15.6716 15.5 16.5C15.5 17.3284 16.1716 18 17 18Z" />
                        </svg>
                    </div>
                    HyperServer.cloud
                </a>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6">
                    Next-generation cloud infrastructure for professionals. Deploy high-performance remote servers in seconds.
                </p>
                <div class="flex gap-4 text-xl text-gray-400 dark:text-gray-500">
                    <i class="fa-brands fa-telegram hover:text-neon-pink cursor-pointer transition-colors"></i>
                    <i class="fa-brands fa-discord hover:text-neon-blue cursor-pointer transition-colors"></i>
                    <i class="fa-brands fa-x-twitter hover:text-white cursor-pointer transition-colors"></i>
                </div>
            </div>
            <div>
                <h4 class="font-bold mb-6 uppercase text-xs tracking-widest text-gray-500">Services</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="#plans" class="hover:text-neon-pink transition-colors">Windows RDP</a></li>
                    <li><a href="#plans" class="hover:text-neon-pink transition-colors">Linux VPS</a></li>
                    <li><a href="#plans" class="hover:text-neon-pink transition-colors">Dedicated Servers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6 uppercase text-xs tracking-widest text-gray-500">Company</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="terms" class="hover:text-neon-pink transition-colors">Terms of Service</a></li>
                    <li><a href="privacy" class="hover:text-neon-pink transition-colors">Privacy Policy</a></li>
                    <li><a href="help" class="hover:text-neon-pink transition-colors">Help Center</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6 uppercase text-xs tracking-widest text-gray-500">Payment</h4>
                <div class="flex flex-wrap gap-4 text-2xl text-gray-500 mb-6">
                    <i class="fa-brands fa-bitcoin hover:text-orange-500 cursor-pointer transition-colors"></i>
                    <i class="fa-brands fa-ethereum hover:text-blue-400 cursor-pointer transition-colors"></i>
                    <i class="fa-brands fa-monero hover:text-orange-600 cursor-pointer transition-colors"></i>
                    <i class="fa-solid fa-credit-card hover:text-white cursor-pointer transition-colors"></i>
                </div>
                <p class="text-[10px] text-gray-600 uppercase tracking-widest leading-relaxed">
                    Secure payments via 256-bit encryption. <br>
                    Instant delivery after confirmation.
                </p>
            </div>
        </div>
        <div class="border-t border-light-border dark:border-dark-border pt-8 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-600">&copy; 2026 HyperServer.cloud Infrastructure. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script>
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Change the icons inside the button based on previous settings
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
    }

    var themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {
        // toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // if set via local storage previously
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }

            // if NOT set via local storage previously
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }
    });
</script>
<script>
    function showOrderDetails(orderId) {
        document.getElementById('orders-list-view').classList.add('hidden');
        // Hide all potential detail panels first
        document.querySelectorAll('[id^="order-details-"]').forEach(el => el.classList.add('hidden'));
        // Show the specific one
        document.getElementById('order-details-' + orderId).classList.remove('hidden');
    }

    function hideOrderDetails() {
        document.querySelectorAll('[id^="order-details-"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('orders-list-view').classList.remove('hidden');
    }

    function togglePassInline(id) {
        const passInput = document.getElementById('inline-pass-' + id);
        const eyeIcon = document.getElementById('inline-eye-' + id);
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function toggleCredentials(id) {
        const credsDiv = document.getElementById('creds-' + id);
        credsDiv.classList.toggle('hidden');
    }
</script>
</body>

</html>