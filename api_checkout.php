<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receive JSON data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $order_no = $data['order_no'] ?? '';
    $name = $data['name'] ?? '';
    $surname = $data['surname'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $total_amount = $data['total_amount'] ?? 0;
    $payment_method = $data['payment_method'] ?? '';
    $items = $data['items'] ?? [];

    $customer_name = trim($name . ' ' . $surname);

    if (empty($order_no) || empty($customer_name) || empty($email) || empty($items)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (order_no, customer_name, customer_email, customer_phone, total_amount, payment_method, items_json, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        $stmt->execute([
            $order_no,
            $customer_name,
            $email,
            $phone,
            $total_amount,
            $payment_method,
            json_encode($items, JSON_UNESCAPED_UNICODE)
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
