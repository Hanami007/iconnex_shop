<?php
global $translations, $current_lang;
// Session is handled by bootstrap.php

// Language detection
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'th';
    setcookie('lang', $_SESSION['lang'], time() + (86400 * 30), "/"); // 30 days
}

$current_lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'th';
if (!isset($_SESSION['lang'])) $_SESSION['lang'] = $current_lang;

$translations = [
    'th' => [
        'nav_home' => 'หน้าหลัก',
        'nav_about' => 'เกี่ยวกับ',
        'nav_services' => 'บริการ',
        'nav_news' => 'ข่าว',
        'nav_portfolio' => 'ผลงาน',
        'nav_contact' => 'ติดต่อเรา',
        'nav_courses' => 'คอร์ส',
        'nav_faq' => 'คำถามที่พบบ่อย',
        'hero_title' => 'เป็นมากกว่าที่ปรึกษา แต่คือ "คู่คิด" ในการสร้างอนาคตธุรกิจคุณ',
        'hero_subtitle' => 'ยกระดับองค์กรสู่ยุคดิจิทัลด้วยกลยุทธ์ Value-First Branding และระบบ AI Automation ที่ออกแบบมาเพื่อเจ้าของธุรกิจที่ต้องการผลลัพธ์จริงในระดับ Masterpiece',
        'hero_btn_explore' => 'สำรวจคอร์สเรียน',
        'hero_btn_about' => 'รู้จักเรา',
        'stats_members' => 'ลูกค้าที่ไว้วางใจ',
        'stats_courses' => 'โปรเจกต์เชิงกลยุทธ์',
        'stats_rating' => 'การันตีความพึงพอใจ',
        'packages_title' => 'แพ็กเกจเรียนรู้',
        'packages_subtitle' => 'เลือกโซลูชันที่ใช่สำหรับธุรกิจคุณ เพื่อการก้าวสู่ความเป็นผู้นำในอุตสาหกรรมด้วยนวัตกรรมและกลยุทธ์อัจฉริยะ',
        'filter_all' => 'คอร์สทั้งหมด',
        'course_by' => 'โดย',
        'course_lessons' => 'บท',
        'course_hours' => 'ชม.',
        'btn_view_course' => 'ดูคอร์ส',
        'cart_popup_title' => 'กรุณาเข้าสู่ระบบ',
        'cart_popup_desc' => 'คุณต้องเข้าสู่ระบบก่อนเพื่อทำรายการเพิ่มสินค้าลงตะกร้าหรือสั่งซื้อคอร์สเรียน',
        'btn_login' => 'เข้าสู่ระบบ',
        'btn_register' => 'สมัครสมาชิก',
        'footer_desc' => 'ICONNEX - พาร์ทเนอร์ที่คุณไว้วางใจในการทรานส์ฟอร์มธุรกิจและสร้างการเติบโตอย่างยั่งยืนในยุคดิจิทัล',
        'footer_phone' => 'โทร:',
        'footer_address' => 'ที่อยู่อาคาร: The Metropolis Samrong',
        'footer_rights' => 'สงวนลิขสิทธิ์',
        // Login Page
        'login_title' => 'เข้าสู่ระบบ',
        'login_subtitle' => 'ยินดีต้อนรับกลับมา! กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ',
        'label_username' => 'ชื่อผู้ใช้',
        'label_password' => 'รหัสผ่าน',
        'btn_submit_login' => 'เข้าสู่ระบบ',
        'link_no_account' => 'ยังไม่มีบัญชี? สมัครสมาชิก',
        // Register Page
        'register_title' => 'สมัครสมาชิก',
        'register_subtitle' => 'เริ่มต้นเส้นทาง Creator ของคุณได้แล้ววันนี้',
        'label_email' => 'อีเมล',
        'label_confirm_password' => 'ยืนยันรหัสผ่าน',
        'btn_submit_register' => 'สมัครสมาชิก',
        'link_has_account' => 'มีบัญชีอยู่แล้ว? เข้าสู่ระบบ',
        // Cart / Checkout
        'cart_title' => 'รถเข็นของคุณ',
        'checkout_title' => 'สรุปรายการสั่งซื้อ',
        'btn_checkout' => 'ไปหน้าระบบชำระเงิน',
        'empty_cart' => 'รถเข็นของคุณยังไม่มีสินค้า',
        // Payment Page
        'pay_title' => 'ชำระเงินคอร์สเรียน',
        'order_no' => 'เลขที่ใบสั่งซื้อ',
        'receipt_summary' => 'สรุปรายการสั่งซื้อ',
        'vat' => 'ภาษีมูลค่าเพิ่ม (7%)',
        'total_pay' => 'ยอดรวมทั้งหมด',
        'customer_info' => 'ข้อมูลผู้ชำระเงิน & วิธีชำระ',
        'first_name' => 'ชื่อ',
        'last_name' => 'นามสกุล',
        'phone' => 'เบอร์โทรศัพท์',
        'select_pay_method' => 'เลือกวิธีชำระเงิน',
        'qr_promptpay' => 'QR PromptPay',
        'bank_transfer' => 'โอนบัญชีธนาคาร',
        'confirm_pay' => 'ยืนยันการชำระเงิน',
        'btn_copy' => 'คัดลอก',
        'btn_see_more' => 'ดูเพิ่มเติม',
        'nav_my_orders' => 'ประวัติการสั่งซื้อ',
        'title_my_orders' => 'ประวัติการสั่งซื้อของฉัน',
        'nav_logout' => 'ออกจากระบบ',
        'testimonials_title' => 'ความประทับใจจากลูกค้า',
        'nav_invoices' => 'ใบกำกับภาษี',
        'title_invoices' => 'ใบกำกับภาษีของฉัน'
    ],
    'en' => [
        'nav_home' => 'Home',
        'nav_about' => 'About',
        'nav_services' => 'Services',
        'nav_news' => 'News',
        'nav_portfolio' => 'Portfolio',
        'nav_contact' => 'Contact',
        'nav_courses' => 'Courses',
        'nav_faq' => 'FAQ',
        'hero_title' => 'Beyond Consulting. We are Your Partner in Business Transformation.',
        'hero_subtitle' => 'Revolutionize your organization with Value-First Branding and AI-driven systems tailored for leaders who demand world-class results and operational excellence.',
        'hero_btn_explore' => 'Get Started',
        'hero_btn_about' => 'About Us',
        'stats_members' => 'Clients Trusted',
        'stats_courses' => 'Strategic Projects',
        'stats_rating' => 'Guaranteed Satisfaction',
        'packages_title' => 'Learning Packages',
        'packages_subtitle' => 'Choose the right solution for your business. Lead your industry with innovation and smart strategies.',
        'filter_all' => 'All Courses',
        'course_by' => 'By',
        'course_lessons' => 'Lessons',
        'course_hours' => 'Hrs',
        'btn_view_course' => 'View Course',
        'cart_popup_title' => 'Please Login',
        'cart_popup_desc' => 'You must login first to add items to your cart or purchase courses.',
        'btn_login' => 'Login',
        'btn_register' => 'Register',
        'footer_desc' => 'ICONNEX - Your trusted partner in business transformation and sustainable growth in the digital era.',
        'footer_phone' => 'Phone:',
        'footer_address' => 'Address: The Metropolis Samrong',
        'footer_rights' => 'All rights reserved',
        // Login Page
        'login_title' => 'Login',
        'login_subtitle' => 'Welcome back! Please login to continue',
        'label_username' => 'Username',
        'label_password' => 'Password',
        'btn_submit_login' => 'Login',
        'link_no_account' => "Don't have an account? Register",
        // Register Page
        'register_title' => 'Register',
        'register_subtitle' => 'Start your Creator journey today',
        'label_email' => 'Email',
        'label_confirm_password' => 'Confirm Password',
        'btn_submit_register' => 'Register',
        'link_has_account' => 'Already have an account? Login',
        // Cart / Checkout
        'cart_title' => 'Your Cart',
        'checkout_title' => 'Order Summary',
        'btn_checkout' => 'Proceed to Checkout',
        'empty_cart' => 'Your cart is empty',
        // Payment Page
        'pay_title' => 'Secure Checkout',
        'order_no' => 'Order No.',
        'receipt_summary' => 'Order Summary',
        'vat' => 'VAT (7%)',
        'total_pay' => 'Grand Total',
        'customer_info' => 'Customer Info & Payment Method',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'phone' => 'Phone Number',
        'select_pay_method' => 'Select Payment Method',
        'qr_promptpay' => 'QR PromptPay',
        'bank_transfer' => 'Bank Transfer',
        'confirm_pay' => 'Confirm Payment',
        'btn_copy' => 'Copy',
        'btn_see_more' => 'See More',
        'nav_my_orders' => 'My Orders',
        'title_my_orders' => 'My Purchase History',
        'nav_invoices' => 'Tax Invoices',
        'title_invoices' => 'My Tax Invoices',
        'nav_logout' => 'Logout',
        'testimonials_title' => 'Client Testimonials'
    ]
];

function __($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}
?>
