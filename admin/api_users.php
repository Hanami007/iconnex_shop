<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formatted = array_map(function($u) {
        return [
            'name' => $u['username'],
            'email' => $u['email'],
            'role' => $u['role'] === 'admin' ? 'Admin' : 'Student',
            'enrolled' => 0, // Features like enrollment can be added later
            'status' => 'active',
            'joined' => date('M j, Y', strtotime($u['created_at']))
        ];
    }, $users);
    
    echo json_encode(['success' => true, 'data' => $formatted]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
