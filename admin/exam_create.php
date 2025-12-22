<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Check admin access
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Get all subjects and units for dropdown
$levels = get_levels();
$subjects_by_level = [];
$units = get_units_by_course(null); // We'll fetch these differently

// Get all subjects
foreach ($levels as $level) {
    $classes = get_classes_by_level($level['id']);
    foreach ($classes as $class) {
        $subjects = get_subjects_by_class($class['id']);
        foreach ($subjects as $subject) {
            $subjects_by_level[$level['id']][] = $subject;
        }
    }
}

// Get all courses and units
$courses = get_courses();
$units_by_course = [];
foreach ($courses as $course) {
    $units_by_course[$course['id']] = get_units_by_course($course['id']);
}

// Handle form submission
$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $exam_type = $_POST['exam_type'] ?? 'subject'; // subject or unit
    $subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
    $unit_id = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null;
    $questions = $_POST['questions'] ?? [];
    $time_limit = (int)($_POST['time_limit'] ?? 60);
    $passing_percentage = (int)($_POST['passing_percentage'] ?? 50);
    $max_attempts = (int)($_POST['max_attempts'] ?? 3);
    $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
    $show_results = isset($_POST['show_results']) ? 1 : 0;
    
    // Validation
    if (empty($title)) {
        $error = "Exam title is required";
    } elseif (empty($questions)) {
        $error = "Please select at least one question";
    } elseif ($exam_type === 'subject' && !$subject_id) {
        $error = "Please select a subject";
    } elseif ($exam_type === 'unit' && !$unit_id) {
        $error = "Please select a unit";
    } else {
        global $mysqli;
        
        // Insert exam
        $title_esc = $mysqli->real_escape_string($title);
        $desc_esc = $mysqli->real_escape_string($description);
        $total_q = count($questions);
        
        $insert_exam = $mysqli->query(
            "INSERT INTO exams 
             (subject_id, unit_id, title, description, total_questions, time_limit_minutes, 
              passing_percentage, max_attempts, shuffle_questions, show_results, created_by) 
             VALUES (" . ($subject_id ?? 'NULL') . ", " . ($unit_id ?? 'NULL') . ", 
                     '$title_esc', '$desc_esc', $total_q, $time_limit, $passing_percentage, 
                     $max_attempts, $shuffle_questions, $show_results, $admin_id)"
        );
        
        if ($insert_exam) {
            $exam_id = $mysqli->insert_id;
            
            // Insert questions for this exam
            $order = 0;
            foreach ($questions as $question_id) {
                $question_id = (int)$question_id;
                $mysqli->query(
                    "INSERT INTO exam_questions (exam_id, question_id, question_order, points) 
                     VALUES ($exam_id, $question_id, $order, 1)"
                );
                $order++;
            }
            
            $success = true;
        } else {
            $error = "Failed to create exam";
        }
    }
}

// Get available questions (all approved topic questions)
$available_questions = [];
foreach ($levels as $level) {
    $classes = get_classes_by_level($level['id']);
    foreach ($classes as $class) {
        $subjects = get_subjects_by_class($class['id']);
        foreach ($subjects as $subject) {
            $topics = get_topics_by_subject($subject['id']);
            foreach ($topics as $topic) {
                $questions = get_topic_questions($topic['id'], 1000);
                foreach ($questions as $q) {
                    $available_questions[] = [
                        'id' => $q['id'],
                        'title' => $q['question_title'] ?? 'Question',
                        'subject' => $subject['name'],
                        'topic' => $topic['title']
                    ];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam - CnEduc Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent: #0f62fe;
            --accent-light: #3d8dff;
            --gray-100: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-600: #525252;
            --gray-900: #171717;
            --radius: 12px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
        }

        body {
            font-family: "Inter", sans-serif;
            background: var(--gray-100);
            color: var(--gray-900);
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        h1 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h1 i {
            background: linear-gradient(135deg, #0f62fe, #3d8dff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .breadcrumb {
            color: var(--gray-600);
            margin-bottom: 30px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-900);
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 98, 254, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .questions-container {
            background: var(--gray-100);
            padding: 20px;
            border-radius: var(--radius);
            max-height: 400px;
            overflow-y: auto;
        }

        .question-item {
            background: white;
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: 0.2s;
        }

        .question-item:hover {
            border-color: var(--accent);
            background: #f0f6ff;
        }

        .question-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .question-item.selected {
            border-color: var(--accent);
            background: #f0f6ff;
        }

        .selected-questions {
            background: var(--gray-100);
            padding: 16px;
            border-radius: var(--radius);
            min-height: 100px;
        }

        .selected-questions h4 {
            margin-bottom: 12px;
            color: var(--gray-600);
        }

        .selected-item {
            background: white;
            padding: 8px 12px;
            margin-bottom: 6px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .btn-remove {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-remove:hover {
            background: #ff5252;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0f62fe, #3d8dff);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(15, 98, 254, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .btn-secondary:hover {
            background: var(--gray-100);
        }

        .info-text {
            font-size: 13px;
            color: var(--gray-600);
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a> / 
            <a href="exams_list.php">Exams</a> / 
            <span>Create Exam</span>
        </div>

        <h1><i class="fas fa-file-alt"></i> Create New Exam</h1>
        <p style="color: var(--gray-600); margin-bottom: 30px;">Set up a new exam with questions from topics</p>

        <?php if ($success): ?>
            <div class="success">
                <strong>✓ Success!</strong> Exam created successfully. <a href="exams_list.php">View all exams</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">
                <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Basic Information -->
            <div class="form-group">
                <label>Exam Title *</label>
                <input type="text" name="title" required placeholder="e.g., Mathematics Final Exam">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Describe the exam purpose and content..."></textarea>
            </div>

            <!-- Exam Type Selection -->
            <div class="form-group">
                <label>Exam Type *</label>
                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="radio" name="exam_type" value="subject" checked> Subject Exam
                    </label>
                    <label class="checkbox-item">
                        <input type="radio" name="exam_type" value="unit"> Unit Exam
                    </label>
                </div>
            </div>

            <!-- Subject/Unit Selection -->
            <div class="grid-2">
                <div class="form-group" id="subject_group">
                    <label>Select Subject *</label>
                    <select name="subject_id">
                        <option value="">Choose a subject...</option>
                        <?php foreach ($levels as $level): 
                            if (isset($subjects_by_level[$level['id']])):
                        ?>
                            <optgroup label="<?php echo htmlspecialchars($level['name']); ?>">
                                <?php foreach ($subjects_by_level[$level['id']] as $subj): ?>
                                    <option value="<?php echo $subj['id']; ?>">
                                        <?php echo htmlspecialchars($subj['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="unit_group" style="display: none;">
                    <label>Select Unit *</label>
                    <select name="unit_id">
                        <option value="">Choose a unit...</option>
                        <?php foreach ($courses as $course): 
                            if (isset($units_by_course[$course['id']])):
                        ?>
                            <optgroup label="<?php echo htmlspecialchars($course['name']); ?>">
                                <?php foreach ($units_by_course[$course['id']] as $unit): ?>
                                    <option value="<?php echo $unit['id']; ?>">
                                        <?php echo htmlspecialchars($unit['title'] ?? $unit['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Settings -->
            <div class="grid-2">
                <div class="form-group">
                    <label>Time Limit (minutes)</label>
                    <input type="number" name="time_limit" value="60" min="5" max="300">
                    <p class="info-text">Total time allowed for the exam</p>
                </div>

                <div class="form-group">
                    <label>Passing Percentage (%)</label>
                    <input type="number" name="passing_percentage" value="50" min="0" max="100">
                    <p class="info-text">Score needed to pass</p>
                </div>

                <div class="form-group">
                    <label>Maximum Attempts</label>
                    <input type="number" name="max_attempts" value="3" min="1" max="10">
                    <p class="info-text">How many times user can take the exam</p>
                </div>

                <div class="form-group">
                    <label>Options</label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="checkbox-item">
                            <input type="checkbox" name="shuffle_questions" checked>
                            Shuffle Questions
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="show_results" checked>
                            Show Results to Student
                        </label>
                    </div>
                </div>
            </div>

            <!-- Questions Selection -->
            <div class="form-group">
                <label>Select Questions * (<?php echo count($available_questions); ?> available)</label>
                <p class="info-text">Click questions to add them to this exam</p>
            </div>

            <div class="grid-2" style="margin-bottom: 30px;">
                <div>
                    <h4 style="margin-bottom: 12px; font-size: 14px;">Available Questions</h4>
                    <div class="questions-container">
                        <?php foreach ($available_questions as $index => $q): ?>
                            <div class="question-item" onclick="toggleQuestion(<?php echo $q['id']; ?>, this)">
                                <input type="checkbox" name="questions[]" value="<?php echo $q['id']; ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($q['title']); ?></strong>
                                    <div style="font-size: 12px; color: #666;">
                                        <?php echo htmlspecialchars($q['subject'] . ' > ' . $q['topic']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 12px; font-size: 14px;">Selected Questions (<span id="selected-count">0</span>)</h4>
                    <div class="selected-questions" id="selected-questions">
                        <p style="color: #999; text-align: center; padding: 20px;">No questions selected</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Create Exam
                </button>
                <a href="exams_list.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        // Toggle exam type
        document.querySelectorAll('input[name="exam_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('subject_group').style.display = 
                    this.value === 'subject' ? 'block' : 'none';
                document.getElementById('unit_group').style.display = 
                    this.value === 'unit' ? 'block' : 'none';
            });
        });

        // Toggle question selection
        function toggleQuestion(questionId, element) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }
            
            updateSelectedQuestions();
        }

        function updateSelectedQuestions() {
            const selected = document.querySelectorAll('input[name="questions[]"]:checked');
            const container = document.getElementById('selected-questions');
            const count = document.getElementById('selected-count');
            
            count.textContent = selected.length;
            
            if (selected.length === 0) {
                container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No questions selected</p>';
                return;
            }
            
            let html = '';
            selected.forEach((checkbox, index) => {
                const item = checkbox.closest('.question-item');
                const title = item.querySelector('strong').textContent;
                html += `<div class="selected-item">
                    <span>${index + 1}. ${title}</span>
                    <button type="button" class="btn-remove" onclick="toggleQuestion(${checkbox.value}, this.closest('.question-item'))">Remove</button>
                </div>`;
            });
            
            container.innerHTML = html;
        }
    </script>
</body>
</html>
