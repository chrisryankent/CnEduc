<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$is_logged_in = is_user_logged_in();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Materials & Past Papers - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
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
            --transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            --gradient-primary: linear-gradient(135deg, #0f62fe 0%, #3d8dff 100%);
        }

        body {
            font-family: "Inter", sans-serif;
            color: var(--gray-900);
            background: white;
            line-height: 1.65;
            padding-top: 80px;
            overflow-x: hidden;
        }

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
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 30px;
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

        .nav-links a:hover, .nav-links a.active {
            color: var(--accent);
        }

        .nav-links a:hover:after, .nav-links a.active:after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            gap: 16px;
        }

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
        }

        .btn-sm {
            padding: 12px 20px;
            font-size: 14px;
        }

        .content-wrapper {
            max-width: 1300px;
            margin: auto;
            padding: 30px;
        }

        .card {
            background: white;
            border: 1px solid rgba(229, 229, 229, 0.5);
            padding: 32px;
            border-radius: var(--radius-xl);
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .card h1 {
            font-size: 36px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card h1 i {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card h2 {
            font-size: 28px;
            margin-bottom: 16px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .material-card {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 24px;
            border-radius: var(--radius-lg);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .material-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
        }

        .material-card h3 {
            margin-bottom: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .material-card i {
            color: var(--accent);
        }

        .material-card p {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 16px;
        }

        .material-card .btn {
            width: 100%;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .nav-links {
                gap: 20px;
            }
            .content-wrapper {
                padding: 20px;
            }
            .grid-2 {
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
            .card h1 {
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
                <a href="university.php"><i class="fas fa-university"></i> University</a>
                <a href="explore.php"><i class="fas fa-search"></i> Explore</a>
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php"><i class="fas fa-user-circle"></i> Account</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <?php if ($is_logged_in): ?>
                    <a href="logout.php" class="btn btn-secondary btn-sm" style="background-color: #ff6b6b; color: white; border-color: #ff6b6b;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <!-- HEADER -->
        <div class="card">
            <h1><i class="fas fa-file-alt"></i> Exam Materials & Past Papers</h1>
            <p>Prepare for your exams with our comprehensive collection of past papers, model answers, and exam guides.</p>
        </div>

        <!-- TAKE EXAM -->
        <?php if ($is_logged_in): ?>
        <div class="card">
            <h2><i class="fas fa-pencil-alt"></i> Take an Exam</h2>
            <p>Test your knowledge by taking interactive exams for subjects and units. Answer questions and get instant feedback.</p>
            <a href="take_exam.php" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-arrow-right"></i> Start Exam Now
            </a>
        </div>
        <?php endif; ?>

        <!-- FEATURED MATERIALS -->
        <div class="card">
            <h2><i class="fas fa-star"></i> Featured Exam Materials</h2>
            <p>Latest and most relevant exam preparation resources:</p>
            <div class="grid-2">
                <div class="material-card">
                    <h3><i class="fas fa-book"></i> Primary Past Papers</h3>
                    <p>Comprehensive collection of past exam papers from P1 to P7 with detailed solutions.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Papers</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-graduation-cap"></i> Secondary Past Papers</h3>
                    <p>S1 to S6 exam papers including O-Level, A-Level, and school examinations.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Papers</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-pencil-alt"></i> Model Answers</h3>
                    <p>Detailed model solutions with step-by-step explanations for all subjects.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Answers</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-clipboard-list"></i> Exam Tips & Guides</h3>
                    <p>Expert tips for exam preparation, time management, and answering techniques.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">Read Guides</a>
                </div>
            </div>
        </div>

        <!-- BY LEVEL -->
        <div class="card">
            <h2><i class="fas fa-layer-group"></i> Exam Materials by Level</h2>
            <p>Browse past papers and resources organized by education level:</p>
            <div class="grid-2">
                <div class="material-card">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Primary Education</h3>
                    <p>P1 - P7 exam papers, model answers, and practice questions for all subjects.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">Browse Primary Papers</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-user-graduate"></i> Secondary Education</h3>
                    <p>S1 - S6 exams including O-Level, A-Level, and Uganda Certificate exams.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">Browse Secondary Papers</a>
                </div>
            </div>
        </div>

        <!-- BY SUBJECT -->
        <div class="card">
            <h2><i class="fas fa-book-open"></i> Exam Materials by Subject</h2>
            <p>Find past papers and exam resources for specific subjects:</p>
            <div class="grid-2">
                <div class="material-card">
                    <h3><i class="fas fa-calculator"></i> Mathematics</h3>
                    <p>Past papers, formulas, and practice problems across all levels.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Mathematics</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-language"></i> English Language</h3>
                    <p>Literature and language exam papers with essay guides and answers.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View English</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-dna"></i> Science</h3>
                    <p>Biology, Chemistry, and Physics past papers with detailed solutions.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Science</a>
                </div>
                <div class="material-card">
                    <h3><i class="fas fa-globe"></i> Social Studies</h3>
                    <p>History, Geography, and Civics exam materials and past papers.</p>
                    <a href="exams.php" class="btn btn-primary btn-sm">View Social Studies</a>
                </div>
            </div>
        </div>

        <!-- EXAM PREPARATION TIPS -->
        <div class="card">
            <h2><i class="fas fa-lightbulb"></i> Exam Preparation Tips</h2>
            <ul style="margin-left: 20px; margin-top: 16px;">
                <li style="margin: 12px 0;"><strong>Start Early:</strong> Begin revision at least 4 weeks before your exam.</li>
                <li style="margin: 12px 0;"><strong>Use Past Papers:</strong> Practice with at least 5 years of past papers to familiarize yourself with question patterns.</li>
                <li style="margin: 12px 0;"><strong>Time Management:</strong> Practice answering questions within the allocated time.</li>
                <li style="margin: 12px 0;"><strong>Model Answers:</strong> Study model answers to understand marking schemes and expected answers.</li>
                <li style="margin: 12px 0;"><strong>Group Study:</strong> Form study groups to discuss difficult topics.</li>
                <li style="margin: 12px 0;"><strong>Rest Well:</strong> Ensure adequate sleep and regular breaks during revision.</li>
            </ul>
        </div>
    </div>
</body>
</html>
