<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: levels.php');
    exit;
}
$topic_id = (int)$_GET['id'];
$topic = get_topic($topic_id);
if (!$topic) {
    echo 'Topic not found';
    exit;
}
$subject = get_subject($topic['subject_id']);
$class = cned_get_class($subject['class_id']);
$level = get_level($class['level_id']);

// Get videos and resources for this topic
$videos = get_topic_videos($topic_id);
$resources = get_topic_resources($topic_id);

// Get related topics
$related_topics = get_related_topics($topic_id, $subject['id']);

// Handle topic completion
$is_logged_in = is_user_logged_in();
$is_topic_complete = false;
if ($is_logged_in) {
    $current_user = cneduc_get_current_user();
    $is_topic_complete = is_topic_complete($current_user['id'], $topic_id);
    
    if (isset($_POST['mark_complete'])) {
        if (mark_topic_complete($current_user['id'], $topic_id)) {
            check_and_award_achievements($current_user['id']);
            $is_topic_complete = true;
        }
    }
}

// Handle question submission
$qa_error = '';
$qa_success = '';
if (isset($_POST['submit_question'])) {
    $q_name = trim($_POST['question_author'] ?? '');
    $q_email = trim($_POST['question_email'] ?? '');
    $q_title = trim($_POST['question_title'] ?? '');
    $q_text = trim($_POST['question_text'] ?? '');
    
    if (!$q_name || !$q_email || !$q_title || !$q_text) {
        $qa_error = 'Please fill in all fields.';
    } elseif (!filter_var($q_email, FILTER_VALIDATE_EMAIL)) {
        $qa_error = 'Please enter a valid email address.';
    } elseif (strlen($q_title) < 5 || strlen($q_title) > 255) {
        $qa_error = 'Question title must be between 5 and 255 characters.';
    } elseif (strlen($q_text) < 10 || strlen($q_text) > 5000) {
        $qa_error = 'Question text must be between 10 and 5000 characters.';
    } else {
        if (add_topic_question($topic_id, $q_name, $q_email, $q_title, $q_text)) {
            $qa_success = 'Your question has been posted. Thank you!';
            $_POST = [];
        } else {
            $qa_error = 'Error posting question. Please try again.';
        }
    }
}

// Handle answer submission
if (isset($_POST['submit_answer'])) {
    $answer_id = (int)$_POST['question_id'];
    $a_name = trim($_POST['answer_author'] ?? '');
    $a_email = trim($_POST['answer_email'] ?? '');
    $a_text = trim($_POST['answer_text'] ?? '');
    
    if (!$a_name || !$a_email || !$a_text) {
        $qa_error = 'Please fill in all fields.';
    } elseif (!filter_var($a_email, FILTER_VALIDATE_EMAIL)) {
        $qa_error = 'Please enter a valid email address.';
    } elseif (strlen($a_text) < 10 || strlen($a_text) > 5000) {
        $qa_error = 'Answer must be between 10 and 5000 characters.';
    } else {
        if (add_question_answer($answer_id, $a_name, $a_email, $a_text)) {
            $qa_success = 'Your answer has been posted. Thank you!';
            $_POST = [];
        } else {
            $qa_error = 'Error posting answer. Please try again.';
        }
    }
}

// Fetch questions for this topic
$questions = get_topic_questions($topic_id, 20, 0);
$question_count = count_topic_questions($topic_id);

include __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($topic['title']); ?> - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (ULTRA ENHANCED)
           ============================================================ */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent: #0f62fe;
            --accent-light: #3d8dff;
            --accent-hover: #0043ce;
            --accent-secondary: #ff6b6b;
            --accent-tertiary: #4ecdc4;

            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-400: #a3a3a3;
            --gray-500: #737373;
            --gray-600: #525252;
            --gray-700: #404040;
            --gray-800: #262626;
            --gray-900: #171717;

            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-md: 0 6px 20px rgba(0,0,0,0.10);
            --shadow-lg: 0 12px 32px rgba(0,0,0,0.15);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.15);

            --transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            --transition-fast: 0.2s ease;

            --gradient-primary: linear-gradient(135deg, #0f62fe 0%, #3d8dff 100%);
            --gradient-secondary: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            --gradient-tertiary: linear-gradient(135deg, #4ecdc4 0%, #6ae2d9 100%);
            --gradient-light: linear-gradient(135deg, #f0f6ff 0%, #ffffff 100%);
            --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
        }

        body {
            font-family: "Inter", sans-serif;
            color: var(--gray-900);
            background: white;
            line-height: 1.65;
            padding-top: 80px;
            overflow-x: hidden;
        }

        /* ============================================================
           NAVBAR
           ============================================================ */

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            height: 80px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(229, 229, 229, 0.7);
            display: flex;
            align-items: center;
            padding: 0 32px;
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            transition: var(--transition);
        }

        .navbar.scrolled {
            height: 70px;
            box-shadow: var(--shadow-md);
            background: rgba(255, 255, 255, 0.98);
        }

        .nav-container {
            max-width: 1400px;
            width: 100%;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-900);
            text-decoration: none;
        }

        .nav-logo i {
            color: var(--accent);
            font-size: 30px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 32px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            padding: 8px 0;
            border-bottom: 2px solid transparent;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: var(--gradient-primary);
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .nav-links a:hover:after,
        .nav-links a.active:after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--accent);
        }

        .nav-actions {
            display: flex;
            gap: 16px;
        }

        /* ============================================================
           GENERAL LAYOUT
           ============================================================ */

        .content-wrapper {
            max-width: 1400px;
            margin: auto;
            padding: 30px;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 32px;
            align-items: start;
        }

        .main-content {
            min-width: 0; /* Prevent grid blowout */
        }

        .sidebar {
            position: sticky;
            top: 100px;
        }

        /* ============================================================
           CARDS
           ============================================================ */

        .card {
            background: var(--gradient-card);
            border: 1px solid rgba(229, 229, 229, 0.5);
            padding: 32px;
            border-radius: var(--radius-xl);
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
        }

        .card:hover:before {
            opacity: 1;
        }

        .card h1, .card h2 {
            margin-bottom: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card h1 {
            font-size: 32px;
        }

        .card h2 {
            font-size: 24px;
        }

        .card h1 i, .card h2 i {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(15, 98, 254, 0.1);
        }

        /* Sidebar Cards */
        .sidebar-card {
            background: var(--gradient-card);
            border: 1px solid rgba(229, 229, 229, 0.5);
            padding: 24px;
            border-radius: var(--radius-xl);
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .sidebar-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        .sidebar-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
        }

        .sidebar-card:hover:before {
            opacity: 1;
        }

        .sidebar-card h3 {
            margin-bottom: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
        }

        .sidebar-card h3 i {
            color: var(--accent);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(15, 98, 254, 0.1);
        }

        /* ============================================================
           BREADCRUMB
           ============================================================ */

        .breadcrumb {
            background: var(--gray-50);
            padding: 16px 24px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--accent-hover);
        }

        /* ============================================================
           TOPIC CONTENT
           ============================================================ */

        .topic-content {
            font-size: 16px;
            line-height: 1.8;
            color: var(--gray-800);
        }

        .topic-content p {
            margin-bottom: 16px;
        }

        .topic-content ul, .topic-content ol {
            margin: 16px 0;
            padding-left: 24px;
        }

        .topic-content li {
            margin-bottom: 8px;
        }

        /* ============================================================
           VIDEO SECTION
           ============================================================ */

        .videos-section {
            margin-top: 40px;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .video-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .video-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .video-player {
            position: relative;
            width: 100%;
            height: 200px;
            background: var(--gray-900);
        }

        .video-player iframe,
        .video-player video {
            width: 100%;
            height: 100%;
            border: none;
        }

        .video-info {
            padding: 20px;
        }

        .video-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .video-title i {
            color: var(--accent);
        }

        .video-description {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--gray-500);
        }

        .video-duration {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ============================================================
           SIDEBAR COMPONENTS
           ============================================================ */

        /* Resources List */
        .resources-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .resource-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 12px;
            transition: var(--transition);
        }

        .resource-item:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-sm);
        }

        .resource-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .resource-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .resource-title i {
            color: var(--accent);
            font-size: 12px;
        }

        .resource-download {
            color: var(--accent);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .resource-description {
            font-size: 12px;
            color: var(--gray-600);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .resource-meta {
            font-size: 11px;
            color: var(--gray-500);
            display: flex;
            gap: 12px;
        }

        /* Tools Grid */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 8px;
        }

        .tool-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            text-align: center;
            text-decoration: none;
            color: var(--gray-900);
            transition: var(--transition);
        }

        .tool-item:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            color: var(--gray-900);
        }

        .tool-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
            margin: 0 auto 8px;
        }

        .tool-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-900);
        }

        /* Related Topics */
        .related-topics-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .related-topic-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 12px;
            text-decoration: none;
            color: var(--gray-900);
            transition: var(--transition);
            display: block;
        }

        .related-topic-item:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            color: var(--gray-900);
        }

        .related-topic-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .related-topic-title i {
            font-size: 12px;
        }

        .related-topic-subject {
            font-size: 12px;
            color: var(--gray-600);
        }

        /* Progress Section */
        .progress-section {
            text-align: center;
            padding: 20px 0;
        }

        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(var(--accent) 75%, var(--gray-200) 0);
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-circle:before {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
        }

        .progress-text {
            position: relative;
            font-size: 14px;
            font-weight: 700;
            color: var(--accent);
        }

        .progress-label {
            font-size: 14px;
            color: var(--gray-600);
            margin-top: 8px;
        }

        /* ============================================================
           Q&A SECTION
           ============================================================ */

        .qa-section {
            margin-top: 40px;
        }

        .qa-form {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
        }

        .qa-form h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qa-form h3 i {
            color: var(--accent);
        }

        .qa-form-group {
            margin-bottom: 16px;
        }

        .qa-form label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 6px;
        }

        .qa-form input,
        .qa-form textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-family: "Inter", sans-serif;
            font-size: 14px;
            color: var(--gray-900);
            transition: var(--transition);
        }

        .qa-form input:focus,
        .qa-form textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .qa-form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .qa-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .qa-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .qa-form-actions button {
            flex: 1;
        }

        .qa-alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            font-size: 14px;
        }

        .qa-alert.error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .qa-alert.success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }

        /* Questions List */
        .questions-list {
            margin-top: 32px;
        }

        .questions-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-200);
        }

        .questions-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .questions-title i {
            color: var(--accent);
        }

        .questions-count {
            background: var(--accent);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .question-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            margin-bottom: 20px;
            overflow: hidden;
            transition: var(--transition);
        }

        .question-item:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-sm);
        }

        .question-header {
            background: var(--gray-50);
            padding: 16px;
            border-bottom: 1px solid var(--gray-200);
        }

        .question-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .question-title i {
            font-size: 14px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .question-meta {
            font-size: 12px;
            color: var(--gray-600);
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .question-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .question-body {
            padding: 16px;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .answers-section {
            background: var(--gray-50);
            padding: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .answers-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .answers-title i {
            color: var(--accent);
        }

        .answer-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 12px;
            margin-bottom: 10px;
        }

        .answer-item:last-child {
            margin-bottom: 0;
        }

        .answer-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .answer-author {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-900);
        }

        .answer-time {
            font-size: 12px;
            color: var(--gray-500);
        }

        .answer-text {
            font-size: 13px;
            color: var(--gray-800);
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .answer-actions {
            display: flex;
            gap: 12px;
            font-size: 12px;
        }

        .answer-action {
            color: var(--accent);
            cursor: pointer;
            transition: var(--transition);
        }

        .answer-action:hover {
            color: var(--accent-hover);
        }

        .no-questions {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 40px;
            text-align: center;
            color: var(--gray-600);
        }

        .no-questions i {
            font-size: 48px;
            color: var(--gray-300);
            margin-bottom: 16px;
            display: block;
        }

        .answer-form {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 12px;
            margin-top: 12px;
            display: none;
        }

        .answer-form.active {
            display: block;
        }

        .answer-form-group {
            margin-bottom: 10px;
        }

        .answer-form-group:last-child {
            margin-bottom: 0;
        }

        .answer-form label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 4px;
        }

        .answer-form input,
        .answer-form textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-family: "Inter", sans-serif;
            font-size: 12px;
            color: var(--gray-900);
        }

        .answer-form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .answer-form-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .answer-form-actions button,
        .answer-form-actions a {
            flex: 1;
            padding: 6px 12px;
            font-size: 12px;
            text-align: center;
        }

        /* ============================================================
           BUTTONS
           ============================================================ */

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(15, 98, 254, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            transform: translateY(-2px);
        }

        /* ============================================================
           ANIMATIONS
           ============================================================ */

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }

        /* ============================================================
           RESPONSIVE FIXES
           ============================================================ */

        @media (max-width: 1100px) {
            .grid-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            
            .sidebar {
                position: static;
                order: -1;
            }
            
            .sidebar-cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 24px;
            }
            
            .sidebar-card {
                margin-bottom: 0;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }
            .nav-links {
                gap: 20px;
            }
            .content-wrapper {
                padding: 20px;
            }
            .videos-grid {
                grid-template-columns: 1fr;
            }
            .tools-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 576px) {
            .nav-links {
                display: none;
            }
            .navbar {
                height: 70px;
            }
            body {
                padding-top: 70px;
            }
            .tools-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .sidebar-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>CnEduc</span>
            </a>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="curriculum.php"><i class="fas fa-book"></i> Curriculum</a>
                <a href="university.php"><i class="fas fa-university"></i> University</a>
                <a href="explore.php"><i class="fas fa-search"></i> Explore</a>
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php"><i class="fas fa-user-circle"></i> Account</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <a href="search.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </a>
                <?php if ($is_logged_in): ?>
                    <a href="logout.php" class="btn btn-sm" style="background: #ff6b6b; color: white; border: none;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <!-- BREADCRUMB -->
        <div class="breadcrumb fade-in">
            <a href="levels.php">Levels</a> &raquo; 
            <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a> &raquo; 
            <a href="subjects.php?class_id=<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></a> &raquo; 
            <a href="topics.php?subject_id=<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></a> &raquo; 
            <span><?php echo htmlspecialchars($topic['title']); ?></span>
        </div>

        <div class="grid-layout">
            <!-- MAIN CONTENT -->
            <main class="main-content">
                <!-- MAIN TOPIC CONTENT -->
                <div class="card fade-in">
                    <h1><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($topic['title']); ?></h1>
                    <div class="topic-content">
                        <?php echo nl2br(htmlspecialchars($topic['content'])); ?>
                    </div>
                </div>

                <!-- VIDEO TUTORIALS SECTION -->
                <?php if (!empty($videos)): ?>
                    <div class="card videos-section fade-in delay-1">
                        <h2><i class="fas fa-video"></i> Video Tutorials</h2>
                        <?php if (!$is_logged_in): ?>
                            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 16px;">
                                <p style="margin: 0 0 12px 0; color: #856404;"><i class="fas fa-lock"></i> Videos are available to registered users only</p>
                                <a href="login.php?redirect=<?php echo urlencode('read_topic.php?id=' . $topic_id); ?>" class="btn btn-primary" style="display: inline-block; padding: 8px 16px;">
                                    <i class="fas fa-sign-in-alt"></i> Login to Watch
                                </a>
                                <a href="register.php" class="btn btn-secondary" style="display: inline-block; padding: 8px 16px; margin-left: 8px;">
                                    <i class="fas fa-user-plus"></i> Register Free
                                </a>
                            </div>
                        <?php else: ?>
                            <p>Watch these video tutorials to better understand the concepts in this topic.</p>
                            <div class="videos-grid">
                                <?php foreach ($videos as $video): ?>
                                    <div class="video-card">
                                        <div class="video-player">
                                            <?php if ($video['video_provider'] === 'youtube'): ?>
                                                <iframe 
                                                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['video_url']); ?>" 
                                                    frameborder="0" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen>
                                                </iframe>
                                            <?php elseif ($video['video_provider'] === 'vimeo'): ?>
                                                <iframe 
                                                    src="https://player.vimeo.com/video/<?php echo htmlspecialchars($video['video_url']); ?>" 
                                                    frameborder="0" 
                                                    allow="autoplay; fullscreen; picture-in-picture" 
                                                    allowfullscreen>
                                                </iframe>
                                            <?php else: ?>
                                                <video controls>
                                                    <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                        <div class="video-info">
                                            <div class="video-title">
                                                <i class="fas fa-play-circle"></i>
                                                <?php echo htmlspecialchars($video['title']); ?>
                                            </div>
                                            <?php if (!empty($video['description'])): ?>
                                                <div class="video-description">
                                                    <?php echo htmlspecialchars($video['description']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="video-meta">
                                                <?php if (!empty($video['duration_seconds'])): ?>
                                                    <div class="video-duration">
                                                        <i class="far fa-clock"></i>
                                                        <?php echo floor($video['duration_seconds'] / 60); ?>:<?php echo str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="video-views">
                                                    <i class="far fa-eye"></i>
                                                    <?php echo $video['views_count'] ?? '0'; ?> views
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- NAVIGATION -->
                <div class="card fade-in delay-3" style="text-align: center;">
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <a href="topics.php?subject_id=<?php echo $subject['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Topics
                        </a>
                        <a href="subjects.php?class_id=<?php echo $class['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-book"></i> View All Subjects
                        </a>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-share"></i> Share Topic
                        </a>
                    </div>
                </div>

                <!-- Q&A SECTION -->
                <div class="qa-section fade-in">
                    <!-- Ask Question Form -->
                    <div class="card">
                        <div class="qa-form">
                            <?php if (!empty($qa_error)): ?>
                                <div class="qa-alert error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($qa_error); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($qa_success)): ?>
                                <div class="qa-alert success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($qa_success); ?></div>
                            <?php endif; ?>

                            <h3><i class="fas fa-lightbulb"></i> Ask a Question</h3>
                            <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 20px;">Have a question about this topic? Ask our community and get answers from fellow learners.</p>

                            <form method="post">
                                <div class="qa-form-row">
                                    <div class="qa-form-group">
                                        <label for="question_author">Your Name *</label>
                                        <input type="text" id="question_author" name="question_author" required value="<?php echo isset($_POST['question_author']) ? htmlspecialchars($_POST['question_author']) : ''; ?>">
                                    </div>
                                    <div class="qa-form-group">
                                        <label for="question_email">Your Email *</label>
                                        <input type="email" id="question_email" name="question_email" required value="<?php echo isset($_POST['question_email']) ? htmlspecialchars($_POST['question_email']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="qa-form-group">
                                    <label for="question_title">Question Title (Brief summary) *</label>
                                    <input type="text" id="question_title" name="question_title" required placeholder="e.g., How do I understand this concept?" value="<?php echo isset($_POST['question_title']) ? htmlspecialchars($_POST['question_title']) : ''; ?>">
                                </div>
                                <div class="qa-form-group">
                                    <label for="question_text">Your Question (Details) *</label>
                                    <textarea id="question_text" name="question_text" required placeholder="Please provide more details about your question..."><?php echo isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : ''; ?></textarea>
                                </div>
                                <div class="qa-form-actions">
                                    <button type="submit" name="submit_question" class="btn btn-primary">
                                        <i class="fas fa-send"></i> Post Question
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Questions & Answers List -->
                    <div class="questions-list">
                        <div class="questions-header">
                            <div class="questions-title">
                                <i class="fas fa-comments"></i> Community Questions
                            </div>
                            <div class="questions-count"><?php echo $question_count; ?> Questions</div>
                        </div>

                        <?php if (empty($questions)): ?>
                            <div class="no-questions">
                                <i class="fas fa-inbox"></i>
                                <p style="margin: 0;">No questions yet. Be the first to ask!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($questions as $q): 
                                $answers = get_question_answers($q['id']);
                            ?>
                                <div class="question-item fade-in">
                                    <div class="question-header">
                                        <div class="question-title">
                                            <i class="fas fa-question-circle"></i>
                                            <?php echo htmlspecialchars($q['question_title']); ?>
                                        </div>
                                        <div class="question-meta">
                                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($q['user_name']); ?></span>
                                            <span><i class="far fa-clock"></i> <?php echo date('M d, Y', strtotime($q['created_at'])); ?></span>
                                            <span><i class="fas fa-reply"></i> <?php echo count($answers); ?> answer<?php echo count($answers) !== 1 ? 's' : ''; ?></span>
                                        </div>
                                    </div>

                                    <div class="question-body">
                                        <?php echo nl2br(htmlspecialchars($q['question_text'])); ?>
                                    </div>

                                    <?php if (!empty($answers)): ?>
                                        <div class="answers-section">
                                            <div class="answers-title">
                                                <i class="fas fa-check-circle"></i> Answers (<?php echo count($answers); ?>)
                                            </div>
                                            <?php foreach ($answers as $a): ?>
                                                <div class="answer-item">
                                                    <div class="answer-header">
                                                        <div class="answer-author"><?php echo htmlspecialchars($a['user_name']); ?></div>
                                                        <div class="answer-time"><?php echo date('M d, Y', strtotime($a['created_at'])); ?></div>
                                                    </div>
                                                    <div class="answer-text">
                                                        <?php echo nl2br(htmlspecialchars($a['answer_text'])); ?>
                                                    </div>
                                                    <div class="answer-actions">
                                                        <span class="answer-action"><i class="fas fa-thumbs-up"></i> Helpful (<?php echo $a['helpful_count']; ?>)</span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="answers-section" style="background: var(--gray-100);">
                                        <button type="button" class="btn btn-sm btn-secondary" style="width: 100%; justify-content: center;" onclick="toggleAnswerForm(this)">
                                            <i class="fas fa-plus"></i> Post Your Answer
                                        </button>

                                        <form method="post" class="answer-form" style="margin-top: 12px;">
                                            <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                            <div class="answer-form-group">
                                                <label>Your Name *</label>
                                                <input type="text" name="answer_author" required placeholder="Your name">
                                            </div>
                                            <div class="answer-form-group">
                                                <label>Your Email *</label>
                                                <input type="email" name="answer_email" required placeholder="your@email.com">
                                            </div>
                                            <div class="answer-form-group">
                                                <label>Your Answer *</label>
                                                <textarea name="answer_text" required placeholder="Provide a helpful answer..."></textarea>
                                            </div>
                                            <div class="answer-form-actions">
                                                <button type="submit" name="submit_answer" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-send"></i> Post Answer
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAnswerForm(this)">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-cards">
                    <!-- LEARNING PROGRESS -->
                    <div class="sidebar-card fade-in">
                        <h3><i class="fas fa-chart-line"></i> Learning Progress</h3>
                        <div class="progress-section">
                            <div class="progress-circle">
                                <span class="progress-text"><?php echo $is_topic_complete ? '100%' : '75%'; ?></span>
                            </div>
                            <div class="progress-label">Topic Completion</div>
                        </div>
                        <div style="text-align: center; margin-top: 16px;">
                            <?php if ($is_logged_in): ?>
                                <?php if ($is_topic_complete): ?>
                                    <div class="btn btn-success btn-sm" style="padding: 8px 16px; background: #4caf50; color: white;">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </div>
                                <?php else: ?>
                                    <form method="post" style="display: inline;">
                                        <button type="submit" name="mark_complete" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
                                            <i class="fas fa-check"></i> Mark Complete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php?redirect=read_topic.php?id=<?php echo $topic_id; ?>" class="btn btn-primary btn-sm" style="padding: 8px 16px; text-decoration: none;">
                                    <i class="fas fa-sign-in-alt"></i> Login to Track
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- LEARNING RESOURCES -->
                    <?php if (!empty($resources)): ?>
                        <div class="sidebar-card fade-in delay-1">
                            <h3><i class="fas fa-file-download"></i> Learning Resources</h3>
                            <?php if (!$is_logged_in): ?>
                                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 16px; text-align: center;">
                                    <p style="margin: 0 0 12px 0; color: #856404; font-size: 13px;"><i class="fas fa-lock"></i> Resources are available to registered users only</p>
                                    <a href="login.php?redirect=<?php echo urlencode('read_topic.php?id=' . $topic_id); ?>" class="btn btn-primary" style="display: inline-block; padding: 6px 12px; font-size: 12px; margin-right: 6px;">
                                        <i class="fas fa-sign-in-alt"></i> Login
                                    </a>
                                    <a href="register.php" class="btn btn-secondary" style="display: inline-block; padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-user-plus"></i> Register
                                    </a>
                                </div>
                            <?php else: ?>
                                <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Download these resources to enhance your learning.</p>
                                
                                <div class="resources-list">
                                    <?php foreach ($resources as $resource): ?>
                                        <div class="resource-item">
                                            <div class="resource-header">
                                                <div class="resource-title">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <?php echo htmlspecialchars($resource['title']); ?>
                                                </div>
                                                <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" 
                                                   download 
                                                   class="resource-download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                            <?php if (!empty($resource['description'])): ?>
                                                <div class="resource-description">
                                                    <?php echo htmlspecialchars($resource['description']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="resource-meta">
                                                <?php if (!empty($resource['file_size'])): ?>
                                                    <span>Size: <?php echo round($resource['file_size'] / 1024 / 1024, 2); ?> MB</span>
                                                <?php endif; ?>
                                                <?php if (!empty($resource['file_type'])): ?>
                                                    <span>Type: <?php echo htmlspecialchars($resource['file_type']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- STUDY TOOLS -->
                    <div class="sidebar-card fade-in delay-1">
                        <h3><i class="fas fa-tools"></i> Study Tools</h3>
                        <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Tools to enhance your learning experience.</p>
                        
                        <div class="tools-grid">
                            <a href="#" class="tool-item">
                                <div class="tool-icon">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                                <div class="tool-name">Timer</div>
                            </a>
                            <a href="#" class="tool-item">
                                <div class="tool-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                <div class="tool-name">Notes</div>
                            </a>
                            <a href="#" class="tool-item">
                                <div class="tool-icon">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                                <div class="tool-name">Bookmark</div>
                            </a>
                            <a href="#" class="tool-item">
                                <div class="tool-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="tool-name">Quiz</div>
                            </a>
                        </div>
                    </div>

                    <!-- RELATED TOPICS -->
                    <?php if (!empty($related_topics)): ?>
                        <div class="sidebar-card fade-in delay-2">
                            <h3><i class="fas fa-link"></i> Related Topics</h3>
                            <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Explore these related topics.</p>
                            
                            <div class="related-topics-list">
                                <?php foreach ($related_topics as $related_topic): ?>
                                    <a href="topic.php?id=<?php echo $related_topic['id']; ?>" class="related-topic-item">
                                        <div class="related-topic-title">
                                            <i class="fas fa-file-alt"></i>
                                            <?php echo htmlspecialchars($related_topic['title']); ?>
                                        </div>
                                        <div class="related-topic-subject">
                                            <?php echo htmlspecialchars($subject['name']); ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- QUICK ACTIONS -->
                    <div class="sidebar-card fade-in delay-2">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="#" class="btn btn-secondary btn-sm" style="justify-content: center;">
                                <i class="fas fa-print"></i> Print Topic
                            </a>
                            <a href="#" class="btn btn-secondary btn-sm" style="justify-content: center;">
                                <i class="fas fa-book"></i> Add to Favorites
                            </a>
                            <a href="#" class="btn btn-secondary btn-sm" style="justify-content: center;">
                                <i class="fas fa-question"></i> Ask Question
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Add animation to elements when they come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .video-card, .resource-item, .tool-item, .related-topic-item, .question-item');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });

        // Toggle answer form visibility
        function toggleAnswerForm(btn) {
            const form = btn.closest('.answers-section').querySelector('.answer-form');
            if (form) {
                form.classList.toggle('active');
                if (form.classList.contains('active')) {
                    form.querySelector('input[type="text"]').focus();
                }
            }
        }
    </script>
</body>
</html>