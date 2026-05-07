<?php
require_once 'course_data.php';
session_start();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ICONNEX – Creators Club</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;800&family=Sarabun:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script defer src="script.js"></script>
    <script>coursesData = <?php echo json_encode($courses); ?>;</script>

</head>

<body>

    <!-- NAV -->
    <nav>
        <div class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </div>
        <ul class="nav-links">
            <li><a href="#hero">หน้าหลัก</a></li>
            <li><a href="#about">เกี่ยวกับ</a></li>
            <li><a href="#services">บริการ</a></li>
            <li><a href="#news">ข่าว</a></li>
            <li><a href="#portfolio">ผลงาน</a></li>
            <li><a href="#contact">ติดต่อเรา</a></li>
            <li><a href="#courses" class="active">คอร์ส</a></li>
            <li>
                <a href="cart.php" style="position:relative">
                    🛒 <span id="cart-count" style="
            display:none; position:absolute; top:-8px; right:-12px;
            background:#6c63ff; color:#fff; border-radius:50%;
            width:18px; height:18px; font-size:.7rem;
            align-items:center; justify-content:center; font-weight:700;
        ">0</span>
                </a>
            </li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" style="color: #ffae35;">ออกจากระบบ (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="color: #4CAF50;">Admin Panel</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login.php" style="color: #4CAF50;">เข้าสู่ระบบ</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <h1>Master editing. Grow an audience. Work with your dream clients.</h1>
        <p>Creators Club is the next gen E-Learning platform for everyone who want to take their editing skills to the
            next level and connect with like-minded individuals. Unlock your full creative potential and join a
            community that inspires and elevates your craft.</p>
        <a href="#courses" class="btn-primary">เริ่มต้น</a>
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
        <div class="hero-stats">
            <div>
                <div class="stat-num"><?php echo $total_members_display; ?></div>
                <div class="stat-label">สมาชิก</div>
            </div>
            <div>
                <div class="stat-num"><?php echo $total_courses; ?>+</div>
                <div class="stat-label">คอร์ส</div>
            </div>
            <div>
                <div class="stat-num"><?php echo $avg_rating; ?> ★</div>
                <div class="stat-label">คะแนนรีวิว</div>
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
    <section id="portfolio" style="overflow: hidden;">
        <h2 class="section-title">ผลงาน</h2>
        <div class="tab-bar">
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

    <!-- COURSES -->
    <section id="courses">
        <!-- Tabs & Filters -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; margin-bottom: 36px;">
            <h1 style="font-size: 3rem;">แพ็กเกจ</h1>
            <p class="section-description">เลือกแพ็กเกจของคุณ <span style="color: var(--accent)">คลิกที่รูปภาพเพื่อดูรายละเอียด</span></p>
            <!-- Category Filters -->
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                <button class="filter-pill active" onclick="setFilter(this,'all')">ทั้งหมด</button>
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
        <div class="category-section" data-category="<?php echo htmlspecialchars($cat); ?>" style="transition: opacity 0.4s ease;">
            <div class="course-section-label"><h2 style="text-align: center;"><?php echo mb_strtoupper(htmlspecialchars($cat), 'UTF-8'); ?></h2></div>
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
                            // Render raw HTML if it was saved as an img tag previously
                            echo $img_val;
                        elseif (strpos($img_val, '.') !== false): 
                            $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . htmlspecialchars($img_val) : 'IMG/' . htmlspecialchars($img_val);
                        ?>
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($course['name']); ?>" class="course-card-img" />
                        <?php else: ?>
                            <div style="height: 180px; display: flex; align-items: center; justify-content: center; font-size: 64px; background: #f5f5f5; border-radius: 8px 8px 0 0;">
                                <?php echo htmlspecialchars($img_val ?: '📚'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="course-card-label"><?php echo htmlspecialchars($course['category']); ?></div>
                        <div class="course-rating">⭐ <?php echo number_format($course['rating'], 1); ?> (<?php echo $course['reviews']; ?>)</div>
                    </div>
                    <div class="course-card-body">
                        <p class="course-name"><?php echo htmlspecialchars($course['name']); ?></p>
                        <p class="course-instructor">โดย <?php echo htmlspecialchars($course['instructor']); ?></p>
                        <p class="course-description"><?php echo htmlspecialchars($course['short_desc']); ?></p>
                        <div class="course-info">
                            <span>📚 <?php echo $course['lessons']; ?> บทเรียน</span>
                            <span>⏱️ <?php echo $course['hours']; ?> ชั่วโมง</span>
                        </div>
                    </div>
                    <div class="course-card-footer">
                        <span class="course-price">฿ <?php echo number_format($course['price']); ?></span>
                        <div style="display:flex;gap:8px;">
                            <button class="course-btn" onclick="event.stopPropagation();addToCart(<?php echo $course['id']; ?>)">🛒</button>
                            <button class="course-btn" onclick="goToDetail(<?php echo $course['id']; ?>)">ดูเพิ่มเติม</button>
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

    <!-- TESTIMONIALS -->
    <section class="section testimonials-bg" id="testimonials">
        <h2 class="section-title">เสียงจากลูกค้าของเรา</h2>
        <div class="testimonials-track reveal">
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
                <div>โดยแพลตฟอร์ม ICONNEX มุ่งนำประโยชน์ใช้สอยดี สร้างความเปลี่ยนแปลงที่ดีให้กับทุกชีวิต</div>
            </div>
            <div class="col-center">094-546-2224</div>
            <div class="col-right">ที่อยู่อาคาร: The Metropolis Samrong</div>
        </div>
    </footer>
</body>

</html>