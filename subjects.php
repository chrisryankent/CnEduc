<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['class_id'])) {
    header('Location: levels.php');
    exit;
}
$class_id = (int)$_GET['class_id'];
$class = cned_get_class($class_id);
if (!$class) {
    echo 'Class not found';
    exit;
}
$level = get_level($class['level_id']);
$subjects = get_subjects_by_class($class_id);
include __DIR__ . '/includes/header.php';

$badge = ($level['id'] === 1) ? '<span class="primary-badge">PRIMARY</span>' : '<span class="secondary-badge">SECONDARY</span>';
?>

<div class="content-wrapper">
  <div class="card">
    <div class="breadcrumb">
      <a href="index.php">Home</a> &raquo; 
      <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a> &raquo; 
      <?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?>
    </div>
    <h1><?php echo htmlspecialchars($class['name']); ?> Subjects</h1>
    <p><?php echo htmlspecialchars($class['description']); ?></p>
  </div>

  <div class="grid">
    <main class="main">
      <div class="card">
        <h2>📖 Available Subjects</h2>
        <p>Select a subject to explore its topics:</p>
        
        <div class="grid-2">
          <?php foreach ($subjects as $subject): ?>
            <a href="topics.php?subject_id=<?php echo $subject['id']; ?>" class="feature-card" style="cursor: pointer; text-decoration: none;">
              <h3 style="color: #0066cc; margin-top: 0;">
                <?php echo htmlspecialchars($subject['name']); ?>
              </h3>
              <p style="font-size: 13px; color: #666;">
                <?php 
                $topic_count = 0;
                $topics = get_topics_by_subject($subject['id']);
                $topic_count = count($topics);
                echo $topic_count . ' topic' . ($topic_count !== 1 ? 's' : '');
                ?>
              </p>
              <div style="color: #0066cc; font-weight: 600; font-size: 13px;">
                View Topics →
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>📚 Navigation</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li><a href="index.php">Home</a></li>
          <li><a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?> Classes</a></li>
          <li><a href="explore.php">Explore All</a></li>
          <li><a href="search.php">Search</a></li>
        </ul>
      </div>

      <div class="card">
        <h3>ℹ️ Class Info</h3>
        <p style="font-size: 13px; color: #666;">
          <strong><?php echo htmlspecialchars($class['name']); ?></strong><br>
          <?php echo htmlspecialchars($level['name']); ?> Level<br>
          <?php echo count($subjects); ?> subject<?php echo count($subjects) !== 1 ? 's' : ''; ?>
        </p>
      </div>

      <div class="card" style="background: #f0f5ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0;">💡 Tip</h3>
        <p style="font-size: 13px; color: #666;">
          Click on any subject to see all the topics for that subject. Topics are arranged from foundational to advanced.
        </p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
