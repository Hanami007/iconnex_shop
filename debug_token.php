<?php
require_once 'src/bootstrap.php';
echo "Session Token: " . ($_SESSION['csrf_token'] ?? 'NOT SET') . "\n";
echo "getCsrfToken(): " . getCsrfToken() . "\n";
echo "New Token: " . ($_SESSION['csrf_token'] ?? 'NOT SET') . "\n";
