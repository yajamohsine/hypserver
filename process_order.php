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
    $user_id = $_SESSION['user_id'];
    $plan_id = $_POST['plan_id'] ?? 0;

    // Fetch Plan Details
    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$plan_id]);
    $plan = $stmt->fetch();

    if (!$plan) {
        die("Invalid plan selected.");
    }

    // Fetch User Balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $balance = $stmt->fetchColumn();

    if ($balance >= $plan['price']) {
        // Sufficient Balance - Process Order
        $pdo->beginTransaction();

        try {
            // 1. Deduct Balance
            $new_balance = $balance - $plan['price'];
            $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$new_balance, $user_id]);

            // 2. Create Order
            // Status = Paid because balance was deducted
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, plan_id, plan_name, amount, status, payment_status) VALUES (?, ?, ?, ?, 'Paid', 'Paid')");
            $stmt->execute([$user_id, $plan['id'], $plan['name'], $plan['price']]);

            $pdo->commit();
            header("Location: dashboard.php?order=success");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error processing order: " . $e->getMessage());
        }
    } else {
        // Insufficient Balance
        header("Location: dashboard.php?error=insufficient_balance");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
