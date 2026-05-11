<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine content type
    $isJson = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    
    if ($isJson) {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    } else {
        $data = $_POST;
        if (isset($data['items']) && is_string($data['items'])) {
            $data['items'] = json_decode($data['items'], true);
        }
    }

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid data payload']);
        exit;
    }

    $order_no = $data['order_no'] ?? '';
    $name = $data['name'] ?? '';
    $surname = $data['surname'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $line_id = $data['line_id'] ?? '';
    $total_amount = $data['total_amount'] ?? 0;
    $payment_method = $data['payment_method'] ?? '';
    $items = $data['items'] ?? [];

    $customer_name = trim($name . ' ' . $surname);

    if (empty($order_no) || empty($customer_name) || empty($email) || empty($items)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    // Handle slip image upload
    $slip_path = null;
    if (isset($_FILES['slip']) && $_FILES['slip']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/slips/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['slip']['name']);
        $ext = strtolower($file_info['extension']);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed_exts)) {
            $new_filename = 'slip_' . $order_no . '_' . uniqid() . '.' . $ext;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['slip']['tmp_name'], $target_file)) {
                $slip_path = 'uploads/slips/' . $new_filename;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'รูปแบบไฟล์สลิปไม่ถูกต้อง (รองรับเฉพาะภาพ)']);
            exit;
        }
    }

    // Auto-migrate: ensure user_id column exists
    try {
        $pdo->query("SELECT user_id FROM orders LIMIT 1");
    } catch (PDOException $e) {
        try {
            $pdo->exec("ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
        } catch (PDOException $ex) {}
    }

    try {
        $user_id = $_SESSION['user_id'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO orders (order_no, user_id, customer_name, customer_email, customer_phone, line_id, total_amount, payment_method, items_json, slip_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        $stmt->execute([
            $order_no,
            $user_id,
            $customer_name,
            $email,
            $phone,
            $line_id,
            $total_amount,
            $payment_method,
            json_encode($items, JSON_UNESCAPED_UNICODE),
            $slip_path
        ]);

        // Clear cart session if successful
        $_SESSION['cart'] = [];

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
