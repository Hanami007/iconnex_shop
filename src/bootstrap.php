<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('BASE_DIR', __DIR__);
define('ROOT_DIR', dirname(BASE_DIR));
define('SERVICES_DIR', BASE_DIR . '/services');
define('UPLOADS_DIR', ROOT_DIR . '/uploads');
define('UTILS_DIR', BASE_DIR . '/utils');
define('PAGES_DIR', BASE_DIR . '/pages');
define('COMPONENTS_DIR', BASE_DIR . '/components');
define('ASSETS_DIR', '/src/assets'); // URL path
define('STYLES_DIR', '/src/styles'); // URL path

// Helper for absolute includes
function useService($name) {
    global $pdo, $courses, $translations, $current_lang, $avg_rating, $total_courses, $total_students;
    require_once SERVICES_DIR . "/$name.php";
}

function useApi($name) {
    global $pdo, $courses, $translations, $current_lang;
    require_once SERVICES_DIR . "/api/$name.php";
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfToken() {
    return $_SESSION['csrf_token'];
}

function csrfInput() {
    echo '<input type="hidden" name="csrf_token" value="' . getCsrfToken() . '">';
}

function validateCsrf() {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['X_CSRF_TOKEN'] ?? '';
        }
        
        if (empty($token) && function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? '';
        }

        if (empty($token) || $token !== $_SESSION['csrf_token']) {
            $uri = $_SERVER['REQUEST_URI'];
            $token_recv = $token ? substr($token, 0, 8) . '...' : 'EMPTY';
            $token_sess = isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 8) . '...' : 'NULL';
            
            $log = sprintf("[%s] CSRF FAIL: SID=%s, URI=%s, Method=%s, Action=%s, TokenRecv=%s, TokenSess=%s\n", 
                date('Y-m-d H:i:s'), session_id(), $uri, $method, $_POST['action'] ?? $_GET['action'] ?? 'N/A', 
                $token_recv, $token_sess);
            file_put_contents(ROOT_DIR . '/debug_csrf.log', $log, FILE_APPEND);
            
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'error' => 'CSRF token validation failed (V3).']);
            exit;
        }
    }
}

/**
 * Rate Limiting Logic
 * @param string $key Unique key for the action (e.g. 'login', 'register')
 * @param int $limit Max requests allowed
 * @param int $period Time period in seconds
 */
function checkRateLimit($key, $limit = 5, $period = 60) {
    global $pdo;
    if (!$pdo) useService('db');
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $now = date('Y-m-d H:i:s');
    
    try {
        // Cleanup expired limits occasionally (1% chance per request)
        if (rand(1, 100) === 1) {
            $pdo->exec("DELETE FROM rate_limits WHERE reset_at < '$now'");
        }

        $stmt = $pdo->prepare("SELECT id, request_count, reset_at FROM rate_limits WHERE ip_address = ? AND action_key = ?");
        $stmt->execute([$ip, $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (strtotime($row['reset_at']) < strtotime($now)) {
                // Reset expired window
                $reset_at = date('Y-m-d H:i:s', time() + $period);
                $stmt = $pdo->prepare("UPDATE rate_limits SET request_count = 1, reset_at = ? WHERE id = ?");
                $stmt->execute([$reset_at, $row['id']]);
            } else {
                if ($row['request_count'] >= $limit) {
                    $retry_after = strtotime($row['reset_at']) - time();
                    header('HTTP/1.1 429 Too Many Requests');
                    header("Retry-After: $retry_after");
                    echo json_encode([
                        'success' => false, 
                        'error' => 'คุณทำรายการบ่อยเกินไป กรุณารออีก ' . $retry_after . ' วินาที'
                    ]);
                    exit;
                }
                $stmt = $pdo->prepare("UPDATE rate_limits SET request_count = request_count + 1 WHERE id = ?");
                $stmt->execute([$row['id']]);
            }
        } else {
            $reset_at = date('Y-m-d H:i:s', time() + $period);
            $stmt = $pdo->prepare("INSERT INTO rate_limits (ip_address, action_key, request_count, reset_at) VALUES (?, ?, 1, ?)");
            $stmt->execute([$ip, $key, $reset_at]);
        }
    } catch (PDOException $e) {
        // If DB error, fail gracefully and allow request? 
        // Or log it. For now, we just proceed to not block users if DB is slow.
    }
}

// Auto-inject components (optional)
function renderComponent($path, $data = []) {
    extract($data);
    include COMPONENTS_DIR . "/$path.php";
}
