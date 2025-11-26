<?php
require_once __DIR__ . '/includes/functions.php';
$levels = get_levels();
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h1>Choose Your Level</h1>
  <div class="grid">
    <div class="main">
      <ul>
        <?php foreach ($levels as $level): ?>
          <li>
            <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a>
            - <?php echo htmlspecialchars($level['description']); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <aside class="sidebar">
      <div class="card">
        <h3>Quick Links</h3>
        <p><a href="courses.php">University Courses</a></p>
        <p><a href="search.php">Search topics & units</a></p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
