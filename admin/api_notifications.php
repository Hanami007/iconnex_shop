<?php
/**
 * api_notifications.php — Unified Notification API
 * Handles: admin_stats, admin_list, create, update, delete, send_to_user, get_orders
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__) . '/db.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Auth guard
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$admin_id = (int) $_SESSION['user_id'];
$method   = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // ── Stats ────────────────────────────────────────────────────────────────
    if ($action === 'admin_stats') {
        try {
            $stats = [];
            $stats['total']        = (int) $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
            $stats['active']       = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE status='active'")->fetchColumn();
            $stats['draft']        = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE status='draft'")->fetchColumn();
            $stats['scheduled']    = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE status='scheduled'")->fetchColumn();
            $stats['unread']       = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read=0")->fetchColumn();
            $stats['high_priority']= (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE priority='high'")->fetchColumn();
            echo json_encode(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── List (paginated) ─────────────────────────────────────────────────────
    if ($action === 'admin_list') {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;
        try {
            $total = (int) $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
            $stmt  = $pdo->prepare("
                SELECT n.*, u.username AS sent_to_username
                FROM notifications n
                LEFT JOIN users u ON n.user_id = u.id
                ORDER BY n.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success'  => true,
                'data'     => $rows,
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Orders list (for the "send from order" feature) ──────────────────────
    if ($action === 'get_orders') {
        try {
            $stmt = $pdo->query("
                SELECT o.id, o.order_no, o.customer_name, o.customer_email,
                       o.status, o.total_amount,
                       u.id AS user_id,
                       COUNT(n.id) AS notification_count
                FROM orders o
                LEFT JOIN users u ON u.email = o.customer_email
                LEFT JOIN notifications n ON n.order_id = o.id
                WHERE o.status IN ('completed','pending')
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 200
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $orders]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Search users ─────────────────────────────────────────────────
    if ($action === 'search_users') {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }
        try {
            $like = '%' . $q . '%';
            $stmt = $pdo->prepare("
                SELECT id, username, email, role,
                       (SELECT COUNT(*) FROM orders o WHERE o.customer_email = users.email AND o.status='completed') AS order_count
                FROM users
                WHERE (username LIKE :q1 OR email LIKE :q2)
                  AND role != 'admin'
                ORDER BY username
                LIMIT 15
            ");
            $stmt->execute([':q1' => $like, ':q2' => $like]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $users]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

// ────────
    if ($action === 'get_order_detail') {
        $oid = (int)($_GET['order_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("
                SELECT o.*, u.id AS user_id
                FROM orders o
                LEFT JOIN users u ON u.email = o.customer_email
                WHERE o.id = ?
            ");
            $stmt->execute([$oid]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($order) {
                $order['items_json'] = json_decode($order['items_json'] ?? '[]', true);
                echo json_encode(['success' => true, 'data' => $order]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Order not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Unknown GET action']);
    exit;
}

// ── POST ──────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Create (broadcast) ───────────────────────────────────────────────────
    if ($action === 'create') {
        $title    = trim($_POST['title']    ?? '');
        $message  = trim($_POST['message']  ?? '');
        $type     = $_POST['type']          ?? 'default';
        $priority = $_POST['priority']      ?? 'low';
        $status   = $_POST['status']        ?? 'active';
        $audience = $_POST['target_audience'] ?? 'all';
        $schedAt  = $_POST['scheduled_at']  ?? null;

        if (!$title || !$message) {
            echo json_encode(['success' => false, 'error' => 'Title and message required']);
            exit;
        }

        try {
            // Resolve user IDs based on audience
            $userIds = resolveAudience($pdo, $audience, $_POST['custom_user_ids'] ?? '');

            if (empty($userIds)) {
                // Global broadcast (user_id = NULL)
                insertNotification($pdo, null, null, $title, $message, $type, $priority, $status, $schedAt, $audience, $admin_id);
            } else {
                foreach ($userIds as $uid) {
                    insertNotification($pdo, (int)$uid, null, $title, $message, $type, $priority, $status, $schedAt, $audience, $admin_id);
                }
            }
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Send to specific user/order ───────────────────────────────────────────
    if ($action === 'send_to_user') {
        $userId  = (int)($_POST['user_id']  ?? 0);
        $orderId = (int)($_POST['order_id'] ?? 0) ?: null;
        $title   = trim($_POST['title']   ?? '');
        $message = trim($_POST['message'] ?? '');
        $type    = $_POST['type']    ?? 'order';
        $priority= $_POST['priority']?? 'medium';

        if (!$userId || !$title || !$message) {
            echo json_encode(['success' => false, 'error' => 'user_id, title and message required']);
            exit;
        }
        try {
            insertNotification($pdo, $userId, $orderId, $title, $message, $type, $priority, 'active', null, 'custom', $admin_id);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Update ───────────────────────────────────────────────────────────────
    if ($action === 'update') {
        $id      = (int)($_POST['id']      ?? 0);
        $title   = trim($_POST['title']   ?? '');
        $message = trim($_POST['message'] ?? '');
        if (!$id || !$title || !$message) {
            echo json_encode(['success' => false, 'error' => 'id, title, message required']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("
                UPDATE notifications SET
                    title=:title, message=:message, type=:type,
                    priority=:priority, status=:status,
                    target_audience=:audience,
                    scheduled_at=:sched,
                    updated_at=NOW()
                WHERE id=:id
            ");
            $stmt->execute([
                ':title'    => $title,
                ':message'  => $message,
                ':type'     => $_POST['type']             ?? 'default',
                ':priority' => $_POST['priority']         ?? 'low',
                ':status'   => $_POST['status']           ?? 'active',
                ':audience' => $_POST['target_audience']  ?? 'all',
                ':sched'    => $_POST['scheduled_at']     ?: null,
                ':id'       => $id,
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Delete ───────────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'error' => 'id required']); exit; }
        try {
            $pdo->prepare("DELETE FROM notifications WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Unknown POST action']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request method']);

// ─────────────────────────────────────────────────────────────────────────────
// Helper: insert one notification row
// ─────────────────────────────────────────────────────────────────────────────
function insertNotification(
    PDO $pdo, ?int $userId, ?int $orderId,
    string $title, string $message,
    string $type, string $priority, string $status,
    ?string $scheduledAt, string $audience, int $createdBy
): int {
    $stmt = $pdo->prepare("
        INSERT INTO notifications
            (user_id, order_id, title, message, type, priority, status,
             target_audience, scheduled_at, created_by, is_read, created_at, updated_at)
        VALUES
            (:uid, :oid, :title, :msg, :type, :prio, :status,
             :audience, :sched, :created_by, 0, NOW(), NOW())
    ");
    $stmt->execute([
        ':uid'        => $userId,
        ':oid'        => $orderId,
        ':title'      => $title,
        ':msg'        => $message,
        ':type'       => $type,
        ':prio'       => $priority,
        ':status'     => $status,
        ':audience'   => $audience,
        ':sched'      => $scheduledAt,
        ':created_by' => $createdBy,
    ]);
    return (int) $pdo->lastInsertId();
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper: resolve user IDs from audience selector
// ─────────────────────────────────────────────────────────────────────────────
function resolveAudience(PDO $pdo, string $audience, string $customIds): array {
    switch ($audience) {
        case 'all':
            return []; // will do single global row instead of per-user

        case 'registered':
            return $pdo->query("SELECT id FROM users WHERE role='user'")->fetchAll(PDO::FETCH_COLUMN);

        case 'purchased':
            return $pdo->query("
                SELECT DISTINCT u.id FROM users u
                INNER JOIN orders o ON o.customer_email = u.email
                WHERE o.status = 'completed'
            ")->fetchAll(PDO::FETCH_COLUMN);

        case 'course_buyers':
            $courseId = (int)($customIds);
            $stmt = $pdo->prepare("
                SELECT DISTINCT u.id FROM users u
                INNER JOIN orders o ON o.customer_email = u.email
                WHERE o.status = 'completed' 
                  AND (o.items_json LIKE :p1 OR o.items_json LIKE :p2 OR o.items_json LIKE :p3)
            ");
            $pattern1 = '%"id":' . $courseId . ',%';
            $pattern2 = '%"id":' . $courseId . '}%';
            $pattern3 = '%"id":"' . $courseId . '"%'; // In case it's a string
            $stmt->execute([':p1' => $pattern1, ':p2' => $pattern2, ':p3' => $pattern3]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        case 'custom':
            $ids = array_filter(array_map('intval', explode(',', $customIds)));
            return array_unique($ids);

        default:
            return [];
    }
}