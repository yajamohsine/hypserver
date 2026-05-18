<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold mb-4 dark:text-white text-slate-900 tracking-tight">Help Center</h1>
            <p class="text-gray-500">Frequently asked questions and support resources.</p>
        </div>

        <div class="glass-card p-8 md:p-12 mb-12">
            <h3 class="font-bold text-xl dark:text-white text-slate-900 mb-6 border-b border-light-border dark:border-dark-border pb-4">Frequently Asked Questions</h3>
            
            <div class="space-y-6 text-sm">
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">How long does server deployment take?</h4>
                    <p class="text-gray-500">Most services are deployed instantly after successful payment confirmation.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">How do I connect to my server?</h4>
                    <p class="text-gray-500">You can connect using Remote Desktop Connection (RDP) or any compatible remote access client using the credentials provided in your dashboard.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">Where can I find my server credentials?</h4>
                    <p class="text-gray-500">Server IP address, username, and password are available inside your client dashboard under "My Orders".</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">What payment methods are accepted?</h4>
                    <p class="text-gray-500">We currently support cryptocurrency payments through secure payment gateways.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">Can I renew my server?</h4>
                    <p class="text-gray-500">Yes. Active services can be renewed before their expiration date directly from the billing section.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">What happens if my invoice is unpaid?</h4>
                    <p class="text-gray-500">Unpaid invoices may lead to temporary suspension or permanent termination of the service.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">How do I open a support ticket?</h4>
                    <p class="text-gray-500">You can create a support ticket directly from the Tickets section in your dashboard.</p>
                </div>
                <div>
                    <h4 class="font-bold dark:text-white text-slate-800 text-base mb-1">How fast is support response time?</h4>
                    <p class="text-gray-500">We aim to respond to most support requests within a few hours depending on workload and priority.</p>
                </div>
            </div>

            <div class="mt-12 bg-admin-primary/10 border border-admin-primary/20 rounded-2xl p-6 text-center">
                <h4 class="font-bold text-lg dark:text-white text-slate-900 mb-2">Need More Help?</h4>
                <p class="text-gray-500 mb-4">Contact our support team directly for assistance.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 text-sm font-medium">
                    <a href="mailto:support@hyperserver.cloud" class="flex items-center justify-center gap-2 px-6 py-3 bg-admin-primary text-white rounded-xl hover:opacity-90 transition-opacity">
                        <i class="fa-solid fa-envelope"></i> support@hyperserver.cloud
                    </a>
                    <a href="https://hyperserver.cloud" class="flex items-center justify-center gap-2 px-6 py-3 border border-light-border dark:border-dark-border rounded-xl dark:text-white text-slate-800 hover:border-admin-primary transition-colors">
                        <i class="fa-solid fa-globe"></i> hyperserver.cloud
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
