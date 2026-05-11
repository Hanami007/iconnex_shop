<?php
require_once 'src/services/db.php';
global $pdo;
$stmt = $pdo->query("SHOW TABLES");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
