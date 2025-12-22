<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$is_logged_in = is_user_logged_in();

$levels = get_levels();
if (!is_array($levels)) {
    $levels = [];
}

$total_classes = 0;
foreach ($levels as $lvl) {
    $cls = get_classes_by_level($lvl['id']);
    $total_classes += is_array($cls) ? count($cls) : 0;
}

$total_subjects = 0;
foreach ($levels as $lvl) {
    $cls = get_classes_by_level($lvl['id']);
    if (is_array($cls)) {
        foreach ($cls as $c) {
            $subs = get_subjects_by_class($c['id']);
            $total_subjects += is_array($subs) ? count($subs) : 0;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Overview - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (CURRICULUM OVERVIEW PAGE)
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
           LEVEL SECTIONS
           ============================================================ */

        .level-section {
            margin-top: 40px;
            padding: 32px;
            border-radius: var(--radius-xl);
            background: var(--gradient-light);
            border: 1px solid rgba(229, 229, 229, 0.5);
            transition: var(--transition);
        }

        .level-section:hover {
            box-shadow: var(--shadow-md);
        }

        .level-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .level-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        .level-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--gray-900);
        }

        .level-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 12px;
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

        .level-description {
            font-size: 16px;
            color: var(--gray-700);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* ============================================================
           CLASSES GRID
           ============================================================ */

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .class-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
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

        .class-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .class-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
        }

        .class-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        .class-description {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .subjects-list {
            list-style: none;
        }

        .subjects-list li {
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--gray-700);
        }

        .subjects-list i {
            color: var(--accent);
            font-size: 12px;
        }

        .empty-subjects {
            font-size: 14px;
            color: var(--gray-500);
            font-style: italic;
        }

        /* ============================================================
           NEW FEATURES
           ============================================================ */

        /* Quick Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--radius-lg);
            text-align: center;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 14px;
        }

        /* Search and Filter */
        .search-filter-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 16px 48px 16px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .filter-dropdown {
            padding: 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: white;
            min-width: 200px;
            cursor: pointer;
            position: relative;
        }

        /* Progress Indicators */
        .progress-section {
            margin-top: 20px;
        }

        .progress-bar {
            height: 6px;
            background: var(--gray-200);
            border-radius: 3px;
            overflow: hidden;
            margin: 8px 0;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 3px;
            transition: width 0.8s ease;
        }

        .progress-info {
            display: flex;
            justify-content: between;
            font-size: 14px;
            color: var(--gray-600);
        }

        /* Featured Resources */
        .featured-resources {
            margin: 40px 0;
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .resource-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 20px;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .resource-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
        }

        .resource-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
            background: rgba(15, 98, 254, 0.1);
            color: var(--accent);
        }

        /* ============================================================
           PROGRESSION INFO
           ============================================================ */

        .progression-section {
            background: #f8f9ff;
            border-left: 4px solid var(--accent);
            padding: 24px;
            border-radius: var(--radius);
            margin-top: 32px;
        }

        .progression-section h3 {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-800);
        }

        .progression-section p {
            color: var(--gray-700);
            line-height: 1.6;
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
            .classes-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
            .classes-grid {
                grid-template-columns: 1fr;
            }
            .level-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .level-badge {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            .search-filter-bar {
                flex-direction: column;
            }
            .search-box {
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
                <a href="courses.php"><i class="fas fa-university"></i> University</a>
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
        <!-- MAIN HEADER -->
        <div class="card fade-in">
            <h1><i class="fas fa-sitemap"></i> Curriculum Overview</h1>
            <p>Explore our comprehensive educational structure across different levels of schooling and understand how students typically progress through the curriculum.</p>
            
            <!-- QUICK STATS -->
            <div class="stats-grid">
                <div class="stat-card fade-in delay-1">
                    <div class="stat-number"><?php echo count($levels); ?></div>
                    <div class="stat-label">Educational Levels</div>
                </div>
                <div class="stat-card fade-in delay-1">
                    <div class="stat-number"><?php echo $total_classes ?? '15+'; ?></div>
                    <div class="stat-label">Total Classes</div>
                </div>
                <div class="stat-card fade-in delay-1">
                    <div class="stat-number"><?php echo $total_subjects ?? '50+'; ?></div>
                    <div class="stat-label">Subjects Offered</div>
                </div>
                <div class="stat-card fade-in delay-1">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Curriculum Coverage</div>
                </div>
            </div>
        </div>

        <!-- SEARCH AND FILTER -->
        <div class="search-filter-bar fade-in">
            <div class="search-box">
                <input type="text" placeholder="Search classes, subjects, or keywords..." id="curriculumSearch">
                <i class="fas fa-search search-icon"></i>
            </div>
            <select class="filter-dropdown" id="levelFilter">
                <option value="">All Levels</option>
                <?php foreach ($levels as $level): ?>
                    <option value="level-<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select class="filter-dropdown" id="subjectFilter">
                <option value="">All Subjects</option>
                <!-- PHP would populate this with subjects -->
            </select>
        </div>

        <!-- FEATURED RESOURCES -->
        <div class="card fade-in">
            <h2><i class="fas fa-star"></i> Featured Learning Resources</h2>
            <div class="resources-grid">
                <a href="#" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Interactive Videos</h3>
                    <p>Engaging video lessons for all subjects</p>
                </a>
                <a href="#" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3>Study Guides</h3>
                    <p>Comprehensive PDF guides and notes</p>
                </a>
                <a href="#" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h3>Learning Games</h3>
                    <p>Fun educational games for better retention</p>
                </a>
                <a href="#" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Progress Analytics</h3>
                    <p>Track learning progress and performance</p>
                </a>
            </div>
        </div>

        <!-- LEVELS SECTIONS -->
        <?php foreach ($levels as $level): 
            $badge_class = '';
            $level_icon = '';
            
            if ($level['id'] === 1) {
                $badge_class = 'primary-badge';
                $level_icon = 'fas fa-child';
            } elseif ($level['id'] === 2) {
                $badge_class = 'secondary-badge';
                $level_icon = 'fas fa-user-graduate';
            } else {
                $badge_class = 'university-badge';
                $level_icon = 'fas fa-university';
            }
        ?>
            <div class="level-section fade-in" id="level-<?php echo $level['id']; ?>">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="<?php echo $level_icon; ?>"></i>
                    </div>
                    <div>
                        <div class="level-title"><?php echo htmlspecialchars($level['name']); ?></div>
                        <span class="level-badge <?php echo $badge_class; ?>"><?php echo strtoupper($level['name']); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($level['description'])): ?>
                    <div class="level-description">
                        <?php echo nl2br(htmlspecialchars($level['description'])); ?>
                    </div>
                <?php endif; ?>

                <!-- Progress Indicator -->
                <div class="progress-section">
                    <div class="progress-info">
                        <span>Curriculum Completion</span>
                        <span>75%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%"></div>
                    </div>
                </div>

                <?php $classes = get_classes_by_level($level['id']); ?>
                <?php if (!empty($classes)): ?>
                    <div class="classes-grid">
                        <?php foreach ($classes as $cl): 
                            // Determine class icon based on name
                            $class_icon = "fas fa-users";
                            if (strpos($cl['name'], 'P1') !== false) $class_icon = "fas fa-pencil-alt";
                            if (strpos($cl['name'], 'P2') !== false) $class_icon = "fas fa-book-open";
                            if (strpos($cl['name'], 'P3') !== false) $class_icon = "fas fa-calculator";
                            if (strpos($cl['name'], 'P4') !== false) $class_icon = "fas fa-flask";
                            if (strpos($cl['name'], 'P5') !== false) $class_icon = "fas fa-globe";
                            if (strpos($cl['name'], 'P6') !== false) $class_icon = "fas fa-graduation-cap";
                            if (strpos($cl['name'], 'P7') !== false) $class_icon = "fas fa-trophy";
                            if (strpos($cl['name'], 'S1') !== false) $class_icon = "fas fa-atom";
                            if (strpos($cl['name'], 'S2') !== false) $class_icon = "fas fa-dna";
                            if (strpos($cl['name'], 'S3') !== false) $class_icon = "fas fa-microscope";
                            if (strpos($cl['name'], 'S4') !== false) $class_icon = "fas fa-chart-line";
                            if (strpos($cl['name'], 'S5') !== false) $class_icon = "fas fa-book-reader";
                            if (strpos($cl['name'], 'S6') !== false) $class_icon = "fas fa-user-graduate";
                        ?>
                            <a href="subjects.php?class_id=<?php echo $cl['id']; ?>" class="class-card fade-in delay-1">
                                <div class="class-header">
                                    <div class="class-name"><?php echo htmlspecialchars($cl['name']); ?></div>
                                    <div class="class-icon">
                                        <i class="<?php echo $class_icon; ?>"></i>
                                    </div>
                                </div>
                                
                                <?php if (!empty($cl['description'])): ?>
                                    <div class="class-description"><?php echo htmlspecialchars($cl['description']); ?></div>
                                <?php endif; ?>
                                
                                <?php $subjects = get_subjects_by_class($cl['id']); ?>
                                <?php if (!empty($subjects)): ?>
                                    <ul class="subjects-list">
                                        <?php foreach ($subjects as $s): ?>
                                            <li>
                                                <i class="fas fa-book"></i>
                                                <?php echo htmlspecialchars($s['name']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="empty-subjects">No subjects defined for this class yet.</div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 20px; color: var(--gray-500);">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <h3 style="margin-bottom: 8px;">No Classes Defined</h3>
                        <p>No classes have been defined for this level yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- PROGRESSION INFO -->
        <div class="progression-section fade-in delay-2">
            <h3><i class="fas fa-lightbulb"></i> Curriculum Progression</h3>
            <p>Our curriculum is designed to build knowledge progressively from foundational concepts in early years to advanced specialized topics in higher grades. Students typically advance through Primary levels (P1-P7) focusing on core literacy and numeracy skills, then progress to Secondary levels (S1-S6) with more specialized subject options, preparing them for university education or vocational pathways.</p>
        </div>

        <!-- CALL TO ACTION -->
        <div class="card fade-in delay-3" style="text-align: center;">
            <h2><i class="fas fa-compass"></i> Ready to Explore?</h2>
            <p>Start exploring our curriculum by selecting a level and class above, or use our search and exploration tools to find specific content.</p>
            <div style="display: flex; gap: 16px; justify-content: center; margin-top: 20px; flex-wrap: wrap;">
                <a href="explore.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Explore Content
                </a>
                <a href="index.php" class="btn" style="background: white; color: var(--accent); border: 1px solid var(--accent);">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="#" class="btn" style="background: white; color: var(--accent); border: 1px solid var(--accent);">
                    <i class="fas fa-download"></i> Download Curriculum
                </a>
            </div>
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

        // Search and filter functionality
        document.getElementById('curriculumSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterContent();
        });

        document.getElementById('levelFilter').addEventListener('change', filterContent);
        document.getElementById('subjectFilter').addEventListener('change', filterContent);

        function filterContent() {
            const searchTerm = document.getElementById('curriculumSearch').value.toLowerCase();
            const levelFilter = document.getElementById('levelFilter').value;
            const subjectFilter = document.getElementById('subjectFilter').value;

            // Implementation for filtering would go here
            console.log('Filtering with:', { searchTerm, levelFilter, subjectFilter });
        }

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
            const elementsToAnimate = document.querySelectorAll('.card, .level-section, .class-card, .stat-card, .resource-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });

            // Animate progress bars
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>
</body>
</html>