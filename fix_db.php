<?php
require_once __DIR__ . '/src/bootstrap.php';
useService('db');
global $pdo;

try {
    echo "Checking tables...\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    print_r($tables);

    if (!in_array('categories', $tables)) {
        echo "Creating categories table...\n";
        $pdo->exec("
            CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        echo "Seeding categories...\n";
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $categories = [
            ['Graphic Design', 'graphic-design'],
            ['Web Development', 'web-development'],
            ['Digital Marketing', 'digital-marketing'],
            ['Business', 'business']
        ];
        foreach ($categories as $cat) {
            $stmt->execute($cat);
        }
    }

    // Also check if courses table has category_id
    echo "Checking courses table structure...\n";
    $cols = $pdo->query("DESCRIBE courses")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('category_id', $cols)) {
        echo "Adding category_id column to courses...\n";
        $pdo->exec("ALTER TABLE courses ADD COLUMN category_id INT NULL AFTER id");
    }

    echo "Done! Refresh your browser.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
