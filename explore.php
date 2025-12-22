<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// Initialize filter variables
$filter_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$filter_subject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

$is_logged_in = is_user_logged_in();

// Get all classes for both levels
$all_classes = [];
$primary_classes = [];
$secondary_classes = [];

// Get primary classes (level_id = 1)
$primary_level_classes = get_classes_by_level(1);
if ($primary_level_classes) {
    $primary_classes = $primary_level_classes;
    $all_classes = array_merge($all_classes, $primary_classes);
}

// Get secondary classes (level_id = 2)
$secondary_level_classes = get_classes_by_level(2);
if ($secondary_level_classes) {
    $secondary_classes = $secondary_level_classes;
    $all_classes = array_merge($all_classes, $secondary_classes);
}

// Get filtered subjects and topics
$filtered_subjects = [];
$topics = [];

if ($filter_class > 0) {
    $filtered_subjects = get_subjects_by_class($filter_class);
    if (!$filtered_subjects) {
        $filtered_subjects = [];
    }
    
    if ($filter_subject > 0) {
        $topics = get_topics_by_subject($filter_subject);
    } else {
        // If no specific subject, get all topics for the class
        foreach ($filtered_subjects as $subj) {
            $subj_topics = get_topics_by_subject($subj['id']);
            if ($subj_topics) {
                $topics = array_merge($topics, $subj_topics);
            }
        }
    }
}

if (!$topics) {
    $topics = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Content - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (EXPLORE PAGE)
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

        .card h1, .card h2, .card h3 {
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

        .card h3 {
            font-size: 20px;
        }

        .card h1 i, .card h2 i, .card h3 i {
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
           BADGES
           ============================================================ */

        .primary-badge, .secondary-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .primary-badge {
            background: var(--gradient-primary);
            color: white;
        }

        .secondary-badge {
            background: var(--gradient-secondary);
            color: white;
        }

        /* ============================================================
           FILTER FORM
           ============================================================ */

        .filter-form {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-700);
        }

        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: var(--transition);
            background: white;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        /* ============================================================
           TOPIC CARDS
           ============================================================ */

        .topic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .topic-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            text-decoration: none;
            color: var(--gray-900);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .topic-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .topic-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
        }

        .topic-card:hover:before {
            transform: scaleX(1);
        }

        .topic-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--gray-900);
            line-height: 1.4;
        }

        .topic-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 14px;
            color: var(--gray-600);
        }

        .topic-subject {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .topic-preview {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.5;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .topic-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ============================================================
           CLASS CARDS
           ============================================================ */

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .class-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            text-decoration: none;
            color: var(--gray-900);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .class-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .class-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
        }

        .class-card:hover:before {
            transform: scaleX(1);
        }

        .class-card-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .class-card-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin-top: 4px;
            font-weight: 500;
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

        .tip-list {
            list-style: none;
        }

        .tip-list li {
            margin: 12px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .tip-list i {
            color: var(--accent);
            margin-top: 2px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            background: white;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        .stat-item:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
        }

        .stat-item:hover:before {
            transform: scaleX(1);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
        }

        .search-card {
            background: #f0f6ff;
            border-left: 4px solid var(--accent);
        }

        /* ============================================================
           BUTTONS
           ============================================================ */

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
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
            padding: 8px 12px;
            font-size: 12px;
        }

        /* ============================================================
           SEARCH FORM
           ============================================================ */

        .search-form {
            display: flex;
            gap: 0;
            margin-top: 8px;
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius) 0 0 var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: var(--transition-fast);
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .search-form input[type="submit"] {
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 12px 20px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-form input[type="submit"]:hover {
            background: var(--accent-hover);
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
            .class-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
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
            .topic-grid, .class-grid {
                grid-template-columns: 1fr;
            }
            .filter-form {
                flex-direction: column;
            }
            .filter-group {
                min-width: 100%;
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
                <a href="courses.php"><i class="fas fa-university"></i> University</a>
                <a href="#" class="active"><i class="fas fa-search"></i> Explore</a>
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php"><i class="fas fa-user-circle"></i> Account</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <a href="search.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </a>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <!-- HEADER -->
        <div class="card fade-in">
            <h1><i class="fas fa-compass"></i> Explore Content</h1>
            <p>Browse topics by class and subject. Use filters to find exactly what you're looking for.</p>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <!-- FILTER FORM -->
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-filter"></i> Filter Content</h2>
                    <form method="get" class="filter-form">
                        <div class="filter-group">
                            <label class="filter-label" for="class_id">Class</label>
                            <select name="class_id" id="class_id" class="filter-select" onchange="this.form.submit()">
                                <option value="0">-- Select a Class --</option>
                                <optgroup label="Primary School">
                                    <?php foreach ($primary_classes as $cls): ?>
                                        <option value="<?php echo $cls['id']; ?>" <?php echo $filter_class === $cls['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cls['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Secondary School">
                                    <?php foreach ($secondary_classes as $cls): ?>
                                        <option value="<?php echo $cls['id']; ?>" <?php echo $filter_class === $cls['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cls['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>

                        <?php if ($filter_class > 0 && !empty($filtered_subjects)): ?>
                            <div class="filter-group">
                                <label class="filter-label" for="subject_id">Subject</label>
                                <select name="subject_id" id="subject_id" class="filter-select" onchange="this.form.submit()">
                                    <option value="0">-- All Subjects --</option>
                                    <?php foreach ($filtered_subjects as $subj): ?>
                                        <option value="<?php echo $subj['id']; ?>" <?php echo $filter_subject === $subj['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subj['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($filter_class > 0): ?>
                            <div class="filter-group">
                                <a href="explore.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- CONTENT DISPLAY -->
                <?php if ($filter_class > 0): ?>
                    <div class="card fade-in delay-2">
                        <?php
                        $class = cned_get_class($filter_class);
                        $level = get_level($class['level_id']);
                        $badge = ($level['id'] === 1) ? '<span class="primary-badge">PRIMARY</span>' : '<span class="secondary-badge">SECONDARY</span>';
                        ?>
                        <h2><?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?> Content</h2>

                        <?php if (empty($topics)): ?>
                            <div style="text-align: center; padding: 40px 20px; color: var(--gray-500);">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <h3 style="margin-bottom: 8px;">No Topics Found</h3>
                                <p>No topics found for the selected filters. Try adjusting your filters or browse classes below.</p>
                            </div>
                        <?php else: ?>
                            <div class="topic-grid">
                                <?php foreach ($topics as $topic): 
                                    $subj = get_subject($topic['subject_id']);
                                ?>
                                    <a href="read_topic.php?id=<?php echo $topic['id']; ?>" class="topic-card fade-in">
                                        <div class="topic-title"><?php echo htmlspecialchars($topic['title']); ?></div>
                                        <div class="topic-meta">
                                            <div class="topic-subject">
                                                <i class="fas fa-book"></i>
                                                <?php echo htmlspecialchars($subj['name']); ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($topic['content'])): ?>
                                            <div class="topic-preview">
                                                <?php 
                                                $preview = strip_tags($topic['content']);
                                                echo htmlspecialchars(substr($preview, 0, 120)) . (strlen($preview) > 120 ? '...' : '');
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="topic-actions">
                                            <div class="topic-duration">
                                                <i class="far fa-clock"></i>
                                                <?php
                                                $word_count = str_word_count(strip_tags($topic['content']));
                                                $reading_time = ceil($word_count / 200);
                                                if ($reading_time < 1) $reading_time = 1;
                                                echo $reading_time . ' min read';
                                                ?>
                                            </div>
                                            <div style="color: var(--accent); font-weight: 600; font-size: 14px;">
                                                Read <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- BROWSE CLASSES -->
                    <div class="card fade-in delay-2">
                        <h2><i class="fas fa-layer-group"></i> Browse Classes</h2>
                        <p>Select a class above to view all topics for that class, or browse classes directly:</p>
                        
                        <h3><span class="primary-badge">PRIMARY SCHOOL</span></h3>
                        <div class="class-grid">
                            <?php foreach ($primary_classes as $cls): ?>
                                <a href="explore.php?class_id=<?php echo $cls['id']; ?>" class="class-card fade-in">
                                    <div class="class-card-title"><?php echo htmlspecialchars($cls['name']); ?></div>
                                    <div class="class-card-subtitle">Explore Topics</div>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <h3 style="margin-top: 30px;"><span class="secondary-badge">SECONDARY SCHOOL</span></h3>
                        <div class="class-grid">
                            <?php foreach ($secondary_classes as $cls): ?>
                                <a href="explore.php?class_id=<?php echo $cls['id']; ?>" class="class-card fade-in delay-1">
                                    <div class="class-card-title"><?php echo htmlspecialchars($cls['name']); ?></div>
                                    <div class="class-card-subtitle">Explore Topics</div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card fade-in delay-1">
                    <h3><i class="fas fa-lightbulb"></i> Exploration Tips</h3>
                    <ul class="tip-list">
                        <li><i class="fas fa-check-circle"></i> Select a class to view all topics</li>
                        <li><i class="fas fa-check-circle"></i> Narrow down by selecting a subject</li>
                        <li><i class="fas fa-check-circle"></i> Click on any topic to read full content</li>
                        <li><i class="fas fa-check-circle"></i> Use search for quick lookups</li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-2">
                    <h3><i class="fas fa-chart-bar"></i> Content Statistics</h3>
                    <?php
                    $all_topics = [];
                    foreach ($all_classes as $cls) {
                        $subjects = get_subjects_by_class($cls['id']);
                        foreach ($subjects as $subj) {
                            $all_topics = array_merge($all_topics, get_topics_by_subject($subj['id']));
                        }
                    }
                    ?>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($all_topics); ?></div>
                            <div class="stat-label">Total Topics</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($all_classes); ?></div>
                            <div class="stat-label">Classes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">
                                <?php
                                $total_subjects = 0;
                                foreach ($all_classes as $cls) {
                                    $subjects = get_subjects_by_class($cls['id']);
                                    $total_subjects += count($subjects);
                                }
                                echo $total_subjects;
                                ?>
                            </div>
                            <div class="stat-label">Subjects</div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card search-card fade-in delay-3">
                    <h3><i class="fas fa-search"></i> Search Content</h3>
                    <form action="search.php" method="get" class="search-form">
                        <input type="text" name="q" placeholder="Search topics, subjects...">
                        <input type="submit" value="Go">
                    </form>
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .topic-card, .class-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>