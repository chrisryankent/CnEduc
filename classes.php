<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['level_id'])) {
    header('Location: levels.php');
    exit;
}
$level_id = (int)$_GET['level_id'];
$level = get_level($level_id);
if (!$level) {
    echo 'Level not found';
    exit;
}
$classes = get_classes_by_level($level_id);
include __DIR__ . '/includes/header.php';

$badge = ($level_id === 1)
    ? '<span class="primary-badge">PRIMARY</span>'
    : ( ($level_id === 2)
        ? '<span class="secondary-badge">SECONDARY</span>'
        : '<span class="university-badge">UNIVERSITY</span>'
      );
?>

<div class="content-wrapper">
  <div class="card">
    <div class="breadcrumb"><a href="index.php">Home</a> &raquo; <?php echo $badge; ?> <?php echo htmlspecialchars($level['name']); ?></div>
    <h1><?php echo htmlspecialchars($level['name']); ?> Classes</h1>
    <p><?php echo htmlspecialchars($level['description']); ?></p>
  </div>

  <div class="grid">
    <main class="main">
      <div class="card">
        <h2>Choose Your Class</h2>
        <p>Select your class to view subjects and topics:</p>
        
        <div class="grid-2">
          <?php foreach ($classes as $class): ?>
            <a href="subjects.php?class_id=<?php echo $class['id']; ?>" class="class-card">
              <div class="class-card-title"><?php echo htmlspecialchars($class['name']); ?></div>
              <div class="class-card-subtitle"><?php echo htmlspecialchars($class['description']); ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <h2>About This Level</h2>
        <p><?php echo htmlspecialchars($level['description']); ?></p>
        
        <?php if ($level_id === 1): ?>
          <h3>Primary School Subjects</h3>
          <p>All primary classes study these core subjects:</p>
          <ul>
            <li><strong>Mathematics</strong> - Numbers, addition, subtraction, multiplication, division, and more</li>
            <li><strong>English</strong> - Reading, writing, grammar, and communication skills</li>
            <li><strong>Science</strong> - Life sciences, physical sciences, and natural phenomena</li>
            <li><strong>Social Studies</strong> - History, geography, civics, and cultural awareness</li>
          </ul>
        <?php elseif ($level_id === 2): ?>
          <h3>Secondary School Subjects</h3>
          <p>Secondary students study a wider range of specialized subjects:</p>
          <ul>
            <li><strong>Mathematics</strong> - Algebra, geometry, calculus, and advanced problem-solving</li>
            <li><strong>English</strong> - Literature, advanced writing, and language studies</li>
            <li><strong>Biology</strong> - Cell biology, genetics, ecology, and human systems</li>
            <li><strong>Chemistry</strong> - Atomic structure, reactions, and periodic table</li>
            <li><strong>Physics</strong> - Forces, energy, waves, and motion</li>
            <li><strong>History</strong> - World history, African history, and Uganda's past</li>
            <li><strong>Geography</strong> - Earth systems, human geography, and environmental studies</li>
            <li><strong>Social Studies</strong> - Government, economics, and social development</li>
          </ul>
        <?php endif; ?>
      </div>
    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>📚 Navigation</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li><a href="index.php">Home</a></li>
          <li><a href="levels.php">All Levels</a></li>
          <li><a href="explore.php">Explore All</a></li>
          <li><a href="search.php">Search</a></li>
        </ul>
      </div>

      <div class="card">
        <h3>💡 Tip</h3>
        <p style="font-size: 13px; color: #666;">
          Click on any class to see all the subjects available for that grade level. Then select a subject to browse its topics.
        </p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
