<?php
include __DIR__ . '/includes/header.php';
?>

<div class="content-wrapper">
  <div class="card">
    <h1>📚 About CnEduc</h1>
    <p>Making Uganda's school curriculum accessible to every student.</p>
  </div>

  <div class="grid">
    <main class="main">
      <!-- Mission & Vision -->
      <div class="card">
        <h2>🎯 Our Mission</h2>
        <p>CnEduc is dedicated to providing free, well-organized access to Uganda's school curriculum. We believe every student deserves easy access to quality learning materials organized by their current class level.</p>

        <h3>What We Do</h3>
        <p>We provide structured learning content for:</p>
        <ul>
          <li><strong>Primary School (P1-P7)</strong> - Foundation subjects including Mathematics, English, Science, and Social Studies</li>
          <li><strong>Secondary School (S1-S6)</strong> - Advanced subjects including Sciences, Mathematics, Humanities, and more</li>
          <li><strong>University Courses</strong> - Bachelor degree programs and their course units</li>
        </ul>

        <p>All content is organized by class and subject, making it easy for students to find exactly what they need for their current level.</p>
      </div>

      <!-- Curriculum Structure -->
      <div class="card">
        <h2>🗂️ How CnEduc Is Organized</h2>

        <h3>Smart Hierarchy</h3>
        <p>Unlike many learning platforms, CnEduc understands that <strong>each class has different topics</strong>:</p>
        <ul>
          <li>P1 Mathematics focuses on numbers 1-10, basic addition/subtraction</li>
          <li>P3 Mathematics introduces larger numbers and multiplication</li>
          <li>S1 Mathematics moves into algebra and advanced calculations</li>
        </ul>
        <p>This ensures students learn content appropriate for their grade level, not generic "Mathematics" content.</p>

        <h3>Complete Coverage</h3>
        <ul>
          <li>7 Primary classes (P1-P7)</li>
          <li>6 Secondary classes (S1-S6)</li>
          <li>Multiple university degree programs</li>
          <li>Hundreds of topics covering Uganda's curriculum standards</li>
        </ul>
      </div>

      <!-- Features -->
      <div class="card">
        <h2>✨ Key Features</h2>

        <h3>Smart Search</h3>
        <p>Search across all topics and courses. Results show full context (class, subject, course) so you know exactly where content belongs.</p>

        <h3>Organized Navigation</h3>
        <p>Navigate hierarchically: Level → Class → Subject → Topic. Clear breadcrumb trails show where you are at all times.</p>

        <h3>Featured Content</h3>
        <p>Homepage highlights popular and important topics to inspire learning and guide students to quality content.</p>

        <h3>Explore & Filter</h3>
        <p>Use the Explore page to systematically browse all content with class and subject filters for focused learning.</p>

        <h3>Responsive Design</h3>
        <p>Works on desktop, tablet, and mobile. Learn anywhere, anytime.</p>
      </div>

      <!-- Curriculum Alignment -->
      <div class="card">
        <h2>✅ Curriculum Alignment</h2>
        <p>CnEduc's content is aligned with Uganda's National Curriculum Framework for Primary and Secondary education, as well as university academic standards.</p>

        <p>Each topic in the platform corresponds to learning objectives appropriate for that specific class or course level.</p>

        <h3>Primary Education</h3>
        <p>Covers foundational literacy, numeracy, science, and social awareness across 7 grades with age-appropriate progression.</p>

        <h3>Secondary Education</h3>
        <p>Provides advanced instruction in 8+ subjects including specialized sciences, mathematics, languages, and humanities for 6 grades.</p>

        <h3>Higher Education</h3>
        <p>Supports university-level learning with structured bachelor degree programs and course units.</p>
      </div>

      <!-- User Benefits -->
      <div class="card">
        <h2>👥 Who CnEduc Helps</h2>

        <h3>Students</h3>
        <ul>
          <li>Find content specific to their current class level</li>
          <li>Review topics anytime, anywhere</li>
          <li>Supplement classroom learning</li>
          <li>Prepare for assessments</li>
        </ul>

        <h3>Teachers</h3>
        <ul>
          <li>Reference materials aligned with their class curriculum</li>
          <li>Easily find specific topics for lesson planning</li>
          <li>Recommend resources to students</li>
        </ul>

        <h3>Parents</h3>
        <ul>
          <li>Understand what their child is learning</li>
          <li>Find resources to help with homework</li>
          <li>Support learning at home</li>
        </ul>

        <h3>Administrators</h3>
        <ul>
          <li>Manage curriculum content securely</li>
          <li>Add, update, and organize topics</li>
          <li>Maintain university course information</li>
        </ul>
      </div>

      <!-- Technology -->
      <div class="card">
        <h2>🔧 Technology & Security</h2>

        <h3>Built for Performance</h3>
        <p>CnEduc is built with modern PHP and MySQL, designed to be fast, reliable, and responsive on any device.</p>

        <h3>Secure Administration</h3>
        <ul>
          <li>Bcrypt password hashing for admin accounts</li>
          <li>CSRF token protection on all forms</li>
          <li>SQL injection prevention on all queries</li>
          <li>Session-based authentication</li>
        </ul>

        <h3>Data Integrity</h3>
        <p>Content is carefully organized in a relational database ensuring consistency, accuracy, and efficient retrieval.</p>
      </div>

      <!-- Future Roadmap -->
      <div class="card">
        <h2>🚀 Future Enhancements</h2>
        <p>We're continuously improving CnEduc. Planned features include:</p>
        <ul>
          <li>User accounts with personalized learning paths</li>
          <li>Progress tracking and learning analytics</li>
          <li>Interactive quizzes and assessments</li>
          <li>Downloadable PDF study guides</li>
          <li>Video content integration</li>
          <li>Discussion forums and Q&A</li>
          <li>Offline learning capability</li>
          <li>Mobile app version</li>
        </ul>
      </div>

    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>📈 By The Numbers</h3>
        <div class="stats-grid" style="grid-template-columns: 1fr;">
          <?php
          require_once __DIR__ . '/includes/functions.php';
          $res = $mysqli->query("SELECT COUNT(*) as count FROM classes");
          $classes = $res ? $res->fetch_assoc()['count'] : 0;
          $res = $mysqli->query("SELECT COUNT(*) as count FROM subjects");
          $subjects = $res ? $res->fetch_assoc()['count'] : 0;
          $res = $mysqli->query("SELECT COUNT(*) as count FROM topics");
          $topics = $res ? $res->fetch_assoc()['count'] : 0;
          $res = $mysqli->query("SELECT COUNT(*) as count FROM courses");
          $courses = $res ? $res->fetch_assoc()['count'] : 0;
          ?>
          <div class="stat-item">
            <div class="stat-number"><?php echo $classes; ?></div>
            <div class="stat-label">Classes</div>
          </div>
          <div class="stat-item">
            <div class="stat-number"><?php echo $subjects; ?></div>
            <div class="stat-label">Subjects</div>
          </div>
          <div class="stat-item">
            <div class="stat-number"><?php echo $topics; ?></div>
            <div class="stat-label">Topics</div>
          </div>
          <div class="stat-item">
            <div class="stat-number"><?php echo $courses; ?></div>
            <div class="stat-label">Courses</div>
          </div>
        </div>
      </div>

      <div class="card" style="background: #e8f5e9; border-left: 4px solid #2e7d32;">
        <h3 style="margin-top: 0; color: #2e7d32;">💡 Getting Started</h3>
        <ul style="font-size: 13px; padding-left: 16px;">
          <li><a href="./">Home</a></li>
          <li><a href="explore.php">Explore Content</a></li>
          <li><a href="how_to_use.php">How to Use</a></li>
          <li><a href="classes.php?level_id=1">Primary Classes</a></li>
          <li><a href="classes.php?level_id=2">Secondary Classes</a></li>
          <li><a href="courses.php">University Courses</a></li>
        </ul>
      </div>

      <div class="card">
        <h3>🤝 Community</h3>
        <p style="font-size: 13px; color: #666;">
          CnEduc is part of Uganda's educational ecosystem, helping students, teachers, and families access quality curriculum content.
        </p>
      </div>

      <div class="card" style="background: #f0f5ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0;">📧 Questions?</h3>
        <p style="font-size: 13px; color: #666;">
          Need help? Check the <a href="how_to_use.php">How to Use guide</a> or use our search feature to find what you're looking for.
        </p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
