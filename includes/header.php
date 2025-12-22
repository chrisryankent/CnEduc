<?php
// header.php - site header/navigation
require_once __DIR__ . '/functions.php';
$is_logged_in = is_user_logged_in();
$current_user = $is_logged_in ? cneduc_get_current_user() : null;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CnEduc Tutorial Site</title>
  <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../assets/style.css' : 'assets/style.css'; ?>" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }
    html { scroll-behavior: smooth; -ms-overflow-style: scrollbar; }
    body { margin: 0; padding: 0; }
    .site-nav { display: flex; align-items: center; gap: 20px; }
    .site-nav a { text-decoration: none; }
    .user-menu { position: relative; display: inline-block; }
    .user-menu-btn { background: linear-gradient(135deg, #0f62fe 0%, #3d8dff 100%); color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; }
    .user-menu-dropdown { position: absolute; top: 100%; right: 0; background: white; border: 1px solid #e5e5e5; border-radius: 8px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); min-width: 180px; display: none; z-index: 1000; margin-top: 8px; }
    .user-menu.active .user-menu-dropdown { display: block; }
    .user-menu-dropdown a { display: block; padding: 12px 16px; text-decoration: none; color: #404040; font-size: 14px; border-bottom: 1px solid #f5f5f5; }
    .user-menu-dropdown a:last-child { border-bottom: none; }
    .user-menu-dropdown a:hover { background: #f5f5f5; }
    .auth-links { display: flex; gap: 12px; }
    .auth-links a { text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 14px; }
    .login-link { color: #0f62fe; border: 1px solid #0f62fe; }
    .login-link:hover { background: #0f62fe10; }
    .register-link { background: linear-gradient(135deg, #0f62fe 0%, #3d8dff 100%); color: white; }
    .register-link:hover { box-shadow: 0 4px 12px rgba(15, 98, 254, 0.3); }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a class="site-title" href="index.php">CnEduc</a>
      <nav class="site-nav">
        <a href="index.php">Home</a>
        <a href="explore.php">Explore</a>
        <a href="search.php">Search</a>
        <a href="how_to_use.php">Guide</a>
        <a href="about.php">About</a>
        <a href="admin/login.php">Admin</a>
        
        <?php if ($is_logged_in): ?>
        <div class="user-menu" onclick="this.classList.toggle('active')">
          <button class="user-menu-btn">
            <i class="fas fa-user-circle"></i>
            <?php echo htmlspecialchars($current_user['first_name']); ?>
            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
          </button>
          <div class="user-menu-dropdown">
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="certificate.php"><i class="fas fa-award"></i> Certificate</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
        <?php else: ?>
        <div class="auth-links">
          <a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Login</a>
          <a href="register.php" class="register-link"><i class="fas fa-user-plus"></i> Register</a>
        </div>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <div class="container">
