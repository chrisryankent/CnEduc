<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$res = $mysqli->query("SELECT c.id, c.name, l.name AS level_name FROM classes c JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position");
$classes = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

if (isset($_POST['name'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $class_id_form = (int)$_POST['class_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = (int)$_POST['position'];
        
        // Validate inputs
        $error = validate_subject_name($name);
        if (!$error && $class_id_form <= 0) {
            $error = 'Error: Please select a valid class.';
        }
        
        if (!$error) {
            $name = $mysqli->real_escape_string($name);
            $description = $mysqli->real_escape_string($description);
            $sql = "INSERT INTO subjects (class_id, name, description, position) VALUES ($class_id_form, '$name', '$description', $position)";
            if ($mysqli->query($sql)) {
                header('Location: subjects_list.php?class_id=' . $class_id_form);
                exit;
            } else {
                $error = 'Failed to add subject. Please try again.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Add Subject</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <form method="post">
    <div>
      <label>Class<br>
        <select name="class_id" required>
          <option value="">-- Select Class --</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $class_id === $c['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['level_name'] . ' - ' . $c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div>
      <label>Subject Name<br><input type="text" name="name" required style="width:100%;"></label>
    </div>
    <div>
      <label>Description<br><input type="text" name="description" style="width:100%;"></label>
    </div>
    <div>
      <label>Position<br><input type="number" name="position" value="0"></label>
    </div>
    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" value="Add Subject">
      <a href="subjects_list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
