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

if (isset($_POST['submit_resource'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF token validation failed';
    } else {
        $target = $_POST['target'] ?? '';
        $target_id = isset($_POST['target_id']) ? (int)$_POST['target_id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;

        // Validate target is selected
        if ($target === '') {
            $error = 'Please select whether you want to attach to a Unit or Topic.';
        } elseif ($title === '') {
            $error = 'Please provide a title for the resource.';
        } elseif ($target_id <= 0) {
            $error = 'Please select a ' . htmlspecialchars($target) . ' to attach the resource to.';
        }

        // Handle file upload
        $uploaded_path = '';
        $file_size = 0;
        $file_type = 'file';
        
        if (!$error && !empty($_FILES['resource_file']['name'])) {
            $file = $_FILES['resource_file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $allowed = ['pdf','doc','docx','ppt','pptx','zip','rar','txt'];
                if (!in_array(strtolower($ext), $allowed)) {
                    $error = 'Unsupported file type. Allowed: ' . implode(', ', $allowed);
                } else {
                    $upload_dir = __DIR__ . '/../assets/uploads/resources';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file['name']);
                    $dest = $upload_dir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $uploaded_path = '/assets/uploads/resources/' . $filename;
                        $file_size = filesize($dest);
                        $file_type = mime_content_type($dest) ?: strtolower($ext);
                    } else {
                        $error = 'Failed to move uploaded file.';
                    }
                }
            } else {
                $error = 'Error uploading file.';
            }
        } elseif (!$error) {
            $error = 'Please choose a file to upload.';
        }

        if (!$error) {
            if ($target === 'unit') {
                if (add_unit_resource($target_id, $title, $description, $uploaded_path, $file_size, $file_type, $position)) {
                    $success = 'Unit resource uploaded successfully.';
                    // Clear form on success
                    $_POST = [];
                } else {
                    $error = 'Database error while adding unit resource.';
                }
            } elseif ($target === 'topic') {
                if (add_topic_resource($target_id, $title, $description, $uploaded_path, $file_size, $file_type, $position)) {
                    $success = 'Topic resource uploaded successfully.';
                    // Clear form on success
                    $_POST = [];
                } else {
                    $error = 'Database error while adding topic resource.';
                }
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h1>Upload Resource (PDF / Document)</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p style="color:green"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div>
      <label>Attach To<br>
        <select name="target">
          <option value="">-- Select --</option>
          <option value="unit">Unit</option>
          <option value="topic">Topic</option>
        </select>
      </label>
    </div>
    <div id="courseWrap" style="display:none;">
      <label>Course<br>
        <select id="courseSelect">
          <option value="">-- Select Course --</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div id="unitWrap" style="display:none;">
      <label>Unit (filtered by selected course)<br>
        <select name="target_id" id="unitSelect" disabled>
          <option value="">-- Select Unit --</option>
        </select>
      </label>
    </div>
      <div id="classWrap" style="display:none;">
        <label>Class<br>
          <select id="classSelect" disabled>
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $cl): ?>
              <option value="<?php echo $cl['id']; ?>"><?php echo htmlspecialchars($cl['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <div id="subjectWrap" style="display:none;">
        <label>Subject<br>
          <select id="subjectSelect" disabled>
            <option value="">-- Select Subject --</option>
          </select>
        </label>
      </div>

      <div id="topicWrap" style="display:none;">
        <label>Topic<br>
          <select name="target_id" id="topicSelect" disabled>
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
      <label>Choose File<br><input type="file" name="resource_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt"></label>
    </div>

    <div>
      <label>Position<br><input type="number" name="position" value="0"></label>
    </div>

    <div>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <input type="submit" name="submit_resource" value="Upload Resource" onclick="validateForm(event)">
      <a href="dashboard.php">Cancel</a>
    </div>
  </form>
</div>

<script>
function validateForm(e) {
  const target = document.querySelector('select[name="target"]').value;
  const unitSelect = document.getElementById('unitSelect');
  const topicSelect = document.getElementById('topicSelect');
  const title = document.querySelector('input[name="title"]').value;
  
  // Get the value from the enabled select
  let targetId = '';
  if (target === 'unit') {
    targetId = unitSelect.value;
  } else if (target === 'topic') {
    targetId = topicSelect.value;
  }
  
  if (!target) {
    e.preventDefault();
    alert('Please select whether to attach to a Unit or Topic');
    return false;
  }
  if (!title.trim()) {
    e.preventDefault();
    alert('Please enter a title');
    return false;
  }
  if (!targetId) {
    e.preventDefault();
    alert('Please select a ' + target);
    return false;
  }
  return true;
}

const units = <?php echo json_encode($units, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const topics = <?php echo json_encode($topics, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const classes = <?php echo json_encode($classes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const subjects = <?php echo json_encode($subjects, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

const targetSel = document.querySelector('select[name="target"]');
const courseWrap = document.getElementById('courseWrap');
const unitWrap = document.getElementById('unitWrap');
const topicWrap = document.getElementById('topicWrap');
const courseSelect = document.getElementById('courseSelect');
const unitSelect = document.getElementById('unitSelect');
const topicSelect = document.getElementById('topicSelect');

function showForTargetRes() {
  const t = targetSel.value;
  const classWrap = document.getElementById('classWrap');
  const subjectWrap = document.getElementById('subjectWrap');
  const classSelect = document.getElementById('classSelect');
  const subjectSelect = document.getElementById('subjectSelect');
  const topicSelect = document.getElementById('topicSelect');
  
  if (t === 'unit') {
    courseWrap.style.display = '';
    unitWrap.style.display = '';
    unitSelect.disabled = false;
    classWrap.style.display = 'none';
    subjectWrap.style.display = 'none';
    topicWrap.style.display = 'none';
    classSelect.disabled = true;
    subjectSelect.disabled = true;
    topicSelect.disabled = true;
    populateUnitsRes();
  } else if (t === 'topic') {
    courseWrap.style.display = 'none';
    unitWrap.style.display = 'none';
    unitSelect.disabled = true;
    classWrap.style.display = '';
    subjectWrap.style.display = '';
    topicWrap.style.display = '';
    classSelect.disabled = false;
    subjectSelect.disabled = false;
    topicSelect.disabled = false;
    // reset subject/topic selects
    subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
    topicSelect.innerHTML = '<option value="">-- Select Topic --</option>';
  } else {
    courseWrap.style.display = 'none';
    unitWrap.style.display = 'none';
    classWrap.style.display = 'none';
    subjectWrap.style.display = 'none';
    topicWrap.style.display = 'none';
    unitSelect.disabled = true;
    classSelect.disabled = true;
    subjectSelect.disabled = true;
    topicSelect.disabled = true;
  }
}

function populateUnitsRes() {
  const courseId = parseInt(courseSelect.value || 0, 10);
  unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
  
  if (courseId === 0) {
    unitSelect.innerHTML = '<option value="">-- Please select a course first --</option>';
    return;
  }
  
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

targetSel.addEventListener('change', showForTargetRes);
courseSelect.addEventListener('change', populateUnitsRes);
// class -> subject -> topic cascade
document.getElementById('classSelect').addEventListener('change', function(){
  const classId = parseInt(this.value || 0, 10);
  const subjectSel = document.getElementById('subjectSelect');
  subjectSel.innerHTML = '<option value="">-- Select Subject --</option>';
  const filtered = subjects.filter(s => Number(s.class_id) === classId);
  filtered.forEach(s => {
    const opt = document.createElement('option'); opt.value = s.id; opt.textContent = s.name; subjectSel.appendChild(opt);
  });
  document.getElementById('topicSelect').innerHTML = '<option value="">-- Select Topic --</option>';
});
document.getElementById('subjectSelect').addEventListener('change', function(){
  const subjId = parseInt(this.value || 0, 10);
  const topicSel = document.getElementById('topicSelect');
  topicSel.innerHTML = '<option value="">-- Select Topic --</option>';
  const filtered = topics.filter(t => Number(t.subject_id) === subjId);
  filtered.forEach(t => { const opt = document.createElement('option'); opt.value = t.id; opt.textContent = t.title; topicSel.appendChild(opt); });
});
showForTargetRes();
</script>

<?php include __DIR__ . '/footer.php'; ?>
