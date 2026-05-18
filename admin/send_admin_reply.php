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
    $admin_id = $_SESSION['admin_id'] ?? 0; // Or just use 0/1 for admin
    $message = trim($_POST['message']);
    $status = $_POST['status'] ?? 'Answered';

    if (!empty($message)) {
        try {
            // Insert reply (user_id is NULL for admin replies)
            $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin) VALUES (?, NULL, ?, 1)");
            $stmt->execute([$ticket_id, $message]);
            
            // Update ticket status
            $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->execute([$status, $ticket_id]);
            
            header("Location: view_ticket.php?id=" . $ticket_id . "&success=reply_sent");
            exit();
        } catch (Exception $e) {
            header("Location: view_ticket.php?id=" . $ticket_id . "&error=db_error");
            exit();
        }
    } else {
        header("Location: view_ticket.php?id=" . $ticket_id . "&error=empty_message");
        exit();
    }
} else {
    header("Location: tickets.php");
    exit();
}
?>
