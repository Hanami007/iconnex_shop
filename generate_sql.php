<?php
$json = json_decode(file_get_contents('courses.json'), true);
$sql = "CREATE TABLE IF NOT EXISTS courses (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL, instructor VARCHAR(100) NOT NULL, price INT NOT NULL, old_price INT NOT NULL, rating DECIMAL(3,1) DEFAULT 0, reviews INT DEFAULT 0, lessons INT DEFAULT 0, hours INT DEFAULT 0, description TEXT, short_desc TEXT, fullDescription TEXT, long_desc TEXT, image VARCHAR(255)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

foreach($json as $c) {
    $sql .= "INSERT INTO courses (id, name, category, instructor, price, old_price, rating, reviews, lessons, hours, description, short_desc, fullDescription, long_desc, image) VALUES ({$c['id']}, '" . addslashes($c['name']) . "', '" . addslashes($c['category']) . "', '" . addslashes($c['instructor']) . "', {$c['price']}, {$c['old_price']}, {$c['rating']}, {$c['reviews']}, {$c['lessons']}, {$c['hours']}, '" . addslashes($c['description']) . "', '" . addslashes($c['short_desc']) . "', '" . addslashes($c['fullDescription']) . "', '" . addslashes($c['long_desc']) . "', '" . addslashes($c['image']) . "');\n";
}

file_put_contents('courses.sql', $sql);
echo "Generated courses.sql\n";
?>
