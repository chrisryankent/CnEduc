<?php
require_once __DIR__ . '/includes/functions.php';
$courses = get_courses();
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h1>University Courses</h1>
  <ul>
    <?php foreach ($courses as $course): ?>
      <li><a href="units.php?course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></a> - <?php echo htmlspecialchars($course['description']); ?></li>
    <?php endforeach; ?>
  </ul>
  <p><a href="levels.php">Back to Levels</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
