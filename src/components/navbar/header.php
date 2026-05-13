<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;
if (!isset($pdo) || $pdo === null) {
    useService('db');
}

// Fetch categories for dropdown
$service_categories_for_menu = [];
try {
    $stmt = $pdo->query("SELECT * FROM service_categories ORDER BY id ASC");
    $service_categories_for_menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist yet
}
?>

<link rel="stylesheet" href="src/components/navbar/header.css"> 
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

<header class="main-header">
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="uploads/iconnexhouse.png" alt="iconnexhouse Logo">
        </a>

        <div class="mobile-search-container">
            <input type="text" class="search-input" placeholder="ค้นหา...">
            <button class="search-button">
                <img src="img/header_search.png" alt="Search Icon">
            </button>
        </div>

        <div class="nav-menu" id="navLinks">
            <div class="nav-left">
                <ul class="nav-links">
                    <li class="dropdown">
                        <a href="service.php" class="dropdown-toggle">Service<span class="arrow-down">v</span></a>
                        <ul class="dropdown-menu">
                            <?php foreach ($service_categories_for_menu as $cat_menu): ?>
                                <li>
                                    <a href="service.php?category=<?= htmlspecialchars($cat_menu['name']) ?>">
                                        <?= htmlspecialchars($cat_menu['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="content.php">Content</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="nav-right">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="ค้นหา...">
                    <button class="search-button">
                        <img src="uploads/header_search.png" alt="Search Icon">
                    </button>
                </div>

                <hr class="divider">

                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php renderComponent('notification/NotificationDropdown'); ?>
                        
                        <a href="#" onclick="openCartModal(event)" style="position:relative; font-size: 1.2rem; color: #8A6436; text-decoration:none;">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count" style="display:none; position:absolute; top:-8px; right:-12px; background:#8A6436; color:#fff; border-radius:50%; width:18px; height:18px; font-size:.7rem; align-items:center; justify-content:center; font-weight:800; border: 2px solid #fff;">0</span>
                        </a>

                        <a href="account.php" class="user-menu" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                            <img src="<?= htmlspecialchars($_SESSION['user_picture_url'] ?? 'uploads/profilekub.png') ?>" alt="Profile" class="profile-pic" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <span><?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        </a>
                        
                        <a href="logout.php" style="background: rgba(138, 100, 54, 0.1); color: #8A6436; padding: 6px 15px; border-radius: 20px; font-size: 0.75rem; text-decoration:none;">
                            Logout
                        </a>
                    <?php else: ?>
                        <div class="auth-buttons" style="display: flex; gap: 10px;">
                           <a href="login.php" class="login-link" style="text-decoration: none; color: #8A6436;">เข้าสู่ระบบ</a>
                           <a href="register.php" class="register-btn" style="background: #8A6436; color: #fff; padding: 8px 20px; border-radius: 20px; text-decoration: none;">ลงทะเบียน</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button class="hamburger-menu" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="overlay" id="navOverlay"></div>
    </nav>
</header>