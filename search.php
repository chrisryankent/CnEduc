<?php
require_once __DIR__ . '/includes/functions.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$topics = [];
$units = [];

if ($q !== '') {
    global $mysqli;
    // Search topics with class and level context
    $search_term = '%' . $mysqli->real_escape_string($q) . '%';
    $res = $mysqli->query(
        "SELECT t.id, t.title AS name, t.content, s.name AS subject_name, c.name AS class_name, l.name AS level_name
         FROM topics t
         JOIN subjects s ON t.subject_id = s.id
         JOIN classes c ON s.class_id = c.id
         JOIN levels l ON c.level_id = l.id
         WHERE t.title LIKE '$search_term' OR t.content LIKE '$search_term' OR s.name LIKE '$search_term'
         ORDER BY l.id, c.position, s.position, t.position
         LIMIT 100"
    );
    $topics = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // Search units with course context
    $res = $mysqli->query(
        "SELECT u.id, CONCAT(u.code, ' - ', u.title) AS name, u.content, c.name AS course_name
         FROM units u
         JOIN courses c ON u.course_id = c.id
         WHERE u.code LIKE '$search_term' OR u.title LIKE '$search_term' OR u.content LIKE '$search_term'
         ORDER BY c.id, u.position
         LIMIT 100"
    );
    $units = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

include __DIR__ . '/includes/header.php';
?>

<div class="content-wrapper">
  <div class="grid">
    <main class="main">
      <div class="card">
        <h1>🔍 Search</h1>
        <form action="search.php" method="get" class="search-form">
          <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search topics or units" required>
          <input type="submit" value="Search">
        </form>

        <?php if ($q !== ''): ?>
          <div style="margin: 16px 0;">
            <h3>Results for "<?php echo htmlspecialchars($q); ?>"</h3>
            <p style="color: #666; font-size: 13px;">Found <?php echo count($topics) + count($units); ?> result(s)</p>
          </div>

          <!-- Topics Section -->
          <h2>📖 Topics (<?php echo count($topics); ?>)</h2>
          <?php if (count($topics) === 0): ?>
            <p style="color: #999;">No topics found.</p>
          <?php else: ?>
            <div class="grid-2">
              <?php foreach ($topics as $t): ?>
                <div class="feature-card">
                  <h3><a href="read_topic.php?id=<?php echo $t['id']; ?>" style="color: #0066cc; text-decoration: none;">
                    <?php echo htmlspecialchars($t['name']); ?>
                  </a></h3>
                  <p style="font-size: 12px; color: #999; margin: 8px 0;">
                    <span class="<?php echo $t['level_name'] === 'Primary' ? 'primary-badge' : 'secondary-badge'; ?>">
                      <?php echo htmlspecialchars($t['level_name']); ?>
                    </span>
                  </p>
                  <p style="font-size: 11px; color: #666; margin: 4px 0;">
                    <?php echo htmlspecialchars($t['level_name']); ?> > 
                    <?php echo htmlspecialchars($t['class_name']); ?> > 
                    <?php echo htmlspecialchars($t['subject_name']); ?>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Units Section -->
          <h2 style="margin-top: 30px;">🎓 University Units (<?php echo count($units); ?>)</h2>
          <?php if (count($units) === 0): ?>
            <p style="color: #999;">No units found.</p>
          <?php else: ?>
            <div class="grid-2">
              <?php foreach ($units as $u): ?>
                <div class="feature-card">
                  <h3><a href="read_unit.php?id=<?php echo $u['id']; ?>" style="color: #0066cc; text-decoration: none;">
                    <?php echo htmlspecialchars($u['name']); ?>
                  </a></h3>
                  <p style="font-size: 12px; color: #999; margin: 8px 0;">
                    <span class="university-badge">UNIVERSITY</span>
                  </p>
                  <p style="font-size: 11px; color: #666;">
                    <?php echo htmlspecialchars($u['course_name']); ?>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div style="background: #f0f5ff; padding: 20px; border-radius: 8px; text-align: center;">
            <p>Enter a keyword above to search across all topics and university units.</p>
            <p style="font-size: 13px; color: #666;">Tip: Try searching for subject names like "Mathematics" or "Biology"</p>
          </div>
        <?php endif; ?>
      </div>
    </main>

    <aside class="sidebar">
      <div class="card" style="background: #e8f5e9; border-left: 4px solid #2e7d32;">
        <h3 style="margin-top: 0; color: #2e7d32;">💡 Search Tips</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li>Search by topic name</li>
          <li>Search by subject (Math, Biology, etc.)</li>
          <li>Search by class (P1, S3, etc.)</li>
          <li>Search by course code (CS101, etc.)</li>
          <li>Be specific for better results</li>
        </ul>
      </div>

      <div class="card">
        <h3>📚 Browse Instead?</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li><a href="explore.php">Explore with Filters</a></li>
          <li><a href="classes.php?level_id=1">Primary Classes</a></li>
          <li><a href="classes.php?level_id=2">Secondary Classes</a></li>
          <li><a href="courses.php">University Courses</a></li>
        </ul>
      </div>

      <div class="card">
        <h3>ℹ️ About Search</h3>
        <p style="font-size: 13px; color: #666;">
          Our search looks through topic titles, content, and course information. Results show the full context to help you understand where each result belongs in the curriculum.
        </p>
      </div>
    </aside>
  </div>
</div>

