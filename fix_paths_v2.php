<?php
$files = [
    'src/assets/js/script.js',
    'src/assets/js/notification/notificationService.js',
    'src/pages/auth/login.php',
    'src/pages/auth/register.php',
    'pay2.1/script.js',
    'admin/login.php',
    'src/pages/courses/detail.php'
];

$replaces = [
    'cart_handler.php'    => 'api/cart.php',
    'auth.php'            => 'api/auth.php',
    'api_checkout.php'    => 'api/checkout.php',
    'api_noti_user.php'   => 'api/notifications.php',
    'register_action.php' => 'api/register.php',
    'review_handler.php'  => 'api/reviews.php',
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        foreach ($replaces as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        // Cleanup double api/api/
        foreach ($replaces as $old => $new) {
            $content = str_replace('api/' . $new, $new, $content);
            $content = str_replace('/' . $new, '/' . $new, $content); // No-op but safety
        }
        file_put_contents($f, $content);
    }
}
echo "Fixed all paths.";
