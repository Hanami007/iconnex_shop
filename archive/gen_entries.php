<?php
$entryPoints = [
    'index.php' => 'pages/index.php',
    'login.php' => 'pages/auth/login.php',
    'register.php' => 'pages/auth/register.php',
    'logout.php' => 'pages/auth/logout.php',
    'my_orders.php' => 'pages/dashboard/my_orders.php',
    'dic_product.php' => 'pages/courses/detail.php',
    'payment.php' => 'pages/courses/payment.php',
    
    // API entries (keeping them in root for compatibility with JS fetch calls)
    'api_noti_user.php' => 'services/notification/notificationService.php',
    'api_checkout.php' => 'services/api/api_checkout.php',
    'cart_handler.php' => 'services/api/cart_handler.php',
    'register_action.php' => 'services/api/register_action.php',
    'review_handler.php' => 'services/api/review_handler.php',
];

foreach ($entryPoints as $rootFile => $srcPath) {
    $content = "<?php\nrequire_once __DIR__ . '/src/bootstrap.php';\nrequire_once BASE_DIR . '/$srcPath';\n";
    file_put_contents($rootFile, $content);
    echo "Created entry: $rootFile -> $srcPath\n";
}
