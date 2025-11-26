<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['course_id'])) {
    header('Location: courses.php');
    exit;
}
$course_id = (int)$_GET['course_id'];
$course = get_course($course_id);
if (!$course) {
    echo 'Course not found';
    exit;
}
$units = get_units_by_course($course_id);
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <div class="breadcrumb"><a href="courses.php">Courses</a> &raquo; <?php echo htmlspecialchars($course['name']); ?></div>
  <h1>Units for <?php echo htmlspecialchars($course['name']); ?></h1>
  <ul>
    <?php foreach ($units as $unit): ?>
      <li><a href="read_unit.php?id=<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['code'] . ' - ' . $unit['title']); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <p><a href="courses.php">Back to Courses</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
