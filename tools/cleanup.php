<?php
$cleanup = [
    'create_dirs.php' => 'archive/create_dirs.php',
    'fix_paths.php' => 'archive/fix_paths.php',
    'gen_entries.php' => 'archive/gen_entries.php',
    'move_files.php' => 'archive/move_files.php',
    'cart.php' => 'archive/cart_legacy.php',
    'courses.json' => 'database/courses.json',
    'courses.sql' => 'database/courses.sql',
    'courses_utf8.json' => 'database/courses_utf8.json',
    'orders.sql' => 'database/orders.sql',
    'users.sql' => 'database/users.sql',
    'index.php_new_courses_section.html' => 'archive/index_new_courses_section.html',
];

foreach ($cleanup as $old => $new) {
    if (file_exists($old)) {
        if (!is_dir(dirname($new))) mkdir(dirname($new), 0777, true);
        rename($old, $new);
        echo "Cleaned: $old -> $new\n";
    }
}
echo "Final cleanup finished.";
