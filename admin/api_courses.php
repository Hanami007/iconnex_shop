<?php
require_once '../db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format to match what admin.js expects
        $formatted = array_map(function($c) {
            return [
                'id' => $c['id'],
                'img' => $c['image'] ?: '📚',
                'title' => $c['name'],
                'category' => $c['category'],
                'price' => '฿' . number_format($c['price']),
                'students' => $c['reviews'] * 10, // Mock students count based on reviews
                'status' => 'published' // Assume all are published for now
            ];
        }, $courses);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
    } catch (PDOException $e) {
        // Fallback to JSON if DB fails
        if (file_exists('../courses.json')) {
            $json = file_get_contents('../courses.json');
            $data = json_decode($json, true);
            $formatted = array_map(function($c) {
                return [
                    'id' => $c['id'],
                    'img' => $c['image'] ?: '📚',
                    'title' => $c['name'],
                    'category' => $c['category'],
                    'price' => '฿' . number_format($c['price']),
                    'students' => $c['reviews'] * 10,
                    'status' => 'published'
                ];
            }, $data);
            echo json_encode(['success' => true, 'data' => array_values($formatted)]);
        } else {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
} elseif ($method === 'POST') {
    // Add a new course
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'No data provided']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO courses (name, category, instructor, price, old_price, rating, reviews, lessons, hours, description, short_desc, fullDescription, long_desc, image) VALUES (:name, :category, :instructor, :price, :old_price, :rating, :reviews, :lessons, :hours, :description, :short_desc, :fullDescription, :long_desc, :image)");
        
        $price = intval($data['price']);
        
        $stmt->execute([
            ':name' => $data['title'],
            ':category' => $data['category'],
            ':instructor' => 'Admin', // Default
            ':price' => $price,
            ':old_price' => $price + 500, // Dummy
            ':rating' => 5.0,
            ':reviews' => 0,
            ':lessons' => 10,
            ':hours' => 20,
            ':description' => $data['description'],
            ':short_desc' => $data['description'],
            ':fullDescription' => $data['description'],
            ':long_desc' => $data['description'],
            ':image' => '✨'
        ]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
