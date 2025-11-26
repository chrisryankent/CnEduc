<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CnEduc - Premium Educational Platform</title>
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
           HERO SECTION (Premium with Student Background)
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
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
           CLASS CARDS (Premium)
           ============================================================ */

        .class-card {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 28px;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-900);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
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

        .class-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .class-card:hover:before {
            transform: scaleX(1);
        }

        .class-card-title {
            font-size: 32px;
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
           NEW FEATURES STYLING
           ============================================================ */

        /* Progress Bars */
        .progress-container {
            margin: 20px 0;
        }

        .progress-bar {
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-text {
            display: flex;
            justify-content: between;
            font-size: 14px;
            color: var(--gray-600);
        }

        /* Featured Courses Carousel */
        .carousel {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-lg);
            margin: 20px 0;
        }

        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-item {
            min-width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .carousel-control {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-300);
            cursor: pointer;
            transition: var(--transition);
        }

        .carousel-control.active {
            background: var(--accent);
            transform: scale(1.2);
        }

        /* Calendar Widget */
        .calendar {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            text-align: center;
            padding: 8px 5px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-500);
        }

        .calendar-date {
            text-align: center;
            padding: 8px 5px;
            font-size: 14px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .calendar-date:hover {
            background: var(--gray-100);
        }

        .calendar-date.active {
            background: var(--gradient-primary);
            color: white;
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell i {
            font-size: 20px;
            color: var(--gray-700);
        }

        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-secondary);
            color: white;
            font-size: 12px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Achievement Badges */
        .badges-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .badge {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-primary);
            color: white;
            font-size: 24px;
            box-shadow: var(--shadow-md);
            position: relative;
        }

        .badge.locked {
            background: var(--gray-300);
            color: var(--gray-500);
        }

        /* Study Timer */
        .timer-container {
            background: var(--gradient-primary);
            color: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .timer-display {
            font-size: 36px;
            font-weight: 700;
            margin: 15px 0;
        }

        .timer-controls {
            display: flex;
            gap: 10px;
            justify-content: center;
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
            <a href="#" class="nav-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>CnEduc</span>
            </a>
            <div class="nav-links">
                <a href="#" class="active"><i class="fas fa-home"></i> Home</a>
                <a href="#"><i class="fas fa-book"></i> Curriculum</a>
                <a href="#"><i class="fas fa-university"></i> University</a>
                <a href="#"><i class="fas fa-search"></i> Explore</a>
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
                <h1>Empowering Education in Uganda</h1>
                <p>Your complete companion for Primary, Secondary, and University learning with premium resources.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-play-circle"></i> Get Started
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-book-open"></i> Browse Courses
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number">14+</div>
                        <div class="hero-stat-label">Classes</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">128+</div>
                        <div class="hero-stat-label">Topics</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">12+</div>
                        <div class="hero-stat-label">Courses</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">5,000+</div>
                        <div class="hero-stat-label">Students</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="grid">
            <!-- MAIN -->
            <main class="main">
                <!-- FEATURED COURSES CAROUSEL -->
                <div class="card fade-in">
                    <h2><i class="fas fa-star"></i> Featured Courses</h2>
                    <div class="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item">
                                <div class="simple-card">
                                    <h3><i class="fas fa-laptop-code"></i> Advanced Web Development</h3>
                                    <p>Master modern web technologies including React, Node.js, and MongoDB with real-world projects.</p>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 65%"></div>
                                        </div>
                                        <div class="progress-text">
                                            <span>65% Complete</span>
                                            <span>12/18 Lessons</span>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-sm">Continue Learning</a>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="simple-card">
                                    <h3><i class="fas fa-chart-line"></i> Data Science Fundamentals</h3>
                                    <p>Learn data analysis, visualization, and machine learning basics with Python and popular libraries.</p>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 30%"></div>
                                        </div>
                                        <div class="progress-text">
                                            <span>30% Complete</span>
                                            <span>6/20 Lessons</span>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-sm">Continue Learning</a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-controls">
                            <div class="carousel-control active"></div>
                            <div class="carousel-control"></div>
                        </div>
                    </div>
                </div>

                <!-- SCHOOL CURRICULUM -->
                <div class="card fade-in">
                    <h2><i class="fas fa-school"></i> School Curriculum</h2>
                    <p>Explore levels and subjects organized by class with premium educational resources.</p>

                    <div class="grid-2">
                        <!-- PRIMARY -->
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-chalkboard-teacher"></i> Primary</h3>
                            <p>Comprehensive content for P1 – P7 with interactive learning materials.</p>
                            <ul class="clean-list">
                                <li><a href="classes.php?level_id=1"><i class="fas fa-list"></i> Browse Primary Classes</a></li>
                                <li><a href="subjects.php?class_id=1"><i class="fas fa-book"></i> P1 Subjects</a></li>
                                <li><a href="subjects.php?class_id=2"><i class="fas fa-book"></i> P2 Subjects</a></li>
                                <li><a href="#"><i class="fas fa-download"></i> Download Resources</a></li>
                            </ul>
                        </div>

                        <!-- SECONDARY -->
                        <div class="simple-card fade-in delay-2">
                            <h3><i class="fas fa-user-graduate"></i> Secondary</h3>
                            <p>Advanced content for S1 – S6 with exam preparation materials.</p>
                            <ul class="clean-list">
                                <li><a href="classes.php?level_id=2"><i class="fas fa-list"></i> Browse Secondary Classes</a></li>
                                <li><a href="subjects.php?class_id=8"><i class="fas fa-book"></i> S1 Subjects</a></li>
                                <li><a href="subjects.php?class_id=9"><i class="fas fa-book"></i> S2 Subjects</a></li>
                                <li><a href="#"><i class="fas fa-file-alt"></i> Past Papers</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- UNIVERSITY COURSES -->
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-university"></i> University Courses</h2>
                    <p>Browse degree programs and their units with advanced learning materials.</p>

                    <div class="grid-2">
                        <div class="simple-card fade-in">
                            <h3><i class="fas fa-laptop-code"></i> Computer Science</h3>
                            <p>Learn programming, algorithms, data structures, and software development with hands-on projects.</p>
                            <div style="margin-top: 20px;">
                                <a href="units.php?course_id=1" class="btn btn-primary btn-sm">View Units</a>
                                <a href="#" class="btn btn-secondary btn-sm" style="margin-left: 10px;">Syllabus</a>
                            </div>
                        </div>
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-balance-scale"></i> Business Administration</h3>
                            <p>Management, marketing, finance, and entrepreneurship courses with case studies.</p>
                            <div style="margin-top: 20px;">
                                <a href="units.php?course_id=2" class="btn btn-primary btn-sm">View Units</a>
                                <a href="#" class="btn btn-secondary btn-sm" style="margin-left: 10px;">Syllabus</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STUDY TOOLS -->
                <div class="card fade-in delay-2">
                    <h2><i class="fas fa-tools"></i> Study Tools</h2>
                    <p>Enhance your learning experience with our interactive study tools.</p>

                    <div class="grid-2">
                        <div class="simple-card fade-in">
                            <h3><i class="fas fa-stopwatch"></i> Study Timer</h3>
                            <p>Boost productivity with focused study sessions using the Pomodoro technique.</p>
                            <div class="timer-container">
                                <div class="timer-display">25:00</div>
                                <div class="timer-controls">
                                    <button class="btn btn-secondary btn-sm">Start</button>
                                    <button class="btn btn-secondary btn-sm">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="simple-card fade-in delay-1">
                            <h3><i class="fas fa-trophy"></i> Your Achievements</h3>
                            <p>Track your learning progress and unlock achievements as you complete courses.</p>
                            <div class="badges-container">
                                <div class="badge">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="badge">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="badge locked">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="badge locked">
                                    <i class="fas fa-medal"></i>
                                </div>
                            </div>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary btn-sm">View All Achievements</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BROWSE BY CLASS -->
                <div class="card fade-in delay-2">
                    <h2><i class="fas fa-layer-group"></i> Browse by Class</h2>
                    <p>Select your class to access tailored educational content and resources.</p>

                    <div class="grid-4">
                        <a href="subjects.php?class_id=1" class="class-card fade-in">
                            <div class="class-card-title">P1</div>
                            <div class="class-card-subtitle">Primary</div>
                        </a>
                        <a href="subjects.php?class_id=2" class="class-card fade-in delay-1">
                            <div class="class-card-title">P2</div>
                            <div class="class-card-subtitle">Primary</div>
                        </a>
                        <a href="subjects.php?class_id=3" class="class-card fade-in delay-2">
                            <div class="class-card-title">P3</div>
                            <div class="class-card-subtitle">Primary</div>
                        </a>
                        <a href="subjects.php?class_id=4" class="class-card fade-in delay-3">
                            <div class="class-card-title">P4</div>
                            <div class="class-card-subtitle">Primary</div>
                        </a>
                        <a href="subjects.php?class_id=8" class="class-card fade-in">
                            <div class="class-card-title">S1</div>
                            <div class="class-card-subtitle">Secondary</div>
                        </a>
                        <a href="subjects.php?class_id=9" class="class-card fade-in delay-1">
                            <div class="class-card-title">S2</div>
                            <div class="class-card-subtitle">Secondary</div>
                        </a>
                        <a href="subjects.php?class_id=10" class="class-card fade-in delay-2">
                            <div class="class-card-title">S3</div>
                            <div class="class-card-subtitle">Secondary</div>
                        </a>
                        <a href="subjects.php?class_id=11" class="class-card fade-in delay-3">
                            <div class="class-card-title">S4</div>
                            <div class="class-card-subtitle">Secondary</div>
                        </a>
                    </div>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="card fade-in">
                    <h3><i class="fas fa-search"></i> Search</h3>
                    <div class="search-box">
                        <input type="text" placeholder="Search topics, units, courses...">
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="card fade-in delay-1">
                    <h3><i class="fas fa-calendar-alt"></i> Study Planner</h3>
                    <div class="calendar">
                        <div class="calendar-header">
                            <button class="btn btn-sm"><i class="fas fa-chevron-left"></i></button>
                            <span>June 2023</span>
                            <button class="btn btn-sm"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="calendar-grid">
                            <div class="calendar-day">Sun</div>
                            <div class="calendar-day">Mon</div>
                            <div class="calendar-day">Tue</div>
                            <div class="calendar-day">Wed</div>
                            <div class="calendar-day">Thu</div>
                            <div class="calendar-day">Fri</div>
                            <div class="calendar-day">Sat</div>
                            <!-- Dates would be dynamically generated in a real app -->
                            <div class="calendar-date">28</div>
                            <div class="calendar-date">29</div>
                            <div class="calendar-date">30</div>
                            <div class="calendar-date">31</div>
                            <div class="calendar-date">1</div>
                            <div class="calendar-date">2</div>
                            <div class="calendar-date">3</div>
                            <div class="calendar-date">4</div>
                            <div class="calendar-date">5</div>
                            <div class="calendar-date">6</div>
                            <div class="calendar-date active">7</div>
                            <div class="calendar-date">8</div>
                            <div class="calendar-date">9</div>
                            <div class="calendar-date">10</div>
                            <div class="calendar-date">11</div>
                            <div class="calendar-date">12</div>
                            <div class="calendar-date">13</div>
                            <div class="calendar-date">14</div>
                            <div class="calendar-date">15</div>
                            <div class="calendar-date">16</div>
                            <div class="calendar-date">17</div>
                            <div class="calendar-date">18</div>
                            <div class="calendar-date">19</div>
                            <div class="calendar-date">20</div>
                            <div class="calendar-date">21</div>
                            <div class="calendar-date">22</div>
                            <div class="calendar-date">23</div>
                            <div class="calendar-date">24</div>
                            <div class="calendar-date">25</div>
                            <div class="calendar-date">26</div>
                            <div class="calendar-date">27</div>
                            <div class="calendar-date">28</div>
                            <div class="calendar-date">29</div>
                            <div class="calendar-date">30</div>
                        </div>
                    </div>
                </div>

                <div class="card fade-in delay-1">
                    <h3><i class="fas fa-bolt"></i> Quick Access</h3>
                    <ul class="clean-list">
                        <li><a href="classes.php?level_id=1"><i class="fas fa-chalkboard"></i> Primary Classes</a></li>
                        <li><a href="classes.php?level_id=2"><i class="fas fa-user-graduate"></i> Secondary Classes</a></li>
                        <li><a href="courses.php"><i class="fas fa-university"></i> University Courses</a></li>
                        <li><a href="explore.php"><i class="fas fa-compass"></i> Explore Content</a></li>
                        <li><a href="resources.php"><i class="fas fa-download"></i> Download Resources</a></li>
                        <li><a href="exams.php"><i class="fas fa-file-alt"></i> Exam Preparation</a></li>
                    </ul>
                </div>

                <div class="card fade-in delay-2">
                    <h3><i class="fas fa-chart-bar"></i> Platform Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">14</div>
                            <div class="stat-label">Classes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">128</div>
                            <div class="stat-label">Topics</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">12</div>
                            <div class="stat-label">Courses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">5,000+</div>
                            <div class="stat-label">Active Students</div>
                        </div>
                    </div>
                </div>

                <div class="card fade-in delay-2">
                    <h3><i class="fas fa-bell"></i> Recent Notifications</h3>
                    <ul class="clean-list">
                        <li><a href="#"><i class="fas fa-bookmark"></i> New content added to Mathematics</a></li>
                        <li><a href="#"><i class="fas fa-users"></i> Study group meeting tomorrow</a></li>
                        <li><a href="#"><i class="fas fa-trophy"></i> You earned a new badge!</a></li>
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

        // Observe all cards and class cards for animation
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToAnimate = document.querySelectorAll('.card, .simple-card, .class-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });

            // Simple carousel functionality
            const carouselInner = document.querySelector('.carousel-inner');
            const carouselControls = document.querySelectorAll('.carousel-control');
            
            carouselControls.forEach((control, index) => {
                control.addEventListener('click', function() {
                    // Update active control
                    carouselControls.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Move carousel
                    carouselInner.style.transform = `translateX(-${index * 100}%)`;
                });
            });

            // Study timer functionality
            const timerDisplay = document.querySelector('.timer-display');
            const startButton = document.querySelector('.timer-controls .btn:first-child');
            let timerInterval;
            let timeLeft = 25 * 60; // 25 minutes in seconds
            let isRunning = false;

            function updateTimerDisplay() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            startButton.addEventListener('click', function() {
                if (!isRunning) {
                    isRunning = true;
                    startButton.textContent = 'Pause';
                    timerInterval = setInterval(() => {
                        timeLeft--;
                        updateTimerDisplay();
                        
                        if (timeLeft === 0) {
                            clearInterval(timerInterval);
                            isRunning = false;
                            startButton.textContent = 'Start';
                            alert('Study session completed! Take a break.');
                        }
                    }, 1000);
                } else {
                    isRunning = false;
                    startButton.textContent = 'Start';
                    clearInterval(timerInterval);
                }
            });

            document.querySelector('.timer-controls .btn:last-child').addEventListener('click', function() {
                clearInterval(timerInterval);
                isRunning = false;
                timeLeft = 25 * 60;
                updateTimerDisplay();
                startButton.textContent = 'Start';
            });
        });
    </script>
</body>
</html>