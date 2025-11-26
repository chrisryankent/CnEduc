<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: courses.php');
    exit;
}
$unit_id = (int)$_GET['id'];
$unit = get_unit($unit_id);
if (!$unit) {
    echo 'Unit not found';
    exit;
}
$course = get_course($unit['course_id']);
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <div class="breadcrumb"><a href="courses.php">Courses</a> &raquo; <a href="units.php?course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></a> &raquo; <?php echo htmlspecialchars($unit['title']); ?></div>
  <h1><?php echo htmlspecialchars($unit['title']); ?></h1>
  <p><strong>Code:</strong> <?php echo htmlspecialchars($unit['code']); ?></p>
  <div><?php echo nl2br(htmlspecialchars($unit['content'])); ?></div>
  <p><a href="units.php?course_id=<?php echo $course['id']; ?>">Back to Units</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
