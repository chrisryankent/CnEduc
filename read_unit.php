<?php
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
            max-width: 1300px;
            margin: auto;
            padding: 30px;
        }

        .grid {
            display: flex;
            gap: 32px;
        }

        .main {
            flex: 1;
        }

        .sidebar {
            width: 340px;
            flex-shrink: 0;
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

        /* ============================================================
           BREADCRUMB
           ============================================================ */

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--gray-600);
        }

        .breadcrumb a {
            text-decoration: none;
            color: var(--gray-600);
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--accent);
        }

        .breadcrumb .separator {
            color: var(--gray-400);
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
            margin-bottom: 8px;
            color: var(--gray-900);
            line-height: 1.3;
        }

        .unit-meta {
            display: flex;
            gap: 16px;
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
           MEDIA (VIDEOS & RESOURCES)
           Inline styles kept here per request (no external CSS)
           ============================================================ */

        .media-section { margin-top: 40px; padding: 20px; border-radius: 8px; }
        .media-section.videos { background: #f5f5f5; }
        .media-section.resources { background: #fff3cd; border-left: 4px solid #ffc107; }

        .media-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; align-items: start; }
        @media (max-width: 900px) { .media-grid { grid-template-columns: 1fr; } }

        .media-item { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; flex-direction: column; }
        .media-item h3 { margin: 0 0 8px 0; font-size: 16px; color: var(--accent); }
        .media-item iframe, .media-item video { border-radius: 4px; margin-bottom: 10px; }

        .resource-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; }
        .resource-item { display:flex; align-items:center; justify-content:space-between; background: white; padding: 12px; border-radius: 4px; border-left: 3px solid #ffc107; }
        .resource-meta { flex: 1; margin-right: 12px; }
        .download-btn { background: #ffc107; color: #333; padding: 8px 15px; border-radius: 4px; text-decoration: none; display: inline-block; }

        @media (max-width: 600px) {
            .media-grid { grid-template-columns: 1fr; }
            .resource-item { flex-direction: column; align-items: stretch; }
            .download-btn { margin-top: 8px; width: 100%; text-align: center; }
        }

        /* ============================================================
           ACTION BUTTONS
           ============================================================ */

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        /* ============================================================
           BUTTONS
           ============================================================ */

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
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

        .btn-sm {
            padding: 10px 16px;
            font-size: 13px;
        }

        /* ============================================================
           SIDEBAR WIDGETS
           ============================================================ */

        .sidebar-card {
            background: white;
            border: 1px solid rgba(229, 229, 229, 0.5);
            padding: 24px;
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .sidebar-card h3 {
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-card h3 i {
            color: var(--accent);
        }

        .navigation-list {
            list-style: none;
        }

        .navigation-list li {
            margin: 12px 0;
        }

        .navigation-list a {
            text-decoration: none;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            padding: 8px 0;
        }

        .navigation-list a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }

        .navigation-list i {
            color: var(--accent);
            width: 20px;
            text-align: center;
        }

        .course-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .course-icon {
            font-size: 36px;
            color: var(--accent);
        }

        .course-details {
            flex: 1;
        }

        .course-name {
            font-weight: 700;
            font-size: 18px;
        }

        .course-meta {
            font-size: 14px;
            color: var(--gray-600);
        }

        .units-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 16px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
        }

        .unit-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .unit-item:last-child {
            border-bottom: none;
        }

        .unit-item:hover {
            background: var(--gray-50);
        }

        .unit-item.active {
            background: rgba(15, 98, 254, 0.1);
            border-left: 3px solid var(--accent);
        }

        .unit-item a {
            text-decoration: none;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .unit-item.active a {
            color: var(--accent);
            font-weight: 600;
        }

        .unit-item-code {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 600;
        }

        .unit-item.active .unit-item-code {
            color: var(--accent);
        }

        .tip-card {
            background: #f0f6ff;
            border-left: 4px solid var(--accent);
        }

        /* ============================================================
           READING PROGRESS
           ============================================================ */

        .reading-progress {
            position: fixed;
            top: 80px;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gray-200);
            z-index: 999;
        }

        .reading-progress-bar {
            height: 100%;
            background: var(--gradient-primary);
            width: 0%;
            transition: width 0.3s ease;
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
            .grid {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
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
            .unit-meta {
                flex-direction: column;
                gap: 8px;
            }
            .action-buttons {
                flex-direction: column;
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
            .reading-progress {
                top: 70px;
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
                <a href="#"><i class="fas fa-user"></i> Account</a>
            </div>
            <div class="nav-actions">
                <a href="search.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </a>
            </div>
        </div>
    </nav>

    <!-- READING PROGRESS BAR -->
    <div class="reading-progress">
        <div class="reading-progress-bar" id="readingProgress"></div>
    </div>

    <div class="content-wrapper">
        <!-- BREADCRUMB AND HEADER -->
        <div class="card fade-in">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <a href="courses.php">University Courses</a>
                <span class="separator">/</span>
                <a href="units.php?course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></a>
                <span class="separator">/</span>
                <span><?php echo htmlspecialchars($unit['title']); ?></span>
            </div>
            
            <div class="unit-header">
                <h1 class="unit-title"><?php echo htmlspecialchars($unit['title']); ?></h1>
                <div class="unit-meta">
                    <div class="unit-code"><?php echo htmlspecialchars($unit['code']); ?></div>
                    <div class="unit-duration">
                        <i class="far fa-clock"></i>
                        <?php
                        // Calculate estimated reading time (approx 200 words per minute)
                        $word_count = str_word_count(strip_tags($unit['content']));
                        $reading_time = ceil($word_count / 200);
                        if ($reading_time < 1) $reading_time = 1;
                        echo $reading_time . ' min read';
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="unit-content">
                <?php 
                // Format the content with proper paragraphs and line breaks
                $content = nl2br(htmlspecialchars($unit['content']));
                
                // Add some basic formatting if there are headings or lists
                // This is a simple approach - in a real app you might use a markdown parser
                echo $content;
                ?>
            </div>

            <!-- VIDEO TUTORIALS SECTION -->
            <?php if (!empty($videos)): ?>
            <div class="media-section videos">
                <h2 style="margin-top: 0; color: #333;"><i class="fas fa-video"></i> Video Tutorials</h2>
                <div class="media-grid">
                    <?php foreach ($videos as $video): ?>
                        <div class="media-item">
                            <h3><i class="fas fa-play-circle"></i> <?php echo htmlspecialchars($video['title']); ?></h3>
                            <?php if (!empty($video['description'])): ?>
                                <p style="color: #666; font-size: 14px; margin: 8px 0;">
                                    <?php echo htmlspecialchars($video['description']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($video['video_provider'] === 'youtube'): ?>
                                <iframe width="100%" height="200"
                                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['video_url']); ?>" 
                                    frameborder="0" allowfullscreen></iframe>
                            <?php elseif ($video['video_provider'] === 'vimeo'): ?>
                                <iframe src="https://player.vimeo.com/video/<?php echo htmlspecialchars($video['video_url']); ?>" 
                                    width="100%" height="200" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <video width="100%" height="200" controls>
                                    <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                            <?php if (!empty($video['duration_seconds'])): ?>
                                <p style="color: #999; font-size: 12px; margin: 0;">
                                    <i class="far fa-clock"></i> Duration: <?php echo floor($video['duration_seconds'] / 60); ?>:<?php echo str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- RESOURCES/PDF SECTION -->
            <?php if (!empty($resources)): ?>
            <div class="media-section resources">
                <h2 style="margin-top: 0; color: #333;"><i class="fas fa-file-pdf"></i> Learning Resources</h2>
                <ul class="resource-list">
                    <?php foreach ($resources as $resource): ?>
                        <li class="resource-item">
                            <div class="resource-meta">
                                <h4 style="margin: 0 0 5px 0; color: #0f62fe;"><i class="fas fa-file-download"></i> <?php echo htmlspecialchars($resource['title']); ?></h4>
                                <?php if (!empty($resource['description'])): ?>
                                    <p style="margin: 5px 0; color: #666; font-size: 14px;">
                                        <?php echo htmlspecialchars($resource['description']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($resource['file_size'])): ?>
                                    <small style="color: #999;">File size: <?php echo round($resource['file_size'] / 1024 / 1024, 2); ?> MB</small>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" download class="download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="units.php?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Units
                </a>
                <button class="btn btn-primary" id="markCompleteBtn">
                    <i class="fas fa-check-circle"></i> Mark as Complete
                </button>
            </div>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT - Already in the card above -->
            
            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card fade-in delay-1">
                    <h3><i class="fas fa-compass"></i> Navigation</h3>
                    <ul class="navigation-list">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="courses.php"><i class="fas fa-graduation-cap"></i> All Courses</a></li>
                        <li><a href="units.php?course_id=<?php echo $course['id']; ?>"><i class="fas fa-layer-group"></i> Course Units</a></li>
                        <li><a href="explore.php"><i class="fas fa-search"></i> Explore Content</a></li>
                        <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-2">
                    <h3><i class="fas fa-info-circle"></i> Course Information</h3>
                    <div class="course-info">
                        <div class="course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="course-details">
                            <div class="course-name"><?php echo htmlspecialchars($course['name']); ?></div>
                            <div class="course-meta">University Course</div>
                        </div>
                    </div>
                    
                    <div class="units-list">
                        <?php
                        $units = get_units_by_course($course['id']);
                        foreach ($units as $course_unit):
                        ?>
                            <div class="unit-item <?php echo $unit['id'] == $course_unit['id'] ? 'active' : ''; ?>">
                                <a href="read_unit.php?id=<?php echo $course_unit['id']; ?>">
                                    <i class="fas fa-file-alt"></i>
                                    <div>
                                        <div class="unit-item-code"><?php echo htmlspecialchars($course_unit['code']); ?></div>
                                        <div><?php echo htmlspecialchars($course_unit['title']); ?></div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sidebar-card tip-card fade-in delay-3">
                    <h3><i class="fas fa-lightbulb"></i> Study Tips</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Take notes as you read through this unit. Try to summarize key concepts in your own words. 
                        Review the material within 24 hours to improve retention.
                    </p>
                </div>

                <div class="sidebar-card fade-in delay-3">
                    <h3><i class="fas fa-bookmark"></i> Your Progress</h3>
                    <div style="text-align: center; padding: 16px;">
                        <div style="font-size: 32px; font-weight: 700; color: var(--accent);">0%</div>
                        <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">Course Completion</div>
                        <div style="height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; width: 0%; background: var(--gradient-primary); transition: width 0.5s ease;"></div>
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

        // Reading progress indicator
        window.addEventListener('scroll', function() {
            const winHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset;
            const trackLength = docHeight - winHeight;
            const progress = Math.floor(scrollTop / trackLength * 100);
            
            document.getElementById('readingProgress').style.width = progress + '%';
        });

        // Mark as complete button
        document.getElementById('markCompleteBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-check"></i> Marked Complete!';
            btn.style.background = 'var(--gradient-tertiary)';
            btn.disabled = true;
            
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = '';
                btn.disabled = false;
            }, 3000);
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>