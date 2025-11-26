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

if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $subject_id = $topic['subject_id'];
        if (delete_topic($topic_id)) {
            header('Location: topics_list.php?subject_id=' . $subject_id);
            exit;
        } else {
            $error = 'Failed to delete topic';
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Delete Topic</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <p>Are you sure you want to delete this topic?</p>
  <p><strong><?php echo htmlspecialchars($topic['title']); ?></strong></p>
  
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="confirm" value="yes">
    <input type="submit" value="Delete Topic">
    <a href="topics_list.php">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
