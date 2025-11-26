<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: levels.php');
    exit;
}
$topic_id = (int)$_GET['id'];
$topic = get_topic($topic_id);
if (!$topic) {
    echo 'Topic not found';
    exit;
}
$subject = get_subject($topic['subject_id']);
$class = cned_get_class($subject['class_id']);
$level = get_level($class['level_id']);
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <div class="breadcrumb"><a href="levels.php">Levels</a> &raquo; <a href="classes.php?level_id=<?php echo $level['id']; ?>">Classes</a> &raquo; <a href="subjects.php?class_id=<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></a> &raquo; <a href="topics.php?subject_id=<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></a> &raquo; <?php echo htmlspecialchars($topic['title']); ?></div>
  <h1><?php echo htmlspecialchars($topic['title']); ?></h1>
  <div><?php echo nl2br(htmlspecialchars($topic['content'])); ?></div>
  <p><a href="topics.php?subject_id=<?php echo $subject['id']; ?>">Back to Topics</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
