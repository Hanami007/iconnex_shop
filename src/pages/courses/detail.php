<?php
useService('course_data');
useService('lang');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$course = isset($courses[$id]) ? $courses[$id] : null;

if (!$course) {
    header('Location: index.php');
    exit;
}

// Prepare content data
$sections = json_decode($course['content_json'], true);

// Recommended Courses (same category, excluding current)
$recommended = array_filter($courses, function($c) use ($course, $id) {
    return $c['category'] === $course['category'] && $c['id'] !== $id;
});
$recommended = array_slice($recommended, 0, 3);
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($course['name']); ?> - ICONNEX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="src/styles/style.css?v=1.2" />
    <link rel="stylesheet" href="src/styles/notification.css?v=1.2" />
    <script src="src/assets/js/notification/notificationService.js?v=1.2"></script>
    <script src="src/assets/js/notification/notificationUI.js?v=1.2"></script>
    <script defer src="src/assets/js/script.js?v=1.2"></script>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const csrfToken = "<?php echo getCsrfToken(); ?>";
    </script>
    <style>
        body { background: var(--navy-deep); color: var(--text-on-navy); font-family: 'Prompt', sans-serif; }
        
        .course-hero-wrap { 
            padding: 140px 5% 60px; 
            background: linear-gradient(to bottom, rgba(11,18,33,1) 0%, rgba(18,30,52,0.4) 100%);
        }
        .hero-container { 
            max-width: 1250px; 
            margin: 0 auto; 
            display: grid; 
            grid-template-columns: 1fr 400px; 
            gap: 50px; 
        }
        
        .hero-info h1 { 
            font-size: 3.5rem; 
            font-family: var(--font-display); 
            font-weight: 800; 
            margin-bottom: 20px; 
            letter-spacing: -1px;
            color: #fff;
        }
        .hero-info .short-desc { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 30px; line-height: 1.6; }
        .hero-meta { display: flex; gap: 20px; margin-bottom: 35px; }
        .meta-item { display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.95rem; color: #fff; }
        .meta-item i { color: var(--gold-soft); font-size: 1.1rem; }
        
        .instructor-strip { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .inst-avatar { 
            width: 45px; height: 45px; border-radius: 50%; 
            background: rgba(255,255,255,0.05); border: 1px solid var(--gold-line); 
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem; 
        }
        .inst-info .label { font-size: 0.65rem; color: var(--text-subtle); text-transform: uppercase; letter-spacing: 1px; }
        .inst-info .name { font-weight: 700; color: #fff; font-size: 1rem; }

        /* Tabs Container moved inside Hero Left */
        .tabs-container { 
            background: rgba(255,255,255,0.02); 
            border: 1px solid var(--navy-border2); 
            border-radius: 20px; 
            padding: 30px; 
            margin-top: 20px;
        }
        .tabs-nav { display: flex; gap: 25px; margin-bottom: 30px; border-bottom: 1px solid var(--navy-border); }
        .tab-btn { 
            background: none; border: none; padding: 12px 5px; color: var(--text-muted); 
            font-weight: 600; cursor: pointer; font-size: 1rem; position: relative; transition: 0.3s;
        }
        .tab-btn.active { color: #fff; }
        .tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: var(--gold); border-radius: 10px; }
        
        .tab-pane h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 25px; font-family: var(--font-display); color: #fff; }

        /* The Floating Card */
        .purchase-card { 
            background: var(--navy-card); 
            border: 1px solid var(--navy-border2); 
            border-radius: 24px; 
            overflow: hidden; 
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
            position: relative;
            z-index: 10;
        }
        .card-img-wrap { width: 100%; height: 220px; overflow: hidden; background: #000; }
        .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .card-body { padding: 30px; }
        .card-price-row { display: flex; align-items: baseline; gap: 10px; margin-bottom: 25px; }
        .card-price-row .new { font-size: 2.8rem; font-weight: 800; color: #fff; font-family: var(--font-display); }
        .card-price-row .old { color: var(--text-subtle); text-decoration: line-through; font-size: 1.1rem; }
        .card-price-row .off { background: #ff4757; color: #fff; padding: 3px 8px; border-radius: 5px; font-size: 0.75rem; font-weight: 800; }
        
        .btn-enroll { 
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; text-decoration: none; background: var(--gold); color: var(--navy-deep); 
            padding: 16px; border-radius: 12px; font-weight: 800; font-size: 1.05rem; margin-bottom: 12px; transition: 0.3s;
            box-shadow: 0 10px 20px rgba(201, 168, 76, 0.2);
        }
        .btn-enroll:hover { background: var(--gold-soft); transform: translateY(-3px); }
        .btn-add-cart { 
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; background: rgba(255,255,255,0.03); color: #fff; 
            border: 1px solid var(--navy-border2); padding: 16px; border-radius: 12px;
            font-weight: 700; font-size: 1.05rem; cursor: pointer; transition: 0.3s;
        }
        .btn-add-cart:hover { background: rgba(255,255,255,0.08); }

        /* Recommended Section */
        .recommended-section { padding: 100px 5% 120px; background: #070c16; }
        .recommended-container { max-width: 1250px; margin: 0 auto; }
        .recommended-title { font-size: 2.2rem; font-family: var(--font-display); font-weight: 800; color: #fff; margin-bottom: 50px; text-align: center; }

        @media (max-width: 1100px) {
            .hero-container { grid-template-columns: 1fr; }
            .purchase-card { margin-top: 40px; position: static; }
        }
    </style>
</head>

<body>
    <!-- NAV (Match index.php) -->
    <nav>
        <a href="index.php" class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </a>
        <ul class="nav-links">
            <li><a href="index.php"><?php echo __('nav_home'); ?></a></li>
            <li><a href="index.php#courses"><?php echo __('nav_courses'); ?></a></li>
            <li><a href="index.php#portfolio"><?php echo __('nav_portfolio'); ?></a></li>
            <li><a href="index.php#contact"><?php echo __('nav_contact'); ?></a></li>
            <li class="lang-switcher">
                <a href="?id=<?php echo $id; ?>&lang=th" class="<?php echo $current_lang === 'th' ? 'active' : ''; ?>">TH</a>
                <span>|</span>
                <a href="?id=<?php echo $id; ?>&lang=en" class="<?php echo $current_lang === 'en' ? 'active' : ''; ?>">EN</a>
            </li>
        </ul>
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px;">
            <a href="#" onclick="openCartModal(event)" style="position:relative; font-size: 1.2rem; color: #fff; text-decoration:none; transition: 0.3s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='#fff'">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" style="display:none; position:absolute; top:-8px; right:-12px; background:var(--gold); color:var(--navy-deep); border-radius:50%; width:18px; height:18px; font-size:.7rem; align-items:center; justify-content:center; font-weight:800; border: 2px solid var(--navy-deep);">0</span>
            </a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php renderComponent('notification/NotificationDropdown'); ?>
                <div style="display: flex; align-items: center; gap: 15px; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                    <a href="my_orders.php" style="color: var(--gold-soft); font-size: 0.85rem; font-weight: 700; text-decoration:none;">
                        <i class="fas fa-history"></i> <?php echo __('nav_my_orders'); ?>
                    </a>
                    <a href="logout.php" style="background: rgba(255,255,255,0.05); color: #fff; padding: 6px 15px; border-radius: 20px; font-size: 0.75rem; text-decoration:none;">
                        <?php echo __('nav_logout'); ?>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" style="background: var(--gold); color: var(--navy-deep); padding: 8px 25px; border-radius: 30px; font-weight: 800; text-decoration:none; font-size: 0.85rem;">
                    <?php echo __('btn_login'); ?>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="course-hero-wrap">
        <div class="hero-container">
            <div class="hero-info">
                <div style="font-size: 0.8rem; color: var(--text-subtle); margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;">
                    Home > Courses > <?php echo htmlspecialchars($course['name']); ?>
                </div>
                <h1><?php echo htmlspecialchars($course['name']); ?></h1>
                <p class="short-desc"><?php echo htmlspecialchars($course['short_desc']); ?></p>
                
                <div class="hero-meta">
                    <div class="meta-item"><i class="far fa-clock"></i> <span><?php echo $course['hours']; ?> ชม.</span></div>
                    <div class="meta-item"><i class="far fa-play-circle"></i> <span><?php echo $course['lessons']; ?> บทเรียน</span></div>
                    <div class="meta-item"><i class="fas fa-star"></i> <span><?php echo $course['rating']; ?> (<?php echo $course['reviews']; ?> รีวิว)</span></div>
                </div>

                <div class="instructor-strip">
                    <div class="inst-avatar"><i class="fas fa-user-tie" style="color: var(--gold-soft);"></i></div>
                    <div class="inst-info">
                        <div class="label">Instructor</div>
                        <div class="name"><?php echo htmlspecialchars($course['instructor']); ?></div>
                    </div>
                </div>

                <!-- CONTENT MOVED UP HERE -->
                <div class="tabs-container">
                    <div class="tabs-nav">
                        <button class="tab-btn" onclick="switchTab(this, 'overview')">รายละเอียด</button>
                        <button class="tab-btn active" onclick="switchTab(this, 'curriculum')">เนื้อหา</button>
                        <button class="tab-btn" onclick="switchTab(this, 'reviews')">รีวิว</button>
                    </div>
                    
                    <div id="overview" class="tab-pane" style="display:none;">
                        <h2>รายละเอียดคอร์ส</h2>
                        <div class="rich-text" style="color: var(--text-muted); line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($course['long_desc'])); ?>
                        </div>
                    </div>
                    
                    <div id="curriculum" class="tab-pane">
                        <h2>เนื้อหาการเรียน</h2>
                        <div class="curriculum-list">
                            <?php if ($sections): foreach($sections as $idx => $sec): ?>
                            <div class="curriculum-item" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px; padding: 25px; margin-bottom: 15px;">
                                <h4 style="color: #fff; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 28px; height: 28px; background: var(--gold-pale); color: var(--gold-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;"><?php echo $idx + 1; ?></span>
                                    <?php echo htmlspecialchars($sec['section']); ?>
                                </h4>
                                <ul style="list-style: none; padding-left: 38px;">
                                    <?php foreach($sec['lessons'] as $lesson): ?>
                                    <li style="color: var(--text-muted); margin-bottom: 10px; font-size: 0.95rem; display: flex; align-items: center; gap: 10px;">
                                        <i class="far fa-play-circle" style="color: var(--gold-soft); opacity: 0.6;"></i> <?php echo htmlspecialchars($lesson); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endforeach; else: ?>
                                <p style="color: var(--text-muted);">Coming soon...</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="reviews" class="tab-pane" style="display:none;">
                        <h2>ความเห็นจากผู้เรียน</h2>
                        
                        <!-- Review Form -->
                        <div class="review-form-container" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 30px; margin-bottom: 40px;">
                            <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem;">เขียนรีวิวของคุณ</h3>
                            <form id="review-form" onsubmit="submitReview(event)">
                                <input type="hidden" name="course_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="action" value="submit">
                                
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; color: var(--text-muted); margin-bottom: 10px; font-size: 0.9rem;">คะแนนความพึงพอใจ</label>
                                    <div class="star-rating" style="display: flex; gap: 10px; font-size: 1.5rem; color: #444;">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="fas fa-star star-input" data-value="<?php echo $i; ?>" style="cursor: pointer; transition: 0.2s;" onclick="setRating(<?php echo $i; ?>)"></i>
                                        <?php endfor; ?>
                                        <input type="hidden" name="rating" id="rating-input" value="5">
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; color: var(--text-muted); margin-bottom: 10px; font-size: 0.9rem;">ความเห็นของคุณ</label>
                                    <textarea name="comment" placeholder="เล่าความประทับใจของคุณ..." style="width: 100%; background: #0b1221; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; color: #fff; font-family: 'Prompt', sans-serif; resize: vertical; min-height: 100px;"></textarea>
                                </div>
                                
                                <button type="submit" class="btn-submit-review" style="background: var(--gold); color: var(--navy-deep); border: none; padding: 12px 30px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;">ส่งรีวิว</button>
                            </form>
                        </div>

                        <div id="reviews-list">
                            <p style="color: var(--text-muted);">กำลังโหลดรีวิว...</p>
                        </div>
                    </div>

                    <script>
                        let currentRating = 5;
                        function setRating(val) {
                            currentRating = val;
                            document.getElementById('rating-input').value = val;
                            document.querySelectorAll('.star-input').forEach((s, idx) => {
                                if (idx < val) s.style.color = 'var(--gold)';
                                else s.style.color = '#444';
                            });
                        }
                        
                        // Initial star highlight
                        setTimeout(() => setRating(5), 100);

                        async function submitReview(e) {
                            e.preventDefault();
                            const form = e.target;
                            const fd = new FormData(form);
                            
                            try {
                                const res = await fetch('api/reviews.php', { method: 'POST', body: fd });
                                const data = await res.json();
                                if (data.success) {
                                    alert('ขอบคุณสำหรับรีวิวครับ!');
                                    form.reset();
                                    setRating(5);
                                    loadReviews();
                                } else {
                                    alert(data.message);
                                }
                            } catch (err) {
                                alert('เกิดข้อผิดพลาดในการส่งรีวิว');
                            }
                        }

                        async function loadReviews() {
                            const list = document.getElementById('reviews-list');
                            const fd = new FormData();
                            fd.append('action', 'get_reviews');
                            fd.append('course_id', <?php echo $id; ?>);
                            
                            try {
                                const res = await fetch('api/reviews.php', { method: 'POST', body: fd });
                                const data = await res.json();
                                
                                if (data.success && data.reviews.length > 0) {
                                    list.innerHTML = '';
                                    data.reviews.forEach(r => {
                                        const date = new Date(r.created_at).toLocaleDateString('th-TH');
                                        const stars = '⭐'.repeat(r.rating);
                                        const html = `
                                            <div style="background: rgba(255,255,255,0.01); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 25px 0; margin-bottom: 5px;">
                                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                                    <div style="display: flex; gap: 12px; align-items: center;">
                                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--navy-light); border: 1px solid var(--gold-line); display: flex; align-items: center; justify-content: center; color: var(--gold-soft); font-weight: 700;">${r.user_name[0]}</div>
                                                        <div>
                                                            <div style="color: #fff; font-weight: 700; font-size: 0.95rem;">${r.user_name}</div>
                                                            <div style="font-size: 0.8rem; color: var(--gold-soft);">${stars}</div>
                                                        </div>
                                                    </div>
                                                    <div style="font-size: 0.75rem; color: var(--text-subtle);">${date}</div>
                                                </div>
                                                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; padding-left: 52px;">${r.comment}</p>
                                            </div>
                                        `;
                                        list.insertAdjacentHTML('beforeend', html);
                                    });
                                } else {
                                    list.innerHTML = '<p style="color: var(--text-muted);">ยังไม่มีรีวิวสำหรับคอร์สนี้ เป็นคนแรกที่รีวิวเลย!</p>';
                                }
                            } catch (err) {
                                list.innerHTML = '<p style="color: var(--text-muted);">โหลดรีวิวไม่สำเร็จ</p>';
                            }
                        }

                        // Load reviews on tab switch or page load
                        document.addEventListener('DOMContentLoaded', loadReviews);
                    </script>
                </div>
            </div>

            <div class="hero-card-col">
                <div class="purchase-card">
                    <div class="card-img-wrap">
                        <?php 
                        $img_val = $course['image'];
                        $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . $img_val : ASSETS_DIR . '/img/' . $img_val;
                        if (strpos($img_val, '<img') !== false && preg_match('/src="([^"]+)"/', $img_val, $m)) $img_src = $m[1];
                        ?>
                        <img src="<?php echo $img_src; ?>" alt="Course Thumbnail">
                    </div>
                    <div class="card-body">
                        <div class="card-price-row">
                            <span class="new">฿<?php echo number_format($course['price']); ?></span>
                            <?php if($course['old_price'] > $course['price']): ?>
                                <span class="old">฿<?php echo number_format($course['old_price']); ?></span>
                                <span class="off"><?php echo round((($course['old_price'] - $course['price']) / $course['old_price']) * 100); ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                        <a href="javascript:void(0)" onclick="buyNow(<?php echo $id; ?>)" class="btn-enroll">
                            <i class="fas fa-bolt"></i> สมัครเรียนเลย
                        </a>
                        <button class="btn-add-cart" onclick="addToCart(<?php echo $id; ?>)">
                            <i class="fas fa-shopping-cart"></i> เพิ่มลงตะกร้า
                        </button>
                        
                        <ul class="card-benefits">
                            <li><i class="fas fa-check-circle"></i> เรียนได้ตลอดชีพ</li>
                            <li><i class="fas fa-check-circle"></i> ดูผ่านมือถือ/แท็บเล็ตได้</li>
                            <li><i class="fas fa-check-circle"></i> มีใบประกาศนียบัตร</li>
                        </ul>
                    </div>
                </div>
                
                <div class="promo-widget" style="background: rgba(255,255,255,0.02); border: 1px solid var(--gold-line); border-radius: 20px; padding: 25px; margin-top: 30px;">
                    <h4 style="color: var(--gold-soft); margin-bottom: 12px; display: flex; gap: 8px; align-items: center;"><i class="fas fa-building"></i> สำหรับองค์กร</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">ต้องการซื้อให้ทีม หรือขอใบเสนอราคาแบบองค์กรเพื่อรับส่วนลดพิเศษ</p>
                    <button style="width: 100%; background: none; border: 1px solid var(--gold-soft); color: var(--gold-soft); padding: 10px; border-radius: 8px; font-weight: 700; cursor: pointer;">ขอใบเสนอราคา</button>
                </div>
            </div>
        </div>
    </div>

    <!-- RECOMMENDED COURSES -->
    <section class="recommended-section">
        <div class="recommended-container">
            <h2 class="recommended-title">คอร์สเรียนที่คุณอาจสนใจ</h2>
            <div class="course-grid">
                <?php foreach ($recommended as $rec): ?>
                <div class="course-card" onclick="location.href='dic_product.php?id=<?php echo $rec['id']; ?>'">
                    <div class="course-card-header">
                        <?php 
                        $r_img = $rec['image'];
                        $r_src = strpos($r_img, 'uploads/') === 0 ? '/' . $r_img : ASSETS_DIR . '/img/' . $r_img;
                        if (strpos($r_img, '<img') !== false && preg_match('/src="([^"]+)"/', $r_img, $m)) $r_src = $m[1];
                        ?>
                        <img src="<?php echo $r_src; ?>" alt="Course" class="course-card-img">
                        <div class="course-card-label"><?php echo htmlspecialchars($rec['category']); ?></div>
                    </div>
                    <div class="course-card-body">
                        <h3 class="course-name"><?php echo htmlspecialchars($rec['name']); ?></h3>
                        <div class="course-instructor">
                            <div class="instructor-avatar-placeholder"><i class="fas fa-user"></i></div>
                            <span><?php echo htmlspecialchars($rec['instructor']); ?></span>
                        </div>
                        <div class="course-info">
                            <span><i class="far fa-clock"></i> <?php echo $rec['hours']; ?> ชม.</span>
                        </div>
                    </div>
                    <div class="course-card-footer">
                        <div class="course-price-container">
                            <span class="course-price-new">฿<?php echo number_format($rec['price']); ?></span>
                        </div>
                        <button class="course-btn" style="padding: 0 15px; border-radius: 10px; font-weight: 700; height: 40px;">ดูคอร์ส</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER (Match index.php) -->
    <footer>
        <div class="footer-socials">
            <a class="social-btn" href="#" title="Facebook">f</a>
            <a class="social-btn" href="#" title="YouTube">▶</a>
            <div class="social-right" style="display:flex;gap:12px;">
                <a class="social-btn" href="#" title="LinkedIn">in</a>
                <a class="social-btn" href="#" title="Instagram">📷</a>
            </div>
        </div>
        <div class="footer-bottom">
            <div>
                <div class="footer-brand">ICONNEX</div>
                <div><?php echo __('footer_desc'); ?></div>
            </div>
            <div class="col-center"><?php echo __('footer_phone'); ?> 094-546-2224</div>
            <div class="col-right"><?php echo __('footer_address'); ?></div>
        </div>
    </footer>

    <script>
        function switchTab(btn, id) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
            btn.classList.add('active');
            document.getElementById(id).style.display = 'block';
        }
        
        async function buyNow(id) {
            if (typeof window.addToCart === 'function') {
                const success = await window.addToCart(id);
                if (success && typeof window.goToPayment === 'function') {
                    window.goToPayment();
                }
            }
        }
    </script>
</body>
</html>
