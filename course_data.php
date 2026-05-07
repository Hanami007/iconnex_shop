<?php
require_once 'db.php';

$courses = array();

try {
    $stmt = $pdo->query("SELECT * FROM courses");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Ensure integer fields are properly casted if needed
        $row['id'] = (int)$row['id'];
        $row['price'] = (int)$row['price'];
        $row['old_price'] = (int)$row['old_price'];
        $row['rating'] = (float)$row['rating'];
        $row['reviews'] = (int)$row['reviews'];
        $row['lessons'] = (int)$row['lessons'];
        $row['hours'] = (int)$row['hours'];
        
        $courses[$row['id']] = $row;
    }
} catch (PDOException $e) {
    // Fallback if table doesn't exist yet, we could use courses.json temporarily
    if (file_exists(__DIR__ . '/courses.json')) {
        $json = file_get_contents(__DIR__ . '/courses.json');
        $fallback = json_decode($json, true);
        foreach($fallback as $item) {
            $courses[$item['id']] = $item;
        }
    }
}
?>
