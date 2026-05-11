<?php
$dirs = [
    'src/app',
    'src/components/common',
    'src/components/navbar',
    'src/components/notification',
    'src/components/modal',
    'src/components/cards',
    'src/components/forms',
    'src/pages/dashboard',
    'src/pages/courses',
    'src/pages/users',
    'src/pages/notifications',
    'src/pages/auth',
    'src/services/api',
    'src/services/notification',
    'src/services/auth',
    'src/services/users',
    'src/store/notification',
    'src/store/auth',
    'src/store/user',
    'src/hooks/notification',
    'src/hooks/auth',
    'src/hooks/shared',
    'src/context',
    'src/layouts',
    'src/utils',
    'src/constants',
    'src/styles',
    'src/types',
    'src/assets/img',
    'src/assets/js',
    'src/assets/fonts',
    'archive'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created: $dir\n";
    }
}
echo "Finished creating directories.";
