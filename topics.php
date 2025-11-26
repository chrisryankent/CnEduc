<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['subject_id'])) {
    header('Location: levels.php');
    exit;
}
$subject_id = (int)$_GET['subject_id'];
$subject = get_subject($subject_id);
if (!$subject) {
    echo 'Subject not found';
    exit;
}
$class = cned_get_class($subject['class_id']);
$level = get_level($class['level_id']);
$topics = get_topics_by_subject($subject_id);
include __DIR__ . '/includes/header.php';

$badge = ($level['id'] === 1) ? '<span class="primary-badge">PRIMARY</span>' : '<span class="secondary-badge">SECONDARY</span>';
?>

<div class="content-wrapper">
  <div class="card">
    <div class="breadcrumb">
      <a href="index.php">Home</a> &raquo; 
      <a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a> &raquo; 
      <a href="subjects.php?class_id=<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></a> &raquo; 
      <?php echo htmlspecialchars($subject['name']); ?>
    </div>
    <h1><?php echo htmlspecialchars($subject['name']); ?> Topics</h1>
    <p style="font-size: 14px; color: #666;">
      <?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?> • 
      <?php echo count($topics); ?> topic<?php echo count($topics) !== 1 ? 's' : ''; ?>
    </p>
  </div>

  <div class="grid">
    <main class="main">
      <div class="card">
        <h2>📚 Topics to Learn</h2>
        
        <?php if (empty($topics)): ?>
          <p style="color: #999;">No topics found for this subject yet.</p>
        <?php else: ?>
          <div class="grid-2">
            <?php foreach ($topics as $topic): ?>
              <a href="read_topic.php?id=<?php echo $topic['id']; ?>" class="feature-card" style="cursor: pointer; text-decoration: none;">
                <h3 style="color: #0066cc; margin-top: 0;">
                  <?php echo htmlspecialchars($topic['title']); ?>
                </h3>
                <?php if (!empty($topic['content'])): ?>
                  <p style="font-size: 13px; color: #666; line-height: 1.4;">
                    <?php echo htmlspecialchars(substr($topic['content'], 0, 100)); ?>...
                  </p>
                <?php endif; ?>
                <div style="color: #0066cc; font-weight: 600; font-size: 13px;">
                  Read More →
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>📚 Navigation</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li><a href="index.php">Home</a></li>
          <li><a href="classes.php?level_id=<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></a></li>
          <li><a href="subjects.php?class_id=<?php echo $class['id']; ?>">All Subjects</a></li>
          <li><a href="explore.php">Explore All</a></li>
          <li><a href="search.php">Search</a></li>
        </ul>
      </div>

      <div class="card">
        <h3>📖 Subject Info</h3>
        <p style="font-size: 13px; color: #666;">
          <strong><?php echo htmlspecialchars($subject['name']); ?></strong><br>
          <?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?><br>
          <?php echo count($topics); ?> topic<?php echo count($topics) !== 1 ? 's' : ''; ?>
        </p>
      </div>

      <div class="card" style="background: #f0f5ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0;">💡 Learning Tip</h3>
        <p style="font-size: 13px; color: #666;">
          Topics are arranged from basic to advanced. Start with the first topic and work through them in order for best learning results.
        </p>
      </div>

      <div class="card">
        <h3>🔍 Search</h3>
        <form action="search.php" method="get" style="display: flex; gap: 6px;">
          <input type="text" name="q" placeholder="Search..." style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
          <input type="submit" value="Go" class="btn" style="padding: 8px 12px; font-size: 12px;">
        </form>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
