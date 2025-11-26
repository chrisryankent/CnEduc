<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: levels.php');
    exit;
}
$topic_id = (int)$_GET['id'];
$topic = get_topic($topic_id);
if (!$topic) {
    echo 'Topic not found';
    exit;
}
$subject = get_subject($topic['subject_id']);
$class = cned_get_class($subject['class_id']);
$level = get_level($class['level_id']);

// Get videos and resources for this topic
$videos = get_topic_videos($topic_id);
$resources = get_topic_resources($topic_id);

include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <div class="breadcrumb"><a href="levels.php">Levels</a> &raquo; <a href="classes.php?level_id=<?php echo $level['id']; ?>">Classes</a> &raquo; <a href="subjects.php?class_id=<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></a> &raquo; <a href="topics.php?subject_id=<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></a> &raquo; <?php echo htmlspecialchars($topic['title']); ?></div>
  <h1><?php echo htmlspecialchars($topic['title']); ?></h1>
  
  <div><?php echo nl2br(htmlspecialchars($topic['content'])); ?></div>

  <!-- VIDEO TUTORIALS SECTION -->
  <?php if (!empty($videos)): ?>
    <div style="margin-top: 40px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
      <h2 style="margin-top: 0; color: #333;"><i class="fas fa-video"></i> Video Tutorials</h2>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($videos as $video): ?>
          <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; font-size: 16px; color: #0f62fe;">
              <i class="fas fa-play-circle"></i> <?php echo htmlspecialchars($video['title']); ?>
            </h3>
            <?php if (!empty($video['description'])): ?>
              <p style="color: #666; font-size: 14px; margin: 8px 0;">
                <?php echo htmlspecialchars($video['description']); ?>
              </p>
            <?php endif; ?>
            <?php if ($video['video_provider'] === 'youtube'): ?>
              <iframe width="100%" height="200" style="border-radius: 4px; margin-bottom: 10px;"
                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['video_url']); ?>" 
                frameborder="0" allowfullscreen></iframe>
            <?php elseif ($video['video_provider'] === 'vimeo'): ?>
              <iframe src="https://player.vimeo.com/video/<?php echo htmlspecialchars($video['video_url']); ?>" 
                width="100%" height="200" frameborder="0" allowfullscreen style="border-radius: 4px; margin-bottom: 10px;"></iframe>
            <?php else: ?>
              <video width="100%" height="200" controls style="border-radius: 4px; margin-bottom: 10px;">
                <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            <?php endif; ?>
            <?php if (!empty($video['duration_seconds'])): ?>
              <p style="color: #999; font-size: 12px; margin: 0;">
                <i class="far fa-clock"></i> Duration: <?php echo floor($video['duration_seconds'] / 60); ?>:<?php echo str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT); ?>
              </p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- RESOURCES/PDF SECTION -->
  <?php if (!empty($resources)): ?>
    <div style="margin-top: 40px; padding: 20px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
      <h2 style="margin-top: 0; color: #333;"><i class="fas fa-file-pdf"></i> Learning Resources</h2>
      <ul style="list-style: none; padding: 0;">
        <?php foreach ($resources as $resource): ?>
          <li style="margin-bottom: 15px; padding: 12px; background: white; border-radius: 4px; border-left: 3px solid #ffc107;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
              <div style="flex: 1;">
                <h4 style="margin: 0 0 5px 0; color: #0f62fe;">
                  <i class="fas fa-file-download"></i> <?php echo htmlspecialchars($resource['title']); ?>
                </h4>
                <?php if (!empty($resource['description'])): ?>
                  <p style="margin: 5px 0; color: #666; font-size: 14px;">
                    <?php echo htmlspecialchars($resource['description']); ?>
                  </p>
                <?php endif; ?>
                <?php if (!empty($resource['file_size'])): ?>
                  <small style="color: #999;">
                    File size: <?php echo round($resource['file_size'] / 1024 / 1024, 2); ?> MB
                  </small>
                <?php endif; ?>
              </div>
              <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" 
                 download 
                 class="btn" 
                 style="background: #ffc107; color: #333; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin-left: 15px; white-space: nowrap;">
                <i class="fas fa-download"></i> Download
              </a>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <p style="margin-top: 30px;"><a href="topics.php?subject_id=<?php echo $subject['id']; ?>">Back to Topics</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
