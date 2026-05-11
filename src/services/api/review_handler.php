<?php
global $pdo;
useService('db');
useService('lang');

header('Content-Type: application/json');
validateCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'submit') {
        $course_id = intval($_POST['course_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;
        $user_name = $_SESSION['user_name'] ?? 'Guest';

        if ($course_id <= 0 || empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }

        try {
            // First, ensure the table exists (Lazy creation for this demo environment)
            $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                course_id INT NOT NULL,
                user_id INT,
                user_name VARCHAR(255),
                rating INT,
                comment TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            $stmt = $pdo->prepare("INSERT INTO reviews (course_id, user_id, user_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$course_id, $user_id, $user_name, $rating, $comment]);

            echo json_encode(['success' => true, 'message' => 'รีวิวสำเร็จ!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } elseif ($action === 'get_reviews') {
        $course_id = intval($_POST['course_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("SELECT * FROM reviews WHERE course_id = ? ORDER BY created_at DESC");
            $stmt->execute([$course_id]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'reviews' => $reviews]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'reviews' => []]);
        }
    }
}
?>
