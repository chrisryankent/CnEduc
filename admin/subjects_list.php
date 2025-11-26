<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$subjects = [];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($class_id > 0) {
  $class = cned_get_class($class_id);
  $subjects = get_subjects_by_class($class_id);
} else {
    $res = $mysqli->query("SELECT s.*, c.name AS class_name, l.name AS level_name FROM subjects s JOIN classes c ON s.class_id = c.id JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position, s.position");
    $subjects = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Manage Subjects</h1>
  <?php if ($class_id > 0): ?>
    <p>Class: <?php echo htmlspecialchars($class['name']); ?></p>
  <?php endif; ?>
  
  <p><a href="subject_add.php<?php echo $class_id > 0 ? '?class_id=' . $class_id : ''; ?>">+ Add Subject</a></p>
  
  <?php if (count($subjects) === 0): ?>
    <p>No subjects found.</p>
  <?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Class</th>
        <th>Level</th>
        <th>Position</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($subjects as $s): ?>
        <tr>
          <td><?php echo $s['id']; ?></td>
          <td><?php echo htmlspecialchars($s['name']); ?></td>
          <td><?php echo htmlspecialchars($s['class_name']); ?></td>
          <td><?php echo htmlspecialchars($s['level_name']); ?></td>
          <td><?php echo $s['position']; ?></td>
          <td>
            <a href="subject_edit.php?id=<?php echo $s['id']; ?>">Edit</a> |
            <a href="subject_delete.php?id=<?php echo $s['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
