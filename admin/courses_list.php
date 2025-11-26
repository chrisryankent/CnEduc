<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_auth.php';
include __DIR__ . '/header.php';

$courses = get_courses();
?>

<div class="grid">
  <main class="main">
    <div class="card">
      <h1>Manage Courses</h1>
      <p><a href="course_add.php" class="btn">+ Add New Course</a></p>
      
      <?php if (empty($courses)): ?>
        <p>No courses added yet.</p>
      <?php else: ?>
        <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th>Course Name</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['name']); ?></td>
                <td><?php echo htmlspecialchars(substr($course['description'], 0, 80)); ?></td>
                <td>
                  <a href="course_edit.php?id=<?php echo (int)$course['id']; ?>">Edit</a> |
                  <a href="course_delete.php?id=<?php echo (int)$course['id']; ?>" onclick="return confirm('Delete this course?');">Delete</a>
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
      <h3>Courses</h3>
      <p>Manage university courses. Each course contains units of learning content.</p>
      <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
