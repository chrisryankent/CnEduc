<?php
require_once __DIR__ . '/includes/functions.php';

// Ensure we have a class_id to show subjects for
if (!isset($_GET['class_id'])) {
    header('Location: classes.php');
    exit;
}

$class_id = (int) $_GET['class_id'];
$class = cned_get_class($class_id);
if (!$class) {
    echo 'Class not found';
    exit;
}

$subjects = get_subjects_by_class($class_id);

// Derive level from the class record
$level = null;
if (!empty($class['level_id'])) {
    $level = get_level((int) $class['level_id']);
}
if (!$level) {
    // Provide a safe fallback to avoid undefined variable warnings
    $level = [ 'id' => 0, 'name' => 'Unknown' ];
}

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
    <title><?php echo htmlspecialchars($class['name']); ?> Subjects - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (SUBJECTS PAGE)
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
           SUBJECT CARDS
           ============================================================ */

        .subject-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .subject-card {
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

        .subject-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .subject-card:before {
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

        .subject-card:hover:before {
            transform: scaleX(1);
        }

        .subject-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        .subject-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--gray-900);
        }

        .subject-meta {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            font-size: 14px;
            color: var(--gray-600);
        }

        .topic-count {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .view-topics {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--accent);
            font-weight: 600;
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

        .tip-card {
            background: #f0f6ff;
            border-left: 4px solid var(--accent);
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
            .subject-grid {
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
                <a href="#" class="active"><i class="fas fa-book"></i> Curriculum</a>
                <a href="#"><i class="fas fa-university"></i> University</a>
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

    <div class="content-wrapper">
        <!-- BREADCRUMB AND HEADER -->
        <div class="card fade-in">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a>
                <span class="separator">/</span>
                <span><?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?></span>
            </div>
            
            <h1><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($class['name']); ?> Subjects</h1>
            <p><?php echo htmlspecialchars($class['description']); ?></p>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-graduation-cap"></i> Available Subjects</h2>
                    <p>Select a subject to explore its topics and learning materials:</p>
                    
                    <div class="subject-grid">
                        <?php foreach ($subjects as $subject): 
                            $topics = get_topics_by_subject($subject['id']);
                            $topic_count = count($topics);
                            
                            // Get subject icon based on name
                            $subject_icon = "fas fa-book";
                            $subject_name_lower = strtolower($subject['name']);
                            
                            if (strpos($subject_name_lower, 'math') !== false) {
                                $subject_icon = "fas fa-calculator";
                            } elseif (strpos($subject_name_lower, 'science') !== false) {
                                $subject_icon = "fas fa-flask";
                            } elseif (strpos($subject_name_lower, 'english') !== false) {
                                $subject_icon = "fas fa-language";
                            } elseif (strpos($subject_name_lower, 'history') !== false) {
                                $subject_icon = "fas fa-monument";
                            } elseif (strpos($subject_name_lower, 'geography') !== false) {
                                $subject_icon = "fas fa-globe-africa";
                            } elseif (strpos($subject_name_lower, 'art') !== false) {
                                $subject_icon = "fas fa-palette";
                            } elseif (strpos($subject_name_lower, 'music') !== false) {
                                $subject_icon = "fas fa-music";
                            } elseif (strpos($subject_name_lower, 'physical') !== false) {
                                $subject_icon = "fas fa-running";
                            }
                        ?>
                            <a href="topics.php?subject_id=<?php echo $subject['id']; ?>" class="subject-card fade-in">
                                <div class="subject-icon">
                                    <i class="<?php echo $subject_icon; ?>"></i>
                                </div>
                                <div class="subject-name"><?php echo htmlspecialchars($subject['name']); ?></div>
                                <div class="subject-meta">
                                    <div class="topic-count">
                                        <i class="fas fa-list-ul"></i>
                                        <?php echo $topic_count; ?> topic<?php echo $topic_count !== 1 ? 's' : ''; ?>
                                    </div>
                                    <div class="view-topics">
                                        View Topics <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card fade-in delay-1">
                    <h3><i class="fas fa-compass"></i> Navigation</h3>
                    <ul class="navigation-list">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="classes.php?level_id=<?php echo $level['id']; ?>"><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($level['name']); ?> Classes</a></li>
                        <li><a href="explore.php"><i class="fas fa-search"></i> Explore All Content</a></li>
                        <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-2">
                    <h3><i class="fas fa-info-circle"></i> Class Information</h3>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <div style="font-size: 36px; color: var(--accent);">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 18px;"><?php echo htmlspecialchars($class['name']); ?></div>
                            <div style="font-size: 14px; color: var(--gray-600);"><?php echo htmlspecialchars($level['name']); ?> Level</div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; background: var(--gray-50); padding: 12px; border-radius: var(--radius);">
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: var(--accent);"><?php echo count($subjects); ?></div>
                            <div style="font-size: 12px; color: var(--gray-600);">Subjects</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: var(--accent);">
                                <?php
                                $total_topics = 0;
                                foreach ($subjects as $subject) {
                                    $topics = get_topics_by_subject($subject['id']);
                                    $total_topics += count($topics);
                                }
                                echo $total_topics;
                                ?>
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Total Topics</div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card tip-card fade-in delay-3">
                    <h3><i class="fas fa-lightbulb"></i> Learning Tip</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Start with subjects you find most interesting to build momentum. Each topic is arranged from foundational concepts to more advanced material, so it's best to follow the sequence for optimal learning.
                    </p>
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .subject-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>