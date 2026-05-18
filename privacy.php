<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold mb-4 dark:text-white text-slate-900 tracking-tight">Privacy Policy</h1>
            <p class="text-gray-500">How we collect, use, and protect your data.</p>
        </div>

        <div class="glass-card p-8 md:p-12 mb-12">
            <div class="space-y-8 text-sm dark:text-gray-300 text-slate-700 leading-relaxed">
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">1. Information We Collect</h3>
                    <p class="mb-2">We may collect the following information:</p>
                    <ul class="list-disc pl-5 space-y-1 text-gray-500">
                        <li>Name</li>
                        <li>Email address</li>
                        <li>Billing information</li>
                        <li>IP address</li>
                        <li>Usage activity</li>
                        <li>Support communications</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">2. How We Use Information</h3>
                    <p class="mb-2">Collected information may be used to:</p>
                    <ul class="list-disc pl-5 space-y-1 text-gray-500">
                        <li>Provide and manage services</li>
                        <li>Process payments</li>
                        <li>Improve platform performance</li>
                        <li>Respond to support requests</li>
                        <li>Prevent abuse and fraud</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">3. Payment Information</h3>
                    <p>Payments are processed through secure third-party payment providers. HyperServer does not store sensitive payment credentials.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">4. Data Security</h3>
                    <p>We implement reasonable security measures to protect user information and infrastructure. However, no online platform can guarantee absolute security.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">5. Cookies</h3>
                    <p>Our website may use cookies to improve user experience, authentication, and analytics. Users may disable cookies through browser settings.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">6. Third-Party Services</h3>
                    <p>Some services may rely on third-party providers such as payment gateways or analytics services. These providers may process certain information according to their own privacy policies.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">7. Changes to Privacy Policy</h3>
                    <p>We reserve the right to modify this Privacy Policy at any time. Updates become effective immediately after publication.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
