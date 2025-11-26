<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_auth.php';

$course_id = (int)($_GET['id'] ?? 0);
if ($course_id <= 0) {
  header('Location: courses_list.php');
  exit;
}

$sql = "SELECT id, name FROM courses WHERE id = $course_id";
$res = $mysqli->query($sql);
if (!$res || $res->num_rows === 0) {
  header('Location: courses_list.php');
  exit;
}
$course = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Invalid token');
  }
  
  // Delete all units in this course first
  $mysqli->query("DELETE FROM units WHERE course_id = $course_id");
  
  // Then delete the course
  $mysqli->query("DELETE FROM courses WHERE id = $course_id");
  
  header('Location: courses_list.php');
  exit;
}

include __DIR__ . '/header.php';
?>

<div class="grid">
  <main class="main">
    <div class="card">
      <h1>Delete Course</h1>
      <p>Are you sure you want to delete the course "<strong><?php echo htmlspecialchars($course['name']); ?></strong>"?</p>
      <p>This will also delete all units belonging to this course.</p>
      
      <form method="post" onsubmit="return confirm('This action cannot be undone. Continue?');">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
        <input type="submit" value="Yes, Delete Course" style="background-color: #dc3545;">
        <a href="courses_list.php">Cancel</a>
      </form>
    </div>
  </main>
  <aside class="sidebar">
    <div class="card">
      <h3>Delete Course</h3>
      <p>Deleting a course will remove all associated units.</p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
