<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Handle delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'CSRF token validation failed';
    } else {
        $type = $_POST['type'] ?? '';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0 && in_array($type, ['unit_video','topic_video','unit_resource','topic_resource'])) {
            // determine table and file column
            switch ($type) {
                case 'unit_video': $table = 'unit_videos'; $fileCol = 'video_url'; break;
                case 'topic_video': $table = 'topic_videos'; $fileCol = 'video_url'; break;
                case 'unit_resource': $table = 'unit_resources'; $fileCol = 'file_path'; break;
                case 'topic_resource': $table = 'topic_resources'; $fileCol = 'file_path'; break;
                default: $table = ''; $fileCol = '';
            }
            if ($table) {
                // get file path to unlink if local
                $stmt = $mysqli->prepare("SELECT `$fileCol` FROM $table WHERE id = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res ? $res->fetch_assoc() : null;
                    $stmt->close();
                    if ($row && !empty($row[$fileCol])) {
                        $path = $row[$fileCol];
                        // if local path starts with /assets/uploads, try unlink
                        if (strpos($path, '/assets/uploads/') === 0) {
                            $fs = __DIR__ . '/..' . $path;
                            if (is_file($fs)) @unlink($fs);
                        }
                    }
                }
                // delete db row
                $del = $mysqli->prepare("DELETE FROM $table WHERE id = ? LIMIT 1");
                if ($del) {
                    $del->bind_param('i', $id);
                    if ($del->execute()) {
                        $message = 'Deleted successfully.';
                    } else {
                        $message = 'Failed to delete from database.';
                    }
                    $del->close();
                }
            }
        } else {
            $message = 'Invalid delete request.';
        }
    }
}

include __DIR__ . '/header.php';

// Fetch lists
$unit_videos = $mysqli->query('SELECT uv.*, u.code AS unit_code, u.title AS unit_title FROM unit_videos uv LEFT JOIN units u ON uv.unit_id = u.id ORDER BY uv.created_at DESC')
    ->fetch_all(MYSQLI_ASSOC);
$topic_videos = $mysqli->query('SELECT tv.*, t.title AS topic_title FROM topic_videos tv LEFT JOIN topics t ON tv.topic_id = t.id ORDER BY tv.created_at DESC')
    ->fetch_all(MYSQLI_ASSOC);
$unit_resources = $mysqli->query('SELECT ur.*, u.code AS unit_code, u.title AS unit_title FROM unit_resources ur LEFT JOIN units u ON ur.unit_id = u.id ORDER BY ur.created_at DESC')
    ->fetch_all(MYSQLI_ASSOC);
$topic_resources = $mysqli->query('SELECT tr.*, t.title AS topic_title FROM topic_resources tr LEFT JOIN topics t ON tr.topic_id = t.id ORDER BY tr.created_at DESC')
    ->fetch_all(MYSQLI_ASSOC);
?>
<div class="card">
  <h1>Media Manager</h1>
  <?php if (!empty($message)): ?><p style="color:green"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>

  <h2>Unit Videos</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Unit</th><th>Title</th><th>Provider / File</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($unit_videos as $v): ?>
      <tr>
        <td><?php echo $v['id']; ?></td>
        <td><?php echo htmlspecialchars(($v['unit_code'] ? $v['unit_code'] . ' - ' : '') . $v['unit_title']); ?></td>
        <td><?php echo htmlspecialchars($v['title']); ?></td>
        <td><?php if ($v['video_provider'] === 'local') { echo '<a href="' . htmlspecialchars($v['video_url']) . '" target="_blank">Local file</a>'; } else { echo htmlspecialchars($v['video_provider'] . ': ' . $v['video_url']); } ?></td>
        <td>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this item?');">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="type" value="unit_video">
            <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
            <input type="submit" value="Delete">
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Topic Videos</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Topic</th><th>Title</th><th>Provider / File</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($topic_videos as $v): ?>
      <tr>
        <td><?php echo $v['id']; ?></td>
        <td><?php echo htmlspecialchars($v['topic_title']); ?></td>
        <td><?php echo htmlspecialchars($v['title']); ?></td>
        <td><?php if ($v['video_provider'] === 'local') { echo '<a href="' . htmlspecialchars($v['video_url']) . '" target="_blank">Local file</a>'; } else { echo htmlspecialchars($v['video_provider'] . ': ' . $v['video_url']); } ?></td>
        <td>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this item?');">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="type" value="topic_video">
            <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
            <input type="submit" value="Delete">
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Unit Resources</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Unit</th><th>Title</th><th>File</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($unit_resources as $r): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars(($r['unit_code'] ? $r['unit_code'] . ' - ' : '') . $r['unit_title']); ?></td>
        <td><?php echo htmlspecialchars($r['title']); ?></td>
        <td><?php echo '<a href="' . htmlspecialchars($r['file_path']) . '" target="_blank">Download</a>'; ?></td>
        <td>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this resource?');">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="type" value="unit_resource">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <input type="submit" value="Delete">
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Topic Resources</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Topic</th><th>Title</th><th>File</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($topic_resources as $r): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['topic_title']); ?></td>
        <td><?php echo htmlspecialchars($r['title']); ?></td>
        <td><?php echo '<a href="' . htmlspecialchars($r['file_path']) . '" target="_blank">Download</a>'; ?></td>
        <td>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this resource?');">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="type" value="topic_resource">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <input type="submit" value="Delete">
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

</div>

<?php include __DIR__ . '/footer.php'; ?>
