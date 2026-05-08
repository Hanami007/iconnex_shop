<?php
require_once 'db.php';
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN line_id VARCHAR(100) DEFAULT NULL AFTER customer_phone");
    $pdo->exec("ALTER TABLE orders ADD COLUMN slip_image VARCHAR(255) DEFAULT NULL AFTER items_json");
    echo "Columns added successfully.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
