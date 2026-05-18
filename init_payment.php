<?php 
session_start();
require_once 'config/db.php';

require_once 'includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $amount = $_POST['amount'] ?? 0;
    $method = $_POST['method'] ?? 'stripe';

    if ($amount < 5) {
        header("Location: billing.php?error=min_amount");
        exit();
    }

    // In a real Stripe implementation, you would do this:
    /*
    require 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey('sk_test_...');
    $session = \Stripe\Checkout\Session::create([...]);
    header("Location: " . $session->url);
    */

    // For now, we simulate the flow
    $_SESSION['pending_deposit'] = [
        'amount' => $amount,
        'method' => $method,
        'token' => bin2hex(random_bytes(16))
    ];

    // Redirect to a mock "Payment Processing" page
    header("Location: mock_payment.php?token=" . $_SESSION['pending_deposit']['token']);
    exit();
}
