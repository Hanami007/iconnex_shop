<?php
require_once 'db.php';
// Need to temporarily rename the original course_data.php or just use the courses.json we made earlier.
// Wait, we already made courses.json earlier! Let's use it.
$json = file_get_contents('courses.json');
$courses = json_decode($json, true);

try {
    // Create table
    $sql = "
    CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        instructor VARCHAR(100) NOT NULL,
        price INT NOT NULL,
        old_price INT NOT NULL,
        rating DECIMAL(3,1) DEFAULT 0,
        reviews INT DEFAULT 0,
        lessons INT DEFAULT 0,
        hours INT DEFAULT 0,
        description TEXT,
        short_desc TEXT,
        fullDescription TEXT,
        long_desc TEXT,
        image VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Table 'courses' created successfully.\n";
    
    // Clear existing data to avoid duplicates on multiple runs
    $pdo->exec("TRUNCATE TABLE courses");
    
    // Insert data
    $stmt = $pdo->prepare("INSERT INTO courses (id, name, category, instructor, price, old_price, rating, reviews, lessons, hours, description, short_desc, fullDescription, long_desc, image) VALUES (:id, :name, :category, :instructor, :price, :old_price, :rating, :reviews, :lessons, :hours, :description, :short_desc, :fullDescription, :long_desc, :image)");
    
    foreach ($courses as $c) {
        $stmt->execute([
            ':id' => $c['id'],
            ':name' => $c['name'],
            ':category' => $c['category'],
            ':instructor' => $c['instructor'],
            ':price' => $c['price'],
            ':old_price' => $c['old_price'],
            ':rating' => $c['rating'],
            ':reviews' => $c['reviews'],
            ':lessons' => $c['lessons'],
            ':hours' => $c['hours'],
            ':description' => $c['description'],
            ':short_desc' => $c['short_desc'],
            ':fullDescription' => $c['fullDescription'],
            ':long_desc' => $c['long_desc'],
            ':image' => $c['image']
        ]);
    }
    
    echo "Data inserted successfully.\n";
    
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
