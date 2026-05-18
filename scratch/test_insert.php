<?php
require_once __DIR__ . '/../config/db.php';

try {
    // 1. Get a valid user_id
    $userStmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $user_id = $userStmt->fetchColumn();
    
    if (!$user_id) {
        die("No users found in database to test with.");
    }
    
    echo "Testing with user_id: $user_id\n";
    
    $order_id = 'DEP-' . strtoupper(bin2hex(random_bytes(5)));
    $amount = 10.00;
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (user_id, order_id, amount, currency, status, provider) 
        VALUES (?, ?, ?, 'EUR', 'pending', 'cryptomus')
    ");
    $stmt->execute([$user_id, $order_id, $amount]);
    
    echo "Insert successful! ID: " . $pdo->lastInsertId() . "\n";
    
    // Clean up
    $pdo->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$order_id]);
    echo "Test payment cleaned up successfully.\n";

} catch (PDOException $e) {
    echo "SQL Exception Caught:\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Message: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Exception Caught:\n";
    echo "Message: " . $e->getMessage() . "\n";
}
?>
