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
            $img = $c['image'] ?: '📚';
            if (strpos($img, '<img') !== false) {
                // Already an img tag, just override style to fit the thumbnail box
                $img = preg_replace('/style="[^"]*"/', 'style="width:40px; height:40px; object-fit:cover; border-radius:4px;"', $img);
            } elseif (strpos($img, '.') !== false) {
                // It's a path. If it doesn't start with uploads/, assume it's the old format (IMG/)
                $src = strpos($img, 'uploads/') === 0 ? '/' . $img : '/IMG/' . $img;
                $img = '<img src="' . $src . '" alt="' . htmlspecialchars($c['name']) . '" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">';
            }
            return [
                'id' => $c['id'],
                'img' => $img,
                'title' => $c['name'],
                'description' => $c['description'],
                'category' => $c['category'],
                'price' => '฿' . number_format($c['price']),
                'raw_price' => $c['price'],
                'instructor' => $c['instructor'],
                'lessons' => $c['lessons'],
                'hours' => $c['hours'],
                'content_json' => $c['content_json'],
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
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No ID provided']);
        }
        exit;
    }
    
    // Add or Edit
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = intval($_POST['price'] ?? 0);
    $instructor = $_POST['instructor'] ?? 'Admin';
    $lessons = intval($_POST['lessons'] ?? 10);
    $hours = intval($_POST['hours'] ?? 20);
    $content_json = $_POST['content_json'] ?? '[]';
    
    if (empty($title) || empty($price)) {
        echo json_encode(['success' => false, 'error' => 'Title and Price are required']);
        exit;
    }
    
    // Handle Image Upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['image']['name']);
        $ext = strtolower($file_info['extension']);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed_exts)) {
            $new_filename = uniqid() . '.' . $ext;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/' . $new_filename; // Relative path for frontend
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            exit;
        }
    }
    
    try {
        if ($action === 'edit' && $id) {
            if ($image_path) {
                // Update with new image path
                $stmt = $pdo->prepare("UPDATE courses SET name = ?, category = ?, instructor = ?, price = ?, old_price = ?, lessons = ?, hours = ?, description = ?, short_desc = ?, fullDescription = ?, long_desc = ?, content_json = ?, image = ? WHERE id = ?");
                $stmt->execute([$title, $category, $instructor, $price, $price + 500, $lessons, $hours, $description, $description, $description, $description, $content_json, $image_path, $id]);
            } else {
                // Update without changing image
                $stmt = $pdo->prepare("UPDATE courses SET name = ?, category = ?, instructor = ?, price = ?, old_price = ?, lessons = ?, hours = ?, description = ?, short_desc = ?, fullDescription = ?, long_desc = ?, content_json = ? WHERE id = ?");
                $stmt->execute([$title, $category, $instructor, $price, $price + 500, $lessons, $hours, $description, $description, $description, $description, $content_json, $id]);
            }
        } else { // add
            $img_val = $image_path ? $image_path : '📚';
            $stmt = $pdo->prepare("INSERT INTO courses (name, category, instructor, price, old_price, rating, reviews, lessons, hours, description, short_desc, fullDescription, long_desc, content_json, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, $category, $instructor, $price, $price + 500, 0.0, 0, $lessons, $hours, $description, $description, $description, $description, $content_json, $img_val
            ]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
