<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// Require login
if (!is_user_logged_in()) {
    header('Location: login.php?redirect=certificate.php');
    exit;
}

$user = cneduc_get_current_user();
$progress = get_user_progress_summary($user['id']);
$achievements = get_user_achievements($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - CnEduc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --gradient-gold: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
            max-width: 900px;
            margin: 0 auto;
        }

        .info-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-md);
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .certificate-container {
            perspective: 1000px;
        }

        .certificate {
            background: white;
            border: 3px solid;
            border-image: linear-gradient(135deg, #0f62fe, #e67e22) 1;
            border-radius: var(--radius-lg);
            padding: 60px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--gradient-primary);
        }

        .certificate::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--gradient-gold);
        }

        .cert-header {
            margin-bottom: 40px;
        }

        .cert-icon {
            font-size: 64px;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .cert-title {
            font-family: "Playfair Display", serif;
            font-size: 48px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .cert-subtitle {
            font-size: 14px;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cert-divider {
            width: 100px;
            height: 2px;
            background: var(--gradient-primary);
            margin: 32px auto;
        }

        .cert-body {
            margin: 32px 0;
            font-size: 16px;
            line-height: 1.8;
        }

        .cert-recipient {
            font-family: "Playfair Display", serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--accent);
            margin: 24px 0;
        }

        .cert-achievement {
            background: var(--gray-100);
            padding: 20px;
            border-radius: var(--radius);
            margin: 24px 0;
        }

        .cert-achievement-label {
            font-size: 14px;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .cert-achievement-value {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent);
        }

        .cert-footer {
            margin-top: 40px;
            font-size: 12px;
            color: var(--gray-700);
        }

        .cert-date {
            font-size: 14px;
            color: var(--gray-700);
            margin-top: 16px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            justify-content: center;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .navbar, .info-card, .actions {
                display: none;
            }
            .certificate-container {
                margin: 0;
            }
            .certificate {
                box-shadow: none;
                border-width: 3px;
            }
        }

        @media (max-width: 600px) {
            .certificate {
                padding: 30px;
            }

            .cert-title {
                font-size: 32px;
            }

            .cert-recipient {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand"><i class="fas fa-graduation-cap"></i> CnEduc</div>
            <div class="navbar-actions">
                <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="info-card">
            <div class="info-title"><i class="fas fa-award"></i> Your Achievement Certificate</div>
            <p>Congratulations on your learning journey! This certificate recognizes your dedication and progress through CnEduc's comprehensive learning platform.</p>
        </div>

        <div class="certificate-container">
            <div class="certificate">
                <div class="cert-header">
                    <div class="cert-icon"><i class="fas fa-award"></i></div>
                    <div class="cert-title">Certificate of Achievement</div>
                    <div class="cert-subtitle">Learning Excellence</div>
                </div>

                <div class="cert-divider"></div>

                <div class="cert-body">
                    <p>This is proudly presented to</p>
                </div>

                <div class="cert-recipient"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>

                <div class="cert-body">
                    <p>In recognition of successful completion of learning activities and demonstrated achievement through the CnEduc platform.</p>
                </div>

                <div class="cert-achievement">
                    <div class="cert-achievement-label">Topics Completed</div>
                    <div class="cert-achievement-value"><i class="fas fa-check-circle"></i> <?php echo $progress['topics_completed']; ?> topics</div>
                </div>

                <div class="cert-achievement">
                    <div class="cert-achievement-label">Units Completed</div>
                    <div class="cert-achievement-value"><i class="fas fa-check-circle"></i> <?php echo $progress['units_completed']; ?> units</div>
                </div>

                <div class="cert-achievement">
                    <div class="cert-achievement-label">Badges Earned</div>
                    <div class="cert-achievement-value"><i class="fas fa-trophy"></i> <?php echo count($achievements); ?> badges</div>
                </div>

                <div class="cert-footer">
                    <p><strong>CnEduc Learning Platform</strong></p>
                    <p>Empowering learners worldwide</p>
                    <div class="cert-date">Issued: <?php echo date('F j, Y'); ?></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print / Save as PDF</button>
            <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
