<?php
// Database configuration - update these values to match your environment
$DB_HOST = '127.0.0.1';
$DB_NAME = 'cneduc';
$DB_USER = 'root';
$DB_PASS = '';

// Configure secure session cookie attributes (must be before session_start())
// Only set if session hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_set_cookie_params([
        'lifetime' => 3600,      // 1 hour
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,   // HTTPS only in production
        'httponly' => true,      // Not accessible from JavaScript
        'samesite' => 'Strict'   // CSRF protection
    ]);
}

// mysqli connection (object-oriented). This file intentionally uses mysqli and
// non-prepared queries per your request. For safety, IDs are cast to int before
// being interpolated into queries.
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

