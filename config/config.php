<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'poke_teambuilder');

define('GEMINI_API_KEY', 'AIzaSyDYSeMjHf6y0MZdhALTp8MtpjJlewWAngU');
define('BASE_URL', 'http://localhost/poke-teambuilder/');
