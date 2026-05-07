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
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <h1>Master editing. Grow an audience. Work with your dream clients.</h1>
        <p>Creators Club is the next gen E-Learning platform for everyone who want to take their editing skills to the
            next level and connect with like-minded individuals. Unlock your full creative potential and join a
            community that inspires and elevates your craft.</p>
        <a href="#courses" class="btn-primary">เริ่มต้น</a>
        <div class="hero-stats">
            <div>
                <div class="stat-num">100K</div>
                <div class="stat-label">สมาชิก</div>
            </div>
            <div>
                <div class="stat-num">50+</div>
                <div class="stat-label">คอร์ส</div>
            </div>
            <div>
                <div class="stat-num">4.9 ★</div>
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
                <button class="filter-pill" onclick="setFilter(this,'editing')">การตัดต่อ</button>
                <button class="filter-pill" onclick="setFilter(this,'business')">ธุรกิจ</button>
                <button class="filter-pill" onclick="setFilter(this,'tiktok')">TikTok</button>
            </div>
        </div>

        <div class="category-section" data-category="editing" style="transition: opacity 0.4s ease;">
            <!-- Category 1 -->
            <p class="course-desc">ที่สุดของการเรียนรู้และทำความเข้าใจ<br>
            รายละเอียด รายละเอียด รายละเอียด รายละเอียด รายละเอียด<br>
            รายละเอียด รายละเอียด รายละเอียด รายละเอียด รายละเอียด รายละเอีย</p>
        <div class="course-grid" id="editing-grid">
            <div class="course-card" onclick="goToDetail(1)">
                <div class="course-card-header">
                    <img src="IMG/chatediter.png" alt="Video Editing Pro" class="course-card-img" />
                    <div class="course-card-label">คอร์สตัดต่อวิดีโอ</div>
                    <div class="course-rating">⭐ 4.8 (250)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Video Editing Pro</p>
                    <p class="course-instructor">โดย สมชาย แก้ว</p>
                    <p class="course-description">เรียนรู้เทคนิคการตัดต่อวิดีโอขั้นสูง ใช้ Adobe Premiere Pro</p>
                    <div class="course-info">
                        <span>📚 12 บทเรียน</span>
                        <span>⏱️ 24 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 1,299</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(1)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(1)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(2)">
                <div class="course-card-header">
                    <div class="course-card-label">Advanced Color Grading</div>
                    <div class="course-rating">⭐ 4.9 (180)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Color Grading Masterclass</p>
                    <p class="course-instructor">โดย ธวัชชัย ศิริ</p>
                    <p class="course-description">เทคนิคปรับสีระดับมืออาชีพสำหรับภาพยนตร์</p>
                    <div class="course-info">
                        <span>📚 15 บทเรียน</span>
                        <span>⏱️ 30 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 1,599</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(2)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(2)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(3)">
                <div class="course-card-header">
                    <div class="course-card-label">Motion Graphics</div>
                    <div class="course-rating">⭐ 4.7 (165)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Motion Graphics Design</p>
                    <p class="course-instructor">โดย ณัฐปอ สิทธิพล</p>
                    <p class="course-description">สร้างแอนิเมชั่น 2D/3D ที่สวยงามด้วย After Effects</p>
                    <div class="course-info">
                        <span>📚 18 บทเรียน</span>
                        <span>⏱️ 36 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 1,899</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(3)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(3)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <!-- Category 2: BUSINESS -->
        <div class="category-section" data-category="business" style="transition: opacity 0.4s ease;">
            <div class="course-section-label"><h2 style="text-align: center;">BUSINESS</h2></div>
            <p class="course-desc" style="text-align: center;">สำหรับผู้ประกอบการและนักพัฒนาธุรกิจที่ต้องการเพิ่มทักษะการตลาดดิจิทัล<br>
            เรียนรู้จากผู้เชี่ยวชาญในด้านธุรกิจออนไลน์</p>
        <div class="course-grid" id="business-grid">
            <div class="course-card" onclick="goToDetail(4)">
                <div class="course-card-header">
                    <div class="course-card-label">Digital Marketing</div>
                    <div class="course-rating">⭐ 4.8 (320)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Digital Marketing Bootcamp</p>
                    <p class="course-instructor">โดย นพดล ธรรมรักษ์</p>
                    <p class="course-description">เทคนิกการตลาดดิจิทัลครบถ้วนตั้งแต่ SEO ถึง Social Media</p>
                    <div class="course-info">
                        <span>📚 20 บทเรียน</span>
                        <span>⏱️ 40 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 1,299</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(4)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(4)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(5)">
                <div class="course-card-header">
                    <div class="course-card-label">Personal Branding</div>
                    <div class="course-rating">⭐ 4.9 (210)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Build Your Personal Brand</p>
                    <p class="course-instructor">โดย วิชิต สุมนา</p>
                    <p class="course-description">สร้างแบรนด์ส่วนตัวของคุณให้เป็นที่รู้จักในโลกดิจิทัล</p>
                    <div class="course-info">
                        <span>📚 16 บทเรียน</span>
                        <span>⏱️ 28 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 999</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(5)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(5)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(6)">
                <div class="course-card-header">
                    <div class="course-card-label">Copywriting Mastery</div>
                    <div class="course-rating">⭐ 4.8 (190)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Sales Copywriting Secrets</p>
                    <p class="course-instructor">โดย นิชา ธัญชนก</p>
                    <p class="course-description">เขียนสัญญาณขายที่ดึงดูดกระตุ้นการซื้อได้อย่างมีประสิทธิ</p>
                    <div class="course-info">
                        <span>📚 14 บทเรียน</span>
                        <span>⏱️ 22 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 799</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(6)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(6)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <!-- Category 3: TIKTOK -->
        <div class="category-section" data-category="tiktok" style="transition: opacity 0.4s ease;">
            <div class="course-section-label"><h2 style="text-align: center;">TIKTOK</h2></div>
            <p class="course-desc" style="text-align: center;">กลยุทธ์การทำ TikTok ให้ไวรัลและสร้างรายได้จากแพลตฟอร์มนี้<br>
                เรียนรู้จากผู้สร้างสรรค์ที่มีผู้ติดตามหลักล้านคน</p>
        <div class="course-grid" id="tiktok-grid">
            <div class="course-card" onclick="goToDetail(7)">
                <div class="course-card-header">
                    <div class="course-card-label">TikTok Viral Mastery</div>
                    <div class="course-rating">⭐ 4.9 (450)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">How to Go Viral on TikTok</p>
                    <p class="course-instructor">โดย อลิเศษ อินสตารา</p>
                    <p class="course-description">สูตรลับการทำวิดีโอให้ไวรัลและเพิ่มผู้ติดตามอย่างรวดเร็ว</p>
                    <div class="course-info">
                        <span>📚 12 บทเรียน</span>
                        <span>⏱️ 18 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 699</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(7)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(7)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(8)">
                <div class="course-card-header">
                    <div class="course-card-label">TikTok Content Creation</div>
                    <div class="course-rating">⭐ 4.8 (380)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Professional TikTok Content</p>
                    <p class="course-instructor">โดย สิตา กมลากร</p>
                    <p class="course-description">สร้างคอนเทนต์มีคุณภาพ ทำให้ผู้ชมติดตามและมีส่วนร่วม</p>
                    <div class="course-info">
                        <span>📚 10 บทเรียน</span>
                        <span>⏱️ 15 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 599</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(8)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(8)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
            <div class="course-card" onclick="goToDetail(9)">
                <div class="course-card-header">
                    <div class="course-card-label">TikTok Monetization</div>
                    <div class="course-rating">⭐ 4.7 (290)</div>
                </div>
                <div class="course-card-body">
                    <p class="course-name">Earn Money on TikTok</p>
                    <p class="course-instructor">โดย ศรัณย์ มัสยา</p>
                    <p class="course-description">วิธีต่างๆ ในการหารายได้จาก TikTok ตั้งแต่ต้น</p>
                    <div class="course-info">
                        <span>📚 11 บทเรียน</span>
                        <span>⏱️ 20 ชั่วโมง</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <span class="course-price">฿ 899</span>
                    <div style="display:flex;gap:8px;">
                        <button class="course-btn" onclick="event.stopPropagation();addToCart(9)">🛒</button>
                        <button class="course-btn" onclick="goToDetail(9)">ดูเพิ่มเติม</button>
                    </div>
                </div>
            </div>
        </div>
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