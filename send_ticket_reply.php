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
    $ticket_id = $_POST['ticket_id'];
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        try {
            // Insert reply
            $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin) VALUES (?, ?, ?, 0)");
            $stmt->execute([$ticket_id, $user_id, $message]);
            
            // Update ticket status to Open (client replied)
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'Open' WHERE id = ?");
            $stmt->execute([$ticket_id]);
            
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
