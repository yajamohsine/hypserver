<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold mb-4 dark:text-white text-slate-900 tracking-tight">Terms of Service</h1>
            <p class="text-gray-500">Please read these terms carefully before using our services.</p>
        </div>

        <div class="glass-card p-8 md:p-12 mb-12">
            <div class="space-y-8 text-sm dark:text-gray-300 text-slate-700 leading-relaxed">
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">1. Introduction</h3>
                    <p>Welcome to HyperServer. By accessing or using our services, you agree to comply with these Terms of Service. If you do not agree with these terms, please discontinue the use of our platform immediately.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">2. Services</h3>
                    <p>HyperServer provides cloud infrastructure services including virtual machines, remote desktop solutions, and related hosting services. All services are provided based on infrastructure availability.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">3. User Responsibilities</h3>
                    <p class="mb-2">Users are responsible for maintaining the security of their accounts and activities performed using our services. You agree not to use HyperServer for:</p>
                    <ul class="list-disc pl-5 space-y-1 text-gray-500">
                        <li>Illegal activities</li>
                        <li>Spam or phishing</li>
                        <li>Malware distribution</li>
                        <li>Unauthorized network attacks</li>
                        <li>Abuse of infrastructure resources</li>
                        <li>Hosting harmful or malicious content</li>
                    </ul>
                    <p class="mt-2 text-neon-pink font-medium">Violation may result in account suspension or permanent termination.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">4. Payments & Billing</h3>
                    <p>All services must be paid in advance unless otherwise specified. Failure to pay invoices before the due date may result in service suspension or termination.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">5. Refund Policy</h3>
                    <p>Due to the nature of digital infrastructure services, refunds are generally not available after service deployment. Refund requests may be reviewed individually at our discretion.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">6. Service Availability</h3>
                    <p>We strive to maintain reliable uptime and stable infrastructure; however, uninterrupted availability is not guaranteed. Scheduled maintenance or unexpected outages may occasionally occur.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">7. Account Suspension</h3>
                    <p>HyperServer reserves the right to suspend or terminate any account involved in abuse, fraudulent activity, or violations of these Terms.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg dark:text-white text-slate-900 mb-2">8. Modifications</h3>
                    <p>We may update these Terms of Service at any time without prior notice. Continued use of our services indicates acceptance of the updated terms.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
