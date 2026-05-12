<?php
global $pdo;
$host = 'localhost';
$db   = 'iconnex';
$user = 'root';
$pass = '?ำไ/2547';
$pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);