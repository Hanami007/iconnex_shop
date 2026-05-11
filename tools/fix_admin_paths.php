<?php
$adminFiles = [
    'admin/api_courses.php',
    'admin/api_notifications.php',
    'admin/api_orders.php',
    'admin/api_users.php',
    'admin/index.php',
    'admin/login.php',
];

foreach ($adminFiles as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace old db.php paths
    $content = str_replace("require_once dirname(__DIR__) . '/db.php'", "require_once dirname(__DIR__) . '/src/services/db.php'", $content);
    $content = str_replace("require_once '../db.php'", "require_once '../src/services/db.php'", $content);
    $content = str_replace("include '../db.php'", "include '../src/services/db.php'", $content);
    
    // Update auth.php and lang.php
    $content = str_replace("require_once dirname(__DIR__) . '/auth.php'", "require_once dirname(__DIR__) . '/src/services/auth.php'", $content);
    $content = str_replace("require_once '../auth.php'", "require_once '../src/services/auth.php'", $content);
    
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}
