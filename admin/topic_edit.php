<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: topics_list.php');
    exit;
}

$topic_id = (int)$_GET['id'];
$topic = get_topic($topic_id);
if (!$topic) {
    die('Topic not found');
}

$res = $mysqli->query("SELECT s.id, s.name, c.name AS class_name, l.name AS level_name FROM subjects s JOIN classes c ON s.class_id = c.id JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position, s.position");
$subjects = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

if (isset($_POST['title'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $subject_id = (int)$_POST['subject_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $position = (int)$_POST['position'];
        
        // Validate inputs
        $error = validate_topic_title($title);
        if (!$error) {
            $error = validate_topic_content($content);
        }
        if (!$error && $subject_id <= 0) {
            $error = 'Error: Please select a valid subject.';
        }
        
        if (!$error) {
            if (update_topic($topic_id, $subject_id, $title, $content, $position)) {
                header('Location: topics_list.php?subject_id=' . $subject_id);
                exit;
            } else {
                $error = 'Failed to update topic. Please try again.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Edit Topic</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <form method="post">
    <div>
      <label>Subject<br>
        <select name="subject_id" required>
          <option value="">-- Select Subject --</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $s['id'] === $topic['subject_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['level_name'] . ' - ' . $s['class_name'] . ' - ' . $s['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div>
      <label>Title<br><input type="text" name="title" value="<?php echo htmlspecialchars($topic['title']); ?>" required style="width:100%;"></label>
    </div>
    <div>
      <label>Content<br><textarea name="content" style="width:100%; height:300px;"><?php echo htmlspecialchars($topic['content']); ?></textarea></label>
    </div>
    <div>
      <label>Position<br><input type="number" name="position" value="<?php echo $topic['position']; ?>"></label>
    </div>
    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" value="Update Topic">
      <a href="topics_list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
