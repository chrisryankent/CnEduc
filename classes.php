<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($level['name']); ?> Classes - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (CLASSES PAGE)
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

        .primary-badge, .secondary-badge, .university-badge {
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

        .university-badge {
            background: var(--gradient-tertiary);
            color: white;
        }

        /* ============================================================
           CLASS CARDS
           ============================================================ */

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .class-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 28px;
            text-decoration: none;
            color: var(--gray-900);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
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

        .class-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 16px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        .class-card-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .class-card-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin-top: 4px;
            font-weight: 500;
            line-height: 1.4;
        }

        /* ============================================================
           SUBJECTS LIST
           ============================================================ */

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin: 20px 0;
        }

        .subject-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            text-align: center;
            transition: var(--transition);
        }

        .subject-item:hover {
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        .subject-icon {
            font-size: 24px;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .subject-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-800);
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
            .class-grid {
                grid-template-columns: 1fr;
            }
            .subjects-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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
                <a href="curriculum.php" class="active"><i class="fas fa-book"></i> Curriculum</a>
                <a href="courses.php"><i class="fas fa-university"></i> University</a>
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
                <span><?php echo $badge; ?> <?php echo htmlspecialchars($level['name']); ?></span>
            </div>
            
            <h1><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($level['name']); ?> Classes</h1>
            <p><?php echo htmlspecialchars($level['description']); ?></p>
        </div>

        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <!-- CLASSES GRID -->
                <div class="card fade-in delay-1">
                    <h2><i class="fas fa-chalkboard-teacher"></i> Choose Your Class</h2>
                    <p>Select your class to view subjects and learning materials:</p>
                    
                    <div class="class-grid">
                        <?php foreach ($classes as $class): 
                            // Determine icon based on class name
                            $class_icon = "fas fa-users";
                            if (strpos($class['name'], 'P1') !== false) $class_icon = "fas fa-pencil-alt";
                            if (strpos($class['name'], 'P2') !== false) $class_icon = "fas fa-book-open";
                            if (strpos($class['name'], 'P3') !== false) $class_icon = "fas fa-calculator";
                            if (strpos($class['name'], 'P4') !== false) $class_icon = "fas fa-flask";
                            if (strpos($class['name'], 'P5') !== false) $class_icon = "fas fa-globe";
                            if (strpos($class['name'], 'P6') !== false) $class_icon = "fas fa-graduation-cap";
                            if (strpos($class['name'], 'P7') !== false) $class_icon = "fas fa-trophy";
                            if (strpos($class['name'], 'S1') !== false) $class_icon = "fas fa-atom";
                            if (strpos($class['name'], 'S2') !== false) $class_icon = "fas fa-dna";
                            if (strpos($class['name'], 'S3') !== false) $class_icon = "fas fa-microscope";
                            if (strpos($class['name'], 'S4') !== false) $class_icon = "fas fa-chart-line";
                            if (strpos($class['name'], 'S5') !== false) $class_icon = "fas fa-book-reader";
                            if (strpos($class['name'], 'S6') !== false) $class_icon = "fas fa-user-graduate";
                        ?>
                            <a href="subjects.php?class_id=<?php echo $class['id']; ?>" class="class-card fade-in">
                                <div class="class-icon">
                                    <i class="<?php echo $class_icon; ?>"></i>
                                </div>
                                <div class="class-card-title"><?php echo htmlspecialchars($class['name']); ?></div>
                                <div class="class-card-subtitle"><?php echo htmlspecialchars($class['description']); ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- LEVEL INFORMATION -->
                <div class="card fade-in delay-2">
                    <h2><i class="fas fa-info-circle"></i> About <?php echo htmlspecialchars($level['name']); ?> Education</h2>
                    <p><?php echo htmlspecialchars($level['description']); ?></p>
                    
                    <?php if ($level_id === 1): ?>
                        <h3><i class="fas fa-book"></i> Primary School Subjects</h3>
                        <p>All primary classes study these core subjects designed to build foundational knowledge:</p>
                        
                        <div class="subjects-grid">
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-calculator"></i></div>
                                <div class="subject-name">Mathematics</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-language"></i></div>
                                <div class="subject-name">English</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-flask"></i></div>
                                <div class="subject-name">Science</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-globe-africa"></i></div>
                                <div class="subject-name">Social Studies</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-running"></i></div>
                                <div class="subject-name">Physical Education</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-palette"></i></div>
                                <div class="subject-name">Creative Arts</div>
                            </div>
                        </div>
                        
                        <p style="margin-top: 20px;">Primary education focuses on developing fundamental literacy, numeracy, and social skills through engaging, age-appropriate activities and materials.</p>
                        
                    <?php elseif ($level_id === 2): ?>
                        <h3><i class="fas fa-book-open"></i> Secondary School Subjects</h3>
                        <p>Secondary students study a wider range of specialized subjects to prepare for advanced education and careers:</p>
                        
                        <div class="subjects-grid">
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-calculator"></i></div>
                                <div class="subject-name">Mathematics</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-language"></i></div>
                                <div class="subject-name">English</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-dna"></i></div>
                                <div class="subject-name">Biology</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-atom"></i></div>
                                <div class="subject-name">Chemistry</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-bolt"></i></div>
                                <div class="subject-name">Physics</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-monument"></i></div>
                                <div class="subject-name">History</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-globe"></i></div>
                                <div class="subject-name">Geography</div>
                            </div>
                            <div class="subject-item">
                                <div class="subject-icon"><i class="fas fa-balance-scale"></i></div>
                                <div class="subject-name">Social Studies</div>
                            </div>
                        </div>
                        
                        <p style="margin-top: 20px;">Secondary education builds on primary foundations with more specialized subjects, critical thinking development, and preparation for national examinations and higher education.</p>
                    <?php endif; ?>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card fade-in delay-1">
                    <h3><i class="fas fa-compass"></i> Navigation</h3>
                    <ul class="navigation-list">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="levels.php"><i class="fas fa-layer-group"></i> All Levels</a></li>
                        <li><a href="explore.php"><i class="fas fa-search"></i> Explore All Content</a></li>
                        <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
                    </ul>
                </div>

                <div class="sidebar-card tip-card fade-in delay-2">
                    <h3><i class="fas fa-lightbulb"></i> Learning Tip</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Click on any class to see all the subjects available for that grade level. Then select a subject to browse its topics organized from foundational to advanced concepts.
                    </p>
                </div>

                <div class="sidebar-card fade-in delay-3">
                    <h3><i class="fas fa-graduation-cap"></i> Level Statistics</h3>
                    <div style="display: flex; justify-content: space-between; background: var(--gray-50); padding: 16px; border-radius: var(--radius); margin-top: 16px;">
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: var(--accent);"><?php echo count($classes); ?></div>
                            <div style="font-size: 12px; color: var(--gray-600);">Classes</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: var(--accent);">
                                <?php
                                $total_subjects = 0;
                                foreach ($classes as $class) {
                                    $subjects = get_subjects_by_class($class['id']);
                                    $total_subjects += count($subjects);
                                }
                                echo $total_subjects;
                                ?>
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Subjects</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: var(--accent);">
                                <?php
                                $total_topics = 0;
                                foreach ($classes as $class) {
                                    $subjects = get_subjects_by_class($class['id']);
                                    foreach ($subjects as $subject) {
                                        $topics = get_topics_by_subject($subject['id']);
                                        $total_topics += count($topics);
                                    }
                                }
                                echo $total_topics;
                                ?>
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Topics</div>
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .class-card, .subject-item');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>