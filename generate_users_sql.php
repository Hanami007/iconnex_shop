<?php
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$user_password = password_hash('user123', PASSWORD_DEFAULT);

$sql = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@example.com', '$admin_password', 'admin'),
('user1', 'user1@example.com', '$user_password', 'user');
";

file_put_contents('users.sql', $sql);
echo "users.sql generated.";
