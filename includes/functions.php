<?php
require_once __DIR__ . '/db.php';

// ============================================================
// ORIGINAL HELPER FUNCTIONS
// ============================================================

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

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

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

function add_unit_video($unit_id, $title, $description, $video_url, $video_provider = 'youtube', $duration_seconds = null, $position = 0) {
    global $mysqli;
    $unit_id = (int)$unit_id;
    $title = $mysqli->real_escape_string($title);
    $description = $mysqli->real_escape_string($description);
    $video_url = $mysqli->real_escape_string($video_url);
    $video_provider = $mysqli->real_escape_string($video_provider);
    $duration_seconds = $duration_seconds === null ? 'NULL' : (int)$duration_seconds;
    $position = (int)$position;
    $sql = "INSERT INTO unit_videos (unit_id, title, description, video_url, video_provider, duration_seconds, position) VALUES ($unit_id, '$title', '$description', '$video_url', '$video_provider', $duration_seconds, $position)";
    return $mysqli->query($sql);
}

function add_topic_video($topic_id, $title, $description, $video_url, $video_provider = 'youtube', $duration_seconds = null, $position = 0) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $title = $mysqli->real_escape_string($title);
    $description = $mysqli->real_escape_string($description);
    $video_url = $mysqli->real_escape_string($video_url);
    $video_provider = $mysqli->real_escape_string($video_provider);
    $duration_seconds = $duration_seconds === null ? 'NULL' : (int)$duration_seconds;
    $position = (int)$position;
    $sql = "INSERT INTO topic_videos (topic_id, title, description, video_url, video_provider, duration_seconds, position) VALUES ($topic_id, '$title', '$description', '$video_url', '$video_provider', $duration_seconds, $position)";
    return $mysqli->query($sql);
}

function add_unit_resource($unit_id, $title, $description, $file_path, $file_size = 0, $file_type = 'file', $position = 0) {
    global $mysqli;
    $unit_id = (int)$unit_id;
    $title = $mysqli->real_escape_string($title);
    $description = $mysqli->real_escape_string($description);
    $file_path = $mysqli->real_escape_string($file_path);
    $file_size = (int)$file_size;
    $file_type = $mysqli->real_escape_string($file_type);
    $position = (int)$position;
    $sql = "INSERT INTO unit_resources (unit_id, title, description, file_path, file_size, file_type, position) VALUES ($unit_id, '$title', '$description', '$file_path', $file_size, '$file_type', $position)";
    return $mysqli->query($sql);
}

function add_topic_resource($topic_id, $title, $description, $file_path, $file_size = 0, $file_type = 'file', $position = 0) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $title = $mysqli->real_escape_string($title);
    $description = $mysqli->real_escape_string($description);
    $file_path = $mysqli->real_escape_string($file_path);
    $file_size = (int)$file_size;
    $file_type = $mysqli->real_escape_string($file_type);
    $position = (int)$position;
    $sql = "INSERT INTO topic_resources (topic_id, title, description, file_path, file_size, file_type, position) VALUES ($topic_id, '$title', '$description', '$file_path', $file_size, '$file_type', $position)";
    return $mysqli->query($sql);
}

function validate_string($str, $min_len = 1, $max_len = 255, $field_name = 'Field') {
    $str = trim($str);
    if (strlen($str) < $min_len) {
        return "Error: $field_name must be at least $min_len character(s).";
    }
    if (strlen($str) > $max_len) {
        return "Error: $field_name must not exceed $max_len character(s).";
    }
    return null;
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

function get_related_topics($topic_id, $subject_id, $limit = 5) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $subject_id = (int)$subject_id;
    $limit = (int)$limit;
    $res = $mysqli->query("SELECT * FROM topics WHERE subject_id = $subject_id AND id != $topic_id ORDER BY position, id LIMIT $limit");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function add_topic_question($topic_id, $user_name, $user_email, $question_title, $question_text) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $user_name = $mysqli->real_escape_string($user_name);
    $user_email = $mysqli->real_escape_string($user_email);
    $question_title = $mysqli->real_escape_string($question_title);
    $question_text = $mysqli->real_escape_string($question_text);
    $sql = "INSERT INTO topic_questions (topic_id, user_name, user_email, question_title, question_text) VALUES ($topic_id, '$user_name', '$user_email', '$question_title', '$question_text')";
    return $mysqli->query($sql);
}

function get_topic_questions($topic_id, $limit = 10, $offset = 0) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $limit = (int)$limit;
    $offset = (int)$offset;
    $res = $mysqli->query("SELECT * FROM topic_questions WHERE topic_id = $topic_id AND is_approved = 1 ORDER BY created_at DESC LIMIT $offset, $limit");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_topic_question($question_id) {
    global $mysqli;
    $question_id = (int)$question_id;
    $res = $mysqli->query("SELECT * FROM topic_questions WHERE id = $question_id");
    return $res ? $res->fetch_assoc() : null;
}

function add_question_answer($question_id, $user_name, $user_email, $answer_text) {
    global $mysqli;
    $question_id = (int)$question_id;
    $user_name = $mysqli->real_escape_string($user_name);
    $user_email = $mysqli->real_escape_string($user_email);
    $answer_text = $mysqli->real_escape_string($answer_text);
    $sql = "INSERT INTO topic_question_answers (question_id, user_name, user_email, answer_text) VALUES ($question_id, '$user_name', '$user_email', '$answer_text')";
    return $mysqli->query($sql);
}

function get_question_answers($question_id) {
    global $mysqli;
    $question_id = (int)$question_id;
    $res = $mysqli->query("SELECT * FROM topic_question_answers WHERE question_id = $question_id AND is_approved = 1 ORDER BY created_at ASC");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function count_topic_questions($topic_id) {
    global $mysqli;
    $topic_id = (int)$topic_id;
    $res = $mysqli->query("SELECT COUNT(*) as cnt FROM topic_questions WHERE topic_id = $topic_id AND is_approved = 1");
    $row = $res ? $res->fetch_assoc() : null;
    return $row ? (int)$row['cnt'] : 0;
}

// ============================================================
// NEW USER AUTHENTICATION & PROGRESS FUNCTIONS
// ============================================================

function register_user($first_name, $last_name, $email, $password) {
    global $mysqli;
    $first_name = trim($first_name);
    $last_name = trim($last_name);
    $email = trim($email);
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format.'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }
    $email_escaped = $mysqli->real_escape_string($email);
    $result = $mysqli->query("SELECT id FROM users WHERE email = '$email_escaped'");
    if ($result && $result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $first_name_escaped = $mysqli->real_escape_string($first_name);
    $last_name_escaped = $mysqli->real_escape_string($last_name);
    $sql = "INSERT INTO users (first_name, last_name, email, password_hash, created_at) VALUES ('$first_name_escaped', '$last_name_escaped', '$email_escaped', '$password_hash', NOW())";
    if ($mysqli->query($sql)) {
        return ['success' => true, 'message' => 'Registration successful. Please log in.', 'user_id' => $mysqli->insert_id];
    } else {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

function login_user($email, $password) {
    global $mysqli;
    $email = trim($email);
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required.'];
    }
    $email_escaped = $mysqli->real_escape_string($email);
    $result = $mysqli->query("SELECT id, first_name, last_name, email, password_hash FROM users WHERE email = '$email_escaped'");
    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
            return ['success' => true, 'message' => 'Login successful.', 'user_id' => $user['id']];
        }
    }
    return ['success' => false, 'message' => 'Invalid email or password.'];
}

function logout_user() {
    session_destroy();
    return true;
}

function is_user_logged_in() {
    return !empty($_SESSION['user_id']);
}

function cneduc_get_current_user() {
    if (is_user_logged_in()) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'first_name' => $_SESSION['user_first_name'],
            'last_name' => $_SESSION['user_last_name']
        ];
    }
    return null;
}

function get_user($user_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $result = $mysqli->query("SELECT id, first_name, last_name, email, created_at FROM users WHERE id = $user_id");
    return $result ? $result->fetch_assoc() : null;
}

function mark_topic_complete($user_id, $topic_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $topic_id = (int)$topic_id;
    $result = $mysqli->query("SELECT id FROM user_topic_completion WHERE user_id = $user_id AND topic_id = $topic_id");
    if ($result && $result->num_rows > 0) {
        return true;
    }
    $sql = "INSERT INTO user_topic_completion (user_id, topic_id, completed_at) VALUES ($user_id, $topic_id, NOW())";
    return $mysqli->query($sql);
}

function is_topic_complete($user_id, $topic_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $topic_id = (int)$topic_id;
    $result = $mysqli->query("SELECT id FROM user_topic_completion WHERE user_id = $user_id AND topic_id = $topic_id");
    return $result && $result->num_rows > 0;
}

function mark_unit_complete($user_id, $unit_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $unit_id = (int)$unit_id;
    $result = $mysqli->query("SELECT id FROM user_unit_completion WHERE user_id = $user_id AND unit_id = $unit_id");
    if ($result && $result->num_rows > 0) {
        return true;
    }
    $sql = "INSERT INTO user_unit_completion (user_id, unit_id, completed_at) VALUES ($user_id, $unit_id, NOW())";
    return $mysqli->query($sql);
}

function is_unit_complete($user_id, $unit_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $unit_id = (int)$unit_id;
    $result = $mysqli->query("SELECT id FROM user_unit_completion WHERE user_id = $user_id AND unit_id = $unit_id");
    return $result && $result->num_rows > 0;
}

function get_user_progress_summary($user_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $topics_result = $mysqli->query("SELECT COUNT(*) as total FROM topics");
    $total_topics = $topics_result ? $topics_result->fetch_assoc()['total'] : 0;
    $completed_topics_result = $mysqli->query("SELECT COUNT(*) as completed FROM user_topic_completion WHERE user_id = $user_id");
    $completed_topics = $completed_topics_result ? $completed_topics_result->fetch_assoc()['completed'] : 0;
    $units_result = $mysqli->query("SELECT COUNT(*) as total FROM units");
    $total_units = $units_result ? $units_result->fetch_assoc()['total'] : 0;
    $completed_units_result = $mysqli->query("SELECT COUNT(*) as completed FROM user_unit_completion WHERE user_id = $user_id");
    $completed_units = $completed_units_result ? $completed_units_result->fetch_assoc()['completed'] : 0;
    return [
        'total_topics' => $total_topics,
        'completed_topics' => $completed_topics,
        'total_units' => $total_units,
        'completed_units' => $completed_units,
        'topic_percentage' => $total_topics > 0 ? round(($completed_topics / $total_topics) * 100, 1) : 0,
        'unit_percentage' => $total_units > 0 ? round(($completed_units / $total_units) * 100, 1) : 0
    ];
}

function award_achievement($user_id, $slug, $name, $description) {
    global $mysqli;
    $user_id = (int)$user_id;
    $slug = $mysqli->real_escape_string($slug);
    $name = $mysqli->real_escape_string($name);
    $description = $mysqli->real_escape_string($description);
    $result = $mysqli->query("SELECT id FROM user_achievements WHERE user_id = $user_id AND achievement_slug = '$slug'");
    if ($result && $result->num_rows > 0) {
        return false;
    }
    $sql = "INSERT INTO user_achievements (user_id, achievement_slug, achievement_name, description, awarded_at) VALUES ($user_id, '$slug', '$name', '$description', NOW())";
    return $mysqli->query($sql);
}

function get_user_achievements($user_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $result = $mysqli->query("SELECT achievement_slug, achievement_name, description, awarded_at FROM user_achievements WHERE user_id = $user_id ORDER BY awarded_at DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function check_and_award_achievements($user_id) {
    $progress = get_user_progress_summary($user_id);
    $completed_topics = $progress['completed_topics'];
    $completed_units = $progress['completed_units'];
    $milestones = [
        ['topics' => 1, 'units' => 0, 'slug' => 'first_topic', 'name' => 'First Steps', 'desc' => 'Completed your first topic'],
        ['topics' => 5, 'units' => 0, 'slug' => 'topic_explorer', 'name' => 'Topic Explorer', 'desc' => 'Completed 5 topics'],
        ['topics' => 10, 'units' => 0, 'slug' => 'topic_master', 'name' => 'Topic Master', 'desc' => 'Completed 10 topics'],
        ['topics' => 25, 'units' => 0, 'slug' => 'topic_legend', 'name' => 'Topic Legend', 'desc' => 'Completed 25 topics'],
        ['topics' => 0, 'units' => 1, 'slug' => 'first_unit', 'name' => 'Unit Starter', 'desc' => 'Completed your first unit'],
        ['topics' => 0, 'units' => 5, 'slug' => 'unit_collector', 'name' => 'Unit Collector', 'desc' => 'Completed 5 units'],
        ['topics' => 0, 'units' => 10, 'slug' => 'unit_champion', 'name' => 'Unit Champion', 'desc' => 'Completed 10 units']
    ];
    foreach ($milestones as $milestone) {
        $threshold_topics = $milestone['topics'];
        $threshold_units = $milestone['units'];
        if (($threshold_topics == 0 || $completed_topics >= $threshold_topics) &&
            ($threshold_units == 0 || $completed_units >= $threshold_units)) {
            award_achievement($user_id, $milestone['slug'], $milestone['name'], $milestone['desc']);
        }
    }
}

// ============================================================
// EXAM SYSTEM FUNCTIONS
// ============================================================

/**
 * Get exam by ID
 */
function get_exam($exam_id) {
    global $mysqli;
    $exam_id = (int)$exam_id;
    $res = $mysqli->query("SELECT * FROM exams WHERE id = $exam_id");
    return $res ? $res->fetch_assoc() : null;
}

/**
 * Get all exams for a subject
 */
function get_exams_by_subject($subject_id) {
    global $mysqli;
    $subject_id = (int)$subject_id;
    $res = $mysqli->query("SELECT * FROM exams WHERE subject_id = $subject_id ORDER BY created_at DESC");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get all exams for a unit
 */
function get_exams_by_unit($unit_id) {
    global $mysqli;
    $unit_id = (int)$unit_id;
    $res = $mysqli->query("SELECT * FROM exams WHERE unit_id = $unit_id ORDER BY created_at DESC");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get questions for an exam (shuffled based on user_id)
 */
function get_exam_questions($exam_id, $user_id = null, $attempt_number = 1) {
    global $mysqli;
    $exam_id = (int)$exam_id;
    
    // Get all exam questions in order
    $res = $mysqli->query(
        "SELECT eq.id as eq_id, eq.question_id, eq.question_order, eq.points, tq.* 
         FROM exam_questions eq
         JOIN topic_questions tq ON eq.question_id = tq.id
         WHERE eq.exam_id = $exam_id
         ORDER BY eq.question_order ASC"
    );
    
    $questions = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    
    // Get exam settings
    $exam = get_exam($exam_id);
    
    if ($exam && $exam['shuffle_questions'] && $user_id) {
        // Shuffle using seed based on user_id + exam_id + attempt
        $seed = ($user_id * 1000) + ($exam_id * 100) + $attempt_number;
        
        // Use Fisher-Yates shuffle with seed
        mt_srand($seed);
        $count = count($questions);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $temp = $questions[$i];
            $questions[$i] = $questions[$j];
            $questions[$j] = $temp;
        }
    }
    
    return $questions;
}

/**
 * Create exam attempt - when user starts exam
 */
function create_exam_attempt($exam_id, $user_id, $ip_address = null, $browser_info = null) {
    global $mysqli;
    $exam_id = (int)$exam_id;
    $user_id = (int)$user_id;
    
    // Get previous attempt count
    $res = $mysqli->query(
        "SELECT MAX(attempt_number) as max_attempt FROM exam_attempts 
         WHERE exam_id = $exam_id AND user_id = $user_id"
    );
    $row = $res->fetch_assoc();
    $attempt_number = ($row['max_attempt'] ?? 0) + 1;
    
    // Get exam for total_points
    $exam = get_exam($exam_id);
    $total_points = $exam['total_questions'] ?? 1;
    
    $ip = $mysqli->real_escape_string($ip_address ?? '');
    $browser = $mysqli->real_escape_string($browser_info ?? '');
    
    $insert = $mysqli->query(
        "INSERT INTO exam_attempts 
         (exam_id, user_id, attempt_number, total_points, ip_address, browser_info) 
         VALUES ($exam_id, $user_id, $attempt_number, $total_points, '$ip', '$browser')"
    );
    
    return $insert ? $mysqli->insert_id : null;
}

/**
 * Get exam attempt details
 */
function get_exam_attempt($attempt_id) {
    global $mysqli;
    $attempt_id = (int)$attempt_id;
    $res = $mysqli->query("SELECT * FROM exam_attempts WHERE id = $attempt_id");
    return $res ? $res->fetch_assoc() : null;
}

/**
 * Save user's answer to a question
 */
function save_exam_answer($attempt_id, $question_id, $user_answer) {
    global $mysqli;
    $attempt_id = (int)$attempt_id;
    $question_id = (int)$question_id;
    $answer = $mysqli->real_escape_string($user_answer);
    
    $res = $mysqli->query(
        "INSERT INTO exam_answers (attempt_id, question_id, user_answer) 
         VALUES ($attempt_id, $question_id, '$answer')
         ON DUPLICATE KEY UPDATE user_answer = '$answer', answered_at = CURRENT_TIMESTAMP"
    );
    
    return $res ? true : false;
}

/**
 * Auto-mark an answer based on keywords
 */
function auto_mark_answer($question_id, $user_answer, $max_points = 1) {
    global $mysqli;
    $question_id = (int)$question_id;
    
    $question = get_topic_question($question_id);
    if (!$question) return ['is_correct' => false, 'points' => 0, 'percentage' => 0];
    
    // Get answer keywords
    $keywords = array_filter(array_map('trim', explode(',', $question['answer_keywords'] ?? '')));
    if (empty($keywords)) {
        // No keywords defined, cannot auto-mark
        return ['is_correct' => null, 'points' => 0, 'percentage' => 0];
    }
    
    $user_answer_lower = strtolower(trim($user_answer));
    $matches = 0;
    
    // Count keyword matches
    foreach ($keywords as $keyword) {
        if (strlen($keyword) > 0 && strpos($user_answer_lower, strtolower($keyword)) !== false) {
            $matches++;
        }
    }
    
    // Calculate percentage
    $percentage = count($keywords) > 0 ? ($matches / count($keywords)) * 100 : 0;
    $is_correct = $percentage >= 70; // 70% match threshold
    $points = (int)(($percentage / 100) * $max_points);
    
    return [
        'is_correct' => $is_correct,
        'points' => $points,
        'percentage' => round($percentage, 2)
    ];
}

/**
 * Submit exam - grade all answers and calculate score
 */
function submit_exam($attempt_id) {
    global $mysqli;
    $attempt_id = (int)$attempt_id;
    
    $attempt = get_exam_attempt($attempt_id);
    if (!$attempt) return false;
    
    // Get all answers for this attempt
    $res = $mysqli->query(
        "SELECT ea.id, ea.question_id, ea.user_answer, eq.points
         FROM exam_answers ea
         JOIN exam_questions eq ON ea.question_id = eq.question_id
         WHERE ea.attempt_id = $attempt_id"
    );
    
    $answers = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $total_score = 0;
    $total_points = 0;
    
    // Grade each answer
    foreach ($answers as $answer) {
        $mark = auto_mark_answer($answer['question_id'], $answer['user_answer'], $answer['points']);
        $total_score += $mark['points'];
        $total_points += $answer['points'];
        
        // Update answer with score
        $answer_id = $answer['id'];
        $points = $mark['points'];
        $is_correct = $mark['is_correct'] === true ? 1 : ($mark['is_correct'] === false ? 0 : null);
        
        $mysqli->query(
            "UPDATE exam_answers SET points_earned = $points, is_correct = $is_correct 
             WHERE id = $answer_id"
        );
    }
    
    // Calculate percentage
    $percentage = $total_points > 0 ? ($total_score / $total_points) * 100 : 0;
    $time_taken = strtotime('now') - strtotime($attempt['started_at']);
    
    // Check for suspicious activity
    $suspicious = check_exam_suspicious_activity($attempt_id);
    
    // Update attempt with final results
    $update = $mysqli->query(
        "UPDATE exam_attempts 
         SET status = 'graded', 
             submitted_at = CURRENT_TIMESTAMP,
             time_taken_seconds = $time_taken,
             score = $total_score, 
             total_points = $total_points, 
             percentage = $percentage,
             suspicious_activity = " . ($suspicious ? 1 : 0) . "
         WHERE id = $attempt_id"
    );
    
    return $update ? [
        'score' => $total_score,
        'total_points' => $total_points,
        'percentage' => round($percentage, 2),
        'passed' => $percentage >= ($attempt['passing_percentage'] ?? 50),
        'suspicious' => $suspicious
    ] : false;
}

/**
 * Check for suspicious activity in exam
 */
function check_exam_suspicious_activity($attempt_id) {
    global $mysqli;
    $attempt_id = (int)$attempt_id;
    
    $attempt = get_exam_attempt($attempt_id);
    if (!$attempt) return false;
    
    // Count tab switches
    $res = $mysqli->query(
        "SELECT COUNT(*) as switch_count FROM exam_tab_switches WHERE attempt_id = $attempt_id"
    );
    $row = $res->fetch_assoc();
    $tab_switches = $row['switch_count'] ?? 0;
    
    // Calculate exam time in minutes
    $time_minutes = ($attempt['time_taken_seconds'] ?? 0) / 60;
    
    $suspicious = false;
    $flags = [];
    
    // Flag criteria
    if ($tab_switches > 5) {
        $suspicious = true;
        $flags[] = "Too many tab switches ($tab_switches)";
    }
    
    if ($time_minutes < 2 && $attempt['percentage'] > 80) {
        $suspicious = true;
        $flags[] = "Exam completed too quickly ($time_minutes minutes)";
    }
    
    if ($attempt['percentage'] > 98) {
        $suspicious = true;
        $flags[] = "Suspiciously high score (" . $attempt['percentage'] . "%)";
    }
    
    // Update tab_switches count
    $mysqli->query(
        "UPDATE exam_attempts SET tab_switches = $tab_switches WHERE id = $attempt_id"
    );
    
    return $suspicious;
}

/**
 * Log tab switch for cheat detection
 */
function log_exam_tab_switch($attempt_id, $switch_type = 'left') {
    global $mysqli;
    $attempt_id = (int)$attempt_id;
    $type = $mysqli->real_escape_string($switch_type);
    
    return $mysqli->query(
        "INSERT INTO exam_tab_switches (attempt_id, switch_type) 
         VALUES ($attempt_id, '$type')"
    );
}

/**
 * Get user's exam attempts
 */
function get_user_exam_attempts($user_id, $exam_id = null) {
    global $mysqli;
    $user_id = (int)$user_id;
    
    $query = "SELECT ea.*, e.title as exam_title 
              FROM exam_attempts ea
              JOIN exams e ON ea.exam_id = e.id
              WHERE ea.user_id = $user_id";
    
    if ($exam_id) {
        $exam_id = (int)$exam_id;
        $query .= " AND ea.exam_id = $exam_id";
    }
    
    $query .= " ORDER BY ea.created_at DESC";
    
    $res = $mysqli->query($query);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Check if user can take exam (within attempt limits)
 */
function can_user_take_exam($user_id, $exam_id) {
    global $mysqli;
    $user_id = (int)$user_id;
    $exam_id = (int)$exam_id;
    
    $exam = get_exam($exam_id);
    if (!$exam) return false;
    
    // Check if user has in-progress attempt
    $res = $mysqli->query(
        "SELECT id FROM exam_attempts 
         WHERE user_id = $user_id AND exam_id = $exam_id AND status = 'in_progress'"
    );
    
    if ($res->num_rows > 0) {
        return false; // Already taking exam
    }
    
    // Check attempt limit
    $res = $mysqli->query(
        "SELECT COUNT(*) as count FROM exam_attempts 
         WHERE user_id = $user_id AND exam_id = $exam_id"
    );
    $row = $res->fetch_assoc();
    
    return $row['count'] < $exam['max_attempts'];
}

/**
 * Calculate exam time limit based on questions
 */
function calculate_exam_time($total_questions, $difficulty = 'intermediate') {
    $base_times = [
        'easy' => 3,
        'intermediate' => 5,
        'hard' => 8
    ];
    
    $time_per_question = $base_times[$difficulty] ?? 5;
    $total_time = $total_questions * $time_per_question;
    
    // Add 10% buffer
    $total_time = (int)($total_time * 1.1);
    
    return $total_time;
}

?>

