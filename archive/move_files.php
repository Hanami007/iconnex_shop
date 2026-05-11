<?php
$moves = [
    // Styles & Scripts
    'style.css' => 'src/styles/style.css',
    'script.js' => 'src/assets/js/script.js',
    'payment.js' => 'src/assets/js/payment.js',

    // Services
    'db.php' => 'src/services/db.php',
    'lang.php' => 'src/services/lang.php',
    'course_data.php' => 'src/services/course_data.php',
    'auth.php' => 'src/services/auth.php',
    'api_noti_user.php' => 'src/services/notification/notificationService.php',
    'api_checkout.php' => 'src/services/api/api_checkout.php',
    'cart_handler.php' => 'src/services/api/cart_handler.php',
    'register_action.php' => 'src/services/api/register_action.php',
    'review_handler.php' => 'src/services/api/review_handler.php',

    // Pages
    'index.php' => 'src/pages/index.php',
    'login.php' => 'src/pages/auth/login.php',
    'register.php' => 'src/pages/auth/register.php',
    'logout.php' => 'src/pages/auth/logout.php',
    'my_orders.php' => 'src/pages/dashboard/my_orders.php',
    'dic_product.php' => 'src/pages/courses/detail.php',
    'payment.php' => 'src/pages/courses/payment.php',

    // Archive
    'alter_db.php' => 'archive/alter_db.php',
    'migrate_courses.php' => 'archive/migrate_courses.php',
    'generate_sql.php' => 'archive/generate_sql.php',
    'generate_users_sql.php' => 'archive/generate_users_sql.php',
    'update_db_slip.php' => 'archive/update_db_slip.php',
    'test_cart.php' => 'archive/test_cart.php',
    'add_user_id_column.php' => 'archive/add_user_id_column.php',
    'debug_noti.php' => 'archive/debug_noti.php',
    'check_noti_cols.php' => 'archive/check_noti_cols.php',
    'migrate_noti_link.php' => 'archive/migrate_noti_link.php',
];

foreach ($moves as $old => $new) {
    if (file_exists($old)) {
        rename($old, $new);
        echo "Moved: $old -> $new\n";
    } else {
        echo "Skipped (not found): $old\n";
    }
}

// Move IMG directory contents
if (is_dir('IMG')) {
    $files = scandir('IMG');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            rename("IMG/$file", "src/assets/img/$file");
        }
    }
    rmdir('IMG');
    echo "Moved IMG/ contents to src/assets/img/ and removed IMG/\n";
}
