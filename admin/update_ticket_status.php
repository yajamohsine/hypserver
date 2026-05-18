<?php
session_start();
require_once '../config/db.php';

require_once '../includes/csrf.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login-admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->execute([$status, $ticket_id]);
        
        header("Location: tickets.php?success=status_updated");
        exit();
    } catch (Exception $e) {
        header("Location: tickets.php?error=db_error");
        exit();
    }
} else {
    header("Location: tickets.php");
    exit();
}
?>
