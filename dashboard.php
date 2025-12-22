<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// Require login
if (!is_user_logged_in()) {
    header('Location: login.php?redirect=dashboard.php');
    exit;
}

$user = cneduc_get_current_user();
$user_full = get_user($user['id']);
$progress = get_user_progress_summary($user['id']);
$achievements = get_user_achievements($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CnEduc</title>
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
            --gray-100: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-700: #404040;
            --gray-900: #171717;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow-md: 0 6px 20px rgba(0,0,0,0.10);
            --transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            --gradient-primary: linear-gradient(135deg, #0f62fe 0%, #3d8dff 100%);
        }

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #0f62fe15 0%, #3d8dff15 100%);
            color: var(--gray-900);
            line-height: 1.65;
            min-height: 100vh;
            padding: 20px;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 16px 20px;
            margin-bottom: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            background: white;
            color: var(--gray-900);
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn:hover {
            background: var(--gray-100);
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(15, 98, 254, 0.3);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-md);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
        }

        .user-details h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .user-details p {
            font-size: 14px;
            color: var(--gray-700);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-md);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin: 12px 0;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 14px;
            color: var(--gray-700);
            display: flex;
            justify-content: space-between;
        }

        .stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .stat:last-child {
            border-bottom: none;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-700);
        }

        .stat-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--accent);
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 16px;
        }

        .achievement {
            text-align: center;
            padding: 16px;
            background: var(--gray-100);
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .achievement:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .achievement-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .achievement-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-900);
            word-break: break-word;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-700);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        a.btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand"><i class="fas fa-graduation-cap"></i> CnEduc</div>
            <div class="navbar-actions">
                <a href="index.php" class="btn"><i class="fas fa-home"></i> Home</a>
                <a href="explore.php" class="btn"><i class="fas fa-compass"></i> Explore</a>
                <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-user-circle"></i> Account</a>
                <a href="logout.php" class="btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- User Header -->
        <div class="header-card">
            <div class="user-info">
                <div class="avatar"><i class="fas fa-user"></i></div>
                <div class="user-details">
                    <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><i class="fas fa-calendar"></i> Member since <?php echo date('M j, Y', strtotime($user_full['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Progress and Stats -->
        <div class="grid">
            <div class="card">
                <div class="card-title"><i class="fas fa-chart-line"></i> Learning Progress</div>
                <div class="stat">
                    <span class="stat-label">Topics Completed</span>
                    <span class="stat-value"><?php echo $progress['completed_topics']; ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Units Completed</span>
                    <span class="stat-value"><?php echo $progress['completed_units']; ?></span>
                </div>
                <div style="margin-top: 16px;">
                    <div class="progress-text">
                        <span>Overall Progress</span>
                        <span><?php $overall = (($progress['completed_topics'] + $progress['completed_units']) / max(($progress['total_topics'] + $progress['total_units']), 1)) * 100; echo round($overall); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $overall; ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-title"><i class="fas fa-trophy"></i> Achievements</div>
                <div class="stat">
                    <span class="stat-label">Badges Earned</span>
                    <span class="stat-value"><?php echo count($achievements); ?></span>
                </div>
                <div class="actions">
                    <a href="certificate.php" class="btn btn-primary"><i class="fas fa-award"></i> View Certificate</a>
                </div>
            </div>
        </div>

        <!-- Achievements -->
        <div class="card">
            <div class="card-title"><i class="fas fa-star"></i> Your Badges</div>
            <?php if (count($achievements) > 0): ?>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement" title="<?php echo htmlspecialchars($achievement['achievement_name']); ?>">
                            <div class="achievement-icon">
                                <?php
                                $icon = 'fa-star';
                                switch ($achievement['achievement_slug']) {
                                    case 'first_step': $icon = 'fa-baby'; break;
                                    case 'getting_started': $icon = 'fa-hiking'; break;
                                    case 'momentum': $icon = 'fa-fire'; break;
                                    case 'quarter_way': $icon = 'fa-quarter'; break;
                                    case 'halfway_there': $icon = 'fa-heart-half'; break;
                                    case 'almost_done': $icon = 'fa-flag'; break;
                                    case 'master_learner': $icon = 'fa-crown'; break;
                                }
                                ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <div class="achievement-name"><?php echo htmlspecialchars($achievement['achievement_name']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div><i class="fas fa-star"></i></div>
                    <p>Start learning to earn badges!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Continue Learning -->
        <div class="card" style="margin-bottom: 40px;">
            <div class="card-title"><i class="fas fa-book"></i> Continue Learning</div>
            <div class="actions">
                <a href="subjects.php" class="btn btn-primary"><i class="fas fa-search"></i> Explore Subjects</a>
                <a href="courses.php" class="btn btn-primary"><i class="fas fa-university"></i> University Courses</a>
                <a href="classes.php" class="btn btn-primary"><i class="fas fa-school"></i> Classes</a>
            </div>
        </div>
    </div>
</body>
</html>
