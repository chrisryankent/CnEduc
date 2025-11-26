<?php
include __DIR__ . '/includes/header.php';
?>

<div class="content-wrapper">
  <div class="card">
    <h1>📖 How to Use CnEduc</h1>
    <p>A quick guide to getting the most out of our learning platform.</p>
  </div>

  <div class="grid">
    <main class="main">
      <!-- Getting Started -->
      <div class="card">
        <h2>🚀 Getting Started</h2>
        <p>CnEduc organizes Uganda's school curriculum into easy-to-navigate classes and subjects. Here's how to use it:</p>

        <h3>Step 1: Choose Your Level</h3>
        <p>Start by selecting whether you're studying:</p>
        <ul>
          <li><strong>Primary School (P1-P7)</strong> - Classes from Primary 1 to Primary 7</li>
          <li><strong>Secondary School (S1-S6)</strong> - Classes from Secondary 1 to Secondary 6</li>
          <li><strong>University Courses</strong> - Bachelor degree programs</li>
        </ul>

        <h3>Step 2: Select Your Class</h3>
        <p>Once you choose a level, select your specific class (P1, P2, S3, etc.). Different classes have different topics even for the same subject.</p>

        <h3>Step 3: Browse Subjects</h3>
        <p>Each class contains core subjects:</p>
        <ul>
          <li><strong>Primary:</strong> Mathematics, English, Science, Social Studies</li>
          <li><strong>Secondary:</strong> Mathematics, English, Sciences (Biology, Chemistry, Physics), Humanities, and more</li>
        </ul>

        <h3>Step 4: Learn Topics</h3>
        <p>Click on any topic to read the full learning content. Topics build on each other within a subject.</p>
      </div>

      <!-- Navigation Tips -->
      <div class="card">
        <h2>🗺️ Navigation Tips</h2>

        <h3>Using the Breadcrumb Trail</h3>
        <p>At the top of each page, you'll see a breadcrumb path showing where you are:</p>
        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin: 12px 0; font-family: monospace;">
          Levels &raquo; Classes &raquo; P1 &raquo; Mathematics &raquo; Numbers 1-10
        </div>
        <p>Click on any link in the breadcrumb to jump back to that section.</p>

        <h3>Quick Navigation Sidebar</h3>
        <p>The right sidebar on most pages has quick links to:</p>
        <ul>
          <li>Search across all content</li>
          <li>Jump to different sections</li>
          <li>Access the admin panel</li>
        </ul>

        <h3>Grid View for Classes</h3>
        <p>On the homepage, you can see all classes in a grid. Click any class card to go directly to its subjects.</p>
      </div>

      <!-- Search Guide -->
      <div class="card">
        <h2>🔍 Using Search</h2>

        <h3>Quick Search</h3>
        <p>The search box is available on every page. Type a keyword to search across:</p>
        <ul>
          <li>Topic titles and content</li>
          <li>Course and unit names</li>
        </ul>

        <h3>Search Results</h3>
        <p>Search results show the full context path, so you know exactly which class and subject each result belongs to:</p>
        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin: 12px 0;">
          <strong>Numbers 1-10</strong><br>
          <span style="font-size: 12px; color: #666;">Primary > P1 > Mathematics</span>
        </div>

        <h3>Advanced Search Tips</h3>
        <ul>
          <li>Search for subject names (e.g., "Biology")</li>
          <li>Search for specific topics (e.g., "Cell Structure")</li>
          <li>Search for course codes (e.g., "CS101")</li>
          <li>Be specific - narrow results are easier to browse</li>
        </ul>
      </div>

      <!-- Explore Feature -->
      <div class="card">
        <h2>📚 Explore All Content</h2>
        <p>Use the Explore page to systematically browse all topics by class and subject.</p>
        <ol>
          <li>Go to <a href="explore.php" class="btn" style="display: inline; padding: 4px 8px; font-size: 12px;">Explore All Content</a></li>
          <li>Select a class from the dropdown</li>
          <li>Optionally filter by subject</li>
          <li>View all matching topics in the results</li>
        </ol>
      </div>

      <!-- Understanding the Curriculum -->
      <div class="card">
        <h2>📊 Understanding the Curriculum</h2>

        <h3>Why Different Topics Per Class?</h3>
        <p>Each class has specific learning objectives. For example:</p>
        <ul>
          <li><strong>P1 Mathematics:</strong> Numbers 1-10, Basic Addition, Basic Subtraction</li>
          <li><strong>P3 Mathematics:</strong> Larger numbers, Multiplication, Division</li>
          <li><strong>S1 Mathematics:</strong> Algebra, Geometry, Advanced Calculations</li>
        </ul>
        <p>This means each class focuses on skills appropriate for that grade level.</p>

        <h3>Core Subjects</h3>
        <p><strong>Primary School</strong> focuses on foundational skills with 4 core subjects that all students study.</p>
        <p><strong>Secondary School</strong> expands to 8+ subjects with more specialized content like individual sciences (Biology, Chemistry, Physics).</p>
        <p><strong>University</strong> offers specialized bachelor degree programs with course units.</p>
      </div>

      <!-- Featured Content -->
      <div class="card">
        <h2>⭐ Featured Content</h2>
        <p>The homepage highlights some featured topics to inspire learning. These are popular or important topics across the curriculum.</p>
        <p>You can always find new content by:</p>
        <ul>
          <li>Browsing your class page directly</li>
          <li>Using the Explore feature with filters</li>
          <li>Searching for specific keywords</li>
          <li>Visiting the homepage for featured recommendations</li>
        </ul>
      </div>

      <!-- Tips for Success -->
      <div class="card">
        <h2>✨ Tips for Effective Learning</h2>
        <ul>
          <li><strong>Start with the basics:</strong> Begin with earlier topics in each subject to build strong foundations</li>
          <li><strong>Read thoroughly:</strong> Take time to understand each topic's content</li>
          <li><strong>Follow sequences:</strong> Topics are ordered logically within subjects</li>
          <li><strong>Use search strategically:</strong> When you want to learn something specific, search for it</li>
          <li><strong>Bookmark favorites:</strong> Use your browser's bookmarks to save important topics</li>
          <li><strong>Review regularly:</strong> Revisit topics to reinforce learning</li>
        </ul>
      </div>

    </main>

    <aside class="sidebar">
      <div class="card">
        <h3>❓ Quick FAQ</h3>
        <dl style="font-size: 13px;">
          <dt style="font-weight: 600; margin-top: 8px;">What level should I choose?</dt>
          <dd style="margin: 4px 0; color: #666;">Choose the class you're currently in or the one you want to study.</dd>

          <dt style="font-weight: 600; margin-top: 12px;">Can I jump between classes?</dt>
          <dd style="margin: 4px 0; color: #666;">Yes! Use the navigation or search to jump to any class or topic.</dd>

          <dt style="font-weight: 600; margin-top: 12px;">Is content updated?</dt>
          <dd style="margin: 4px 0; color: #666;">Content is maintained by administrators regularly.</dd>

          <dt style="font-weight: 600; margin-top: 12px;">Can I print content?</dt>
          <dd style="margin: 4px 0; color: #666;">Use your browser's print function to print any page.</dd>

          <dt style="font-weight: 600; margin-top: 12px;">Who maintains CnEduc?</dt>
          <dd style="margin: 4px 0; color: #666;">Administrators manage content through the secure admin panel.</dd>
        </dl>
      </div>

      <div class="card" style="background: #e8f5e9; border-left: 4px solid #2e7d32;">
        <h3 style="margin-top: 0; color: #2e7d32;">✅ Navigation Summary</h3>
        <ol style="font-size: 13px; padding-left: 16px;">
          <li><a href="./">Home</a> - Start here</li>
          <li><a href="explore.php">Explore</a> - Browse by class</li>
          <li><a href="search.php">Search</a> - Find specific content</li>
          <li><a href="classes.php?level_id=1">Primary</a> - Primary classes</li>
          <li><a href="classes.php?level_id=2">Secondary</a> - Secondary classes</li>
          <li><a href="courses.php">University</a> - University courses</li>
        </ol>
      </div>

      <div class="card">
        <h3>📞 Contact & Support</h3>
        <p style="font-size: 13px; color: #666;">
          For questions about specific content, use the search feature to find relevant topics, or navigate through the class structure to locate information.
        </p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
