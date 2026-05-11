<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('BASE_DIR', __DIR__);
define('ROOT_DIR', dirname(BASE_DIR));
define('SERVICES_DIR', BASE_DIR . '/services');
define('UPLOADS_DIR', ROOT_DIR . '/uploads');
define('UTILS_DIR', BASE_DIR . '/utils');
define('PAGES_DIR', BASE_DIR . '/pages');
define('COMPONENTS_DIR', BASE_DIR . '/components');
define('ASSETS_DIR', '/src/assets'); // URL path
define('STYLES_DIR', '/src/styles'); // URL path

// Helper for absolute includes
function useService($name) {
    global $pdo, $courses, $translations, $current_lang, $avg_rating, $total_courses, $total_students;
    require_once SERVICES_DIR . "/$name.php";
}

function useApi($name) {
    global $pdo, $courses, $translations, $current_lang;
    require_once SERVICES_DIR . "/api/$name.php";
}

// Auto-inject components (optional)
function renderComponent($path, $data = []) {
    extract($data);
    include COMPONENTS_DIR . "/$path.php";
}
