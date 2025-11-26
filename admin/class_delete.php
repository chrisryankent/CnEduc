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

if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $level_id = $class['level_id'];
        if ($mysqli->query("DELETE FROM classes WHERE id = $class_id")) {
            header('Location: classes_list.php?level_id=' . $level_id);
            exit;
        } else {
            $error = 'Failed to delete class';
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Delete Class</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <p>Are you sure you want to delete this class?</p>
  <p><strong><?php echo htmlspecialchars($class['name']); ?></strong></p>
  <p style="color:red; font-weight:bold;">Warning: This will also delete all subjects and topics in this class.</p>
  
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="confirm" value="yes">
    <input type="submit" value="Delete Class">
    <a href="classes_list.php">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
