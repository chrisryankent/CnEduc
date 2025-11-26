<?php
require_once __DIR__ . '/includes/functions.php';
$levels = get_levels();

// Dynamically fetch first class of each level instead of hard-coding IDs
$primary_classes = get_classes_by_level(1);
$secondary_classes = get_classes_by_level(2);
$p1_id = !empty($primary_classes) ? $primary_classes[0]['id'] : 1;
$p2_id = count($primary_classes) > 1 ? $primary_classes[1]['id'] : 2;
$s1_id = !empty($secondary_classes) ? $secondary_classes[0]['id'] : 8;
$s2_id = count($secondary_classes) > 1 ? $secondary_classes[1]['id'] : 9;

include __DIR__ . '/includes/header.php';
?>

<div class="hero">
  <h1>Welcome to CnEduc</h1>
  <p>Learn Uganda's curriculum subjects: Primary, Secondary, and University courses</p>
</div>

<div class="content-wrapper">
  <div class="grid">
    <main class="main">
      <!-- School Curriculum Section -->
      <div class="card">
        <h2>📚 School Curriculum</h2>
        <p>Explore subjects and topics organized by class level. Each class has tailored content for your learning stage.</p>
        
        <div class="grid-2">
          <!-- Primary Section -->
          <div class="feature-card">
            <h3><span class="primary-badge">PRIMARY</span></h3>
            <p>Grades P1 to P7 with subjects: Mathematics, English, Science, and Social Studies.</p>
            <ul style="margin: 12px 0; padding-left: 20px; font-size: 13px;">
              <li><a href="classes.php?level_id=1">Browse Primary Classes</a></li>
              <li><a href="subjects.php?class_id=<?php echo $p1_id; ?>">P1 Subjects</a></li>
              <li><a href="subjects.php?class_id=<?php echo $p2_id; ?>">P2 Subjects</a></li>
            </ul>
          </div>

          <!-- Secondary Section -->
          <div class="feature-card">
            <h3><span class="secondary-badge">SECONDARY</span></h3>
            <p>Grades S1 to S6 with advanced sciences, humanities, and specialized subjects.</p>
            <ul style="margin: 12px 0; padding-left: 20px; font-size: 13px;">
              <li><a href="classes.php?level_id=2">Browse Secondary Classes</a></li>
              <li><a href="subjects.php?class_id=<?php echo $s1_id; ?>">S1 Subjects</a></li>
              <li><a href="subjects.php?class_id=<?php echo $s2_id; ?>">S2 Subjects</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- University Section -->
      <div class="card">
        <h2>🎓 University Courses</h2>
        <p>Explore bachelor degree programs and their units. Perfect for higher education learners.</p>
        
        <div class="grid-2">
          <?php 
          $courses = get_courses();
          foreach ($courses as $course): 
          ?>
            <div class="feature-card">
              <h3><?php echo htmlspecialchars(substr($course['name'], 0, 20)); ?></h3>
              <p><?php echo htmlspecialchars(substr($course['description'], 0, 80)); ?></p>
              <a href="units.php?course_id=<?php echo $course['id']; ?>" class="btn" style="font-size: 12px; padding: 8px 12px;">View Units</a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Quick Navigation Grid -->
      <div class="card">
        <h2>🗂️ Browse by Class</h2>
        <div class="grid-4">
          <?php 
          // Get all classes for quick access
          $primary_classes = get_classes_by_level(1);
          $secondary_classes = get_classes_by_level(2);
          
          foreach ($primary_classes as $class):
          ?>
            <a href="subjects.php?class_id=<?php echo $class['id']; ?>" class="class-card">
              <div class="class-card-title"><?php echo htmlspecialchars($class['name']); ?></div>
              <div class="class-card-subtitle"><span class="primary-badge">PRIMARY</span></div>
            </a>
          <?php endforeach; ?>
          
          <?php foreach ($secondary_classes as $class): ?>
            <a href="subjects.php?class_id=<?php echo $class['id']; ?>" class="class-card">
              <div class="class-card-title"><?php echo htmlspecialchars($class['name']); ?></div>
              <div class="class-card-subtitle"><span class="secondary-badge">SECONDARY</span></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Featured Topics -->
      <div class="card">
        <h2>⭐ Featured Topics</h2>
        <p>Check out these popular learning topics:</p>
        <div class="grid-2">
          <?php
          // Get some sample topics for featured section
          $res = $mysqli->query("
            SELECT t.id, t.title, s.name as subject_name, cl.name as class_name
            FROM topics t
            JOIN subjects s ON t.subject_id = s.id
            JOIN classes cl ON s.class_id = cl.id
            LIMIT 6
          ");
          if ($res) {
            while ($row = $res->fetch_assoc()) {
          ?>
              <div class="feature-card">
                <h3><a href="read_topic.php?id=<?php echo $row['id']; ?>" style="color: #0066cc; text-decoration: none;">
                  <?php echo htmlspecialchars($row['title']); ?>
                </a></h3>
                <p style="font-size: 12px; color: #999; margin: 8px 0;">
                  <?php echo htmlspecialchars($row['class_name']); ?> • <?php echo htmlspecialchars($row['subject_name']); ?>
                </p>
              </div>
          <?php 
            }
          }
          ?>
        </div>
      </div>

    </main>

    <aside class="sidebar">
      <!-- Search Widget -->
      <div class="card">
        <h3>🔍 Search</h3>
        <form class="search-form" action="search.php" method="get">
          <input type="text" name="q" placeholder="Search topics, units..." required>
          <input type="submit" value="Go">
        </form>
        <p style="font-size: 12px; color: #666; margin: 0;">Search across all classes and courses</p>
      </div>

      <!-- Quick Navigation -->
      <div class="card">
        <h3>📖 Quick Access</h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <li style="margin: 8px 0;"><a href="classes.php?level_id=1">Primary Classes</a></li>
          <li style="margin: 8px 0;"><a href="classes.php?level_id=2">Secondary Classes</a></li>
          <li style="margin: 8px 0;"><a href="courses.php">University Courses</a></li>
          <li style="margin: 8px 0;"><a href="explore.php">Explore All Content</a></li>
          <li style="margin: 8px 0;"><a href="how_to_use.php">How to Use</a></li>
        </ul>
      </div>

      <!-- Admin Access -->
      <div class="card" style="background: #f0f5ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0;">👤 Admin</h3>
        <p style="margin: 0 0 12px 0; font-size: 13px;">Manage and add content</p>
        <a href="admin/login.php" class="btn" style="font-size: 12px; width: 100%; text-align: center;">Admin Login</a>
      </div>

      <!-- Statistics -->
      <div class="card">
        <h3>📊 Statistics</h3>
        <div class="stats-grid" style="grid-template-columns: 1fr;">
          <?php
          // Get counts for stats
          $levels_count = count($levels);
          $res = $mysqli->query("SELECT COUNT(*) as count FROM classes");
          $classes_count = $res ? $res->fetch_assoc()['count'] : 0;
          $res = $mysqli->query("SELECT COUNT(*) as count FROM topics");
          $topics_count = $res ? $res->fetch_assoc()['count'] : 0;
          $res = $mysqli->query("SELECT COUNT(*) as count FROM courses");
          $courses_count = $res ? $res->fetch_assoc()['count'] : 0;
          ?>
          <div class="stat-item">
            <div class="stat-number"><?php echo $classes_count; ?></div>
            <div class="stat-label">Classes</div>
          </div>
          <div class="stat-item">
            <div class="stat-number"><?php echo $topics_count; ?></div>
            <div class="stat-label">Topics</div>
          </div>
          <div class="stat-item">
            <div class="stat-number"><?php echo $courses_count; ?></div>
            <div class="stat-label">Courses</div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
