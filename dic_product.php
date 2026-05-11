<?php
require_once 'course_data.php';
require_once 'lang.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$course = isset($courses[$id]) ? $courses[$id] : null;

if (!$course) {
    header('Location: index.php');
    exit;
}
// Get related courses in the same category
$relatedCourses = array_filter($courses, function($c) use ($course, $id) {
    return $c['category'] === $course['category'] && $c['id'] !== $id;
});

?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ICONNEX – <?php echo htmlspecialchars($course['name']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Prompt:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <script defer src="script.js"></script>
    <script defer src="course_data.js"></script>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const langData = {
            cart_popup_title: "<?php echo __('cart_popup_title'); ?>",
            cart_popup_desc: "<?php echo __('cart_popup_desc'); ?>",
            btn_login: "<?php echo __('btn_login'); ?>",
            btn_register: "<?php echo __('btn_register'); ?>"
        };
    </script>
</head>

<body class="course-detail-page">

    <nav>
        <a href="index.php" class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </a>
        <ul class="nav-links">
            <li><a href="index.php"><?php echo __('nav_home'); ?></a></li>
            <li><a href="index.php#portfolio"><?php echo __('nav_portfolio'); ?></a></li>
            <li><a href="index.php#courses" class="active"><?php echo __('nav_courses'); ?></a></li>
            <li><a href="index.php#faq"><?php echo __('nav_faq'); ?></a></li>
        </ul>
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px;">
            <div class="lang-switcher">
                <a href="?id=<?php echo $id; ?>&lang=th" class="<?php echo $current_lang == 'th' ? 'active' : ''; ?>">TH</a>
                <span>|</span>
                <a href="?id=<?php echo $id; ?>&lang=en" class="<?php echo $current_lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
            <a href="#" class="cart-icon-btn" onclick="openCartModal(event)" style="position: relative; color: white; font-size: 1.4rem; text-decoration: none;">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" style="position: absolute; top: -10px; right: -12px; background: var(--gold); color: var(--navy-deep); font-size: 0.7rem; font-weight: 800; width: 20px; height: 20px; border-radius: 50%; display: none; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">0</span>
            </a>
        </div>
    </nav>

    <!-- COURSE HERO -->
    <section class="course-hero">
        <div class="container">
            <div class="breadcrumb reveal">
                <a href="index.php"><?php echo __('nav_home'); ?></a>
                <i class="fas fa-chevron-right"></i>
                <a href="index.php#courses"><?php echo __('nav_courses'); ?></a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($course['name']); ?></span>
            </div>

            <div class="hero-grid">
                <div class="hero-content reveal">
                    <div class="course-badge">ONLINE COURSE</div>
                    <h1><?php echo htmlspecialchars($course['name']); ?></h1>
                    <p class="short-desc"><?php echo htmlspecialchars($course['short_desc']); ?></p>
                    
                    <div class="course-meta-strip">
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span><?php echo $course['hours']; ?> <?php echo $current_lang == 'th' ? 'ชั่วโมง' : 'Hours'; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-play-circle"></i>
                            <span><?php echo $course['lessons']; ?> <?php echo $current_lang == 'th' ? 'บทเรียน' : 'Lessons'; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-star"></i>
                            <span><?php echo $course['rating']; ?> (<?php echo $course['reviews']; ?> รีวิว)</span>
                        </div>
                    </div>
                    <div class="hero-instructor">
                        <div class="instructor-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <span class="label"><?php echo $current_lang == 'th' ? 'ผู้สอน' : 'Instructor'; ?></span>
                            <span class="name"><?php echo htmlspecialchars($course['instructor']); ?></span>
                        </div>
                    </div>

                    <!-- MOVE CONTENT TABS HERE -->
                    <div class="content-card reveal" style="margin-top: 40px;">
                        <div class="content-tabs">
                            <button class="tab-btn active" onclick="switchContentTab(this, 'overview')"><?php echo $current_lang == 'th' ? 'รายละเอียด' : 'Overview'; ?></button>
                            <button class="tab-btn" onclick="switchContentTab(this, 'curriculum')"><?php echo $current_lang == 'th' ? 'เนื้อหา' : 'Curriculum'; ?></button>
                            <button class="tab-btn" onclick="switchContentTab(this, 'reviews')"><?php echo $current_lang == 'th' ? 'รีวิว' : 'Reviews'; ?></button>
                        </div>

                        <div id="tab-overview" class="tab-pane active">
                            <h3 class="pane-title"><?php echo $current_lang == 'th' ? 'เกี่ยวกับคอร์สนี้' : 'About this course'; ?></h3>
                            <div class="rich-text">
                                <?php echo nl2br(htmlspecialchars($course['long_desc'])); ?>
                            </div>
                        </div>

                        <div id="tab-curriculum" class="tab-pane">
                            <h3 class="pane-title"><?php echo $current_lang == 'th' ? 'เนื้อหาการเรียน' : 'Curriculum'; ?></h3>
                            <div class="curriculum-list">
                                <?php 
                                $content_json = $course['content_json'];
                                $sections = !empty($content_json) ? json_decode($content_json, true) : [];
                                if (!empty($sections) && is_array($sections)):
                                    foreach($sections as $index => $sec):
                                ?>
                                <div class="curriculum-section">
                                    <div class="section-header">
                                        <span class="num"><?php echo $index + 1; ?></span>
                                        <h4><?php echo htmlspecialchars($sec['section']); ?></h4>
                                    </div>
                                    <ul class="lesson-list">
                                        <?php if (!empty($sec['lessons']) && is_array($sec['lessons'])): ?>
                                            <?php foreach($sec['lessons'] as $lesson): ?>
                                                <li><i class="far fa-play-circle"></i> <?php echo htmlspecialchars($lesson); ?></li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="empty"><?php echo $current_lang == 'th' ? 'ไม่มีรายละเอียดบทเรียน' : 'No lessons listed'; ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <?php endforeach; else: ?>
                                    <p class="empty-state"><?php echo $current_lang == 'th' ? 'ยังไม่มีรายละเอียดเนื้อหา' : 'No curriculum details available'; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="tab-reviews" class="tab-pane">
                            <div class="reviews-summary">
                                <div class="rating-avg">
                                    <div class="num"><?php echo $course['rating']; ?></div>
                                    <div class="stars">★★★★★</div>
                                    <div class="count"><?php echo $course['reviews']; ?> รีวิว</div>
                                </div>
                            </div>
                            <div class="review-list">
                                <div class="review-item">
                                    <div class="reviewer">
                                        <div class="avatar">👤</div>
                                        <div class="info">
                                            <div class="name">นางสาวสมหญิง</div>
                                            <div class="date">2 วันที่แล้ว</div>
                                        </div>
                                        <div class="stars">★★★★★</div>
                                    </div>
                                    <p>เนื้อหาดีมากค่ะ เข้าใจง่าย นำไปปรับใช้กับงานได้จริง แนะนำเลยค่ะ</p>
                                </div>
                                <div class="review-item">
                                    <div class="reviewer">
                                        <div class="avatar">👤</div>
                                        <div class="info">
                                            <div class="name">นายสมชาย</div>
                                            <div class="date">1 สัปดาห์ที่แล้ว</div>
                                        </div>
                                        <div class="stars">★★★★★</div>
                                    </div>
                                    <p>อธิบายขั้นตอนได้ชัดเจนมากครับ คุ้มค่ากับราคาที่จ่ายไป</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-card-wrap reveal">
                    <div class="course-buy-card">
                        <div class="card-image">
                            <?php 
                            $img_val = $course['image'];
                            if (strpos($img_val, '<img') !== false): 
                                echo str_replace('<img', '<img class="main-img"', $img_val);
                            elseif (strpos($img_val, '.') !== false): 
                                $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . htmlspecialchars($img_val) : 'IMG/' . htmlspecialchars($img_val);
                            ?>
                                <img src="<?php echo $img_src; ?>" alt="Course" class="main-img" />
                            <?php else: ?>
                                <div class="emoji-img"><?php echo htmlspecialchars($img_val ?: '📚'); ?></div>
                            <?php endif; ?>
                            <div class="image-overlay"></div>
                        </div>
                        <div class="card-body">
                            <div class="price-box">
                                <div class="current-price">฿<?php echo number_format($course['price']); ?></div>
                                <div class="old-price">฿<?php echo number_format($course['old_price']); ?></div>
                                <div class="discount-badge"><?php echo round((($course['old_price'] - $course['price']) / $course['old_price']) * 100); ?>% OFF</div>
                            </div>
                            
                            <script>
                                const currentCourseData = {
                                    id: <?php echo $course['id']; ?>,
                                    name: <?php echo json_encode($course['name']); ?>,
                                    price: <?php echo $course['price']; ?>,
                                    instructor: <?php echo json_encode($course['instructor']); ?>,
                                    image: <?php echo json_encode($course['image']); ?>,
                                    description: <?php echo json_encode($course['description']); ?>,
                                    category: <?php echo json_encode($course['category']); ?>
                                };
                            </script>
                            
                            <div class="action-btns">
                                <button class="btn-primary" onclick="buyNow(currentCourseData)">
                                    <i class="fas fa-bolt"></i> <?php echo $current_lang == 'th' ? 'สมัครเรียนเลย' : 'Enroll Now'; ?>
                                </button>
                                <button class="btn-outline" onclick="addToCart(<?php echo $course['id']; ?>)">
                                    <i class="fas fa-shopping-cart"></i> <?php echo $current_lang == 'th' ? 'เพิ่มลงตะกร้า' : 'Add to Cart'; ?>
                                </button>
                            </div>

                            <ul class="benefit-list">
                                <li><i class="fas fa-check-circle"></i> <?php echo $current_lang == 'th' ? 'เรียนได้ตลอดชีพ' : 'Lifetime Access'; ?></li>
                                <li><i class="fas fa-check-circle"></i> <?php echo $current_lang == 'th' ? 'ดูผ่านมือถือ/แท็บเล็ตได้' : 'Mobile/Tablet Support'; ?></li>
                                <li><i class="fas fa-check-circle"></i> <?php echo $current_lang == 'th' ? 'มีใบประกาศนียบัตร' : 'Certificate of Completion'; ?></li>
                            </ul>
                        </div>
                    </div>

                    <!-- MOVE SIDEBAR WIDGETS HERE -->
                    <div class="sticky-side" style="margin-top: 30px;">
                        <div class="promo-card">
                            <h4><i class="fas fa-building"></i> สำหรับองค์กร</h4>
                            <p>ต้องการซื้อให้ทีม หรือขอใบเสนอราคาแบบองค์กรเพื่อรับส่วนลดพิเศษ</p>
                            <button class="btn-ghost-gold">ขอใบเสนอราคา</button>
                        </div>

                        <?php if (count($relatedCourses) > 0): ?>
                        <div class="related-widget">
                            <h4>คอร์สที่เกี่ยวข้อง</h4>
                            <?php foreach (array_slice($relatedCourses, 0, 3) as $related): ?>
                            <a href="dic_product.php?id=<?php echo $related['id']; ?>" class="related-item">
                                <div class="rel-img">
                                    <img src="IMG/chatediter.png" alt="Course">
                                </div>
                                <div class="rel-info">
                                    <div class="rel-name"><?php echo htmlspecialchars($related['name']); ?></div>
                                    <div class="rel-price">฿<?php echo number_format($related['price']); ?></div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-socials">
            <a class="social-btn" href="#" title="Facebook">f</a>
            <a class="social-btn" href="#" title="YouTube">▶</a>
            <div class="social-right">
                <a class="social-btn" href="#" title="LinkedIn">in</a>
                <a class="social-btn" href="#" title="Instagram">📷</a>
            </div>
        </div>
        <div class="footer-bottom">
            <div>
                <div class="footer-brand">ICONNEX</div>
                <div>Premium E-Learning Platform</div>
            </div>
            <div class="col-center">094-546-2224</div>
            <div class="col-right">© 2025 ICONNEX Creators Club.</div>
        </div>
    </footer>

    <script>
        function switchContentTab(btn, tabId) {
            document.querySelectorAll('.content-tabs .tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        }
    </script>
</body>

</html>
