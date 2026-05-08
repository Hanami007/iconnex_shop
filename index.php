<?php
require_once 'course_data.php';
require_once 'lang.php';
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ICONNEX – Creators Club</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;800&family=Sarabun:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <script defer src="script.js"></script>
    <script>
        const coursesData = <?php echo json_encode($courses); ?>;
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const langData = {
            cart_popup_title: "<?php echo __('cart_popup_title'); ?>",
            cart_popup_desc: "<?php echo __('cart_popup_desc'); ?>",
            btn_login: "<?php echo __('btn_login'); ?>",
            btn_register: "<?php echo __('btn_register'); ?>"
        };
    </script>

</head>

<body>

    <!-- NAV -->
    <nav>
        <div class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </div>
        <ul class="nav-links">
            <li><a href="#hero"><?php echo __('nav_home'); ?></a></li>
            <li><a href="#about"><?php echo __('nav_about'); ?></a></li>
            <li><a href="#services"><?php echo __('nav_services'); ?></a></li>
            <li><a href="#news"><?php echo __('nav_news'); ?></a></li>
            <li><a href="#portfolio"><?php echo __('nav_portfolio'); ?></a></li>
            <li><a href="#contact"><?php echo __('nav_contact'); ?></a></li>
            <li><a href="#courses" class="active"><?php echo __('nav_courses'); ?></a></li>
            <li class="lang-switcher">
                <a href="?lang=th" class="<?php echo $current_lang === 'th' ? 'active' : ''; ?>">TH</a> | 
                <a href="?lang=en" class="<?php echo $current_lang === 'en' ? 'active' : ''; ?>">EN</a>
            </li>
            <li>
                <a href="#" onclick="openCartModal(event)" style="position:relative">
                    🛒 <span id="cart-count" style="
            display:none; position:absolute; top:-8px; right:-12px;
            background:#6c63ff; color:#fff; border-radius:50%;
            width:18px; height:18px; font-size:.7rem;
            align-items:center; justify-content:center; font-weight:700;
        ">0</span>
                </a>
            </li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" style="color: #ffae35;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="color: #4CAF50;">Admin Panel</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login.php" style="color: #4CAF50;"><?php echo __('btn_login'); ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <h1><?php echo __('hero_title'); ?></h1>
        <p><?php echo __('hero_subtitle'); ?></p>
        <a href="#courses" class="btn-primary"><?php echo __('hero_btn_explore'); ?></a>
        <?php
        $total_courses = count($courses);
        $total_rating = 0;
        $total_reviews = 0;
        foreach ($courses as $c) {
            $total_rating += floatval($c['rating']);
            $total_reviews += intval($c['reviews']);
        }
        $avg_rating = $total_courses > 0 ? number_format($total_rating / $total_courses, 1) : "5.0";
        $total_members = $total_reviews * 10; // Mock calculation based on reviews
        $total_members_display = $total_members > 1000 ? round($total_members / 1000, 1) . 'K' : number_format($total_members);
        if ($total_members == 0) $total_members_display = '0';
        ?>
        <div class="hero-stats" style="margin-top: 40px; border-top: 1px solid var(--border); padding-top: 30px;">
            <div class="reveal-pro">
                <div class="stat-num accent-text"><?php echo $total_members_display; ?></div>
                <div class="stat-label"><?php echo __('stats_members'); ?></div>
            </div>
            <div class="reveal-pro" style="transition-delay: 0.1s;">
                <div class="stat-num accent-text"><?php echo $total_courses; ?>+</div>
                <div class="stat-label"><?php echo __('stats_courses'); ?></div>
            </div>
            <div class="reveal-pro" style="transition-delay: 0.2s;">
                <div class="stat-num accent-text"><?php echo $avg_rating; ?> ★</div>
                <div class="stat-label"><?php echo __('stats_rating'); ?></div>
            </div>
        </div>
    </section>

    <!-- VIDEO -->
    <div class="video-section">
        <div class="video-container">
            <iframe width="900" height="506" src="https://www.youtube.com/embed/PorE9ETx9Ek" frameborder="0"
                allowfullscreen>
            </iframe>
        </div>
    </div>

    <!-- PORTFOLIO -->
    <section id="portfolio">
        <h2 class="section-title reveal">ผลงาน</h2>
        <p class="section-description reveal" style="margin-bottom: 40px; opacity: 0.8;">คัดสรรผลงานระดับพรีเมียมที่เราภูมิใจนำเสนอ เพื่อการันตีคุณภาพงานสร้างสรรค์ในทุกมิติ</p>
        <div class="tab-bar reveal">
            <button class="tab-btn" onclick="switchTab(this,'photo')">ภาพ</button>
            <button class="tab-btn active" onclick="switchTab(this,'video')">วิดีโอ</button>
        </div>

        <!-- PHOTOS MARQUEE -->
        <div id="portfolio-photo-content" class="portfolio-marquee-container">
            <div class="portfolio-marquee">
                <!-- Original set -->
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80" alt="THAIFEX 2025" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&q=80" alt="Event" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&q=80" alt="Stage" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=600&q=80" alt="Media" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1516280440502-861054b1f6f8?w=600&q=80" alt="Event 2" /></div>
                <!-- Duplicated set for seamless scrolling -->
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80" alt="THAIFEX 2025" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&q=80" alt="Event" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&q=80" alt="Stage" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=600&q=80" alt="Media" /></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1516280440502-861054b1f6f8?w=600&q=80" alt="Event 2" /></div>
            </div>
        </div>
        <!-- VIDEOS MARQUEE -->
        <div id="portfolio-video-content" class="portfolio-marquee-container" style="display: none;">
            <div class="portfolio-marquee">
                <!-- Original set -->
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&q=80" style="filter: brightness(0.7);" alt="Video 1" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=600&q=80" style="filter: brightness(0.7);" alt="Video 2" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80" style="filter: brightness(0.7);" alt="Video 3" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&q=80" style="filter: brightness(0.7);" alt="Video 4" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1516280440502-861054b1f6f8?w=600&q=80" style="filter: brightness(0.7);" alt="Video 5" /><span class="video-play-icon">▶</span></div>
                <!-- Duplicated set for seamless scrolling -->
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&q=80" style="filter: brightness(0.7);" alt="Video 1" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=600&q=80" style="filter: brightness(0.7);" alt="Video 2" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80" style="filter: brightness(0.7);" alt="Video 3" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&q=80" style="filter: brightness(0.7);" alt="Video 4" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card"><img src="https://images.unsplash.com/photo-1516280440502-861054b1f6f8?w=600&q=80" style="filter: brightness(0.7);" alt="Video 5" /><span class="video-play-icon">▶</span></div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="section testimonials-bg" id="testimonials">
        <h2 class="section-title reveal">เสียงจากลูกค้าของเรา</h2>
        <div class="testimonials-marquee-container">
            <div class="testimonials-track">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">
                        "คอร์สนี้เปลี่ยนชีวิตผมจริงๆ
                        ตอนนี้มีรายได้จากการตัดต่อวิดีโอเต็มเวลาแล้ว ขอบคุณ ICONNEX มากๆ
                        ครับ"
                    </p>
                <div class="testimonial-author">
                    <div class="avatar">ก</div>
                    <div class="author-info">
                        <div class="name">กิตติภพ ส.</div>
                        <div class="role">Freelance Editor</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">
                    "คอร์สนี้เปลี่ยนชีวิตผมจริงๆ
                    ตอนนี้มีรายได้จากการตัดต่อวิดีโอเต็มเวลาแล้ว ขอบคุณ ICONNEX มากๆ
                    ครับ"
                </p>
                <div class="testimonial-author">
                    <div class="avatar">ก</div>
                    <div class="author-info">
                        <div class="name">กิตติภพ ส.</div>
                        <div class="role">Freelance Editor</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">
                    "คอร์สนี้เปลี่ยนชีวิตผมจริงๆ
                    ตอนนี้มีรายได้จากการตัดต่อวิดีโอเต็มเวลาแล้ว ขอบคุณ ICONNEX มากๆ
                    ครับ"
                </p>
                <div class="testimonial-author">
                    <div class="avatar">ก</div>
                    <div class="author-info">
                        <div class="name">กิตติภพ ส.</div>
                        <div class="role">Freelance Editor</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">
                    "เนื้อหาละเอียดมากค่ะ อาจารย์อธิบายเข้าใจง่าย ทำตามได้เลย
                    ผลงานของเราพัฒนาขึ้นเร็วมากหลังเรียน"
                </p>
                <div class="testimonial-author">
                    <div class="avatar">น</div>
                    <div class="author-info">
                        <div class="name">นภัสสร ว.</div>
                        <div class="role">Content Creator</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">
                    "ชอบ Workshop สดมากครับ ได้ Feedback ตรงๆ จากผู้เชี่ยวชาญ
                    ทำให้พัฒนาได้เร็วกว่าเรียนคนเดียวมาก"
                </p>
                <div class="testimonial-author">
                    <div class="avatar">พ</div>
                    <div class="author-info">
                        <div class="name">พีระพัฒน์ ล.</div>
                        <div class="role">Videographer</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★☆</div>
                <p class="testimonial-text">
                    "คุ้มค่ามากค่ะ ตอนแรกกังวลว่าจะยากเกินไป แต่ระบบสอนดี ค่อยๆ เรียนได้
                    สนุกมากเลยค่ะ"
                </p>
                <div class="testimonial-author">
                    <div class="avatar">ส</div>
                    <div class="author-info">
                        <div class="name">สิรินดา ภ.</div>
                        <div class="role">Social Media Manager</div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- COURSES -->
    <section id="courses">
        <!-- Tabs & Filters -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; margin-bottom: 48px;">
            <div style="text-align: center;">
                <h2 style="font-size: 3.5rem; font-family: 'Prompt', sans-serif; font-weight: 800; margin-bottom: 12px; color: var(--white);"><?php echo __('packages_title'); ?></h2>
                <p class="section-description" style="font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto;"><?php echo __('packages_subtitle'); ?></p>
            </div>
            
            <!-- Category Filters -->
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 10px;">
                <button class="filter-pill active" onclick="setFilter(this,'all')"><?php echo __('filter_all'); ?></button>
                <?php 
                $unique_categories = [];
                foreach ($courses as $c) {
                    if (!in_array($c['category'], $unique_categories)) {
                        $unique_categories[] = $c['category'];
                    }
                }
                foreach ($unique_categories as $cat): 
                ?>
                <button class="filter-pill" onclick="setFilter(this,'<?php echo htmlspecialchars($cat); ?>')"><?php echo htmlspecialchars($cat); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach ($unique_categories as $cat): ?>
        <div class="category-section" data-category="<?php echo htmlspecialchars($cat); ?>" style="transition: all 0.4s ease; margin-bottom: 60px;">
            <div class="course-section-label" style="margin-bottom: 30px;">
                <h3 style="text-align: center; font-size: 1.5rem; color: var(--accent); letter-spacing: 2px; text-transform: uppercase;"><?php echo htmlspecialchars($cat); ?></h3>
                <div style="width: 60px; height: 3px; background: var(--accent); margin: 12px auto; border-radius: 2px;"></div>
            </div>
            
            <div class="course-grid">
                <?php 
                foreach ($courses as $course): 
                    if ($course['category'] === $cat):
                ?>
                <div class="course-card" onclick="goToDetail(<?php echo $course['id']; ?>)">
                    <div class="course-card-header">
                        <?php 
                        $img_val = $course['image'];
                        if (strpos($img_val, '<img') !== false): 
                            echo str_replace('<img', '<img class="course-card-img"', $img_val);
                        elseif (strpos($img_val, '.') !== false): 
                            $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . htmlspecialchars($img_val) : 'IMG/' . htmlspecialchars($img_val);
                        ?>
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($course['name']); ?>" class="course-card-img" />
                        <?php else: ?>
                            <div style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; font-size: 80px; background: #1a1a1a;">
                                <?php echo htmlspecialchars($img_val ?: '📚'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="course-card-label" style="position: absolute; top: 15px; right: 15px; margin: 0;"><?php echo htmlspecialchars($course['category']); ?></div>
                        <div class="course-rating" style="position: absolute; bottom: 15px; left: 15px; margin: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">⭐ <?php echo number_format($course['rating'], 1); ?></div>
                    </div>
                    
                    <div class="course-card-body">
                        <p class="course-name"><?php echo htmlspecialchars($course['name']); ?></p>
                        <p class="course-instructor">
                            <?php if (!empty($course['instructor_avatar'])): ?>
                                <img src="<?php echo htmlspecialchars($course['instructor_avatar']); ?>" alt="Instructor" class="instructor-avatar">
                            <?php else: ?>
                                <div class="instructor-avatar-placeholder"><i class="fas fa-user"></i></div>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($course['instructor']); ?>
                        </p>
                        
                        <div class="course-info">
                            <span><i class="fas fa-users"></i> <?php echo rand(50, 500); ?></span>
                            <span><i class="far fa-clock"></i> <?php echo $course['hours']; ?> ชม.</span>
                            <span><i class="fas fa-signal"></i> พื้นฐาน</span>
                        </div>
                        
                        <p class="course-description" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;"><?php echo htmlspecialchars($course['short_desc']); ?></p>
                    </div>
                    
                    <div class="course-card-footer">
                        <div class="course-price-container">
                            <span class="course-price-new">฿<?php echo number_format($course['price']); ?></span>
                            <?php if(isset($course['old_price']) && $course['old_price'] > $course['price']): ?>
                                <span class="course-price-old">฿<?php echo number_format($course['old_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="course-actions">
                            <button class="course-btn-cart" onclick="event.stopPropagation();addToCart(<?php echo $course['id']; ?>)">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <button class="course-btn-buy" onclick="goToDetail(<?php echo $course['id']; ?>)">
                                ซื้อคอร์สนี้
                            </button>
                        </div>
                    </div>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>


    <!-- FAQ -->
    <section id="faq">
        <h2 class="section-title">คำถามที่พบบ่อย</h2>
        <div class="faq-box">
            <div class="faq-item open">
                <div class="faq-question">
                    <span>Creators Club คืออะไร?</span>
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-answer">
                    Creators Club เป็นแพลตฟอร์ม E-Learning
                    สำหรับผู้ที่ต้องการพัฒนาทักษะการตัดต่อและเชื่อมต่อกับชุมชนนักสร้างสรรค์
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>ฉันสามารถเรียนได้เมื่อไหร่?</span>
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-answer">
                    คุณสามารถเรียนได้ตลอด 24 ชั่วโมง ไม่มีข้อจำกัดด้านเวลา
                    เข้าถึงเนื้อหาได้ทุกที่ทุกเวลาผ่านอุปกรณ์ใดก็ได้
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>มีการรับประกันคืนเงินหรือไม่?</span>
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-answer">
                    เรามีนโยบายคืนเงินภายใน 7 วันหากคุณไม่พอใจกับคอร์สที่เลือก
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>มีใบรับรองหลังจบคอร์สหรือเปล่า?</span>
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-answer">
                    ใช่ ผู้เรียนทุกคนที่จบหลักสูตรจะได้รับใบรับรองการผ่านการอบรมจาก ICONNEX
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>ชำระเงินได้อย่างไร?</span>
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-answer">
                    รับชำระผ่านบัตรเครดิต/เดบิต, PromptPay, และการโอนเงินผ่านธนาคาร
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
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
</body>

</html>
