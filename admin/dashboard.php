<?php
require_once __DIR__ . '/_auth.php';
include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Admin Dashboard</h1>
  <p>Welcome, admin. Use these links to manage content:</p>
  
  <h2>School Curriculum</h2>
  <ul>
    <li><a href="classes_list.php">Manage Classes (P1-P7, S1-S6)</a></li>
    <li><a href="subjects_list.php">Manage Subjects</a></li>
    <li><a href="topics_list.php">Manage Topics</a></li>
  </ul>
  
  <h2>University</h2>
  <ul>
    <li><a href="courses_list.php">Manage Courses</a></li>
    <li><a href="units_list.php">Manage Units</a></li>
  </ul>
  
  <p style="color:#666; font-size:12px;">All operations are protected with CSRF tokens. Passwords are hashed with bcrypt.</p>
</div>

<?php include __DIR__ . '/footer.php'; ?>
