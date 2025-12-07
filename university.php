<?php
require_once __DIR__ . '/includes/functions.php';
$courses = get_courses();
include __DIR__ . '/includes/header.php';

// Calculate stats for the dashboard
$total_courses = count($courses);
$total_units = 0;
foreach ($courses as $course) {
    $units = get_units_by_course($course['id']);
    $total_units += count($units);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Courses - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (ULTRA ENHANCED)
           Elegant • Modern • Interactive • With Premium Graphics
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
            --gradient-hero: linear-gradient(135deg, rgba(15, 98, 254, 0.85) 0%, rgba(61, 141, 255, 0.85) 100%);
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
           NAVBAR (Premium)
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
           HERO SECTION (Premium with University Background)
           ============================================================ */

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .clean-hero {
            position: relative;
            text-align: center;
            background: linear-gradient(rgba(15, 98, 254, 0.7), rgba(61, 141, 255, 0.7)), 
                        url('assets/university-hero.jpg');
            background-size: cover;
            background-position: center;
            padding: 140px 20px;
            border-radius: var(--radius-xl);
            margin-bottom: 60px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            color: white;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .clean-hero h1 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: fadeInUp 0.8s ease-out;
        }

        .clean-hero p {
            font-size: 22px;
            margin-bottom: 40px;
            opacity: 0.9;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-number {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .hero-stat-label {
            font-size: 16px;
            opacity: 0.9;
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
           CARDS (Premium)
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
            transform: translateY(-8px);
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

        .card h2 {
            margin-bottom: 16px;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card h2 i {
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

        .card h3 {
            margin-bottom: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card h3 i {
            color: var(--accent);
            font-size: 18px;
        }

        /* Minimal cards inside grids */
        .simple-card {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 28px;
            border-radius: var(--radius-lg);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .simple-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
        }

        .simple-card:before {
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

        .simple-card:hover:before {
            transform: scaleX(1);
        }

        /* ============================================================
           GRID SYSTEM
           ============================================================ */

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }

        /* ============================================================
           COURSE CARDS (Premium)
           ============================================================ */

        .course-card {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 28px;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-900);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: block;
        }

        .course-card:before {
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

        .course-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
            text-decoration: none;
            color: var(--gray-900);
        }

        .course-card:hover:before {
            transform: scaleX(1);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .course-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }

        .course-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        .course-description {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .course-units {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--gray-600);
        }

        .course-units i {
            color: var(--accent);
        }

        .course-action {
            color: var(--accent);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ============================================================
           BUTTONS (Premium)
           ============================================================ */

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn:hover:after {
            animation: ripple 1s ease-out;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 10px 25px rgba(15, 98, 254, 0.4);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: white;
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .btn-sm {
            padding: 12px 20px;
            font-size: 14px;
        }

        /* ============================================================
           SEARCH BOX (Premium)
           ============================================================ */

        .search-box {
            display: flex;
            gap: 0;
            margin-top: 8px;
        }

        .search-box input {
            flex: 1;
            padding: 14px 18px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius) 0 0 var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            transition: var(--transition-fast);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .search-box button {
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 14px 22px;
        }

        /* ============================================================
           STATS (Premium)
           ============================================================ */

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 24px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            background: white;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
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
            font-size: 32px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* ============================================================
           SIDEBAR WIDGETS (Premium)
           ============================================================ */

        .clean-list {
            list-style: none;
            margin-top: 10px;
        }

        .clean-list li {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--transition-fast);
            padding: 8px 0;
        }

        .clean-list li:hover {
            transform: translateX(8px);
        }

        .clean-list a {
            text-decoration: none;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .clean-list a:hover {
            color: var(--accent);
        }

        .clean-list i {
            color: var(--accent);
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15, 98, 254, 0.1);
            border-radius: 6px;
        }

        /* ============================================================
           ANIMATIONS
           ============================================================ */

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
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
            .grid-4 {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 900px) {
            .navbar {
                padding: 0 20px;
            }
            .nav-links {
                gap: 20px;
            }
            .clean-hero {
                padding: 100px 20px;
            }
            .clean-hero h1 {
                font-size: 42px;
            }
            .clean-hero p {
                font-size: 18px;
            }
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            .hero-stats {
                flex-wrap: wrap;
                gap: 20px;
            }
            .content-wrapper {
                padding: 20px;
            }
            .grid-2, .grid-3, .grid-4 {
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
            .clean-hero h1 {
                font-size: 36px;
            }
            .hero-stat-number {
                font-size: 28px;
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
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <div class="notification-count">3</div>
                </div>
                <a href="#" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <div class="hero-container">
        <div class="clean-hero">
            <div class="hero-content">
                <h1>University Excellence</h1>
                <p>Access comprehensive degree programs, course materials, and advanced learning resources for higher education.</p>
                <div class="hero-buttons">
                    <a href="#courses" class="btn btn-primary">
                        <i class="fas fa-book"></i> Browse Courses
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Download Catalog
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number"><?php echo $total_courses; ?>+</div>
                        <div class="hero-stat-label">Courses</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number"><?php echo $total_units; ?>+</div>
                        <div class="hero-stat-label">Units</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">24/7</div>
                        <div class="hero-stat-label">Access</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">100%</div>
                        <div class="hero-stat-label">Digital</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="grid">
            <!-- MAIN -->
            <main class="main">
                <!-- FEATURED COURSES -->
                <div class="card fade-in">
                    <h2><i class="fas fa-star"></i> Featured Degree Programs</h2>
                    <div class="grid-2">
                        <div class="simple-card fade-in">
                            <h3><i class="fas fa-laptop-code"></i> Computer Science</h3>
                            <p>Master programming, algorithms, data structures, and software development with hands-on projects and real-world applications.</p>
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 75%"></div>
                                </div>
                                <div class="progress-text">
                                    <span>75% Complete</span>
                                    <span>15/20 Units</span>
                                </div>
                            </div>
                            <a href="units.php?course_id=1" class="btn btn-primary btn-sm">Continue Learning</a>
                        </div>
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-balance-scale"></i> Business Administration</h3>
                            <p>Learn management, marketing, finance, and entrepreneurship with case studies and industry insights.</p>
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 45%"></div>
                                </div>
                                <div class="progress-text">
                                    <span>45% Complete</span>
                                    <span>9/20 Units</span>
                                </div>
                            </div>
                            <a href="units.php?course_id=2" class="btn btn-primary btn-sm">Continue Learning</a>
                        </div>
                    </div>
                </div>

                <!-- ALL COURSES -->
                <div class="card fade-in" id="courses">
                    <h2><i class="fas fa-book"></i> All University Courses</h2>
                    <p>Browse our comprehensive catalog of degree programs and access detailed course materials.</p>

                    <div class="search-box">
                        <input type="text" placeholder="Search courses, departments, or keywords..." id="courseSearch">
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <?php if (!empty($courses)): ?>
                        <div class="grid-2" style="margin-top: 24px;">
                            <?php foreach ($courses as $course): 
                                // Get course units for metadata
                                $units = get_units_by_course($course['id']);
                                $unit_count = count($units);
                                
                                // Determine course icon based on name or category
                                $course_icon = "fas fa-book";
                                $course_name_lower = strtolower($course['name']);
                                if (strpos($course_name_lower, 'computer') !== false || strpos($course_name_lower, 'programming') !== false) {
                                    $course_icon = "fas fa-laptop-code";
                                } elseif (strpos($course_name_lower, 'math') !== false || strpos($course_name_lower, 'calculus') !== false) {
                                    $course_icon = "fas fa-calculator";
                                } elseif (strpos($course_name_lower, 'physics') !== false) {
                                    $course_icon = "fas fa-atom";
                                } elseif (strpos($course_name_lower, 'chemistry') !== false) {
                                    $course_icon = "fas fa-flask";
                                } elseif (strpos($course_name_lower, 'biology') !== false || strpos($course_name_lower, 'medical') !== false) {
                                    $course_icon = "fas fa-dna";
                                } elseif (strpos($course_name_lower, 'business') !== false || strpos($course_name_lower, 'economics') !== false) {
                                    $course_icon = "fas fa-chart-line";
                                } elseif (strpos($course_name_lower, 'history') !== false) {
                                    $course_icon = "fas fa-monument";
                                } elseif (strpos($course_name_lower, 'language') !== false || strpos($course_name_lower, 'english') !== false) {
                                    $course_icon = "fas fa-language";
                                } elseif (strpos($course_name_lower, 'psychology') !== false) {
                                    $course_icon = "fas fa-brain";
                                }
                            ?>
                                <a href="units.php?course_id=<?php echo $course['id']; ?>" class="course-card fade-in delay-1">
                                    <div class="course-header">
                                        <h3 class="course-name"><?php echo htmlspecialchars($course['name']); ?></h3>
                                        <div class="course-icon">
                                            <i class="<?php echo $course_icon; ?>"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="course-description">
                                        <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                                    </div>
                                    
                                    <div class="course-meta">
                                        <div class="course-units">
                                            <i class="fas fa-layer-group"></i>
                                            <span><?php echo $unit_count; ?> <?php echo $unit_count === 1 ? 'Unit' : 'Units'; ?></span>
                                        </div>
                                        <div class="course-action">
                                            View Course <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px; color: var(--gray-500);">
                            <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                            <h3 style="margin-bottom: 12px; color: var(--gray-600);">No Courses Available</h3>
                            <p>No university courses have been added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- UNIVERSITY RESOURCES -->
                <div class="card fade-in delay-2">
                    <h2><i class="fas fa-tools"></i> University Resources</h2>
                    <p>Access additional tools and resources to enhance your university learning experience.</p>

                    <div class="grid-2">
                        <div class="simple-card fade-in">
                            <h3><i class="fas fa-graduation-cap"></i> Academic Calendar</h3>
                            <p>Important dates, deadlines, semester schedules, and academic events for proper planning.</p>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary btn-sm">View Calendar</a>
                            </div>
                        </div>
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-book-open"></i> Research Library</h3>
                            <p>Access to academic journals, research papers, publications, and digital library resources.</p>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary btn-sm">Access Library</a>
                            </div>
                        </div>
                        <div class="simple-card fade-in">
                            <h3><i class="fas fa-users"></i> Study Groups</h3>
                            <p>Connect with peers for collaborative learning, group discussions, and project work.</p>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary btn-sm">Join Groups</a>
                            </div>
                        </div>
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-chart-line"></i> Career Services</h3>
                            <p>Internship opportunities, career guidance, job placement, and professional development.</p>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary btn-sm">Explore Careers</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="card fade-in">
                    <h3><i class="fas fa-search"></i> Search Courses</h3>
                    <div class="search-box">
                        <input type="text" placeholder="Search courses, units...">
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="card fade-in delay-1">
                    <h3><i class="fas fa-filter"></i> Filter by Department</h3>
                    <ul class="clean-list">
                        <li><a href="#"><i class="fas fa-laptop-code"></i> Computer Science</a></li>
                        <li><a href="#"><i class="fas fa-balance-scale"></i> Business</a></li>
                        <li><a href="#"><i class="fas fa-flask"></i> Sciences</a></li>
                        <li><a href="#"><i class="fas fa-heartbeat"></i> Medicine</a></li>
                        <li><a href="#"><i class="fas fa-gavel"></i> Law</a></li>
                        <li><a href="#"><i class="fas fa-paint-brush"></i> Arts</a></li>
                    </ul>
                </div>

                <div class="card fade-in delay-1">
                    <h3><i class="fas fa-bolt"></i> Quick Access</h3>
                    <ul class="clean-list">
                        <li><a href="courses.php"><i class="fas fa-book"></i> All Courses</a></li>
                        <li><a href="resources.php"><i class="fas fa-download"></i> Download Resources</a></li>
                        <li><a href="exams.php"><i class="fas fa-file-alt"></i> Past Papers</a></li>
                        <li><a href="timetable.php"><i class="fas fa-calendar"></i> Academic Timetable</a></li>
                        <li><a href="lecturers.php"><i class="fas fa-chalkboard-teacher"></i> Lecturers</a></li>
                        <li><a href="support.php"><i class="fas fa-headset"></i> Student Support</a></li>
                    </ul>
                </div>

                <div class="card fade-in delay-2">
                    <h3><i class="fas fa-chart-bar"></i> University Stats</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $total_courses; ?></div>
                            <div class="stat-label">Courses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $total_units; ?></div>
                            <div class="stat-label">Units</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Access</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Digital</div>
                        </div>
                    </div>
                </div>

                <div class="card fade-in delay-2">
                    <h3><i class="fas fa-bell"></i> University News</h3>
                    <ul class="clean-list">
                        <li><a href="#"><i class="fas fa-newspaper"></i> New Engineering Program</a></li>
                        <li><a href="#"><i class="fas fa-calendar"></i> Semester Registration Open</a></li>
                        <li><a href="#"><i class="fas fa-trophy"></i> Research Grant Awarded</a></li>
                    </ul>
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
            const elementsToAnimate = document.querySelectorAll('.card, .simple-card, .course-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });

            // Simple search functionality
            const courseSearch = document.getElementById('courseSearch');
            const courseCards = document.querySelectorAll('.course-card');
            
            courseSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                
                courseCards.forEach(card => {
                    const courseName = card.querySelector('.course-name').textContent.toLowerCase();
                    const courseDescription = card.querySelector('.course-description').textContent.toLowerCase();
                    
                    if (courseName.includes(searchTerm) || courseDescription.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>