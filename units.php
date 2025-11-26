<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> Units - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (UNITS PAGE)
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
           UNIT CARDS
           ============================================================ */

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .unit-card {
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

        .unit-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .unit-card:before {
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

        .unit-card:hover:before {
            transform: scaleX(1);
        }

        .unit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .unit-code {
            font-size: 14px;
            font-weight: 700;
            color: var(--accent);
            background: rgba(15, 98, 254, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
        }

        .unit-icon {
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

        .unit-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--gray-900);
            line-height: 1.4;
        }

        .unit-meta {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            font-size: 14px;
            color: var(--gray-600);
        }

        .read-unit {
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
            .unit-grid {
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
                <a href="#"><i class="fas fa-book"></i> Curriculum</a>
                <a href="#" class="active"><i class="fas fa-university"></i> University</a>
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
                <a href="courses.php">University Courses</a>
                <span class="separator">/</span>
                <span><?php echo htmlspecialchars($course['name']); ?></span>
            </div>
            
            <h1><i class="fas fa-book-open"></i> Units for <?php echo htmlspecialchars($course['name']); ?></h1>
            <p>Explore all the units in this course. Click on any unit to start learning.</p>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-layer-group"></i> Course Units</h2>
                    <p>All units are organized in a recommended learning sequence:</p>
                    
                    <div class="unit-grid">
                        <?php foreach ($units as $index => $unit): 
                            // Get unit icon based on index or content
                            $unit_icon = "fas fa-file-alt";
                            if ($index === 0) {
                                $unit_icon = "fas fa-play-circle";
                            } elseif ($index % 3 === 0) {
                                $unit_icon = "fas fa-chart-bar";
                            } elseif ($index % 3 === 1) {
                                $unit_icon = "fas fa-cogs";
                            } elseif ($index % 3 === 2) {
                                $unit_icon = "fas fa-lightbulb";
                            }
                        ?>
                            <a href="read_unit.php?id=<?php echo $unit['id']; ?>" class="unit-card fade-in">
                                <div class="unit-header">
                                    <div class="unit-code"><?php echo htmlspecialchars($unit['code']); ?></div>
                                    <div class="unit-icon">
                                        <i class="<?php echo $unit_icon; ?>"></i>
                                    </div>
                                </div>
                                <div class="unit-title"><?php echo htmlspecialchars($unit['title']); ?></div>
                                <div class="unit-meta">
                                    <div class="unit-number">
                                        Unit <?php echo $index + 1; ?>
                                    </div>
                                    <div class="read-unit">
                                        Read Unit <i class="fas fa-arrow-right"></i>
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
                        <li><a href="courses.php"><i class="fas fa-graduation-cap"></i> All Courses</a></li>
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
                    
                    <?php if (!empty($course['description'])): ?>
                        <p style="font-size: 14px; color: var(--gray-700); margin-bottom: 16px; line-height: 1.5;">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($units); ?></div>
                            <div class="stat-label">Units</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($units) * 3; ?>+</div>
                            <div class="stat-label">Topics</div>
                        </div>
                    </div>
                    
                    <a href="courses.php" class="btn btn-secondary" style="width: 100%; margin-top: 16px; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>

                <div class="sidebar-card tip-card fade-in delay-3">
                    <h3><i class="fas fa-lightbulb"></i> Study Strategy</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Follow the units in sequence for the best learning experience. Each unit builds on concepts from previous ones. Take notes and complete any exercises to reinforce your understanding.
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .unit-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>