<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: units_list.php');
    exit;
}

$unit_id = (int)$_GET['id'];
$unit = get_unit($unit_id);
if (!$unit) {
    die('Unit not found');
}

if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF token validation failed';
    } else {
        $course_id = $unit['course_id'];
        if (delete_unit($unit_id)) {
            header('Location: units_list.php?course_id=' . $course_id);
            exit;
        } else {
            $error = 'Failed to delete unit';
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Delete Unit</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  
  <p>Are you sure you want to delete this unit?</p>
  <p><strong><?php echo htmlspecialchars($unit['title']); ?></strong></p>
  
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="confirm" value="yes">
    <input type="submit" value="Delete Unit">
    <a href="units_list.php">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
