<?php
global $courses, $pdo;

useService('course_data');

header('Content-Type: application/json');

// Security: Validate CSRF for all POST actions
validateCsrf();

$action    = $_POST['action']    ?? '';
$course_id = intval($_POST['course_id'] ?? 0);
$quantity  = intval($_POST['quantity']  ?? 1);

// init
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {

    case 'add':
        if (!isset($courses[$course_id])) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบคอร์สนี้']);
            exit;
        }
        if (isset($_SESSION['cart'][$course_id])) {
            $_SESSION['cart'][$course_id]++;
        } else {
            $_SESSION['cart'][$course_id] = 1;
        }
        echo json_encode([
            'success' => true,
            'message' => 'เพิ่มลงตะกร้าแล้ว',
            'count'   => getCount()
        ]);
        break;

    case 'update':
        if ($quantity < 1) {
            unset($_SESSION['cart'][$course_id]);
        } else {
            $_SESSION['cart'][$course_id] = $quantity;
        }
        echo json_encode([
            'success' => true,
            'count'   => getCount(),
            'total'   => getTotal($courses)
        ]);
        break;

    case 'remove':
        unset($_SESSION['cart'][$course_id]);
        echo json_encode([
            'success' => true,
            'count'   => getCount(),
            'total'   => getTotal($courses)
        ]);
        break;

    case 'count':
        echo json_encode(['count' => getCount()]);
        break;

    case 'get_cart':
        $items = [];
        $total = 0;
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if (isset($courses[$id])) {
                $c = $courses[$id];
                $c['qty'] = $qty;
                $items[] = $c;
                $total += $c['price'] * $qty;
            }
        }
        echo json_encode([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => getCount()
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'action ไม่ถูกต้อง']);
}

function getCount() {
    return array_sum($_SESSION['cart'] ?? []);
}

function getTotal($courses) {
    $total = 0;
    foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
        if (isset($courses[$id])) {
            $total += $courses[$id]['price'] * $qty;
        }
    }
    return $total;
}