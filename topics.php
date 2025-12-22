<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$is_logged_in = is_user_logged_in();

// Ensure we have a subject_id to show topics for
if (!isset($_GET['subject_id'])) {
    header('Location: classes.php');
    exit;
}

$subject_id = (int) $_GET['subject_id'];
$subject = get_subject($subject_id);
if (!$subject) {
    echo 'Subject not found';
    exit;
}

$class = cned_get_class($subject['class_id']);
if (!$class) {
    echo 'Class not found';
    exit;
}

// Derive level from the class record
$level = null;
if (!empty($class['level_id'])) {
    $level = get_level((int) $class['level_id']);
}
if (!$level) {
    // Provide a safe fallback to avoid undefined variable warnings
    $level = [ 'id' => 0, 'name' => 'Unknown' ];
}

$topics = get_topics_by_subject($subject_id);

$badge = ($level['id'] === 1)
    ? '<span class="primary-badge">PRIMARY</span>'
    : ( ($level['id'] === 2)
        ? '<span class="secondary-badge">SECONDARY</span>'
        : '<span class="university-badge">UNIVERSITY</span>'
      );

include __DIR__ . '/includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject['name']); ?> Topics - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (TOPICS PAGE)
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

        .topic-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .topic-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--gray-900);
            line-height: 1.4;
        }

        .topic-preview {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.5;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .topic-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--gray-600);
        }

        .read-topic {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--accent);
            font-weight: 600;
        }

        .topic-duration {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
        }

        /* ============================================================
           EMPTY STATE
           ============================================================ */

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: var(--gray-300);
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 8px;
            color: var(--gray-600);
        }

        .empty-state p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
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

        .subject-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .subject-icon {
            font-size: 36px;
            color: var(--accent);
        }

        .subject-details {
            flex: 1;
        }

        .subject-name {
            font-weight: 700;
            font-size: 18px;
        }

        .subject-meta {
            font-size: 14px;
            color: var(--gray-600);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
        }

        .stat-item {
            text-align: center;
            padding: 12px;
            background: var(--gray-50);
            border-radius: var(--radius);
        }

        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            font-size: 12px;
            color: var(--gray-600);
        }

        .tip-card {
            background: #f0f6ff;
            border-left: 4px solid var(--accent);
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
            .topic-grid {
                grid-template-columns: 1fr;
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
            .stats-grid {
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
                <a href="curriculum.php" class="active"><i class="fas fa-book"></i> Curriculum</a>
                <a href="university.php"><i class="fas fa-university"></i> University</a>
                <a href="explore.php"><i class="fas fa-search"></i> Explore</a>
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
        <!-- BREADCRUMB AND HEADER -->
        <div class="card fade-in">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a>
                <span class="separator">/</span>
                <a href="subjects.php?class_id=<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></a>
                <span class="separator">/</span>
                <span><?php echo htmlspecialchars($subject['name']); ?></span>
            </div>
            
            <h1><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($subject['name']); ?> Topics</h1>
            <div style="display: flex; align-items: center; gap: 12px;">
                <?php echo $badge; ?>
                <span style="font-size: 14px; color: var(--gray-600);">
                    <?php echo htmlspecialchars($class['name']); ?> • 
                    <?php echo count($topics); ?> topic<?php echo count($topics) !== 1 ? 's' : ''; ?>
                </span>
            </div>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-list-ul"></i> Topics to Learn</h2>
                    <p>Explore all topics in this subject. Click on any topic to start learning.</p>
                    
                    <?php if (empty($topics)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No Topics Available</h3>
                            <p>There are no topics available for this subject yet. Check back later for updates.</p>
                        </div>
                    <?php else: ?>
                        <div class="topic-grid">
                            <?php foreach ($topics as $index => $topic): 
                                // Calculate estimated reading time (approx 200 words per minute)
                                $word_count = str_word_count(strip_tags($topic['content']));
                                $reading_time = ceil($word_count / 200);
                                if ($reading_time < 1) $reading_time = 1;
                            ?>
                                <a href="read_topic.php?id=<?php echo $topic['id']; ?>" class="topic-card fade-in">
                                    <div class="topic-number"><?php echo $index + 1; ?></div>
                                    <div class="topic-title"><?php echo htmlspecialchars($topic['title']); ?></div>
                                    <?php if (!empty($topic['content'])): ?>
                                        <div class="topic-preview">
                                            <?php 
                                            $preview = strip_tags($topic['content']);
                                            echo htmlspecialchars(substr($preview, 0, 120)) . (strlen($preview) > 120 ? '...' : '');
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="topic-meta">
                                        <div class="topic-duration">
                                            <i class="far fa-clock"></i>
                                            <?php echo $reading_time; ?> min read
                                        </div>
                                        <div class="read-topic">
                                            Read Topic <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card fade-in delay-1">
                    <h3><i class="fas fa-compass"></i> Navigation</h3>
                    <ul class="navigation-list">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="classes.php?level_id=<?php echo $level['id']; ?>"><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($level['name']); ?></a></li>
                        <li><a href="subjects.php?class_id=<?php echo $class['id']; ?>"><i class="fas fa-book"></i> All Subjects</a></li>
                        <li><a href="explore.php"><i class="fas fa-search"></i> Explore All Content</a></li>
                        <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-2">
                    <h3><i class="fas fa-info-circle"></i> Subject Information</h3>
                    <div class="subject-info">
                        <div class="subject-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="subject-details">
                            <div class="subject-name"><?php echo htmlspecialchars($subject['name']); ?></div>
                            <div class="subject-meta">
                                <?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($topics); ?></div>
                            <div class="stat-label">Topics</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">
                                <?php
                                $total_reading_time = 0;
                                foreach ($topics as $topic) {
                                    $word_count = str_word_count(strip_tags($topic['content']));
                                    $reading_time = ceil($word_count / 200);
                                    $total_reading_time += $reading_time;
                                }
                                echo $total_reading_time;
                                ?>
                            </div>
                            <div class="stat-label">Min Read</div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card tip-card fade-in delay-3">
                    <h3><i class="fas fa-lightbulb"></i> Learning Tip</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Topics are arranged from basic to advanced. Start with the first topic and work through them in order for the best learning results. Take notes and review previous topics regularly.
                    </p>
                </div>

                <div class="sidebar-card fade-in delay-3">
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .topic-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>