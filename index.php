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
        <a href="index.php" class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </a>
        <ul class="nav-links">
            <li><a href="#hero"><?php echo __('nav_home'); ?></a></li>
            <li><a href="#courses"><?php echo __('nav_courses'); ?></a></li>
            <li><a href="#portfolio"><?php echo __('nav_portfolio'); ?></a></li>
            <li><a href="#contact"><?php echo __('nav_contact'); ?></a></li>
            <li class="lang-switcher">
                <a href="?lang=th" class="<?php echo $current_lang === 'th' ? 'active' : ''; ?>">TH</a>
                <span>|</span>
                <a href="?lang=en" class="<?php echo $current_lang === 'en' ? 'active' : ''; ?>">EN</a>
            </li>
        </ul>
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px;">
            <a href="#" onclick="openCartModal(event)" style="position:relative; font-size: 1.2rem; color: #fff; text-decoration:none;">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" style="display:none; position:absolute; top:-8px; right:-12px; background:var(--gold); color:var(--navy-deep); border-radius:50%; width:18px; height:18px; font-size:.7rem; align-items:center; justify-content:center; font-weight:800; border: 2px solid var(--navy-deep);">0</span>
            </a>
            <?php if(isset($_SESSION['user_id'])): ?>
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

    <!-- HERO -->
    <section class="hero" id="hero" style="padding: 140px 5% 100px; text-align: center; display: flex; flex-direction: column; align-items: center;">
        <h1 style="font-size: 4.5rem; background: linear-gradient(135deg, #fff 0%, var(--gold-soft) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 25px;"><?php echo __('hero_title'); ?></h1>
        <p style="font-size: 1.25rem; max-width: 800px; line-height: 1.6; margin-bottom: 40px; color: var(--text-muted);"><?php echo __('hero_subtitle'); ?></p>
        <div style="display: flex; gap: 20px;">
            <a href="#courses" class="btn-primary" style="padding: 18px 45px; border-radius: 50px; font-size: 1.1rem; font-weight: 800;"><?php echo __('hero_btn_explore'); ?></a>
            <a href="#about" class="btn-outline" style="padding: 18px 45px; border-radius: 50px; font-size: 1.1rem; font-weight: 800; border: 1px solid var(--navy-border2); background: rgba(255,255,255,0.03);"><?php echo __('hero_btn_about'); ?></a>
        </div>
        
        <?php
        $total_courses = count($courses);
        $total_rating = 0;
        $total_reviews = 0;
        foreach ($courses as $c) {
            $total_rating += floatval($c['rating']);
            $total_reviews += intval($c['reviews']);
        }
        $avg_rating = $total_courses > 0 ? number_format($total_rating / $total_courses, 1) : "5.0";
        $total_members = $total_reviews * 10;
        $total_members_display = $total_members > 1000 ? round($total_members / 1000, 1) . 'K' : number_format($total_members);
        ?>
        <div class="hero-stats-premium reveal-pro" style="margin-top: 80px;">
            <div class="stat-item" style="padding: 0 40px; border-right: 1px solid rgba(255,255,255,0.1);">
                <div style="font-family: 'Sora', sans-serif; font-size: 2.2rem; font-weight: 800; color: var(--gold);"><?php echo $total_members_display; ?></div>
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;"><?php echo __('stats_members'); ?></div>
            </div>
            <div class="stat-item" style="padding: 0 40px; border-right: 1px solid rgba(255,255,255,0.1);">
                <div style="font-family: 'Sora', sans-serif; font-size: 2.2rem; font-weight: 800; color: var(--gold);"><?php echo $total_courses; ?>+</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;"><?php echo __('stats_courses'); ?></div>
            </div>
            <div class="stat-item" style="padding: 0 40px;">
                <div style="font-family: 'Sora', sans-serif; font-size: 2.2rem; font-weight: 800; color: var(--gold);"><?php echo $avg_rating; ?> ★</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;"><?php echo __('stats_rating'); ?></div>
            </div>
        </div>
    </section>

    <!-- VIDEO -->
    <div class="video-section" style="padding: 0 5% 100px;">
        <div class="video-container" style="border-radius: 30px; overflow: hidden; box-shadow: 0 50px 100px rgba(0,0,0,0.5); border: 1px solid var(--navy-border2);">
            <iframe width="100%" height="600" src="https://www.youtube.com/embed/PorE9ETx9Ek" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <!-- PORTFOLIO -->
    <section id="portfolio" style="padding: 120px 0; background: radial-gradient(circle at 50% 0%, rgba(168, 133, 46, 0.05), transparent 70%);">
        <div style="text-align: center; margin-bottom: 70px; padding: 0 5%;">
            <span style="color: var(--gold); font-weight: 800; letter-spacing: 3px; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 15px; display: block;">Our Creations</span>
            <h2 style="font-size: 3.5rem; font-family: 'Sora', sans-serif; font-weight: 800; color: #fff; margin-bottom: 20px;"><?php echo __('nav_portfolio'); ?></h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1.1rem; line-height: 1.6;">สัมผัสผลงานระดับ Masterpiece ที่คัดสรรมาเพื่อการันตีความเป็นมืออาชีพในทุกมิติ</p>
        </div>

        <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 60px;">
            <button class="tab-btn active" onclick="switchTab(this,'video')" style="padding: 14px 35px; border-radius: 40px; border: 1px solid var(--navy-border2); background: rgba(255,255,255,0.03); color: #fff; cursor: pointer; font-weight: 700; font-family: 'Sora', sans-serif; transition: 0.3s;">VIDEOS</button>
            <button class="tab-btn" onclick="switchTab(this,'photo')" style="padding: 14px 35px; border-radius: 40px; border: 1px solid var(--navy-border2); background: rgba(255,255,255,0.03); color: #fff; cursor: pointer; font-weight: 700; font-family: 'Sora', sans-serif; transition: 0.3s;">PHOTOS</button>
        </div>

        <div id="portfolio-photo-content" class="testimonials-marquee-container" style="display: none;">
            <div class="portfolio-marquee" style="display: flex; gap: 30px; width: max-content; animation: scroll-marquee 40s linear infinite;">
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
                <!-- Duplicate for seamless scroll -->
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border);"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;" /></div>
            </div>
        </div>

        <div id="portfolio-video-content" class="testimonials-marquee-container">
            <div class="portfolio-marquee" style="display: flex; gap: 30px; width: max-content; animation: scroll-marquee 35s linear infinite;">
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
                <!-- Duplicate -->
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
                <div class="portfolio-card" style="width: 400px; height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid var(--navy-border); position: relative;"><img src="https://images.unsplash.com/photo-1561489396-888724a1543d?w=800&q=80" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);" /><span class="video-play-icon">▶</span></div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section id="testimonials" style="padding: 120px 0; background: #0b1221; border-top: 1px solid var(--navy-border); border-bottom: 1px solid var(--navy-border);">
        <div style="text-align: center; margin-bottom: 60px; padding: 0 5%;">
            <span style="color: var(--gold); font-weight: 800; letter-spacing: 3px; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 15px; display: block;">Reviews</span>
            <h2 style="font-size: 3rem; font-family: 'Sora', sans-serif; font-weight: 800; color: #fff; margin-bottom: 20px;"><?php echo __('testimonials_title') ?? 'ความประทับใจจากลูกค้า'; ?></h2>
        </div>
        
        <div class="testimonials-marquee-container">
            <div class="testimonials-track" style="display: flex; gap: 30px; padding: 20px 0;">
                <!-- Testimonial 1 -->
                <div class="testimonial-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; width: 450px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="color: var(--gold); margin-bottom: 20px; font-size: 1.2rem;">★★★★★</div>
                    <p style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px; font-family: 'Prompt', sans-serif;">"คอร์สเรียนนี้ช่วยให้ผมประหยัดเวลาในการลองผิดลองถูกไปได้เยอะมาก เนื้อหากระชับ เข้าใจง่าย และที่สำคัญคือเทคนิคที่สอนมันใช้งานได้จริงในระดับมืออาชีพครับ"</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-rich) 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--navy-deep); font-size: 1.2rem;">K</div>
                        <div>
                            <div style="color: #fff; font-weight: 700; font-size: 1rem; font-family: 'Sora', sans-serif;">คุณกิตติศักดิ์</div>
                            <div style="color: var(--gold-soft); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Creative Director</div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="testimonial-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; width: 450px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="color: var(--gold); margin-bottom: 20px; font-size: 1.2rem;">★★★★★</div>
                    <p style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px; font-family: 'Prompt', sans-serif;">"ประทับใจระบบหลังบ้านและทีมสนับสนุนมากค่ะ เวลาติดปัญหาตรงไหนถามไปก็ได้คำตอบที่ชัดเจนตลอด คุ้มค่าแก่การลงทุนเพื่อพัฒนาตัวเองจริงๆ ค่ะ"</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-rich) 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--navy-deep); font-size: 1.2rem;">N</div>
                        <div>
                            <div style="color: #fff; font-weight: 700; font-size: 1rem; font-family: 'Sora', sans-serif;">คุณนภัสสร</div>
                            <div style="color: var(--gold-soft); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Content Strategist</div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="testimonial-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; width: 450px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="color: var(--gold); margin-bottom: 20px; font-size: 1.2rem;">★★★★★</div>
                    <p style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px; font-family: 'Prompt', sans-serif;">"ผมเรียนจบคอร์สนี้แล้วสามารถรับงานตัดต่อวิดีโอได้เลย คุ้มค่ามากครับ อาจารย์สอนเทคนิคที่หาเรียนที่ไหนไม่ได้ และคอมมูนิตี้ในกลุ่มก็น่ารักมากครับ"</p>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-rich) 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--navy-deep); font-size: 1.2rem;">P</div>
                        <div>
                            <div style="color: #fff; font-weight: 700; font-size: 1rem; font-family: 'Sora', sans-serif;">คุณพีระพัฒน์</div>
                            <div style="color: var(--gold-soft); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Professional YouTuber</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSES -->
    <section id="courses" style="padding: 100px 5%;">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 3.5rem; font-family: 'Sora', sans-serif; font-weight: 800; margin-bottom: 20px; color: #fff;"><?php echo __('packages_title'); ?></h2>
            <p style="font-size: 1.1rem; color: var(--text-muted); max-width: 700px; margin: 0 auto;"><?php echo __('packages_subtitle'); ?></p>
        </div>

        <!-- Category Filters -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-bottom: 60px;">
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
        
        <div class="course-grid" id="courseGrid">
            <?php 
            $count = 0;
            foreach ($courses as $course): 
                $img_val = $course['image'];
                $img_src = '';
                if (strpos($img_val, '<img') !== false) {
                    if (preg_match('/src="([^"]+)"/', $img_val, $m)) $img_src = $m[1];
                } else {
                    $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . $img_val : 'IMG/' . $img_val;
                }
                $count++;
                $displayClass = $count > 3 ? 'extra-course' : '';
                $catData = htmlspecialchars($course['category']);
            ?>
                <div class="course-card <?php echo $displayClass; ?>" data-category="<?php echo $catData; ?>" onclick="goToDetail(<?php echo $course['id']; ?>)" style="<?php echo $count > 3 ? 'display: none;' : ''; ?>">
                    <div class="course-card-header">
                        <img src="<?php echo $img_src; ?>" alt="Course" class="course-card-img">
                        <div class="course-card-label">
                            <?php echo htmlspecialchars($course['category']); ?>
                        </div>
                        <div class="course-rating">
                            ⭐ <?php echo number_format($course['rating'], 1); ?>
                        </div>
                    </div>
                    <div class="course-card-body">
                        <h3 class="course-name"><?php echo htmlspecialchars($course['name']); ?></h3>
                        <div class="course-instructor">
                            <div class="instructor-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                        </div>
                        <div class="course-info">
                            <span><i class="far fa-clock"></i> <?php echo $course['hours']; ?> ชม.</span>
                            <span><i class="far fa-play-circle"></i> <?php echo $course['lessons']; ?> บทเรียน</span>
                        </div>
                        <p class="course-description">
                            <?php echo htmlspecialchars($course['short_desc']); ?>
                        </p>
                    </div>
                    <div class="course-card-footer">
                        <div class="course-price-container">
                            <span class="course-price-new">฿<?php echo number_format($course['price']); ?></span>
                            <?php if($course['old_price'] > $course['price']): ?>
                                <span class="course-price-old">฿<?php echo number_format($course['old_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="course-btn-icon" onclick="event.stopPropagation();addToCart(<?php echo $course['id']; ?>)" title="Add to Cart">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <button class="course-btn-action" onclick="goToDetail(<?php echo $course['id']; ?>)">
                                ดูคอร์ส
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <!-- See More Button -->
        <?php $showSeeMore = count($courses) > 3; ?>
        <div id="see-more-container" class="see-more-container" style="display: <?php echo $showSeeMore ? 'flex' : 'none'; ?>; justify-content: center; margin: 50px 0;">
            <div role="button" class="btn-see-more-final" onclick="showAllCourses()" style="
                background: linear-gradient(135deg, #d4af37 0%, #f1c40f 100%);
                color: #0b1221;
                padding: 14px 40px;
                border-radius: 40px;
                font-weight: 700;
                font-size: 1rem;
                font-family: 'Sora', sans-serif;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
                transition: all 0.3s var(--ease);
                border: 1px solid rgba(255,255,255,0.2);
            " onmouseover="this.style.transform='translateY(-4px) scale(1.02)'; this.style.boxShadow='0 15px 35px rgba(212, 175, 55, 0.4)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 10px 25px rgba(212, 175, 55, 0.3)';" onclick="showAllCourses()">
                <span><?php echo __('btn_see_more'); ?></span>
                <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
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
                <div><?php echo __('footer_desc'); ?></div>
            </div>
            <div class="col-center"><?php echo __('footer_phone'); ?> 094-546-2224</div>
            <div class="col-right"><?php echo __('footer_address'); ?></div>
        </div>
    </footer>
</body>

</html>
