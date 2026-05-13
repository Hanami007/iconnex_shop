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
        $stmt = $pdo->query("SELECT i.*, u.username as student_name FROM invoices i LEFT JOIN users u ON i.user_id = u.id ORDER BY i.id DESC");
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formatted = array_map(function($inv) {
            return [
                'id' => $inv['id'],
                'invoice_no' => $inv['invoice_no'],
                'student' => $inv['customer_name'] ?: $inv['student_name'],
                'amount' => '฿' . number_format($inv['total'], 2),
                'status' => $inv['status'],
                'date' => date('M j, Y', strtotime($inv['created_at']))
            ];
        }, $invoices);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
