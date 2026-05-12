<?php
global $pdo;
$host = 'localhost';
$db   = 'iconnex';
$user = 'root';
$pass = 'Baskbask5678';
$pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);