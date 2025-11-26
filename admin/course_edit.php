<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_auth.php';
include __DIR__ . '/header.php';

$course_id = (int)($_GET['id'] ?? 0);
if ($course_id <= 0) {
  die('Invalid course ID');
}

$sql = "SELECT id, name, description FROM courses WHERE id = $course_id";
$res = $mysqli->query($sql);
if (!$res || $res->num_rows === 0) {
  die('Course not found');
}
$course = $res->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid token';
  } else {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validate inputs
    $error = validate_course_name($name);
    
    if (!$error) {
      $name_esc = $mysqli->real_escape_string($name);
      $desc_esc = $mysqli->real_escape_string($description);
      
      $sql = "UPDATE courses SET name = '$name_esc', description = '$desc_esc' WHERE id = $course_id";
      if ($mysqli->query($sql)) {
        $success = 'Course updated successfully!';
        header('Location: courses_list.php');
        exit;
      } else {
        $error = 'Error updating course. Please try again.';
      }
    }
  }
}
?>

<div class="grid">
  <main class="main">
    <div class="card">
      <h1>Edit Course</h1>
      
      <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>
      
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
        
        <label for="name">Course Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($course['description']); ?></textarea>
        
        <input type="submit" value="Update Course">
      </form>
    </div>
  </main>
  <aside class="sidebar">
    <div class="card">
      <h3>Edit Course</h3>
      <p>Modify course details. Units belonging to this course will not be affected.</p>
      <p><a href="courses_list.php">Back to Courses</a></p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
