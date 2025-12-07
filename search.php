<?php
require_once __DIR__ . '/includes/functions.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$topics = [];
$units = [];
$topic_videos = [];
$unit_videos = [];
$topic_resources = [];
$unit_resources = [];

if ($q !== '') {
    global $mysqli;
    $search_term = '%' . $mysqli->real_escape_string($q) . '%';

    // Search topics with class and level context
    $res = $mysqli->query(
        "SELECT t.id, t.title AS name, t.content, s.name AS subject_name, c.name AS class_name, l.name AS level_name
         FROM topics t
         JOIN subjects s ON t.subject_id = s.id
         JOIN classes c ON s.class_id = c.id
         JOIN levels l ON c.level_id = l.id
         WHERE t.title LIKE '$search_term' OR t.content LIKE '$search_term' OR s.name LIKE '$search_term'
         ORDER BY l.id, c.position, s.position, t.position
         LIMIT 100"
    );
    $topics = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // Search units with course context
    $res = $mysqli->query(
        "SELECT u.id, CONCAT(u.code, ' - ', u.title) AS name, u.content, c.name AS course_name
         FROM units u
         JOIN courses c ON u.course_id = c.id
         WHERE u.code LIKE '$search_term' OR u.title LIKE '$search_term' OR u.content LIKE '$search_term'
         ORDER BY c.id, u.position
         LIMIT 100"
    );
    $units = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // topic videos
    $res = $mysqli->query(
        "SELECT tv.*, t.title AS topic_title, s.name AS subject_name, c.name AS class_name, l.name AS level_name
         FROM topic_videos tv
         JOIN topics t ON tv.topic_id = t.id
         JOIN subjects s ON t.subject_id = s.id
         JOIN classes c ON s.class_id = c.id
         JOIN levels l ON c.level_id = l.id
         WHERE tv.title LIKE '$search_term' OR tv.description LIKE '$search_term' OR tv.video_url LIKE '$search_term'
         ORDER BY tv.created_at DESC
         LIMIT 100"
    );
    $topic_videos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // unit videos
    $res = $mysqli->query(
        "SELECT uv.*, u.code AS unit_code, u.title AS unit_title, co.name AS course_name
         FROM unit_videos uv
         JOIN units u ON uv.unit_id = u.id
         JOIN courses co ON u.course_id = co.id
         WHERE uv.title LIKE '$search_term' OR uv.description LIKE '$search_term' OR uv.video_url LIKE '$search_term'
         ORDER BY uv.created_at DESC
         LIMIT 100"
    );
    $unit_videos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // topic resources
    $res = $mysqli->query(
        "SELECT tr.*, t.title AS topic_title, s.name AS subject_name, c.name AS class_name
         FROM topic_resources tr
         JOIN topics t ON tr.topic_id = t.id
         JOIN subjects s ON t.subject_id = s.id
         JOIN classes c ON s.class_id = c.id
         WHERE tr.title LIKE '$search_term' OR tr.description LIKE '$search_term' OR tr.file_path LIKE '$search_term'
         ORDER BY tr.created_at DESC
         LIMIT 100"
    );
    $topic_resources = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // unit resources
    $res = $mysqli->query(
        "SELECT ur.*, u.code AS unit_code, u.title AS unit_title, co.name AS course_name
         FROM unit_resources ur
         JOIN units u ON ur.unit_id = u.id
         JOIN courses co ON u.course_id = co.id
         WHERE ur.title LIKE '$search_term' OR ur.description LIKE '$search_term' OR ur.file_path LIKE '$search_term'
         ORDER BY ur.created_at DESC
         LIMIT 100"
    );
    $unit_resources = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           CnEduc – Premium White UI (SEARCH PAGE)
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
           SEARCH FORM
           ============================================================ */

        .search-form {
            display: flex;
            gap: 0;
            margin: 24px 0;
            max-width: 600px;
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 16px 20px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius) 0 0 var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            transition: var(--transition-fast);
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .search-form input[type="submit"] {
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 16px 24px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 16px;
        }

        .search-form input[type="submit"]:hover {
            background: var(--accent-hover);
        }

        /* ============================================================
           SEARCH RESULTS
           ============================================================ */

        .results-header {
            margin: 24px 0;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
        }

        .results-count {
            font-size: 14px;
            color: var(--gray-600);
            margin-top: 8px;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .result-card {
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

        .result-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .result-card:before {
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

        .result-card:hover:before {
            transform: scaleX(1);
        }

        .result-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--gray-900);
            line-height: 1.4;
        }

        .result-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .result-badge {
            display: inline-block;
            padding: 4px 8px;
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

        .tertiary-badge {
            background: var(--gradient-tertiary);
            color: white;
        }

        .result-context {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .result-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .result-type {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--gray-500);
        }

        .view-result {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--accent);
            font-weight: 600;
            font-size: 14px;
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
            font-size: 64px;
            margin-bottom: 16px;
            color: var(--gray-300);
        }

        .empty-state h3 {
            font-size: 24px;
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

        .browse-list {
            list-style: none;
        }

        .browse-list li {
            margin: 12px 0;
        }

        .browse-list a {
            text-decoration: none;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            padding: 8px 0;
        }

        .browse-list a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }

        .browse-list i {
            color: var(--accent);
            width: 20px;
            text-align: center;
        }

        .tips-card {
            background: #e8f5e9;
            border-left: 4px solid #2e7d32;
        }

        .tips-card h3 {
            color: #2e7d32;
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
            .results-grid {
                grid-template-columns: 1fr;
            }
            .search-form {
                flex-direction: column;
            }
            .search-form input[type="text"],
            .search-form input[type="submit"] {
                border-radius: var(--radius);
                width: 100%;
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
                <a href="university.php"><i class="fas fa-university"></i> University</a>
                <a href="explore.php"><i class="fas fa-search"></i> Explore</a>
                <a href="#" class="active"><i class="fas fa-user"></i> Account</a>
            </div>
            <div class="nav-actions">
                <a href="search.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </a>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="grid">
            <!-- MAIN CONTENT -->
            <main class="main">
                <div class="card fade-in">
                    <h1><i class="fas fa-search"></i> Search Content</h1>
                    <p>Search across all topics and university units to find exactly what you need.</p>
                    
                    <form action="search.php" method="get" class="search-form">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search topics, units, subjects, or courses..." required>
                        <input type="submit" value="Search">
                    </form>

                    <?php if ($q !== ''): ?>
                        <div class="results-header">
                            <h2>Search Results for "<?php echo htmlspecialchars($q); ?>"</h2>
                            <div class="results-count">Found <?php echo (count($topics) + count($units) + (isset($topic_videos) ? count($topic_videos) : 0) + (isset($unit_videos) ? count($unit_videos) : 0) + (isset($topic_resources) ? count($topic_resources) : 0) + (isset($unit_resources) ? count($unit_resources) : 0)); ?> result(s)</div>
                        </div>

                        <!-- Topics Section -->
                        <?php if (count($topics) > 0): ?>
                            <h2><i class="fas fa-book-open"></i> School Topics (<?php echo count($topics); ?>)</h2>
                            <div class="results-grid">
                                <?php foreach ($topics as $t): ?>
                                    <a href="read_topic.php?id=<?php echo $t['id']; ?>" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="result-meta">
                                            <span class="result-badge <?php echo $t['level_name'] === 'Primary' ? 'primary-badge' : 'secondary-badge'; ?>">
                                                <?php echo htmlspecialchars($t['level_name']); ?>
                                            </span>
                                        </div>
                                        <div class="result-context">
                                            <strong><?php echo htmlspecialchars($t['class_name']); ?></strong> • 
                                            <?php echo htmlspecialchars($t['subject_name']); ?>
                                        </div>
                                        <div class="result-actions">
                                            <div class="result-type">
                                                <i class="fas fa-book"></i> Topic
                                            </div>
                                            <div class="view-result">
                                                View Topic <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-book-open"></i>
                                <h3>No Topics Found</h3>
                                <p>We couldn't find any topics matching your search. Try different keywords or browse by class.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Units Section -->
                        <?php if (count($units) > 0): ?>
                            <h2 style="margin-top: 40px;"><i class="fas fa-university"></i> University Units (<?php echo count($units); ?>)</h2>
                            <div class="results-grid">
                                <?php foreach ($units as $u): ?>
                                    <a href="read_unit.php?id=<?php echo $u['id']; ?>" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($u['name']); ?></div>
                                        <div class="result-meta">
                                            <span class="result-badge tertiary-badge">UNIVERSITY</span>
                                        </div>
                                        <div class="result-context">
                                            Course: <?php echo htmlspecialchars($u['course_name']); ?>
                                        </div>
                                        <div class="result-actions">
                                            <div class="result-type">
                                                <i class="fas fa-file-alt"></i> Unit
                                            </div>
                                            <div class="view-result">
                                                View Unit <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-university"></i>
                                <h3>No Units Found</h3>
                                <p>We couldn't find any university units matching your search. Try different keywords or browse courses.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Videos Section -->
                        <h2 style="margin-top: 30px;"><i class="fas fa-video"></i> Videos</h2>
                        <?php if (!empty($topic_videos) || !empty($unit_videos)): ?>
                            <div class="results-grid">
                                <?php if (!empty($topic_videos)): foreach ($topic_videos as $tv): ?>
                                    <a href="read_topic.php?id=<?php echo $tv['topic_id']; ?>" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($tv['title']); ?></div>
                                        <div class="result-meta"><span class="result-badge">Topic Video</span></div>
                                        <div class="result-context"><?php echo htmlspecialchars($tv['topic_title']); ?> • <?php echo htmlspecialchars($tv['class_name'] . ' / ' . $tv['subject_name']); ?></div>
                                        <div class="result-actions"><div class="view-result">Open <i class="fas fa-arrow-right"></i></div></div>
                                    </a>
                                <?php endforeach; endif; ?>

                                <?php if (!empty($unit_videos)): foreach ($unit_videos as $uv): ?>
                                    <a href="read_unit.php?id=<?php echo $uv['unit_id']; ?>" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($uv['title']); ?></div>
                                        <div class="result-meta"><span class="result-badge">Unit Video</span></div>
                                        <div class="result-context"><?php echo htmlspecialchars(($uv['unit_code'] ? $uv['unit_code'] . ' - ' : '') . $uv['unit_title']); ?></div>
                                        <div class="result-actions"><div class="view-result">Open <i class="fas fa-arrow-right"></i></div></div>
                                    </a>
                                <?php endforeach; endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state"><i class="fas fa-video"></i><h3>No Videos Found</h3><p>Try different keywords or upload videos in the admin panel.</p></div>
                        <?php endif; ?>

                        <!-- Resources Section -->
                        <h2 style="margin-top: 30px;"><i class="fas fa-file"></i> Resources</h2>
                        <?php if (!empty($topic_resources) || !empty($unit_resources)): ?>
                            <div class="results-grid">
                                <?php if (!empty($topic_resources)): foreach ($topic_resources as $tr): ?>
                                    <a href="<?php echo htmlspecialchars($tr['file_path']); ?>" target="_blank" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($tr['title']); ?></div>
                                        <div class="result-meta"><span class="result-badge">Topic Resource</span></div>
                                        <div class="result-context"><?php echo htmlspecialchars($tr['topic_title']); ?></div>
                                        <div class="result-actions"><div class="view-result">Download <i class="fas fa-arrow-right"></i></div></div>
                                    </a>
                                <?php endforeach; endif; ?>

                                <?php if (!empty($unit_resources)): foreach ($unit_resources as $ur): ?>
                                    <a href="<?php echo htmlspecialchars($ur['file_path']); ?>" target="_blank" class="result-card fade-in">
                                        <div class="result-title"><?php echo htmlspecialchars($ur['title']); ?></div>
                                        <div class="result-meta"><span class="result-badge">Unit Resource</span></div>
                                        <div class="result-context"><?php echo htmlspecialchars(($ur['unit_code'] ? $ur['unit_code'] . ' - ' : '') . $ur['unit_title']); ?></div>
                                        <div class="result-actions"><div class="view-result">Download <i class="fas fa-arrow-right"></i></div></div>
                                    </a>
                                <?php endforeach; endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state"><i class="fas fa-file"></i><h3>No Resources Found</h3><p>Try different keywords or upload resources in the admin panel.</p></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h3>Ready to Search?</h3>
                            <p>Enter a keyword above to search across all topics and university units. Try searching for subject names like "Mathematics" or "Biology".</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card tips-card fade-in delay-1">
                    <h3><i class="fas fa-lightbulb"></i> Search Tips</h3>
                    <ul class="tip-list">
                        <li><i class="fas fa-check-circle"></i> Search by topic name</li>
                        <li><i class="fas fa-check-circle"></i> Search by subject (Math, Biology, etc.)</li>
                        <li><i class="fas fa-check-circle"></i> Search by class (P1, S3, etc.)</li>
                        <li><i class="fas fa-check-circle"></i> Search by course code (CS101, etc.)</li>
                        <li><i class="fas fa-check-circle"></i> Be specific for better results</li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-2">
                    <h3><i class="fas fa-compass"></i> Browse Instead?</h3>
                    <ul class="browse-list">
                        <li><a href="explore.php"><i class="fas fa-search"></i> Explore with Filters</a></li>
                        <li><a href="classes.php?level_id=1"><i class="fas fa-chalkboard"></i> Primary Classes</a></li>
                        <li><a href="classes.php?level_id=2"><i class="fas fa-user-graduate"></i> Secondary Classes</a></li>
                        <li><a href="courses.php"><i class="fas fa-graduation-cap"></i> University Courses</a></li>
                    </ul>
                </div>

                <div class="sidebar-card fade-in delay-3">
                    <h3><i class="fas fa-info-circle"></i> About Search</h3>
                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        Our search looks through topic titles, content, and course information. Results show the full context to help you understand where each result belongs in the curriculum.
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
            const elementsToAnimate = document.querySelectorAll('.card, .sidebar-card, .result-card');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>