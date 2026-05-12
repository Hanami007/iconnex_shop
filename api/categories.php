<?php
require_once __DIR__ . '/../src/bootstrap.php';
useService('category_service');

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Only admins can modify categories
if ($method !== 'GET') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    validateCsrf();
}

try {
    if ($method === 'GET') {
        if ($action === 'list') {
            echo json_encode(['success' => true, 'data' => getCategories()]);
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(['success' => true, 'data' => getCategoryById($_GET['id'])]);
        } else {
            echo json_encode(['success' => true, 'data' => getCategories()]);
        }
    } elseif ($method === 'POST') {
        if ($action === 'create') {
            $name = trim($_POST['name'] ?? '');
            if (!$name) {
                echo json_encode(['success' => false, 'error' => 'Name is required']);
                exit;
            }
            $id = createCategory($name);
            if ($id) {
                echo json_encode(['success' => true, 'id' => $id, 'name' => $name]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Category already exists or failed to create']);
            }
        }
    } elseif ($method === 'PUT' || ($method === 'POST' && $action === 'update')) {
        $id = $_POST['id'] ?? $_GET['id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        if (!$id || !$name) {
            echo json_encode(['success' => false, 'error' => 'ID and Name are required']);
            exit;
        }
        if (updateCategory($id, $name)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update']);
        }
    } elseif ($method === 'DELETE' || ($method === 'POST' && $action === 'delete')) {
        $id = $_POST['id'] ?? $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID is required']);
            exit;
        }
        if (deleteCategory($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Category is in use or failed to delete']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
