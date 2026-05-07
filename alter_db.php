<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE courses ADD COLUMN content_json JSON DEFAULT NULL");
    echo "Success";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
