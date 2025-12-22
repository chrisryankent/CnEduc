<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: courses.php');
    exit;
}
$unit_id = (int)$_GET['id'];
$unit = get_unit($unit_id);
if (!$unit) {
    echo 'Unit not found';
    exit;
}
$course = get_course($unit['course_id']);
if (!$course) {
    echo 'Course not found';
    exit;
}

// Get videos and resources for this unit
$videos = get_unit_videos($unit_id);
$resources = get_unit_resources($unit_id);

// Get all units in this course for navigation
$all_units = get_units_by_course($course['id']);

// Calculate estimated reading time
$word_count = str_word_count(strip_tags($unit['content']));
$reading_time = ceil($word_count / 200);
if ($reading_time < 1) $reading_time = 1;

// Handle unit completion
$is_logged_in = is_user_logged_in();
$is_unit_complete = false;
if ($is_logged_in) {
    $current_user = cneduc_get_current_user();
    $is_unit_complete = is_unit_complete($current_user['id'], $unit_id);
    
    if (isset($_POST['mark_complete'])) {
        if (mark_unit_complete($current_user['id'], $unit_id)) {
            check_and_award_achievements($current_user['id']);
            $is_unit_complete = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($unit['title']); ?> - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (UNIT READING PAGE)
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
            min-width: 0;
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
           UNIT CONTENT
           ============================================================ */

        .unit-header {
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-200);
        }

        .unit-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--gray-900);
            line-height: 1.3;
        }

        .unit-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .unit-code {
            display: inline-block;
            padding: 6px 12px;
            background: var(--gradient-primary);
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
        }

        .unit-duration {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--gray-600);
            font-size: 14px;
        }

        .unit-content {
            font-size: 16px;
            line-height: 1.7;
            color: var(--gray-800);
        }

        .unit-content p {
            margin-bottom: 20px;
        }

        .unit-content h2, .unit-content h3 {
            margin: 32px 0 16px;
            color: var(--gray-900);
        }

        .unit-content h2 {
            font-size: 24px;
            font-weight: 700;
        }

        .unit-content h3 {
            font-size: 20px;
            font-weight: 600;
        }

        .unit-content ul, .unit-content ol {
            margin: 16px 0;
            padding-left: 24px;
        }

        .unit-content li {
            margin-bottom: 8px;
        }

        .unit-content blockquote {
            border-left: 4px solid var(--accent);
            padding-left: 20px;
            margin: 20px 0;
            font-style: italic;
            color: var(--gray-700);
        }

        .unit-content code {
            background: var(--gray-100);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .unit-content pre {
            background: var(--gray-900);
            color: white;
            padding: 16px;
            border-radius: var(--radius);
            overflow-x: auto;
            margin: 20px 0;
        }

        .unit-content pre code {
            background: none;
            padding: 0;
            color: inherit;
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
            max-height: 300px;
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
            cursor: pointer;
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

        /* Units Navigation */
        .units-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .unit-nav-item {
            padding: 12px 16px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            margin-bottom: 8px;
            transition: var(--transition);
            text-decoration: none;
            color: var(--gray-700);
            display: block;
        }

        .unit-nav-item:hover {
            border-color: var(--accent);
            background: var(--gray-50);
            text-decoration: none;
            color: var(--gray-900);
        }

        .unit-nav-item.active {
            border-color: var(--accent);
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
            font-weight: 600;
        }

        .unit-nav-code {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .unit-nav-item.active .unit-nav-code {
            color: var(--accent);
        }

        .unit-nav-title {
            font-size: 14px;
            font-weight: 500;
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
            background: conic-gradient(var(--accent) <?php echo $is_unit_complete ? '360' : '0'; ?>deg, var(--gray-200) 0);
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: background 0.5s ease;
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

        /* Study Tools */
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
            cursor: pointer;
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

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
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

        .btn-success {
            background: var(--gradient-tertiary);
            color: white;
        }

        .btn-success:hover {
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.3);
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
                <a href="university.php" class="active"><i class="fas fa-university"></i> University</a>
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
            <a href="index.php">Home</a> &raquo; 
            <a href="courses.php">University Courses</a> &raquo; 
            <a href="units.php?course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></a> &raquo; 
            <span><?php echo htmlspecialchars($unit['title']); ?></span>
        </div>

        <div class="grid-layout">
            <!-- MAIN CONTENT -->
            <main class="main-content">
                <!-- UNIT CONTENT -->
                <div class="card fade-in">
                    <div class="unit-header">
                        <h1 class="unit-title"><?php echo htmlspecialchars($unit['title']); ?></h1>
                        <div class="unit-meta">
                            <div class="unit-code"><?php echo htmlspecialchars($unit['code']); ?></div>
                            <div class="unit-duration">
                                <i class="far fa-clock"></i>
                                <?php echo $reading_time; ?> min read
                            </div>
                        </div>
                    </div>
                    
                    <div class="unit-content">
                        <?php echo nl2br(htmlspecialchars($unit['content'])); ?>
                    </div>
                </div>

                <!-- VIDEO TUTORIALS SECTION -->
                <?php if (!empty($videos)): ?>
                    <div class="card videos-section fade-in delay-1">
                        <h2><i class="fas fa-video"></i> Video Tutorials</h2>
                        <?php if (!$is_logged_in): ?>
                            <div class="sidebar-card" style="background: #fff3cd; border-color: #ffc107;">
                                <h3><i class="fas fa-lock"></i> Content Locked</h3>
                                <p style="font-size: 14px; color: #856404; margin-bottom: 16px;">
                                    Videos are available to registered users only.
                                </p>
                                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                    <a href="login.php?redirect=<?php echo urlencode('read_unit.php?id=' . $unit_id); ?>" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Login to Watch
                                    </a>
                                    <a href="register.php" class="btn btn-secondary">
                                        <i class="fas fa-user-plus"></i> Register Free
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Watch these video tutorials to better understand the concepts in this unit.</p>
                            
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
                        <a href="units.php?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Units
                        </a>
                        <?php if ($is_logged_in): ?>
                            <?php if ($is_unit_complete): ?>
                                <div class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Completed
                                </div>
                            <?php else: ?>
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="mark_complete" class="btn btn-primary">
                                        <i class="fas fa-check-circle"></i> Mark as Complete
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php?redirect=read_unit.php?id=<?php echo $unit_id; ?>" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login to Track
                            </a>
                        <?php endif; ?>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-share"></i> Share Unit
                        </a>
                    </div>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-cards">
                    <!-- COURSE INFO -->
                    <div class="sidebar-card fade-in">
                        <h3><i class="fas fa-graduation-cap"></i> Course Information</h3>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(15, 98, 254, 0.1); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-book" style="color: var(--accent); font-size: 20px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--gray-900);"><?php echo htmlspecialchars($course['name']); ?></div>
                                <div style="font-size: 14px; color: var(--gray-600);">University Course</div>
                            </div>
                        </div>
                        <div style="margin-top: 16px;">
                            <a href="units.php?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                                <i class="fas fa-layer-group"></i> View All Units
                            </a>
                        </div>
                    </div>

                    <!-- UNIT NAVIGATION -->
                    <div class="sidebar-card fade-in delay-1">
                        <h3><i class="fas fa-list-ol"></i> Unit Navigation</h3>
                        <div class="units-list">
                            <?php foreach ($all_units as $course_unit): ?>
                                <a href="read_unit.php?id=<?php echo $course_unit['id']; ?>" class="unit-nav-item <?php echo $unit['id'] == $course_unit['id'] ? 'active' : ''; ?>">
                                    <div class="unit-nav-code"><?php echo htmlspecialchars($course_unit['code']); ?></div>
                                    <div class="unit-nav-title"><?php echo htmlspecialchars($course_unit['title']); ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- LEARNING RESOURCES -->
                    <?php if (!empty($resources)): ?>
                        <div class="sidebar-card fade-in delay-1">
                            <h3><i class="fas fa-file-download"></i> Learning Resources</h3>
                            <?php if (!$is_logged_in): ?>
                                <div style="background: #fff3cd; border-radius: var(--radius); padding: 16px; margin-bottom: 16px;">
                                    <p style="font-size: 14px; color: #856404; margin-bottom: 12px;">
                                        <i class="fas fa-lock"></i> Resources are available to registered users only
                                    </p>
                                    <a href="login.php?redirect=<?php echo urlencode('read_unit.php?id=' . $unit_id); ?>" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 8px;">
                                        <i class="fas fa-sign-in-alt"></i> Login to Download
                                    </a>
                                </div>
                            <?php else: ?>
                                <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Download these resources to enhance your learning.</p>
                                
                                <div class="resources-list">
                                    <?php foreach ($resources as $resource): ?>
                                        <div class="resource-item">
                                            <div class="resource-header">
                                                <div class="resource-title">
                                                    <?php if (strpos($resource['file_type'], 'pdf') !== false): ?>
                                                        <i class="fas fa-file-pdf"></i>
                                                    <?php elseif (strpos($resource['file_type'], 'word') !== false || strpos($resource['file_type'], 'doc') !== false): ?>
                                                        <i class="fas fa-file-word"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-file"></i>
                                                    <?php endif; ?>
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
                    <div class="sidebar-card fade-in delay-2">
                        <h3><i class="fas fa-tools"></i> Study Tools</h3>
                        <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Tools to enhance your learning experience.</p>
                        
                        <div class="tools-grid">
                            <div class="tool-item" onclick="startStudyTimer()">
                                <div class="tool-icon">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                                <div class="tool-name">Timer</div>
                            </div>
                            <div class="tool-item" onclick="takeNotes()">
                                <div class="tool-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                <div class="tool-name">Notes</div>
                            </div>
                            <div class="tool-item" onclick="bookmarkUnit()">
                                <div class="tool-icon">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                                <div class="tool-name">Bookmark</div>
                            </div>
                            <div class="tool-item" onclick="startQuiz()">
                                <div class="tool-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="tool-name">Quiz</div>
                            </div>
                        </div>
                    </div>

                    <!-- LEARNING PROGRESS -->
                    <div class="sidebar-card fade-in delay-2">
                        <h3><i class="fas fa-chart-line"></i> Learning Progress</h3>
                        <div class="progress-section">
                            <div class="progress-circle">
                                <span class="progress-text"><?php echo $is_unit_complete ? '100%' : '0%'; ?></span>
                            </div>
                            <div class="progress-label">Unit Completion</div>
                        </div>
                        <?php if ($is_logged_in && !$is_unit_complete): ?>
                            <form method="post" style="margin-top: 16px;">
                                <button type="submit" name="mark_complete" class="btn btn-primary" style="width: 100%; padding: 12px;">
                                    <i class="fas fa-check"></i> Mark Complete
                                </button>
                            </form>
                        <?php elseif ($is_logged_in && $is_unit_complete): ?>
                            <div class="btn btn-success" style="width: 100%; padding: 12px; margin-top: 16px;">
                                <i class="fas fa-check-circle"></i> Completed
                            </div>
                        <?php elseif (!$is_logged_in): ?>
                            <a href="login.php?redirect=read_unit.php?id=<?php echo $unit_id; ?>" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 16px; display: block; text-decoration: none;">
                                <i class="fas fa-sign-in-alt"></i> Login to Track
                            </a>
                        <?php endif; ?>
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .video-card, .resource-item, .tool-item, .unit-nav-item');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });

            // Animate progress circle on load
            if (<?php echo $is_unit_complete ? 'true' : 'false'; ?>) {
                const progressCircle = document.querySelector('.progress-circle');
                if (progressCircle) {
                    setTimeout(() => {
                        progressCircle.style.background = 'conic-gradient(var(--accent) 360deg, var(--gray-200) 0)';
                    }, 500);
                }
            }
        });

        // Study tool functions
        function startStudyTimer() {
            alert('Starting study timer! Focus for 25 minutes.');
            // In a real app, this would open a timer modal
        }

        function takeNotes() {
            alert('Opening notes for this unit.');
            // In a real app, this would open a notes editor
        }

        function bookmarkUnit() {
            alert('Bookmark added!');
            // In a real app, this would save to bookmarks
        }

        function startQuiz() {
            alert('Starting quiz for this unit.');
            // In a real app, this would navigate to a quiz page
        }
    </script>
</body>
</html>