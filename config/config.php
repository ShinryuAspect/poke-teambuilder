<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'poke_teambuilder');
}

if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', 'API KEY');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/poke-teambuilder/');
}
