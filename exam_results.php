<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$is_logged_in = is_user_logged_in();

if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

$user = cneduc_get_current_user();
$user_id = $user['id'];

// Get all exam attempts for this user
$attempts = get_user_exam_attempts($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results - CnEduc</title>
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
            max-width: 1200px;
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

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--gray-100);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 2px solid var(--gray-200);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--gray-200);
        }

        tr:hover {
            background: var(--gray-50);
        }

        .exam-title {
            font-weight: 600;
            color: var(--accent);
        }

        .score {
            font-weight: 600;
            font-size: 16px;
        }

        .score.passed {
            color: #2ecc71;
        }

        .score.failed {
            color: #e74c3c;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-graded {
            background: #d4edda;
            color: #155724;
        }

        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .suspicious {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--gray-400);
            margin-bottom: 16px;
            display: block;
        }

        .empty-state p {
            color: var(--gray-600);
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .card h1 {
                font-size: 24px;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 12px;
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
                <a href="exams.php"><i class="fas fa-file-alt"></i> Exams</a>
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
        <div class="card">
            <h1><i class="fas fa-chart-bar"></i> My Exam Results</h1>
            <p>View your exam attempts and performance history.</p>
        </div>

        <?php if (count($attempts) > 0): ?>
            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Attempt</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Time Taken</th>
                                <th>Flags</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attempts as $attempt): ?>
                                <tr>
                                    <td class="exam-title"><?php echo htmlspecialchars($attempt['exam_title']); ?></td>
                                    <td><?php echo $attempt['attempt_number']; ?></td>
                                    <td>
                                        <small><?php echo date('M d, Y', strtotime($attempt['created_at'])); ?></small>
                                        <br>
                                        <small style="color: var(--gray-600);"><?php echo date('H:i', strtotime($attempt['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($attempt['status']); ?>">
                                            <?php echo ucfirst($attempt['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="score <?php echo $attempt['percentage'] >= 50 ? 'passed' : 'failed'; ?>">
                                            <?php echo round($attempt['percentage'] ?? 0, 1); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $seconds = $attempt['time_taken_seconds'];
                                            if ($seconds) {
                                                $mins = floor($seconds / 60);
                                                $secs = $seconds % 60;
                                                echo sprintf('%d:%02d', $mins, $secs);
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($attempt['suspicious_activity']): ?>
                                            <span class="status-badge suspicious">
                                                <i class="fas fa-exclamation-triangle"></i> Flagged
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--gray-600);">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card empty-state">
                <i class="fas fa-inbox"></i>
                <p>No exam attempts yet</p>
                <a href="take_exam.php" class="btn btn-primary">
                    <i class="fas fa-pencil-alt"></i> Take an Exam
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
