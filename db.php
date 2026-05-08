<?php
$host = 'localhost';
$db   = 'iconnex';
$user = 'root';
$pass = 'Baskbask5678';
$pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);