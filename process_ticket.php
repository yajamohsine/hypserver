<?php
session_start();
require_once 'config/db.php';

require_once 'includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $user_id = $_SESSION['user_id'];
    $subject = trim($_POST['subject']);
    $department = $_POST['department'];
    $priority = $_POST['priority'];
    $message = trim($_POST['message']);

    if (!empty($subject) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (user_id, subject, department, priority, message, status) VALUES (?, ?, ?, ?, ?, 'Open')");
            $stmt->execute([$user_id, $subject, $department, $priority, $message]);
            
            header("Location: tickets.php?success=ticket_created");
            exit();
        } catch (Exception $e) {
            header("Location: tickets.php?error=db_error");
            exit();
        }
    } else {
        header("Location: tickets.php?error=empty_fields");
        exit();
    }
} else {
    header("Location: tickets.php");
    exit();
}
?>
