<?php
global $pdo;

useService('db');

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
    $phone = $data['phone'] ?? '';
    $line_id = $data['line_id'] ?? '';
    $total_amount = $data['total_amount'] ?? 0;
    $payment_method = $data['payment_method'] ?? '';
    $items = $data['items'] ?? [];

    // SECURITY: Get user info from session/DB, not from frontend inputs
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'กรุณาเข้าสู่ระบบก่อนชำระเงิน']);
        exit;
    }

    $stmtUser = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$userRow) {
        echo json_encode(['success' => false, 'error' => 'ไม่พบข้อมูลผู้ใช้งาน']);
        exit;
    }

    $customer_name = $userRow['username'];
    $email = $userRow['email'];

    if (empty($order_no) || empty($items)) {
        echo json_encode(['success' => false, 'error' => 'ข้อมูลคำสั่งซื้อไม่สมบูรณ์']);
        exit;
    }

    // Handle slip image upload
    $slip_path = null;
    if (isset($_FILES['slip']) && $_FILES['slip']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = UPLOADS_DIR . '/slips/';
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

    // Auto-migrate: ensure table and columns exist
    try {
        $pdo->query("SELECT 1 FROM orders LIMIT 1");
        // Table exists, check for user_id
        try {
            $pdo->query("SELECT user_id FROM orders LIMIT 1");
        } catch (PDOException $e) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
        }
    } catch (PDOException $e) {
        // Table doesn't exist, create it
        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            order_no VARCHAR(50) NOT NULL UNIQUE,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            line_id VARCHAR(255) NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            items_json TEXT NOT NULL,
            slip_image VARCHAR(255) NULL,
            status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
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
