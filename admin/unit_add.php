<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$courses = get_courses();

if (isset($_POST['title'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $course_id_form = (int)$_POST['course_id'];
        $code = $_POST['code'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $position = (int)$_POST['position'];
        
        // Validate inputs
        $error = validate_unit_code($code);
        if (!$error) {
            $error = validate_unit_title($title);
        }
        if (!$error) {
            $error = validate_topic_content($content);
        }
        if (!$error && $course_id_form <= 0) {
            $error = 'Error: Please select a valid course.';
        }
        
        if (!$error) {
            if (add_unit($course_id_form, $code, $title, $content, $position)) {
                header('Location: units_list.php?course_id=' . $course_id_form);
                exit;
            } else {
                $error = 'Failed to add unit. Please try again.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Add Unit</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <form method="post">
    <div>
      <label>Course<br>
        <select name="course_id" required>
          <option value="">-- Select Course --</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $course_id === $c['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div>
      <label>Unit Code<br><input type="text" name="code" placeholder="e.g., CS101" style="width:100%;"></label>
    </div>
    <div>
      <label>Title<br><input type="text" name="title" required style="width:100%;"></label>
    </div>
    <div>
      <label>Content<br><textarea name="content" style="width:100%; height:300px;"></textarea></label>
    </div>
    <div>
      <label>Position<br><input type="number" name="position" value="0"></label>
    </div>
    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" value="Add Unit">
      <a href="units_list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
