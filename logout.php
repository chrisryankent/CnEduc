<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

logout_user();
header('Location: index.php');
exit;
