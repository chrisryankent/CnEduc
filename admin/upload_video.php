<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';
$success = '';

// Fetch units and topics for selection
$units = [];
$topics = [];
$res = $mysqli->query('SELECT id, code, title, course_id FROM units ORDER BY course_id, position');
if ($res) {
    $units = $res->fetch_all(MYSQLI_ASSOC);
}
$res = $mysqli->query('SELECT id, title, subject_id FROM topics ORDER BY subject_id, position');
if ($res) {
    $topics = $res->fetch_all(MYSQLI_ASSOC);
}
// Fetch courses for course -> units selection
$courses = get_courses();
// Fetch classes and subjects for class->subject->topic selection
$classes = [];
$subjects = [];
$res = $mysqli->query('SELECT id, name, level_id FROM classes ORDER BY level_id, position');
if ($res) {
  $classes = $res->fetch_all(MYSQLI_ASSOC);
}
$res = $mysqli->query('SELECT id, name, class_id FROM subjects ORDER BY class_id, position');
if ($res) {
  $subjects = $res->fetch_all(MYSQLI_ASSOC);
}

if (isset($_POST['submit_video'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF token validation failed';
    } else {
        $target = $_POST['target'] ?? '';
        $target_id = isset($_POST['target_id']) ? (int)$_POST['target_id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $video_provider = $_POST['video_provider'] ?? 'youtube';
        $video_url = trim($_POST['video_url'] ?? '');
        $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;

        if ($title === '') {
            $error = 'Please provide a title for the video.';
        }

        // Handle local file upload
        $uploaded_path = '';
        if ($video_provider === 'local' && !empty($_FILES['video_file']['name'])) {
            $file = $_FILES['video_file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $allowed = ['mp4','webm','ogg','mov','mkv'];
                if (!in_array(strtolower($ext), $allowed)) {
                    $error = 'Unsupported video type. Allowed: ' . implode(', ', $allowed);
                } else {
                    $upload_dir = __DIR__ . '/../assets/uploads/videos';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file['name']);
                    $dest = $upload_dir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        // store web path
                        $uploaded_path = '/assets/uploads/videos/' . $filename;
                        $video_url = $uploaded_path;
                    } else {
                        $error = 'Failed to move uploaded file.';
                    }
                }
            } else {
                $error = 'Error uploading file.';
            }
        }

        if (!$error) {
            if ($target === 'unit') {
                if ($target_id <= 0) $error = 'Please select a unit.';
                else {
                    if (add_unit_video($target_id, $title, $description, $video_url, $video_provider, null, $position)) {
                        $success = 'Unit video uploaded/added successfully.';
                    } else {
                        $error = 'Database error while adding unit video.';
                    }
                }
            } elseif ($target === 'topic') {
                if ($target_id <= 0) $error = 'Please select a topic.';
                else {
                    if (add_topic_video($target_id, $title, $description, $video_url, $video_provider, null, $position)) {
                        $success = 'Topic video uploaded/added successfully.';
                    } else {
                        $error = 'Database error while adding topic video.';
                    }
                }
            } else {
                $error = 'Please select a valid target (unit or topic).';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Upload Video</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p style="color:green"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div>
      <label>Attach To<br>
        <select name="target" id="targetSelect">
          <option value="">-- Select --</option>
          <option value="unit">Unit</option>
          <option value="topic">Topic</option>
        </select>
      </label>
    </div>

    <div id="courseWrap" style="display:none;">
      <label>Course<br>
        <select name="course_id" id="courseSelect">
          <option value="">-- Select Course --</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div id="unitWrap" style="display:none;">
      <label>Unit (filtered by selected course)<br>
        <select name="target_id" id="unitSelect">
          <option value="">-- Select Unit --</option>
        </select>
      </label>
    </div>

    <div id="classWrap" style="display:none;">
      <label>Class<br>
        <select id="classSelect">
          <option value="">-- Select Class --</option>
          <?php foreach ($classes as $cl): ?>
            <option value="<?php echo $cl['id']; ?>"><?php echo htmlspecialchars($cl['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div id="subjectWrap" style="display:none;">
      <label>Subject<br>
        <select id="subjectSelect">
          <option value="">-- Select Subject --</option>
        </select>
      </label>
    </div>

    <div id="topicWrap" style="display:none;">
      <label>Topic<br>
        <select name="target_id" id="topicSelect">
          <option value="">-- Select Topic --</option>
        </select>
      </label>
    </div>

    <div>
      <label>Title<br><input type="text" name="title" required style="width:100%"></label>
    </div>
    <div>
      <label>Description<br><textarea name="description" style="width:100%; height:120px"></textarea></label>
    </div>

    <div>
      <label>Video Provider<br>
        <select name="video_provider">
          <option value="youtube">YouTube</option>
          <option value="vimeo">Vimeo</option>
          <option value="local">Upload File (MP4/WEBM/...)</option>
        </select>
      </label>
    </div>

    <div>
      <label>Video URL / ID (for YouTube/Vimeo)<br><input type="text" name="video_url" placeholder="YouTube ID or full URL" style="width:100%"></label>
    </div>

    <div>
      <label>Or Upload Video File<br><input type="file" name="video_file" accept="video/*"></label>
    </div>

    <div>
      <label>Position<br><input type="number" name="position" value="0"></label>
    </div>

    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" name="submit_video" value="Upload/Add Video">
      <a href="dashboard.php">Cancel</a>
    </div>
  </form>
</div>

<script>
// Client-side filtering: keep units/topics arrays and populate units by course
const units = <?php echo json_encode($units, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const topics = <?php echo json_encode($topics, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const classes = <?php echo json_encode($classes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const subjects = <?php echo json_encode($subjects, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

const targetSelect = document.getElementById('targetSelect');
const courseWrap = document.getElementById('courseWrap');
const unitWrap = document.getElementById('unitWrap');
const topicWrap = document.getElementById('topicWrap');
const courseSelect = document.getElementById('courseSelect');
const unitSelect = document.getElementById('unitSelect');
const topicSelect = document.getElementById('topicSelect');

function showForTarget() {
  const t = targetSelect.value;
  if (t === 'unit') {
    courseWrap.style.display = '';
    unitWrap.style.display = '';
    topicWrap.style.display = 'none';
    populateUnits();
  } else if (t === 'topic') {
    courseWrap.style.display = 'none';
    unitWrap.style.display = 'none';
    // show class/subject/topic cascade
    document.getElementById('classWrap').style.display = '';
    document.getElementById('subjectWrap').style.display = '';
    document.getElementById('topicWrap').style.display = '';
    populateSubjects();
  } else {
    courseWrap.style.display = 'none';
    unitWrap.style.display = 'none';
    document.getElementById('classWrap').style.display = 'none';
    document.getElementById('subjectWrap').style.display = 'none';
    document.getElementById('topicWrap').style.display = 'none';
  }
}

function populateUnits() {
  const courseId = parseInt(courseSelect.value || 0, 10);
  unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
  const filtered = units.filter(u => Number(u.course_id) === courseId);
  if (filtered.length === 0) {
    unitSelect.innerHTML = '<option value="">-- No units for selected course --</option>';
    return;
  }
  filtered.forEach(u => {
    const opt = document.createElement('option');
    opt.value = u.id;
    opt.textContent = 'Unit: ' + (u.code ? (u.code + ' - ') : '') + u.title;
    unitSelect.appendChild(opt);
  });
}

// Populate subjects based on selected class
function populateSubjects() {
  const classId = parseInt(document.getElementById('classSelect').value || 0, 10);
  const subjectSel = document.getElementById('subjectSelect');
  subjectSel.innerHTML = '<option value="">-- Select Subject --</option>';
  const filtered = subjects.filter(s => Number(s.class_id) === classId);
  if (filtered.length === 0) {
    subjectSel.innerHTML = '<option value="">-- No subjects for selected class --</option>';
    // clear topics
    document.getElementById('topicSelect').innerHTML = '<option value="">-- Select Topic --</option>';
    return;
  }
  filtered.forEach(s => {
    const opt = document.createElement('option');
    opt.value = s.id;
    opt.textContent = s.name;
    subjectSel.appendChild(opt);
  });
  // also clear topics
  document.getElementById('topicSelect').innerHTML = '<option value="">-- Select Topic --</option>';
}

// Populate topics for selected subject
function populateTopicsForSubject() {
  const subjectId = parseInt(document.getElementById('subjectSelect').value || 0, 10);
  const topicSel = document.getElementById('topicSelect');
  topicSel.innerHTML = '<option value="">-- Select Topic --</option>';
  const filtered = topics.filter(t => Number(t.subject_id) === subjectId);
  if (filtered.length === 0) {
    topicSel.innerHTML = '<option value="">-- No topics for selected subject --</option>';
    return;
  }
  filtered.forEach(t => {
    const opt = document.createElement('option');
    opt.value = t.id;
    opt.textContent = t.title;
    topicSel.appendChild(opt);
  });
}

document.getElementById('classSelect').addEventListener('change', populateSubjects);
document.getElementById('subjectSelect').addEventListener('change', populateTopicsForSubject);

targetSelect.addEventListener('change', showForTarget);
courseSelect.addEventListener('change', populateUnits);

// initialize (in case user opens and selects immediately)
showForTarget();
</script>

<?php include __DIR__ . '/footer.php'; ?>
