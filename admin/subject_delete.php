<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: subjects_list.php');
    exit;
}

$subject_id = (int)$_GET['id'];
$subject = get_subject($subject_id);
if (!$subject) {
    die('Subject not found');
}

if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $class_id = $subject['class_id'];
        if ($mysqli->query("DELETE FROM subjects WHERE id = $subject_id")) {
            header('Location: subjects_list.php?class_id=' . $class_id);
            exit;
        } else {
            $error = 'Failed to delete subject';
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Delete Subject</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <p>Are you sure you want to delete this subject?</p>
  <p><strong><?php echo htmlspecialchars($subject['name']); ?></strong></p>
  <p style="color:red; font-weight:bold;">Warning: This will also delete all topics in this subject.</p>
  
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="confirm" value="yes">
    <input type="submit" value="Delete Subject">
    <a href="subjects_list.php">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
