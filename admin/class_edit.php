<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: classes_list.php');
    exit;
}

$class_id = (int)$_GET['id'];
$class = cned_get_class($class_id);
if (!$class) {
    die('Class not found');
}

$levels = get_levels();

if (isset($_POST['name'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $level_id = (int)$_POST['level_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = (int)$_POST['position'];
        
        // Validate inputs
        $error = validate_class_name($name);
        if (!$error && $level_id <= 0) {
            $error = 'Error: Please select a valid level.';
        }
        
        if (!$error) {
            $name = $mysqli->real_escape_string($name);
            $description = $mysqli->real_escape_string($description);
            $sql = "UPDATE classes SET level_id = $level_id, name = '$name', description = '$description', position = $position WHERE id = $class_id";
            if ($mysqli->query($sql)) {
                header('Location: classes_list.php?level_id=' . $level_id);
                exit;
            } else {
                $error = 'Failed to update class. Please try again.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Edit Class</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <form method="post">
    <div>
      <label>Level<br>
        <select name="level_id" required>
          <option value="">-- Select Level --</option>
          <?php foreach ($levels as $l): ?>
            <option value="<?php echo $l['id']; ?>" <?php echo $l['id'] === $class['level_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($l['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div>
      <label>Class Name<br><input type="text" name="name" value="<?php echo htmlspecialchars($class['name']); ?>" required style="width:100%;"></label>
    </div>
    <div>
      <label>Description<br><input type="text" name="description" value="<?php echo htmlspecialchars($class['description']); ?>" style="width:100%;"></label>
    </div>
    <div>
      <label>Position<br><input type="number" name="position" value="<?php echo $class['position']; ?>"></label>
    </div>
    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" value="Update Class">
      <a href="classes_list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
