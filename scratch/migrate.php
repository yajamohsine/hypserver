<?php
require_once __DIR__ . '/../config/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id VARCHAR(100) NOT NULL,
    provider_payment_id VARCHAR(255) DEFAULT NULL,
    provider VARCHAR(50) DEFAULT 'cryptomus',
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'EUR',
    status ENUM('pending', 'processing', 'paid', 'paid_over', 'failed', 'expired', 'cancelled') DEFAULT 'pending',
    wallet_address VARCHAR(255) DEFAULT NULL,
    network VARCHAR(100) DEFAULT NULL,
    webhook_payload LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_order_id (order_id),
    UNIQUE INDEX idx_provider_payment_id (provider_payment_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
";

try {
    $pdo->exec($sql);
    echo "SUCCESS: payments table created successfully!\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
