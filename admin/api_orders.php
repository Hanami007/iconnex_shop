<?php
require_once dirname(__DIR__) . '/src/bootstrap.php';
useService('db');

header('Content-Type: application/json');
validateCsrf();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Query columns including slip_image and line_id
        $stmt = $pdo->query("SELECT id, order_no, customer_name, customer_email, customer_phone, line_id, total_amount, payment_method, items_json, slip_image, status, created_at FROM orders ORDER BY id DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formatted = array_map(function($o) {
            $items = json_decode($o['items_json'], true);
            $course_names = [];
            if (is_array($items)) {
                foreach($items as $item) {
                    $course_names[] = $item['name'] ?? 'Unnamed Course';
                }
            }
            $course_string = !empty($course_names) ? implode(', ', $course_names) : 'Unknown Course';

            return [
                'id' => $o['id'],
                'order_no' => $o['order_no'],
                'student' => $o['customer_name'],
                'email' => $o['customer_email'],
                'phone' => $o['customer_phone'],
                'line_id' => $o['line_id'],
                'course' => $course_string,
                'items' => is_array($items) ? $items : [],
                'amount' => '฿' . number_format($o['total_amount']),
                'raw_amount' => $o['total_amount'],
                'payment_method' => $o['payment_method'],
                'status' => $o['status'],
                'slip' => $o['slip_image'],
                'date' => date('M j, Y H:i', strtotime($o['created_at']))
            ];
        }, $orders);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_status') {
        $order_id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        if ($order_id && in_array($status, ['pending', 'completed', 'cancelled'])) {
            try {
                $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$status, $order_id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
