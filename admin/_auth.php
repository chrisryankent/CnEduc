<?php
// admin/_auth.php - ensure admin is logged in
// Configure session BEFORE starting it
require_once __DIR__ . '/../includes/db.php';
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    header('Location: login.php');
    exit;
}

