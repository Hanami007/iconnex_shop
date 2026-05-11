<?php
global $pdo;

useService('db');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $login_type = $_POST['login_type'] ?? 'user'; // 'user' or 'admin'
    $target_redirect = $_POST['redirect'] ?? '';

    // Validate redirect path to prevent open redirect vulnerabilities
    if ($target_redirect) {
        $allowed = ['pay2.1', 'index.php', 'dic_product.php'];
        $isValid = false;
        foreach($allowed as $path) {
            if (strpos($target_redirect, $path) !== false) { $isValid = true; break; }
        }
        if (!$isValid) $target_redirect = '';
    }

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

            $defaultRedirect = $user['role'] === 'admin' ? 'admin/index.php' : 'index.php';
            $finalRedirect = $target_redirect ? ($target_redirect . '/index.php') : $defaultRedirect;
            // Special case for pay2.1 since it's a folder
            if ($target_redirect === 'pay2.1') $finalRedirect = 'pay2.1/index.php';

            echo json_encode([
                'success' => true,
                'role' => $user['role'],
                'redirect' => $finalRedirect
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
