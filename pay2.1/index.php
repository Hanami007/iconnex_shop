<?php
require_once '../lang.php';
useService('db');
global $pdo;

// Auth Guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=pay2.1');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduCourse — <?php echo __('pay_title'); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="styles.css">
<script>const csrfToken = "<?php echo getCsrfToken(); ?>";</script>
</head>
<body>
<div class="page-wrapper">

  <!-- HEADER -->
  <header class="site-header">
    <div class="logo-mark">
      <div class="logo-icon">🌀</div>
      <div class="logo-text">ICON<span>NEX</span></div>
    </div>
    <div class="header-meta">
      <div class="label"><?php echo __('order_no'); ?></div>
      <div class="order-no" id="hdr-order">ORD-———</div>
      <div class="secure-badge">
        <i class="ti ti-shield-check"></i> SSL Secured Checkout
      </div>
    </div>
  </header>

  <!-- MAIN GRID -->
  <div class="checkout-grid">

    <div class="left-col">
 
      <!-- Course Hero Card (Single Frame with Carousel) -->
      <div class="course-hero">
        <div class="course-banner" id="hero-banner" style="height: 240px; position: relative; background: var(--navy-dark); overflow: hidden;">
          <!-- Cover Image -->
          <img id="hero-cover-img" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
          <div id="hero-placeholder-icon" style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; font-size: 4rem; color: rgba(255,255,255,0.1);">
            <i class="ti ti-school"></i>
          </div>
          
          <div class="banner-pattern" style="z-index: 1;"></div>
          
          <!-- Navigation Arrows -->
          <button class="hero-nav-btn prev" id="hero-prev" onclick="prevHeroCourse()" style="z-index: 2;">
            <i class="ti ti-chevron-left"></i>
          </button>
          <button class="hero-nav-btn next" id="hero-next" onclick="nextHeroCourse()" style="z-index: 2;">
            <i class="ti ti-chevron-right"></i>
          </button>
 
          <!-- Text Overlay with Gradient for Readability -->
          <div class="banner-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(11, 18, 33, 0.9) 0%, rgba(11, 18, 33, 0.4) 40%, transparent 100%); z-index: 1;"></div>
 
          <div class="banner-content" style="z-index: 2; bottom: 20px; left: 25px; text-align: left; position: absolute; width: calc(100% - 50px);">
            <div class="banner-title" id="hero-banner-title" style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 5px; line-height: 1.2;">Course Name</div>
            <div class="banner-sub" id="hero-banner-sub" style="font-size: 14px; font-weight: 500; color: var(--gold-light); opacity: 0.9;">Category</div>
          </div>
 
          <!-- Course Counter Badge -->
          <div class="hero-counter" id="hero-counter" style="z-index: 2;">1 / 1</div>
        </div>
        <div class="course-info">
          <div class="course-badges">
            <span class="badge badge-pop"><i class="ti ti-flame"></i> คอร์สยอดนิยม</span>
            <span class="badge badge-online"><i class="ti ti-video"></i> เรียนออนไลน์</span>
            <span class="badge badge-instant"><i class="ti ti-bolt"></i> เริ่มได้ทันที</span>
          </div>
          <div class="course-name" id="hero-course-name">Course Name</div>
          <div class="course-desc" id="hero-course-desc">Description</div>
          <div class="features-grid" id="hero-features-grid">
            <!-- Features populated by JS -->
          </div>
        </div>
      </div>
      
      <div id="checkout-items-container" style="display:none;"></div>

      <!-- Add More Courses -->
      <div class="add-courses-card">
        <div class="section-title">เพิ่มคอร์สอื่นๆ ที่สนใจ</div>
        <div class="catalogue-list">
          <?php 
          useService('course_data');
          global $courses;
          foreach ($courses as $c): 
          ?>
          <div class="catalogue-wrapper">
            <button class="catalogue-toggle" id="toggle-cat-<?php echo $c['id']; ?>" data-course="cat-<?php echo $c['id']; ?>">
              <div class="cat-icon" style="background: var(--gold-pale);">
                <?php if(strpos($c['image'], 'http') === 0 || strpos($c['image'], 'uploads/') === 0): ?>
                  <img src="<?php echo strpos($c['image'], 'uploads/') === 0 ? '../'.$c['image'] : $c['image']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                <?php else: ?>
                  <span style="font-size: 20px;"><?php echo $c['image'] ?: '📚'; ?></span>
                <?php endif; ?>
              </div>
              <div class="cat-info">
                <div class="cat-name"><?php echo htmlspecialchars($c['name']); ?></div>
                <div class="cat-price"><?php echo number_format($c['price']); ?> บาท • <?php echo $c['hours']; ?> ชั่วโมง</div>
              </div>
              <div class="toggle-icon">
                <i class="ti ti-chevron-down"></i>
              </div>
            </button>
            <div class="catalogue-detail hidden" id="detail-cat-<?php echo $c['id']; ?>">
              <div class="detail-content">
                <div class="detail-section">
                  <div class="detail-label">เรทติ้ง</div>
                  <div class="detail-value">⭐⭐⭐⭐⭐ <?php echo $c['rating']; ?> (<?php echo $c['reviews']; ?> รีวิว)</div>
                </div>
                <div class="detail-section">
                  <div class="detail-label">เกี่ยวกับคอร์ส</div>
                  <div class="detail-value"><?php echo htmlspecialchars($c['short_desc']); ?></div>
                </div>
                <div class="detail-section">
                  <div class="detail-label">สอนโดย</div>
                  <div class="detail-value">🧑‍💼 <?php echo htmlspecialchars($c['instructor']); ?></div>
                </div>
              </div>
              <button class="cat-add-btn" 
                data-id="cat-<?php echo $c['id']; ?>" 
                data-price="<?php echo $c['price']; ?>" 
                data-name="<?php echo htmlspecialchars($c['name']); ?>"
                data-image="<?php echo htmlspecialchars($c['image']); ?>"
                data-category="<?php echo htmlspecialchars($c['category']); ?>"
                data-description="<?php echo htmlspecialchars($c['short_desc']); ?>"
                data-hours="<?php echo $c['hours']; ?>"
                data-lessons="<?php echo $c['lessons']; ?>">
                <i class="ti ti-plus"></i> เพิ่มไปยังตะกร้า
              </button>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>
    </div><!-- end left-col -->

    <!-- ===== RIGHT COLUMN ===== -->
    <div class="right-col">

      <!-- Receipt Card -->
      <div class="receipt-card">
        <div class="receipt-header">
          <div>
            <div class="receipt-label"><?php echo __('receipt_summary'); ?></div>
            <div class="receipt-title">Order Summary</div>
          </div>
          <div class="receipt-logo">
            สั่งซื้อจาก
            <strong>EduCourse</strong>
          </div>
        </div>
        <div class="receipt-meta">
          <div class="rmeta-item">
            <div class="rmeta-label">เลขที่</div>
            <div class="rmeta-val" id="meta-order">ORD-———</div>
          </div>
          <div class="rmeta-item" style="text-align:center">
            <div class="rmeta-label">วันที่</div>
            <div class="rmeta-val" id="meta-date">—</div>
          </div>
          <div class="rmeta-item" style="text-align:right">
            <div class="rmeta-label">หมดเขต</div>
            <div class="rmeta-val" id="meta-exp">—</div>
          </div>
        </div>
        <div class="receipt-body">
          <div class="receipt-items-label">รายการสินค้า</div>
          <div id="receipt-lines">
          </div>
          <div class="receipt-totals">
            <div class="rtotal-row">
              <span>ยอดรวม</span>
              <span id="tot-sub">5,000 ฿</span>
            </div>
            <div class="rtotal-row vat">
              <span><?php echo __('vat'); ?></span>
              <span id="tot-vat">350 ฿</span>
            </div>
            <div class="rtotal-grand">
              <span class="label"><i class="ti ti-receipt"></i> <?php echo __('total_pay'); ?></span>
              <span class="amount" id="tot-grand">5,350 ฿</span>
            </div>
          </div>
        </div>
      </div><!-- end receipt-card -->

      <!-- Payment Form Card -->
      <div class="payment-form-card">
        <div class="pform-header">
          <div class="pform-header-title">
            <i class="ti ti-credit-card"></i> <?php echo __('customer_info'); ?>
          </div>
        </div>
        <div class="pform-body">

          <!-- Customer Info -->
          <div class="input-row">
            <div class="input-group">
              <label class="input-label" for="inp-name"><?php echo __('first_name'); ?></label>
              <input class="input-field" type="text" id="inp-name" value="<?php echo htmlspecialchars($currentUser['username']); ?>" readonly style="background: rgba(255,255,255,0.02); color: var(--gold-soft); cursor: not-allowed;">
            </div>
            <div class="input-group" style="display:none;">
              <label class="input-label" for="inp-surname"><?php echo __('last_name'); ?></label>
              <input class="input-field" type="text" id="inp-surname" value="User" readonly>
            </div>
          </div>
          <div class="input-group">
            <label class="input-label" for="inp-email"><?php echo __('label_email'); ?></label>
            <input class="input-field" type="email" id="inp-email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" readonly style="background: rgba(255,255,255,0.02); color: var(--gold-soft); cursor: not-allowed;">
          </div>
          <div class="input-group">
            <label class="input-label" for="inp-phone"><?php echo __('phone'); ?></label>
            <input class="input-field" type="tel" id="inp-phone" placeholder="086-xxx-xxxx">
          </div>
          <div class="input-group">
            <label class="input-label" for="inp-line">LINE ID</label>
            <input class="input-field" type="text" id="inp-line" placeholder="LINE ID ของคุณ">
          </div>
          
          <div style="margin-top: 25px; padding-top: 20px; border-top: 1px dashed rgba(255,255,255,0.1);">
            <div class="pform-header-title" style="font-size: 0.9rem; margin-bottom: 15px;">
              <i class="ti ti-file-description"></i> ข้อมูลออกใบกำกับภาษี (Optional)
            </div>
            <div class="input-group">
              <label class="input-label" for="inp-tax-id">เลขประจำตัวผู้เสียภาษี</label>
              <input class="input-field" type="text" id="inp-tax-id" placeholder="เลข 13 หลัก">
            </div>
            <div class="input-group">
              <label class="input-label" for="inp-address">ที่อยู่สำหรับออกใบกำกับภาษี</label>
              <textarea class="input-field" id="inp-address" rows="3" placeholder="บ้านเลขที่, ถนน, แขวง/ตำบล, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์" style="resize: none; padding-top: 10px;"></textarea>
            </div>
          </div>

          <!-- Payment Method Tabs -->
          <div class="pay-method-section">
            <div class="pay-method-label"><?php echo __('select_pay_method'); ?></div>
            <div class="pay-tabs">
              <button class="pay-tab" id="tab-qr" data-method="qr">
                <span class="tab-icon">📱</span><?php echo __('qr_promptpay'); ?>
              </button>
              <button class="pay-tab" id="tab-bank" data-method="bank">
                <span class="tab-icon">🏦</span><?php echo __('bank_transfer'); ?>
              </button>
            </div>
          </div>

          <!-- QR Section -->
          <div id="qr-section" class="hidden">
            <div class="qr-area">
              <div class="qr-box" id="qr-visual"></div>
              <div class="qr-info">
                <div class="qr-amount" id="qr-amount-lbl">5,350 บาท</div>
                <div class="qr-hint">สแกนด้วยแอปธนาคารหรือ Mobile Banking</div>
              </div>
              <div class="qr-promptpay">
                <i class="ti ti-qrcode"></i> PromptPay: 0-8612-34567
              </div>
            </div>
          </div>

          <!-- Bank Transfer Section -->
          <div id="bank-section" class="hidden">
            <div class="bank-select-wrap">
              <select class="input-field" id="bank-select">
                <option value="">— เลือกธนาคาร —</option>
                <option value="scb">ธนาคารไทยพาณิชย์ (SCB)</option>
                <option value="ktb">ธนาคารกรุงไทย (KTB)</option>
                <option value="bbl">ธนาคารกรุงเทพ (BBL)</option>
              </select>
            </div>
            <div id="bank-info-box" class="bank-info-box hidden">
              <div class="binfo-row"><span>ธนาคาร</span><strong id="bi-bank">—</strong></div>
              <div class="binfo-row"><span>สาขา</span><strong id="bi-branch">—</strong></div>
              <div class="binfo-row"><span>ชื่อบัญชี</span><strong id="bi-name">—</strong></div>
              <div class="binfo-row">
                <span>เลขบัญชี</span>
                <span class="binfo-copy">
                  <strong id="bi-acc">—</strong>
                  <button class="copy-btn" id="copy-acc-btn">คัดลอก</button>
                </span>
              </div>
              <div class="binfo-row">
                <span>ยอดโอน</span>
                <strong id="bi-amount" style="color:var(--gold)">—</strong>
              </div>
            </div>
          </div>

          <!-- Slip Upload -->
          <div class="pay-method-section" style="margin-top:20px;">
            <div class="pay-method-label">แนบสลิปโอนเงิน (จำเป็น)</div>
            <div class="input-group">
              <input class="input-field" type="file" id="inp-slip" accept="image/*" style="padding:10px;">
            </div>
          </div>

          <!-- Confirm Button -->
          <button class="confirm-btn" id="confirm-btn">
            <i class="ti ti-lock"></i>
            <span><?php echo __('confirm_pay'); ?></span>
            <span class="btn-amount" id="btn-amount-lbl">5,350 ฿</span>
          </button>

          <!-- Security Strip -->
          <div class="security-strip">
            <span class="sec-item"><i class="ti ti-shield-lock"></i> SSL Encrypted</span>
            <span class="sec-item"><i class="ti ti-eye-off"></i> ข้อมูลปลอดภัย</span>
            <span class="sec-item"><i class="ti ti-refresh"></i> คืนเงินใน 7 วัน</span>
          </div>

        </div>
      </div><!-- end payment-form-card -->

    </div><!-- end right-col -->
  </div><!-- end checkout-grid -->

  <!-- FOOTER -->
  <footer class="site-footer">
    <p>© 2026 ICONNEX Creators Club ·
      <a href="#">นโยบายความเป็นส่วนตัว</a> ·
      <a href="#">เงื่อนไขการใช้งาน</a> ·
      support@educourse.com · 02-123-4567
    </p>
  </footer>

</div><!-- end page-wrapper -->
<script src="script.js"></script>
</body>
</html>