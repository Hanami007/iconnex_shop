<?php
$pdo = new PDO('mysql:host=localhost;dbname=iconnex;charset=utf8', 'root', 'Baskbask5678');
$stmt = $pdo->query('DESCRIBE notifications');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . "\n";
}
