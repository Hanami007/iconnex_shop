<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'list') {
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, message, link, type, priority, is_read, created_at
            FROM notifications
            WHERE (user_id = ? OR user_id IS NULL)
            AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $rows]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'mark_read') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) exit;
    try {
        // Only mark read if it belongs to the user or is global
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND (user_id = ? OR user_id IS NULL)");
        $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'unread_count') {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0 AND status = 'active'");
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();
        echo json_encode(['success' => true, 'count' => (int)$count]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
