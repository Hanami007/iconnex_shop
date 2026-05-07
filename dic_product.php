<?php
require_once 'course_data.php';
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
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ICONNEX – <?php echo $course['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;800&family=Sarabun:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script defer src="script.js"></script>
    <script defer src="course_data.js"></script>
</head>

<body>

    <nav>
        <div class="nav-logo">
            <div class="logo-icon">🌀</div>
            ICONNEX
        </div>
        <ul class="nav-links">
            <li><a href="index.php">หน้าหลัก</a></li>
            <li><a href="about">เกี่ยวกับ</a></li>
            <li><a href="services">บริการ</a></li>
            <li><a href="news">ข่าว</a></li>
            <li><a href="portfolio">ผลงาน</a></li>
            <li><a href="contact">ติดต่อเรา</a></li>
            <li><a href="index.php#courses" class="active">คอร์ส</a></li>
        </ul>
    </nav>

    <!-- COURSE DETAIL SECTION -->
    <section id="course-detail" class="course-detail-section"
        style="background: linear-gradient(135deg, #022f58 0%, #0f015f 100%); padding: 60px 20px;">
        
        <!-- Breadcrumb -->
        <div style="max-width: 1200px; margin: 0 auto 20px; font-size: 14px; color: rgba(255,255,255,0.6); display: flex; align-items: center; gap: 10px;">
            <a href="index.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.8)'">หน้าหลัก</a>
            <span>›</span>
            <a href="index.php#courses" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.8)'">คอร์สเรียน</a>
            <span>›</span>
            <span style="color: white; font-weight: 600;"><?php echo htmlspecialchars($course['name']); ?></span>
        </div>

        <div class="course-detail-container"
            style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">

            <!-- Left: Course Image & Info -->
            <div class="course-detail-left">
                <div
                    style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <div
                        style="background: #f5f5f5; border-radius: 8px; margin-bottom: 20px; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php 
                        $img_val = $course['image'];
                        if (strpos($img_val, '<img') !== false): 
                            // Try to strip styling and apply our own, or just echo it
                            echo str_replace('<img', '<img style="width: 100%; height: 100%; object-fit: cover;"', $img_val);
                        elseif (strpos($img_val, '.') !== false): 
                            $img_src = strpos($img_val, 'uploads/') === 0 ? '/' . htmlspecialchars($img_val) : 'IMG/' . htmlspecialchars($img_val);
                        ?>
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($course['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                        <?php else: ?>
                            <div style="font-size: 120px;">
                                <?php echo htmlspecialchars($img_val ?: '📚'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; gap: 8px; margin-bottom: 15px;">
                        <span
                            style="background: #4CAF50; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">ONLINE
                            COURSE</span>
                    </div>
                    <h2 id="course-name" style="font-size: 24px; font-weight: 700; margin-bottom: 15px;"><?php echo htmlspecialchars($course['name']); ?></h2>
                    <p style="color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 20px;"><?php echo htmlspecialchars($course['short_desc']); ?></p>

                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="color: #999; font-size: 13px;">ราคาเดิม</span>
                            <span style="text-decoration: line-through; color: #999;">฿ <?php echo number_format($course['old_price']); ?>.-</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; color: #333;">ราคาพิเศษ</span>
                            <span style="font-size: 28px; font-weight: 700; color: #E53935;">฿ <?php echo number_format($course['price']); ?>.-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Course Details & CTA -->
            <div class="course-detail-right">
                <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 20px;"><?php echo htmlspecialchars($course['name']); ?></h1>
                <p style="color: rgba(255,255,255,0.9); font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                    <?php echo htmlspecialchars($course['short_desc']); ?></p>

                <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="font-weight: 600; margin-bottom: 15px; color: #333;">📦 Price Details</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div style="text-align: center;">
                            <div style="font-size: 12px; color: #999; margin-bottom: 5px;">ราคาปกติ</div>
                            <div style="font-size: 14px; font-weight: 600; color: #999; text-decoration: line-through;">
                                ฿ <?php echo number_format($course['old_price']); ?>.-</div>
                        </div>
                        <div style="text-align: center; border-left: 1px solid #eee;">
                            <div style="font-size: 12px; color: #E53935; margin-bottom: 5px; font-weight: 600;">ลดราคา
                            </div>
                            <div style="font-size: 20px; font-weight: 700; color: #E53935;">฿ <?php echo number_format($course['price']); ?>.-</div>
                        </div>
                    </div>
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
                <button
                    onclick="buyNow(currentCourseData)"
                    style="width: 100%; background: linear-gradient(135deg, #ffae35 0%, #f74d2f 100%); color: white; border: none; padding: 16px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-bottom: 20px; transition: all 0.2s; transform: scale(1);"
                    onmouseover="this.style.transform='scale(1.02)'"
                    onmouseout="this.style.transform='scale(1)'">
                    สั่งซื้อเลย
                </button>

                <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 20px; color: white;">
                    <h4 style="margin-bottom: 12px; font-weight: 600;">📊 สำหรับองค์กร</h4>
                    <p style="font-size: 13px; line-height: 1.6; margin-bottom: 12px;">ซื้อคอร์สนี้ให้ทีมขององค์กร
                        ลดราคา อัปเดตสมาชิก</p>
                    <button
                        style="width: 100%; background: white; color: #FF6B35; border: none; padding: 10px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">ยอใบเสนอราคา</button>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSE CONTENT SECTION -->
    <section style="padding: 60px 20px; background: #f9f9f9;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">

                <!-- Left: Content -->
                <div>
                    <!-- Tabs - Simplified -->
                    <div style="display: flex; gap: 0; margin-bottom: 30px; border-bottom: 2px solid #e0e0e0;">
                        <button
                            style="padding: 15px 20px; background: none; border: none; border-bottom: 3px solid #FF9800; color: #FF9800; font-weight: 600; cursor: pointer;">รายละเอียดคอร์ส</button>
                        <button
                            style="padding: 15px 20px; background: none; border: none; border-bottom: 2px solid transparent; color: #999; font-weight: 600; cursor: pointer;">โปรแกรมเรียนรู้</button>
                        <button
                            style="padding: 15px 20px; background: none; border: none; border-bottom: 2px solid transparent; color: #999; font-weight: 600; cursor: pointer;">รีวิว</button>
                    </div>

                    <!-- Description Section -->
                    <h3 style="font-size: 20px; font-weight: 700; margin-top: 40px; margin-bottom: 20px; color: #333;">
                        รายละเอียด</h3>
                    <div style="background: white; border-radius: 8px; padding: 20px; line-height: 1.8; color: #555;">
                        <p style="margin-bottom: 15px;"><strong>ชื่อคอร์ส:</strong> <?php echo htmlspecialchars($course['name']); ?></p>
                        <p style="margin-bottom: 15px;"><strong>ผู้สอน:</strong> <?php echo htmlspecialchars($course['instructor']); ?></p>
                        <p style="margin-bottom: 15px;"><strong>ประเภท:</strong> <?php echo htmlspecialchars($course['category']); ?></p>
                        <p style="margin-bottom: 15px;"><strong>บทเรียน:</strong> <?php echo $course['lessons']; ?> บท | <strong>ระยะเวลา:</strong> <?php echo $course['hours']; ?> ชั่วโมง</p>
                        <p style="margin-bottom: 15px;"><strong>คะแนน:</strong> ⭐ <?php echo $course['rating']; ?> (<?php echo $course['reviews']; ?> รีวิว)</p>
                        <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                        <p style="margin-bottom: 15px;"><?php echo htmlspecialchars($course['long_desc']); ?></p>
                    </div>

                    <!-- Related Courses in same category -->
                    <?php if (count($relatedCourses) > 0): ?>
                    <h3 style="font-size: 18px; font-weight: 700; margin-top: 40px; margin-bottom: 20px; color: #333;">
                        คอร์สอื่นในประเภท <?php echo htmlspecialchars($course['category']); ?></h3>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($relatedCourses as $related): ?>
                        <a href="dic_product.php?courseId=<?php echo $related['id']; ?>"
                           style="background: white; border-radius: 8px; overflow: hidden; display: grid; grid-template-columns: 120px 1fr; gap: 15px; padding: 12px; cursor: pointer; text-decoration: none; transition: all 0.3s; border: 2px solid #f5f5f5;"
                           onmouseover="this.style.borderColor='#FF9800'; this.style.boxShadow='0 4px 12px rgba(255, 152, 0, 0.15)';"
                           onmouseout="this.style.borderColor='#f5f5f5'; this.style.boxShadow='none';">
                            <div style="background: #f5f5f5; border-radius: 6px; aspect-ratio: 1;">
                                <img src="IMG/chatediter.png" alt="Course"
                                    style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px;"><?php echo htmlspecialchars($related['name']); ?></h4>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px; line-height: 1.4;">
                                    <?php echo htmlspecialchars(substr($related['short_desc'], 0, 60)) . '...'; ?></p>
                                <div style="display: flex; align-items: center; gap: 10px; font-size: 11px; color: #999;">
                                    <span>⏱️ <?php echo $related['hours']; ?> ชั่วโมง</span>
                                    <span>⭐ <?php echo $related['rating']; ?></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <h3 style="font-size: 20px; font-weight: 700; margin-top: 40px; margin-bottom: 20px; color: #333;">
                        เนื้อหาในคอร์ส</h3>
                    <div style="background: white; border-radius: 8px; padding: 20px; line-height: 1.8; color: #555;">
                        <?php 
                        $content_json = $course['content_json'];
                        $sections = [];
                        if (!empty($content_json)) {
                            $sections = json_decode($content_json, true);
                        }
                        
                        if (!empty($sections) && is_array($sections)):
                            foreach($sections as $index => $sec):
                        ?>
                        <ul style="list-style: none; padding: 0; margin: 0; margin-bottom: 20px;">
                            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: #333; display: flex; align-items: center; gap: 8px;">
                                <span style="background: #f0f0f0; padding: 4px 10px; border-radius: 20px; font-size: 12px; color: #666;"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <?php echo htmlspecialchars($sec['section']); ?>
                            </h4>
                            <div style="margin-left: 45px; font-size: 14px; color: #777;">
                                <?php 
                                if (!empty($sec['lessons']) && is_array($sec['lessons'])):
                                    foreach($sec['lessons'] as $lesson):
                                ?>
                                <p style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 8px;">
                                    <span style="color: #FF9800; font-size: 12px; margin-top: 2px;">▶</span> 
                                    <?php echo htmlspecialchars($lesson); ?>
                                </p>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                <p style="margin-bottom: 10px; color: #aaa;">(ยังไม่มีรายละเอียด)</p>
                                <?php endif; ?>
                            </div>
                        </ul>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <p style="text-align: center; color: #999; padding: 20px;">ยังไม่มีการระบุเนื้อหาคอร์สเรียน</p>
                        <?php endif; ?>
                    </div>
                    <!-- REVIEWS SECTION -->
                    <div id="reviews-section" style="margin-top: 50px;">
                        <!-- Title and Rating -->
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 4px; height: 32px; background: #FFB400; border-radius: 2px;"></div>
                            <h2 style="font-size: 24px; font-weight: 700; color: #333; margin: 0;">รีวิวจากผู้เรียน</h2>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                            <div style="color: #FFB400; font-size: 24px;">★★★★<span style="position: relative; display: inline-block; width: 12px; overflow: hidden;">★<span style="position: absolute; left: 0; width: 50%; color: #FFB400; overflow: hidden;">★</span></span></div>
                            <div style="font-size: 32px; font-weight: 700; color: #FFB400;">4.5</div>
                            <div style="font-size: 14px; color: #999;">(138 รีวิว)</div>
                        </div>
                        <!-- Summary Box -->
                        <div style="border: 1px solid #eee; border-radius: 12px; padding: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); background: white;">
                            
                            <!-- Left Summary -->
                            <div style="text-align: center; border-right: 1px solid #eee; padding-right: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div style="font-size: 15px; color: #555; margin-bottom: 15px; font-weight: 600;">ความคาดหวังของผู้เรียน</div>
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 15px;">
                                    <div style="font-size: 48px; font-weight: 700; color: #333; line-height: 1;">96%</div>
                                    <div style="text-align: left; font-size: 13px; color: #666; line-height: 1.4;">ของรีวิว<br>บอกว่าคอร์สนี้</div>
                                </div>
                                <div style="border: 1px solid #00C853; color: #00C853; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                    ตรงตามความคาดหวัง <span>😊</span>
                                </div>
                            </div>
                            <!-- Right Summary -->
                            <div style="padding-left: 10px;">
                                <div style="font-size: 15px; color: #555; margin-bottom: 20px; font-weight: 600;">สิ่งที่ผู้เรียนชอบมากที่สุด</div>
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    <div style="background: #f5f5f5; border-radius: 6px; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555;"><span>👍</span> เนื้อหา</div>
                                        <div style="font-size: 13px; font-weight: 600; color: #333;">73%</div>
                                    </div>
                                    <div style="background: #f5f5f5; border-radius: 6px; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555;"><span>👍</span> ผู้สอน</div>
                                        <div style="font-size: 13px; font-weight: 600; color: #333;">64%</div>
                                    </div>
                                    <div style="background: #f5f5f5; border-radius: 6px; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555;"><span>👍</span> การจัดเรียงเนื้อหา</div>
                                        <div style="font-size: 13px; font-weight: 600; color: #333;">59%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Review List Header -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="font-size: 18px; font-weight: 700; color: #333;">ความคิดเห็น (2)</h3>
                            <div style="font-size: 14px; color: #555; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                เรียงตามความคิดเห็นแนะนำ <span style="font-size: 10px;">▼</span>
                            </div>
                        </div>
                        <!-- Review Cards -->
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <!-- Card 1 -->
                            <div style="background: #F8F9FA; border-radius: 12px; padding: 24px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div style="display: flex; gap: 15px;">
                                        <!-- Avatar -->
                                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #999; background: white;">
                                            😐
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #333; font-size: 16px; margin-bottom: 4px;">จิ๊บ นลินี</div>
                                            <div style="font-size: 12px; color: #999;">28 กุมภาพันธ์ 2569 เวลา 07:32</div>
                                        </div>
                                    </div>
                                    <div style="color: #FFB400; font-size: 14px;">★★★★★</div>
                                </div>
                                <div style="height: 1px; background: #E0E0E0; margin: 15px 0;"></div>
                                <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0;">มีเทคนิคใหม่ที่ไม่เคยรู้คือ Stac และอื่นๆอีกหลายอย่างเลย ดีมากค่ะ</p>
                            </div>
                            <!-- Card 2 -->
                            <div style="background: #F8F9FA; border-radius: 12px; padding: 24px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div style="display: flex; gap: 15px;">
                                        <!-- Avatar -->
                                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #999; background: white;">
                                            🙂
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #333; font-size: 16px; margin-bottom: 4px;">Nattida Tavarojn</div>
                                            <div style="font-size: 12px; color: #999;">17 กันยายน 2568 เวลา 21:08</div>
                                        </div>
                                    </div>
                                    <div style="color: #FFB400; font-size: 14px;">★★★<span style="color: #ccc;">★★</span></div>
                                </div>
                                <div style="height: 1px; background: #E0E0E0; margin: 15px 0;"></div>
                                <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0;">อยากให้โชว์resultที่อยากได้ไว้ด้านข้าง เวลาเขียนcodeจะได้รู้ว่าอยากได้แบบไหน และสามารถมองเห็นตลอด ไม่ต้องกลับไปกลับมา</p>
                            </div>
                        </div>
                    </div>
                </div>

                </div>

                <!-- Right: Sidebar -->
                <div>
                    <div
                        style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <h4 style="font-weight: 600; margin-bottom: 15px; color: #333;">📊 สำหรับองค์กร</h4>
                        <p style="font-size: 13px; color: #666; margin-bottom: 12px; line-height: 1.6;">
                            ซื้อคอร์สนี้ให้ทีมขององค์กร ลดราคา อัปเดตสมาชิก</p>
                        <p style="font-size: 12px; font-weight: 600; color: #FF9800; margin-bottom: 15px;">ขึ้นต่อสินค้า
                            50%</p>
                        <button
                            style="width: 100%; background: #4A68BD; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">ขอใบเสนอราคา</button>
                        <button
                            style="width: 100%; background: white; color: #4A68BD; border: 2px solid #4A68BD; padding: 10px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">สนใจ
                            In-House Training</button>
                    </div>

                    <div style="background: #f5f5f5; border-radius: 8px; padding: 20px;">
                        <h4 style="font-weight: 600; margin-bottom: 15px; color: #333;">📊 ข้อมูลคอร์ส</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 8px 0; font-size: 13px; color: #555;">📚 <strong>บทเรียน:</strong> <?php echo $course['lessons']; ?> บท</li>
                            <li style="padding: 8px 0; font-size: 13px; color: #555;">⏱️ <strong>ระยะเวลา:</strong> <?php echo $course['hours']; ?> ชั่วโมง</li>
                            <li style="padding: 8px 0; font-size: 13px; color: #555;">⭐ <strong>คะแนน:</strong> <?php echo $course['rating']; ?> / 5.0</li>
                            <li style="padding: 8px 0; font-size: 13px; color: #555;">💬 <strong>รีวิว:</strong> <?php echo $course['reviews']; ?> รีวิว</li>
                            <li style="padding: 8px 0; font-size: 13px; color: #555;">👨‍🏫 <strong>ผู้สอน:</strong> <?php echo htmlspecialchars($course['instructor']); ?></li>
                        </ul>
                    </div>
                </div>
                <!-- review section -->
                <div style="margin-top: 40px;"></div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #333;">รีวิวจากผู้เรียน</h3>
                    <div style="background: white; border-radius: 8px; padding: 20px; line-height: 1.8; color: #555;">
                        <p style="margin-bottom: 15px;"><strong>นางสาวสมหญิง:</strong> คอร์สนี้ช่วยให้ฉันเข้าใจการวิเคราะห์ข้อมูลบุคลากรและนำไปใช้ในงานได้จริง</p>
                        <p style="margin-bottom: 15px;"><strong>นายสมชาย:</strong> เนื้อหาครอบคลุมและอธิบายง่ายมาก แนะนำสำหรับคนที่อยากเริ่มต้นกับ People Analytics</p>
                        <p style="margin-bottom: 15px;"><strong>นางสาวสวยงาม:</strong> ผู้สอนมีความรู้ลึกซึ้งและสามารถตอบคำถามได้ดีมาก คอร์สนี้คุ้มค่ามากๆ</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-socials">
            <a href="#" class="social-btn">📘</a>
            <a href="#" class="social-btn">🐦</a>
            <a href="#" class="social-btn">📷</a>
            <a href="#" class="social-btn">▶️</a>
            <span class="social-right">© 2025 ICONNEX. All rights reserved.</span>
        </div>
    </footer>

</body>

</html>