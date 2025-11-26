<?php
require_once __DIR__ . '/db.php';

function get_levels() {
    global $mysqli;
    $res = $mysqli->query('SELECT * FROM levels ORDER BY id');
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_level($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM levels WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

// Class functions (for Primary P1-P7, Secondary S1-S6)
function get_classes_by_level($level_id) {
    global $mysqli;
    $level_id = (int)$level_id;
    $res = $mysqli->query("SELECT * FROM classes WHERE level_id = $level_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function cned_get_class($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM classes WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

function get_subjects_by_level($level_id) {
    global $mysqli;
    $level_id = (int)$level_id;
    $res = $mysqli->query("SELECT * FROM subjects WHERE level_id = $level_id ORDER BY id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// Get subjects for a specific class
function get_subjects_by_class($class_id) {
    global $mysqli;
    $class_id = (int)$class_id;
    $res = $mysqli->query("SELECT * FROM subjects WHERE class_id = $class_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_subject($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM subjects WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

function get_topics_by_subject($subject_id) {
    global $mysqli;
    $subject_id = (int)$subject_id;
    $res = $mysqli->query("SELECT * FROM topics WHERE subject_id = $subject_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_topic($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM topics WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

function get_courses() {
    global $mysqli;
    $res = $mysqli->query('SELECT * FROM courses ORDER BY id');
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_course($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM courses WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

function get_units_by_course($course_id) {
    global $mysqli;
    $course_id = (int)$course_id;
    $res = $mysqli->query("SELECT * FROM units WHERE course_id = $course_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_unit($id) {
    global $mysqli;
    $id = (int)$id;
    $res = $mysqli->query("SELECT * FROM units WHERE id = $id");
    return $res ? $res->fetch_assoc() : null;
}

// Admin authentication functions
function login_admin($username, $password) {
    global $mysqli;
    $username = $mysqli->real_escape_string($username);
    $res = $mysqli->query("SELECT id, username, password_hash FROM admin_users WHERE username = '$username'");
    if ($res && $user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            return true;
        }
    }
    return false;
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function get_admin_user() {
    if (is_admin_logged_in()) {
        return ['id' => $_SESSION['admin_id'], 'username' => $_SESSION['admin_username']];
    }
    return null;
}

// CSRF token functions
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Topic CRUD functions
function add_topic($subject_id, $title, $content, $position = 0) {
    global $mysqli;
    $subject_id = (int)$subject_id;
    $title = $mysqli->real_escape_string($title);
    $content = $mysqli->real_escape_string($content);
    $position = (int)$position;
    $sql = "INSERT INTO topics (subject_id, title, content, position) VALUES ($subject_id, '$title', '$content', $position)";
    return $mysqli->query($sql);
}

function update_topic($id, $subject_id, $title, $content, $position = 0) {
    global $mysqli;
    $id = (int)$id;
    $subject_id = (int)$subject_id;
    $title = $mysqli->real_escape_string($title);
    $content = $mysqli->real_escape_string($content);
    $position = (int)$position;
    $sql = "UPDATE topics SET subject_id = $subject_id, title = '$title', content = '$content', position = $position WHERE id = $id";
    return $mysqli->query($sql);
}

function delete_topic($id) {
    global $mysqli;
    $id = (int)$id;
    return $mysqli->query("DELETE FROM topics WHERE id = $id");
}

// Unit CRUD functions
function add_unit($course_id, $code, $title, $content, $position = 0) {
    global $mysqli;
    $course_id = (int)$course_id;
    $code = $mysqli->real_escape_string($code);
    $title = $mysqli->real_escape_string($title);
    $content = $mysqli->real_escape_string($content);
    $position = (int)$position;
    $sql = "INSERT INTO units (course_id, code, title, content, position) VALUES ($course_id, '$code', '$title', '$content', $position)";
    return $mysqli->query($sql);
}

function update_unit($id, $course_id, $code, $title, $content, $position = 0) {
    global $mysqli;
    $id = (int)$id;
    $course_id = (int)$course_id;
    $code = $mysqli->real_escape_string($code);
    $title = $mysqli->real_escape_string($title);
    $content = $mysqli->real_escape_string($content);
    $position = (int)$position;
    $sql = "UPDATE units SET course_id = $course_id, code = '$code', title = '$title', content = '$content', position = $position WHERE id = $id";
    return $mysqli->query($sql);
}

function delete_unit($id) {
    global $mysqli;
    $id = (int)$id;
    return $mysqli->query("DELETE FROM units WHERE id = $id");
}

// Topic videos and resources
function get_topic_videos($topic_id) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $res = $mysqli->query("SELECT * FROM topic_videos WHERE topic_id = $topic_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_topic_resources($topic_id) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $res = $mysqli->query("SELECT * FROM topic_resources WHERE topic_id = $topic_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// Unit videos and resources
function get_unit_videos($unit_id) {
    global $mysqli;
    $unit_id = (int)$unit_id;
    $res = $mysqli->query("SELECT * FROM unit_videos WHERE unit_id = $unit_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_unit_resources($unit_id) {
    global $mysqli;
    $unit_id = (int)$unit_id;
    $res = $mysqli->query("SELECT * FROM unit_resources WHERE unit_id = $unit_id ORDER BY position, id");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// Validation functions
function validate_string($str, $min_len = 1, $max_len = 255, $field_name = 'Field') {
    $str = trim($str);
    if (strlen($str) < $min_len) {
        return "Error: $field_name must be at least $min_len character(s).";
    }
    if (strlen($str) > $max_len) {
        return "Error: $field_name must not exceed $max_len character(s).";
    }
    return null; // No error
}

function validate_class_name($name) {
    return validate_string($name, 1, 50, 'Class name');
}

function validate_subject_name($name) {
    return validate_string($name, 1, 150, 'Subject name');
}

function validate_topic_title($title) {
    return validate_string($title, 3, 255, 'Topic title');
}

function validate_topic_content($content) {
    if (strlen($content) > 10000) {
        return 'Error: Content must not exceed 10,000 characters.';
    }
    return null;
}

function validate_course_name($name) {
    return validate_string($name, 3, 255, 'Course name');
}

function validate_unit_code($code) {
    return validate_string($code, 1, 50, 'Unit code');
}

function validate_unit_title($title) {
    return validate_string($title, 3, 255, 'Unit title');
}

