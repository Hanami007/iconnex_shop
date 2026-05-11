<?php
require_once 'db.php';
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
    echo "Column user_id added successfully";
} catch (PDOException $e) {
    echo "Error adding column: " . $e->getMessage();
}
?>
