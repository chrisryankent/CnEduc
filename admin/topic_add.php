<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$res = $mysqli->query("SELECT s.id, s.name, c.name AS class_name, l.name AS level_name FROM subjects s JOIN classes c ON s.class_id = c.id JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position, s.position");
$subjects = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

if (isset($_POST['title'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $subject_id_form = (int)$_POST['subject_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $position = (int)$_POST['position'];
        
        // Validate inputs
        $error = validate_topic_title($title);
        if (!$error) {
            $error = validate_topic_content($content);
        }
        if (!$error && $subject_id_form <= 0) {
            $error = 'Error: Please select a valid subject.';
        }
        
        if (!$error) {
            if (add_topic($subject_id_form, $title, $content, $position)) {
                header('Location: topics_list.php?subject_id=' . $subject_id_form);
                exit;
            } else {
                $error = 'Failed to add topic. Please try again.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Add Topic</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <form method="post">
    <div>
      <label>Subject<br>
        <select name="subject_id" required>
          <option value="">-- Select Subject --</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $subject_id === $s['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['level_name'] . ' - ' . $s['class_name'] . ' - ' . $s['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
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
      <input type="submit" value="Add Topic">
      <a href="topics_list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
