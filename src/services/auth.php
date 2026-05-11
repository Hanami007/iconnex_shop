<?php
global $pdo;

useService('db');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $login_type = $_POST['login_type'] ?? 'user'; // 'user' or 'admin'

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Please provide both username and password.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            // Check if the user is trying to log in to the wrong portal
            if ($login_type === 'admin' && $user['role'] !== 'admin') {
                echo json_encode(['success' => false, 'error' => 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (Admin Only)']);
                exit;
            }
            if ($login_type === 'user' && $user['role'] === 'admin') {
                echo json_encode(['success' => false, 'error' => 'กรุณาเข้าสู่ระบบผ่านหน้า Admin Login']);
                exit;
            }

            // Password is correct, set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                'success' => true,
                'role' => $user['role'],
                'redirect' => $user['role'] === 'admin' ? 'admin/index.php' : 'index.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
