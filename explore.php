<?php
require_once __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$filter_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$filter_subject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Get all classes and subjects for filtering
$primary_classes = get_classes_by_level(1);
$secondary_classes = get_classes_by_level(2);
$all_classes = array_merge($primary_classes, $secondary_classes);

$filtered_subjects = [];
if ($filter_class > 0) {
  $filtered_subjects = get_subjects_by_class($filter_class);
}

// Get topics based on filters
$topics = [];
if ($filter_class > 0) {
  if ($filter_subject > 0) {
    $topics = get_topics_by_subject($filter_subject);
  } else {
    // Get all topics for the selected class
    $subjects = get_subjects_by_class($filter_class);
    foreach ($subjects as $subject) {
      $subject_topics = get_topics_by_subject($subject['id']);
      $topics = array_merge($topics, $subject_topics);
    }
  }
}
?>

<div class="content-wrapper">
  <div class="card">
    <h1>🔎 Explore Content</h1>
    <p>Browse topics by class and subject. Use filters to find what you're looking for.</p>
  </div>

  <div class="grid">
    <main class="main">
      <!-- Filter Results -->
      <div class="card">
        <h2>Filter Results</h2>
        <form method="get" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
          
          <div style="flex: 1; min-width: 200px;">
            <label for="class_id">Class:</label>
            <select name="class_id" id="class_id" onchange="this.form.submit()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
              <option value="0">-- Select a Class --</option>
              <optgroup label="Primary">
                <?php foreach ($primary_classes as $cls): ?>
                  <option value="<?php echo $cls['id']; ?>" <?php echo $filter_class === $cls['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cls['name']); ?>
                  </option>
                <?php endforeach; ?>
              </optgroup>
              <optgroup label="Secondary">
                <?php foreach ($secondary_classes as $cls): ?>
                  <option value="<?php echo $cls['id']; ?>" <?php echo $filter_class === $cls['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cls['name']); ?>
                  </option>
                <?php endforeach; ?>
              </optgroup>
            </select>
          </div>

          <?php if ($filter_class > 0 && !empty($filtered_subjects)): ?>
            <div style="flex: 1; min-width: 200px;">
              <label for="subject_id">Subject:</label>
              <select name="subject_id" id="subject_id" onchange="this.form.submit()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="0">-- All Subjects --</option>
                <?php foreach ($filtered_subjects as $subj): ?>
                  <option value="<?php echo $subj['id']; ?>" <?php echo $filter_subject === $subj['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($subj['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>

          <?php if ($filter_class > 0): ?>
            <a href="explore.php" class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px;">Clear</a>
          <?php endif; ?>
        </form>
      </div>

      <!-- Topics Display -->
      <?php if ($filter_class > 0): ?>
        <div class="card">
          <?php
          $class = cned_get_class($filter_class);
          $level = get_level($class['level_id']);
          $badge = ($level['id'] === 1) ? '<span class="primary-badge">PRIMARY</span>' : '<span class="secondary-badge">SECONDARY</span>';
          ?>
          <h2><?php echo $badge; ?> <?php echo htmlspecialchars($class['name']); ?> Content</h2>

          <?php if (empty($topics)): ?>
            <p>No topics found for the selected filters.</p>
          <?php else: ?>
            <div class="grid-2">
              <?php foreach ($topics as $topic): 
                $subj = get_subject($topic['subject_id']);
              ?>
                <div class="feature-card">
                  <h3><a href="read_topic.php?id=<?php echo $topic['id']; ?>" style="color: #0066cc; text-decoration: none;">
                    <?php echo htmlspecialchars($topic['title']); ?>
                  </a></h3>
                  <p style="font-size: 12px; color: #999; margin: 8px 0;">
                    📖 <?php echo htmlspecialchars($subj['name']); ?>
                  </p>
                  <?php if (!empty($topic['content'])): ?>
                    <p style="font-size: 13px; color: #666; line-height: 1.4;">
                      <?php echo htmlspecialchars(substr($topic['content'], 0, 100)); ?>...
                    </p>
                  <?php endif; ?>
                  <a href="read_topic.php?id=<?php echo $topic['id']; ?>" class="btn" style="font-size: 11px; padding: 6px 10px;">Read More</a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="card">
          <h2>📚 Browse Classes</h2>
          <p>Select a class above to view all topics for that class.</p>
          
          <h3><span class="primary-badge">PRIMARY SCHOOL</span></h3>
          <div class="grid-4">
            <?php foreach ($primary_classes as $cls): ?>
              <a href="explore.php?class_id=<?php echo $cls['id']; ?>" class="class-card">
                <div class="class-card-title"><?php echo htmlspecialchars($cls['name']); ?></div>
                <div class="class-card-subtitle">Explore Topics</div>
              </a>
            <?php endforeach; ?>
          </div>

          <h3 style="margin-top: 30px;"><span class="secondary-badge">SECONDARY SCHOOL</span></h3>
          <div class="grid-4">
            <?php foreach ($secondary_classes as $cls): ?>
              <a href="explore.php?class_id=<?php echo $cls['id']; ?>" class="class-card">
                <div class="class-card-title"><?php echo htmlspecialchars($cls['name']); ?></div>
                <div class="class-card-subtitle">Explore Topics</div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>💡 Tips</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li>Select a class to view all topics</li>
          <li>Narrow down by selecting a subject</li>
          <li>Click on any topic to read full content</li>
          <li>Use search for quick lookups</li>
        </ul>
      </div>

      <div class="card">
        <h3>📊 Quick Stats</h3>
        <?php
        $all_topics = [];
        foreach ($all_classes as $cls) {
          $subjects = get_subjects_by_class($cls['id']);
          foreach ($subjects as $subj) {
            $all_topics = array_merge($all_topics, get_topics_by_subject($subj['id']));
          }
        }
        ?>
        <div class="stats-grid" style="grid-template-columns: 1fr;">
          <div class="stat-item">
            <div class="stat-number"><?php echo count($all_topics); ?></div>
            <div class="stat-label">Total Topics</div>
          </div>
        </div>
      </div>

      <div class="card" style="background: #f0f5ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0;">🔍 Search</h3>
        <form action="search.php" method="get">
          <div style="margin: 8px 0;">
            <input type="text" name="q" placeholder="Search..." required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
          <input type="submit" value="Search" class="btn" style="width: 100%; text-align: center;">
        </form>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
