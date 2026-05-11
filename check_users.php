<?php
require_once 'src/services/db.php';
global $pdo;
$stmt = $pdo->query("DESCRIBE users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
