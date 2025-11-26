<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_auth.php';
include __DIR__ . '/header.php';

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
      
      $sql = "INSERT INTO courses (name, description) VALUES ('$name_esc', '$desc_esc')";
      if ($mysqli->query($sql)) {
        $success = 'Course added successfully!';
        header('Location: courses_list.php');
        exit;
      } else {
        $error = 'Error adding course. Please try again.';
      }
    }
  }
}
?>

<div class="grid">
  <main class="main">
    <div class="card">
      <h1>Add New Course</h1>
      
      <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>
      
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
        
        <label for="name">Course Name:</label>
        <input type="text" id="name" name="name" placeholder="e.g., Computer Science" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Course description" rows="5"></textarea>
        
        <input type="submit" value="Add Course">
      </form>
    </div>
  </main>
  <aside class="sidebar">
    <div class="card">
      <h3>Add Course</h3>
      <p>Create a new university course. You can add units to the course later.</p>
      <p><a href="courses_list.php">Back to Courses</a></p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
