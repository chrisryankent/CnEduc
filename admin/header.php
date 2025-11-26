<?php
// admin/header.php - admin area header and sidebar navigation
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin - CnEduc</title>
  <link rel="stylesheet" href="../assets/admin.css" type="text/css">
  <style>
    * { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }
    html { scroll-behavior: smooth; -ms-overflow-style: scrollbar; }
    body { margin: 0; padding: 0; }
  </style>
</head>
<body>
  <!-- Admin Top Bar -->
  <header class="admin-topbar">
    <div class="admin-topbar-content">
      <a class="admin-logo" href="../index.php">
        <span class="admin-logo-icon">📚</span>
        <span class="admin-logo-text">CnEduc</span>
      </a>
      <div class="admin-topbar-right">
        <a href="../" class="admin-topbar-link">View Site</a>
        <a href="logout.php" class="admin-topbar-link logout">Logout</a>
      </div>
    </div>
  </header>

  <!-- Admin Sidebar -->
  <aside class="admin-sidebar">
    <nav class="admin-sidebar-nav">
      <!-- School Curriculum -->
      <div class="admin-sidebar-section">
        <h3 class="admin-sidebar-title">School Curriculum</h3>
        <a href="dashboard.php" class="admin-sidebar-link">📊 Dashboard</a>
        <a href="classes_list.php" class="admin-sidebar-link">🎓 Classes</a>
        <a href="subjects_list.php" class="admin-sidebar-link">📖 Subjects</a>
        <a href="topics_list.php" class="admin-sidebar-link">📝 Topics</a>
      </div>

      <!-- University -->
      <div class="admin-sidebar-section">
        <h3 class="admin-sidebar-title">University</h3>
        <a href="courses_list.php" class="admin-sidebar-link">🎯 Courses</a>
        <a href="units_list.php" class="admin-sidebar-link">📚 Units</a>
      </div>

      <!-- System -->
      <div class="admin-sidebar-section">
        <h3 class="admin-sidebar-title">System</h3>
        <a href="register.php" class="admin-sidebar-link">👤 Add Admin</a>
      </div>
    </nav>
  </aside>

  <!-- Admin Main Content -->
  <main class="admin-main">
    <div class="admin-content">

