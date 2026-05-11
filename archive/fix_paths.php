<?php
$files = [
    'src/pages/index.php',
    'src/pages/auth/login.php',
    'src/pages/auth/register.php',
    'src/pages/dashboard/my_orders.php',
    'src/pages/courses/detail.php',
    'src/pages/courses/payment.php',
    'src/services/notification/notificationService.php',
    'src/services/api/api_checkout.php',
    'src/services/api/cart_handler.php',
    'src/services/api/register_action.php',
    'src/services/api/review_handler.php',
    'src/services/auth.php',
    'src/services/lang.php',
];

$replacements = [
    "require_once 'db.php';" => "useService('db');",
    "require_once 'lang.php';" => "useService('lang');",
    "require_once 'course_data.php';" => "useService('course_data');",
    "require_once 'auth.php';" => "useService('auth');",
    "require_once 'cart_handler.php';" => "useApi('cart_handler');",
    
    // Fix asset paths in HTML
    "src=\"script.js" => "src=\"src/assets/js/script.js",
    "href=\"style.css" => "href=\"src/styles/style.css",
    "src=\"IMG/" => "src=\"src/assets/img/",
    "src=\"uploads/" => "src=\"uploads/", // Uploads usually stay in root or move
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    foreach ($replacements as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    
    // Also handle dynamic image paths in PHP logic
    $content = str_replace("'IMG/'", "ASSETS_DIR . '/img/'", $content);
    $content = str_replace("\"IMG/\"", "ASSETS_DIR . '/img/'", $content);

    file_put_contents($file, $content);
    echo "Processed: $file\n";
}
