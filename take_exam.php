<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$is_logged_in = is_user_logged_in();

// Redirect to login if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

$user = cneduc_get_current_user();
$user_id = $user['id'];

// Get levels and courses for initial navigation
$levels = get_levels();
$courses = get_courses();

// Handle navigation stages
$stage = isset($_GET['stage']) ? $_GET['stage'] : 'type';
$exam_type = isset($_GET['exam_type']) ? $_GET['exam_type'] : null; // 'subject' or 'unit'
$selected_level = isset($_GET['level_id']) ? (int)$_GET['level_id'] : null;
$selected_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
$selected_subject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : null;
$selected_unit = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : null;
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : null);
$exam_started = isset($_GET['start']) && $_GET['start'] == '1';
$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : null;

$questions = [];
$exam_title = '';
$exam_type_display = '';
$exam_data = null;
$attempt_data = null;
$exam_subjects = [];
$exam_units = [];

// Load data based on stage
$available_levels = $levels;
$available_classes = [];
$available_subjects = [];
$available_units = [];

if ($selected_level) {
    $available_classes = get_classes_by_level($selected_level);
}

if ($selected_class) {
    $available_subjects = get_subjects_by_class($selected_class);
}

if ($selected_course) {
    $available_units = get_units_by_course($selected_course);
}

// Get exams for selected subject/unit
if ($selected_subject) {
    $exam_subjects = get_exams_by_subject($selected_subject);
}

if ($selected_unit) {
    $exam_units = get_exams_by_unit($selected_unit);
}

// Load exam and start if needed
if ($exam_id && !$exam_started) {
    $exam_data = get_exam($exam_id);
    if ($exam_data) {
        // Check if user can take this exam
        if (!can_user_take_exam($user_id, $exam_id)) {
            $exam_data = null;
        }
    }
}

// Start exam - create attempt
if ($exam_id && $exam_started) {
    $exam_data = get_exam($exam_id);
    
    if ($exam_data && can_user_take_exam($user_id, $exam_id)) {
        // Create new attempt
        $attempt_id = create_exam_attempt(
            $exam_id, 
            $user_id, 
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
        
        if ($attempt_id) {
            // Get shuffled questions for this user
            $questions = get_exam_questions($exam_id, $user_id, 1);
            $exam_title = $exam_data['title'];
            $exam_type_display = $exam_data['subject_id'] ? 'Subject' : 'Unit';
        }
    }
}

// Load in-progress attempt
if ($attempt_id && !$exam_started) {
    $attempt_data = get_exam_attempt($attempt_id);
    if ($attempt_data && $attempt_data['user_id'] == $user_id) {
        $exam_data = get_exam($attempt_data['exam_id']);
        $exam_title = $attempt_data['exam_title'] ?? $exam_data['title'];
        $exam_type_display = $exam_data['subject_id'] ? 'Subject' : 'Unit';
        
        // Get questions for this attempt
        $questions = get_exam_questions($attempt_data['exam_id'], $user_id, $attempt_data['attempt_number']);
    }
}

// Handle exam submission
$submission_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    $answers = isset($_POST['answers']) ? $_POST['answers'] : [];
    $score = 0;
    $total = count($questions);
    
    // Simple scoring: count correct answers (this is basic - you'll customize this)
    foreach ($questions as $question) {
        $qid = $question['id'];
        if (isset($answers[$qid]) && !empty($answers[$qid])) {
            $score++;
        }
    }
    
    $percentage = $total > 0 ? ($score / $total) * 100 : 0;
    
    $submission_result = [
        'score' => $score,
        'total' => $total,
        'percentage' => round($percentage, 2),
        'passed' => $percentage >= 50
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $exam_started && $exam_title ? $exam_title : 'Take Exam'; ?> - CnEduc</title>
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
            background: #f5f5f5;
            line-height: 1.65;
            padding-top: 80px;
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
            position: relative;
        }

        .nav-links a:hover {
            color: var(--accent);
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
        }

        .btn-sm {
            padding: 12px 20px;
            font-size: 14px;
        }

        .content-wrapper {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 32px;
            border-radius: var(--radius-xl);
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
        }

        .card h1 {
            font-size: 32px;
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

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .exam-option {
            background: white;
            border: 2px solid var(--gray-200);
            padding: 24px;
            border-radius: var(--radius-lg);
            transition: var(--transition);
            cursor: pointer;
        }

        .exam-option:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
        }

        .exam-option h3 {
            margin-bottom: 12px;
            font-weight: 600;
        }

        .exam-option p {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 16px;
        }

        .exam-option a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .exam-option a:hover {
            text-decoration: underline;
        }

        .type-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            margin-top: 32px;
        }

        .type-card {
            background: white;
            border: 2px solid var(--gray-200);
            padding: 40px 24px;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-900);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .type-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-8px);
        }

        .type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .type-card:hover::before {
            opacity: 0.05;
        }

        .type-icon {
            font-size: 48px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .type-card h3 {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .type-card p {
            color: var(--gray-600);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .type-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            transition: var(--transition);
        }

        .type-card:hover .type-arrow {
            background: var(--accent-hover);
            transform: translateX(5px);
        }

        .selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .selection-card {
            background: white;
            border: 2px solid var(--gray-200);
            padding: 24px;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-900);
            transition: var(--transition);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .selection-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 32px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }

        .selection-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .selection-card p {
            font-size: 13px;
            color: var(--gray-600);
        }

        .search-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .search-input::placeholder {
            color: var(--gray-400);
        }

        .selection-card.hidden {
            display: none;
        }

        .exam-card {
            background: white;
            border: 2px solid var(--gray-200);
            padding: 20px;
            border-radius: var(--radius-lg);
            transition: var(--transition);
        }

        .exam-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-5px);
        }

        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }

        .exam-header h3 {
            margin: 0;
            font-size: 16px;
            flex: 1;
        }

        .exam-questions {
            background: var(--accent);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 8px;
        }

        .exam-card p {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .exam-details {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--gray-600);
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
        }

        .exam-details span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .exam-details i {
            color: var(--accent);
        }

        .question-container {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 24px;
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
        }

        .question-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-100);
        }

        .question-number {
            background: var(--gradient-primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .question-title {
            flex: 1;
            font-weight: 600;
            font-size: 16px;
        }

        .question-text {
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .answer-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            resize: vertical;
            min-height: 100px;
        }

        .answer-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        .result-card {
            background: linear-gradient(135deg, #f0f6ff 0%, #ffffff 100%);
            border: 2px solid var(--accent);
            padding: 40px;
            border-radius: var(--radius-xl);
            text-align: center;
        }

        .result-score {
            font-size: 56px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .result-status {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .result-status.passed {
            color: #2ecc71;
        }

        .result-status.failed {
            color: #e74c3c;
        }

        .exam-controls {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            justify-content: center;
        }

        .timer {
            position: fixed;
            top: 100px;
            right: 30px;
            background: var(--gradient-primary);
            color: white;
            padding: 16px 24px;
            border-radius: var(--radius);
            font-weight: 700;
            font-size: 18px;
            box-shadow: var(--shadow-lg);
            z-index: 100;
        }

        @media (max-width: 900px) {
            .content-wrapper {
                padding: 20px;
            }
            .grid-2 {
                grid-template-columns: 1fr;
            }
            .timer {
                position: static;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 576px) {
            .nav-links {
                display: none;
            }
            body {
                padding-top: 70px;
            }
            .card h1 {
                font-size: 24px;
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
                <a href="exams.php" class="active"><i class="fas fa-file-alt"></i> Exams</a>
                <a href="dashboard.php"><i class="fas fa-user-circle"></i> Account</a>
            </div>
            <div class="nav-actions">
                <a href="logout.php" class="btn btn-secondary btn-sm" style="background-color: #ff6b6b; color: white; border-color: #ff6b6b;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <?php if (!$exam_started && !$submission_result): ?>
            <!-- SELECT EXAM TYPE - FIRST STAGE -->
            <?php if (!$exam_type): ?>
                <div class="card">
                    <h1><i class="fas fa-file-alt"></i> Take an Exam</h1>
                    <p>Choose whether you want to take a subject exam (school curriculum) or a unit exam (university).</p>
                </div>

                <div class="card">
                    <div class="type-selection">
                        <a href="take_exam.php?exam_type=subject&stage=level" class="type-card">
                            <div class="type-icon"><i class="fas fa-book"></i></div>
                            <h3>Subject Exam</h3>
                            <p>Take exams for P1-P7 and S1-S6 subjects</p>
                            <span class="type-arrow"><i class="fas fa-arrow-right"></i></span>
                        </a>
                        <a href="take_exam.php?exam_type=unit&stage=course" class="type-card">
                            <div class="type-icon"><i class="fas fa-graduation-cap"></i></div>
                            <h3>Unit Exam</h3>
                            <p>Take exams for university courses and units</p>
                            <span class="type-arrow"><i class="fas fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>

            <!-- SELECT LEVEL - SUBJECT EXAMS -->
            <?php elseif ($exam_type === 'subject' && !$selected_level): ?>
                <div class="card">
                    <h1><i class="fas fa-layer-group"></i> Select Education Level</h1>
                    <p>Choose the education level for your exam:</p>
                </div>

                <div class="card">
                    <div class="selection-grid">
                        <?php foreach ($available_levels as $level): ?>
                            <a href="take_exam.php?exam_type=subject&stage=class&level_id=<?php echo $level['id']; ?>" class="selection-card">
                                <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                                <h3><?php echo htmlspecialchars($level['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($level['description'] ?? '', 0, 60)); ?>...</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="take_exam.php" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT CLASS - SUBJECT EXAMS -->
            <?php elseif ($exam_type === 'subject' && $selected_level && !$selected_class): ?>
                <div class="card">
                    <h1><i class="fas fa-list"></i> Select Class</h1>
                    <p>Choose a class for your exam:</p>
                </div>

                <div class="card">
                    <div class="selection-grid">
                        <?php foreach ($available_classes as $class): ?>
                            <a href="take_exam.php?exam_type=subject&stage=subject&level_id=<?php echo $selected_level; ?>&class_id=<?php echo $class['id']; ?>" class="selection-card">
                                <div class="card-icon"><i class="fas fa-book"></i></div>
                                <h3><?php echo htmlspecialchars($class['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($class['description'] ?? '', 0, 60)); ?>...</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="take_exam.php?exam_type=subject&stage=level&level_id=<?php echo $selected_level; ?>" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT SUBJECT - WITH SEARCH & EXAMS -->
            <?php elseif ($exam_type === 'subject' && $selected_class && !$selected_subject): ?>
                <div class="card">
                    <h1><i class="fas fa-search"></i> Select Subject</h1>
                    <p>Choose a subject to take the exam for:</p>
                    
                    <?php if (count($available_subjects) > 5): ?>
                        <input type="text" id="subjectSearch" class="search-input" placeholder="Search subjects..." style="margin-top: 16px;">
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="selection-grid" id="subjectGrid">
                        <?php foreach ($available_subjects as $subject): ?>
                            <a href="take_exam.php?exam_type=subject&id=<?php echo $subject['id']; ?>&selected_subject=1" class="selection-card subject-card" data-name="<?php echo strtolower($subject['name']); ?>">
                                <div class="card-icon"><i class="fas fa-book-open"></i></div>
                                <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                                <p>Click to view exams</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="take_exam.php?exam_type=subject&stage=class&level_id=<?php echo $selected_level; ?>" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT EXAM - SUBJECT EXAMS -->
            <?php elseif ($exam_type === 'subject' && isset($_GET['selected_subject']) && $selected_subject): ?>
                <div class="card">
                    <h1><i class="fas fa-file-alt"></i> Available Exams</h1>
                    <p>Select an exam to start:</p>
                </div>

                <div class="card">
                    <?php if (count($exam_subjects) > 0): ?>
                        <div class="selection-grid">
                            <?php foreach ($exam_subjects as $exam): ?>
                                <div class="exam-card">
                                    <div class="exam-header">
                                        <h3><?php echo htmlspecialchars($exam['title']); ?></h3>
                                        <span class="exam-questions"><?php echo $exam['total_questions']; ?> Q</span>
                                    </div>
                                    <p><?php echo htmlspecialchars(substr($exam['description'] ?? '', 0, 80)); ?></p>
                                    <div class="exam-details">
                                        <span><i class="fas fa-clock"></i> <?php echo $exam['time_limit_minutes']; ?> min</span>
                                        <span><i class="fas fa-check"></i> <?php echo $exam['passing_percentage']; ?>% pass</span>
                                    </div>
                                    <a href="take_exam.php?exam_id=<?php echo $exam['id']; ?>&start=1&attempt_id=0" class="btn btn-primary" style="width: 100%; margin-top: 12px;">
                                        Start Exam
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-info-circle" style="color: var(--accent); font-size: 32px; margin-bottom: 16px; display: block;"></i>
                            <p>No exams available for this subject yet.</p>
                        </div>
                    <?php endif; ?>
                    <a href="take_exam.php?exam_type=subject&stage=subject&level_id=<?php echo $selected_level; ?>&class_id=<?php echo $selected_class; ?>" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT COURSE - UNIT EXAMS -->
            <?php elseif ($exam_type === 'unit' && !$selected_course): ?>
                <div class="card">
                    <h1><i class="fas fa-university"></i> Select Course</h1>
                    <p>Choose a course to take the exam for:</p>
                    
                    <?php if (count($courses) > 8): ?>
                        <input type="text" id="courseSearch" class="search-input" placeholder="Search courses..." style="margin-top: 16px;">
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="selection-grid" id="courseGrid">
                        <?php foreach ($courses as $course): ?>
                            <a href="take_exam.php?exam_type=unit&stage=unit&course_id=<?php echo $course['id']; ?>" class="selection-card course-card" data-name="<?php echo strtolower($course['name']); ?>">
                                <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                                <h3><?php echo htmlspecialchars($course['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 60)); ?>...</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="take_exam.php" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT UNIT - WITH SEARCH & EXAMS -->
            <?php elseif ($exam_type === 'unit' && $selected_course && !isset($_GET['selected_unit'])): ?>
                <div class="card">
                    <h1><i class="fas fa-search"></i> Select Unit</h1>
                    <p>Choose a unit to take the exam for:</p>
                    
                    <?php if (count($available_units) > 8): ?>
                        <input type="text" id="unitSearch" class="search-input" placeholder="Search units..." style="margin-top: 16px;">
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="selection-grid" id="unitGrid">
                        <?php foreach ($available_units as $unit): ?>
                            <a href="take_exam.php?exam_type=unit&course_id=<?php echo $selected_course; ?>&selected_unit=1&unit_id=<?php echo $unit['id']; ?>" class="selection-card unit-card" data-name="<?php echo strtolower($unit['title'] ?? $unit['name'] ?? ''); ?>">
                                <div class="card-icon"><i class="fas fa-book-open"></i></div>
                                <h3><?php echo htmlspecialchars($unit['title'] ?? $unit['name'] ?? 'Unit'); ?></h3>
                                <p>Click to view exams</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="take_exam.php?exam_type=unit&stage=course" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <!-- SELECT EXAM - UNIT EXAMS -->
            <?php elseif ($exam_type === 'unit' && isset($_GET['selected_unit']) && !$exam_started): ?>
                <div class="card">
                    <h1><i class="fas fa-file-alt"></i> Available Exams</h1>
                    <p>Select an exam to start:</p>
                </div>

                <div class="card">
                    <?php if (count($exam_units) > 0): ?>
                        <div class="selection-grid">
                            <?php foreach ($exam_units as $exam): ?>
                                <div class="exam-card">
                                    <div class="exam-header">
                                        <h3><?php echo htmlspecialchars($exam['title']); ?></h3>
                                        <span class="exam-questions"><?php echo $exam['total_questions']; ?> Q</span>
                                    </div>
                                    <p><?php echo htmlspecialchars(substr($exam['description'] ?? '', 0, 80)); ?></p>
                                    <div class="exam-details">
                                        <span><i class="fas fa-clock"></i> <?php echo $exam['time_limit_minutes']; ?> min</span>
                                        <span><i class="fas fa-check"></i> <?php echo $exam['passing_percentage']; ?>% pass</span>
                                    </div>
                                    <a href="take_exam.php?exam_id=<?php echo $exam['id']; ?>&start=1" class="btn btn-primary" style="width: 100%; margin-top: 12px;">
                                        Start Exam
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-info-circle" style="color: var(--accent); font-size: 32px; margin-bottom: 16px; display: block;"></i>
                            <p>No exams available for this unit yet.</p>
                        </div>
                    <?php endif; ?>
                    <a href="take_exam.php?exam_type=unit&stage=unit&course_id=<?php echo $selected_course; ?>" class="btn btn-secondary" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            <?php endif; ?>

        <?php elseif ($exam_started && !$submission_result): ?>
            <!-- EXAM IN PROGRESS -->
            <div class="timer" id="timer">Time: <span id="time">00:00</span></div>

            <div class="card">
                <h1><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($exam_title); ?> Exam</h1>
                <p><?php echo htmlspecialchars($exam_type_display); ?> Exam - <?php echo count($questions); ?> Questions</p>
                <p style="color: var(--gray-600); margin-top: 16px;">Answer all questions below. Your answers will be saved when you submit the exam.</p>
            </div>

            <?php if (count($questions) > 0): ?>
                <form method="POST">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-container">
                            <div class="question-header">
                                <div class="question-number"><?php echo $index + 1; ?></div>
                                <div class="question-title"><?php echo htmlspecialchars($question['question_title'] ?? 'Question ' . ($index + 1)); ?></div>
                            </div>
                            <div class="question-text">
                                <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
                            </div>
                            <textarea name="answers[<?php echo $question['id']; ?>]" class="answer-input" placeholder="Enter your answer here..." required></textarea>
                        </div>
                    <?php endforeach; ?>

                    <div class="exam-controls">
                        <button type="submit" name="submit_exam" class="btn btn-primary">
                            <i class="fas fa-check"></i> Submit Exam
                        </button>
                        <a href="take_exam.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="card">
                    <p style="text-align: center; padding: 40px;">
                        <i class="fas fa-info-circle" style="color: var(--accent); font-size: 32px; margin-bottom: 16px; display: block;"></i>
                        No questions available for this exam yet.
                    </p>
                    <div style="text-align: center;">
                        <a href="take_exam.php" class="btn btn-primary">Back to Exam Selection</a>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($submission_result): ?>
            <!-- EXAM RESULTS -->
            <div class="card">
                <h1><i class="fas fa-chart-bar"></i> Exam Results</h1>
                <p>Your exam for <?php echo htmlspecialchars($exam_title); ?> has been submitted.</p>
            </div>

            <div class="card result-card">
                <div class="result-score"><?php echo $submission_result['percentage']; ?>%</div>
                <div class="result-status <?php echo $submission_result['passed'] ? 'passed' : 'failed'; ?>">
                    <?php echo $submission_result['passed'] ? '✓ PASSED' : '✗ FAILED'; ?>
                </div>
                <p style="font-size: 18px; color: var(--gray-600); margin-bottom: 32px;">
                    You answered <strong><?php echo $submission_result['score']; ?></strong> out of <strong><?php echo $submission_result['total']; ?></strong> questions correctly.
                </p>
                <div class="exam-controls">
                    <a href="take_exam.php" class="btn btn-primary">Take Another Exam</a>
                    <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Search/filter functionality
        const subjectSearch = document.getElementById('subjectSearch');
        const courseSearch = document.getElementById('courseSearch');
        const unitSearch = document.getElementById('unitSearch');

        function setupSearch(searchInput, gridId, cardClass) {
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const grid = document.getElementById(gridId);
                const cards = grid.querySelectorAll('.' + cardClass);

                cards.forEach(card => {
                    const name = card.dataset.name;
                    if (name.includes(query)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        }

        setupSearch(subjectSearch, 'subjectGrid', 'subject-card');
        setupSearch(courseSearch, 'courseGrid', 'course-card');
        setupSearch(unitSearch, 'unitGrid', 'unit-card');

        // Simple timer
        let seconds = 0;
        setInterval(() => {
            seconds++;
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            const timeDisplay = document.getElementById('time');
            if (timeDisplay) {
                timeDisplay.textContent = 
                    String(hours).padStart(2, '0') + ':' +
                    String(mins).padStart(2, '0') + ':' +
                    String(secs).padStart(2, '0');
            }
        }, 1000);
    </script>
</body>
</html>
