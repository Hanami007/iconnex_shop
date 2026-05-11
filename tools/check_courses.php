<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=iconnex;charset=utf8', 'root', 'Baskbask5678');
    $stmt = $pdo->query('DESCRIBE courses');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
