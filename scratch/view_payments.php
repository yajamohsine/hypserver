<?php
require_once __DIR__ . '/../config/db.php';

try {
    $payments = $pdo->query("SELECT * FROM payments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo "Current payments in DB (" . count($payments) . "):\n";
    foreach ($payments as $p) {
        echo "ID: {$p['id']} | User ID: {$p['user_id']} | Order: {$p['order_id']} | Amount: €{$p['amount']} | Status: {$p['status']} | Created: {$p['created_at']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
