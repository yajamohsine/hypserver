<?php
require_once __DIR__ . '/../config/db.php';

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
        // Show schema
        $desc = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($desc as $col) {
            echo "  * {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
