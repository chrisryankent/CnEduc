<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';

$units = [];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id > 0) {
    $course = get_course($course_id);
    $units = get_units_by_course($course_id);
} else {
    $res = $mysqli->query("SELECT u.*, c.name as course_name FROM units u LEFT JOIN courses c ON u.course_id = c.id ORDER BY u.course_id, u.position, u.id");
    $units = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

include __DIR__ . '/header.php';
?>
<div class="grid">
  <main class="main">
    <div class="card">
      <h1>Manage Units</h1>
      <?php if ($course_id > 0): ?>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($course['name']); ?></p>
      <?php endif; ?>
      
      <p><a href="unit_add.php<?php echo $course_id > 0 ? '?course_id=' . $course_id : ''; ?>" class="btn">+ Add Unit</a></p>
      
      <?php if (count($units) === 0): ?>
        <p>No units found.</p>
      <?php else: ?>
        <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th>Code</th>
              <th>Title</th>
              <th>Course</th>
              <th>Position</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($units as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['code']); ?></td>
                <td><?php echo htmlspecialchars($u['title']); ?></td>
                <td><?php echo htmlspecialchars($u['course_name'] ?? 'N/A'); ?></td>
                <td><?php echo (int)$u['position']; ?></td>
                <td>
                  <a href="unit_edit.php?id=<?php echo (int)$u['id']; ?>">Edit</a> |
                  <a href="unit_delete.php?id=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete this unit?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
  <aside class="sidebar">
    <div class="card">
      <h3>Units</h3>
      <p>Manage university course units. Each unit is part of a course.</p>
      <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
