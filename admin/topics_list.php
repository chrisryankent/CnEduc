<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$topics = [];
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
if ($subject_id > 0) {
    $subject = get_subject($subject_id);
    $topics = get_topics_by_subject($subject_id);
} else {
    $res = $mysqli->query("SELECT t.*, s.name AS subject_name, c.name AS class_name, l.name AS level_name FROM topics t JOIN subjects s ON t.subject_id = s.id JOIN classes c ON s.class_id = c.id JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position, s.position, t.position");
    $topics = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Manage Topics</h1>
  <?php if ($subject_id > 0): ?>
    <p>Subject: <?php echo htmlspecialchars($subject['name']); ?></p>
  <?php endif; ?>
  
  <p><a href="topic_add.php<?php echo $subject_id > 0 ? '?subject_id=' . $subject_id : ''; ?>">+ Add Topic</a></p>
  
  <?php if (count($topics) === 0): ?>
    <p>No topics found.</p>
  <?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Subject</th>
        <th>Class</th>
        <th>Level</th>
        <th>Position</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($topics as $t): ?>
        <tr>
          <td><?php echo $t['id']; ?></td>
          <td><?php echo htmlspecialchars($t['title']); ?></td>
          <td><?php echo htmlspecialchars($t['subject_name']); ?></td>
          <td><?php echo htmlspecialchars($t['class_name']); ?></td>
          <td><?php echo htmlspecialchars($t['level_name']); ?></td>
          <td><?php echo $t['position']; ?></td>
          <td>
            <a href="topic_edit.php?id=<?php echo $t['id']; ?>">Edit</a> |
            <a href="topic_delete.php?id=<?php echo $t['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
