<?php
useService('db');
useService('lang');
useService('course_data');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Auto-migrate: check if user_id column exists
try {
    $pdo->query("SELECT user_id FROM orders LIMIT 1");
} catch (PDOException $e) {
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
        $pdo->exec("UPDATE orders o JOIN users u ON o.customer_email COLLATE utf8mb4_unicode_ci = u.email COLLATE utf8mb4_unicode_ci SET o.user_id = u.id WHERE o.user_id IS NULL");
    } catch (PDOException $ex) {}
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR customer_email COLLATE utf8mb4_unicode_ci = (SELECT email COLLATE utf8mb4_unicode_ci FROM users WHERE id = ?) ORDER BY created_at DESC");
$stmt->execute([$user_id, $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to find course image by name
function getCourseImage($name, $courses) {
    foreach ($courses as $c) {
        if ($c['name'] === $name) {
            $img = $c['image'];
            if (strpos($img, '<img') !== false) {
                if (preg_match('/src="([^"]+)"/', $img, $m)) return $m[1];
            }
            return (strpos($img, 'uploads/') === 0 ? '' : ASSETS_DIR . '/img/') . $img;
        }
    }
    return 'https://via.placeholder.com/150x100?text=Course';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICONNEX – <?php echo __('nav_my_orders'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="src/styles/style.css?v=1.1" />
    <link rel="stylesheet" href="src/styles/notification.css?v=1.1" />
    <style>
        :root {
            --sidebar-w: 280px;
            --dashboard-bg: #070b14;
        }
        body { background: var(--dashboard-bg); }
        
        .dashboard-wrapper { display: flex; min-height: 100vh; padding-top: 80px; }
        
        /* Sidebar */
        .dashboard-sidebar { width: var(--sidebar-w); padding: 20px; border-right: 1px solid var(--navy-border); position: sticky; top: 80px; height: calc(100vh - 80px); }
        .sidebar-menu { display: flex; flex-direction: column; gap: 8px; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; color: var(--text-muted); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .menu-item:hover { background: var(--navy-light); color: var(--gold-soft); }
        .menu-item.active { background: var(--gold-pale); color: var(--gold); border: 1px solid var(--gold-line); }
        .menu-item i { font-size: 1.1rem; width: 24px; text-align: center; }

        /* Content */
        .dashboard-content { flex: 1; padding: 40px 60px; max-width: 1200px; }
        .content-header { margin-bottom: 40px; }
        .content-header h1 { font-size: 2rem; font-family: 'Sora', sans-serif; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .content-header p { color: var(--text-muted); font-size: 0.95rem; }

        /* Order Cards */
        .order-list { display: flex; flex-direction: column; gap: 20px; }
        .order-item-card { background: var(--navy-card); border: 1px solid var(--navy-border); border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; }
        .order-item-card:hover { border-color: var(--gold-line); transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.4); }
        
        .card-top { display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--navy-border); font-size: 0.85rem; color: var(--text-muted); }
        .card-body { padding: 25px; display: flex; gap: 25px; align-items: center; }
        
        .course-preview { width: 120px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0; background: var(--navy-deep); border: 1px solid var(--navy-border); }
        .course-preview img { width: 100%; height: 100%; object-fit: cover; }
        
        .order-info { flex: 1; }
        .order-info h3 { font-size: 1.1rem; color: #fff; margin-bottom: 6px; }
        .order-info .order-meta { display: flex; gap: 20px; font-size: 0.8rem; color: var(--text-muted); }
        
        .order-status-price { text-align: right; display: flex; flex-direction: column; gap: 10px; align-items: flex-end; }
        .price-tag { font-family: 'Sora', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--gold); }
        
        .status-pill { padding: 6px 14px; border-radius: 30px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background: rgba(251, 191, 36, 0.1); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.2); }
        .status-completed { background: rgba(34, 211, 160, 0.1); color: #22d3a0; border: 1px solid rgba(34, 211, 160, 0.2); }
        .status-cancelled { background: rgba(248, 113, 113, 0.1); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.2); }

        .empty-dashboard { text-align: center; padding: 80px 0; background: var(--navy-card); border-radius: 24px; border: 1px dashed var(--navy-border); }
        .empty-dashboard i { font-size: 3.5rem; color: var(--gold-pale); margin-bottom: 20px; }

        @media (max-width: 992px) {
            .dashboard-wrapper { flex-direction: column; }
            .dashboard-sidebar { width: 100%; height: auto; position: static; border-right: none; border-bottom: 1px solid var(--navy-border); }
            .dashboard-content { padding: 30px 20px; }
            .card-body { flex-direction: column; align-items: flex-start; }
            .order-status-price { text-align: left; align-items: flex-start; margin-top: 15px; border-top: 1px solid var(--navy-border); padding-top: 15px; width: 100%; }
        }
    </style>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
</head>
<body>
    <!-- NAV (Same as main site) -->
    <nav style="background: rgba(7, 11, 20, 0.85); backdrop-filter: blur(20px);">
        <a href="index.php" class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </a>
        <ul class="nav-links">
            <li><a href="index.php"><?php echo __('nav_home'); ?></a></li>
            <li><a href="index.php#courses"><?php echo __('nav_courses'); ?></a></li>
            <li><a href="logout.php" style="color: #ffae35;"><?php echo __('nav_logout'); ?></a></li>
        </ul>
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px; margin-right: 40px;">
            <?php renderComponent('notification/NotificationDropdown'); ?>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-menu">
                <a href="#" class="menu-item"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="#" class="menu-item"><i class="fas fa-play-circle"></i> My Learning</a>
                <a href="my_orders.php" class="menu-item active"><i class="fas fa-history"></i> Purchase History</a>
                <a href="#" class="menu-item"><i class="fas fa-user-circle"></i> Profile Settings</a>
                <hr style="border: none; border-top: 1px solid var(--navy-border); margin: 15px 0;">
                <a href="logout.php" class="menu-item" style="color: var(--red);"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Content -->
        <main class="dashboard-content">
            <div class="content-header">
                <h1><?php echo __('title_my_orders'); ?></h1>
                <p>จัดการออเดอร์และการสั่งซื้อทั้งหมดของคุณได้ที่นี่</p>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-dashboard">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>ยังไม่มีประวัติการสั่งซื้อ</h3>
                    <p style="color: var(--text-muted); margin: 10px 0 25px;">เลือกซื้อคอร์สที่น่าสนใจเพื่อเริ่มต้นการเรียนรู้ในระดับ Creator</p>
                    <a href="index.php#courses" class="btn-primary" style="text-decoration:none; display:inline-block;">ดูคอร์สเรียนทั้งหมด</a>
                </div>
            <?php else: ?>
                <div class="order-list">
                    <?php foreach ($orders as $order): 
                        $items = json_decode($order['items_json'], true);
                        $itemCount = count($items);
                        $firstItem = $items[0] ?? ['name' => 'Unknown Course'];
                        $imgUrl = getCourseImage($firstItem['name'], $courses);
                        $statusClass = 'status-' . $order['status'];
                    ?>
                        <div class="order-item-card">
                            <div class="card-top">
                                <span>ORDER ID: <strong style="color:#fff;"><?php echo htmlspecialchars($order['order_no']); ?></strong></span>
                                <span><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="course-preview">
                                    <img src="<?php echo $imgUrl; ?>" alt="Course">
                                </div>
                                <div class="order-info">
                                    <h3><?php echo htmlspecialchars($firstItem['name']); ?> <?php echo $itemCount > 1 ? '<span style="color:var(--gold); font-size:0.8rem;">(+'.($itemCount-1).' more)</span>' : ''; ?></h3>
                                    <div class="order-meta">
                                        <span><i class="fas fa-credit-card"></i> <?php echo $order['payment_method'] === 'qr' ? 'PromptPay' : 'Bank Transfer'; ?></span>
                                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($order['customer_name']); ?></span>
                                    </div>
                                </div>
                                <div class="order-status-price">
                                    <div class="price-tag">฿<?php echo number_format($order['total_amount']); ?></div>
                                    <div class="status-pill <?php echo $statusClass; ?>">
                                        <?php 
                                            switch($order['status']) {
                                                case 'pending': echo 'กำลังตรวจสอบ'; break;
                                                case 'completed': echo 'ชำระเงินเรียบร้อย'; break;
                                                case 'cancelled': echo 'ยกเลิก'; break;
                                                default: echo strtoupper($order['status']);
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer style="padding: 60px 20px; background: var(--navy-deep); border-top: 1px solid var(--navy-border); text-align: center; color: var(--text-muted); font-size: 0.9rem;">
        <div style="margin-bottom: 20px; font-weight: 700; color: #fff;">ICONNEX CREATORS CLUB</div>
        <p>© 2025 ICONNEX. All rights reserved.</p>
    </footer>
    <script src="src/assets/js/notification/notificationService.js?v=1.1"></script>
    <script src="src/assets/js/notification/notificationUI.js?v=1.1"></script>
    <script src="src/assets/js/script.js?v=1.1"></script>
</body>
</html>
