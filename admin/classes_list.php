<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$classes = [];
$level_id = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;
if ($level_id > 0) {
    $level = get_level($level_id);
    $classes = get_classes_by_level($level_id);
} else {
    $res = $mysqli->query("SELECT c.*, l.name AS level_name FROM classes c JOIN levels l ON c.level_id = l.id ORDER BY l.id, c.position");
    $classes = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Manage Classes</h1>
  <?php if ($level_id > 0): ?>
    <p>Level: <?php echo htmlspecialchars($level['name']); ?></p>
  <?php endif; ?>
  
  <p><a href="class_add.php<?php echo $level_id > 0 ? '?level_id=' . $level_id : ''; ?>">+ Add Class</a></p>
  
  <?php if (count($classes) === 0): ?>
    <p>No classes found.</p>
  <?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Level</th>
        <th>Description</th>
        <th>Position</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($classes as $c): ?>
        <tr>
          <td><?php echo $c['id']; ?></td>
          <td><?php echo htmlspecialchars($c['name']); ?></td>
          <td><?php echo htmlspecialchars($c['level_name'] ?? get_level($c['level_id'])['name']); ?></td>
          <td><?php echo htmlspecialchars($c['description']); ?></td>
          <td><?php echo $c['position']; ?></td>
          <td>
            <a href="class_edit.php?id=<?php echo $c['id']; ?>">Edit</a> |
            <a href="class_delete.php?id=<?php echo $c['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
