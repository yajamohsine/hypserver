<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Detect directory nesting for relative redirection paths
$isPaymentSubdir = (strpos($_SERVER['SCRIPT_NAME'], '/payments/') !== false);
$loginRedirect = $isPaymentSubdir ? '../login.php' : 'login.php';
$adminRedirect = $isPaymentSubdir ? '../admin/orders.php' : 'admin/orders.php';

// 1. Ensure session contains user_id
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $loginRedirect);
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch User from Database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // 3. Fallback: Check if they are an admin
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$user_id]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: " . $adminRedirect);
        exit();
    } else {
        // 4. Stale session/invalid user: Clean logout
        session_destroy();
        header("Location: " . $loginRedirect . "?error=account_not_found");
        exit();
    }
}

// Keep a clean global handle for individual pages to access pre-fetched data
$GLOBALS['currentUser'] = $user;
?>
