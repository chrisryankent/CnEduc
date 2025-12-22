<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Log tab switch
if ($action === 'log_tab_switch') {
    $attempt_id = (int)($_POST['attempt_id'] ?? 0);
    $switch_type = $_POST['switch_type'] ?? 'left';
    
    if ($attempt_id > 0) {
        $result = log_exam_tab_switch($attempt_id, $switch_type);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid attempt ID']);
    }
    exit;
}

// Save answer
if ($action === 'save_answer') {
    $attempt_id = (int)($_POST['attempt_id'] ?? 0);
    $question_id = (int)($_POST['question_id'] ?? 0);
    $answer = $_POST['answer'] ?? '';
    
    if ($attempt_id > 0 && $question_id > 0) {
        $result = save_exam_answer($attempt_id, $question_id, $answer);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
    exit;
}

// Submit exam
if ($action === 'submit_exam') {
    $attempt_id = (int)($_POST['attempt_id'] ?? 0);
    
    if ($attempt_id > 0) {
        $attempt = get_exam_attempt($attempt_id);
        
        // Verify user owns this attempt
        if ($attempt && $attempt['user_id'] == $user_id) {
            $result = submit_exam($attempt_id);
            echo json_encode(['success' => $result ? true : false, 'data' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid attempt ID']);
    }
    exit;
}

// Get exam details
if ($action === 'get_exam') {
    $exam_id = (int)($_GET['exam_id'] ?? 0);
    
    if ($exam_id > 0) {
        $exam = get_exam($exam_id);
        echo json_encode(['success' => true, 'data' => $exam]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
    }
    exit;
}

// Check if user can take exam
if ($action === 'can_take') {
    $exam_id = (int)($_GET['exam_id'] ?? 0);
    
    if ($exam_id > 0) {
        $can_take = can_user_take_exam($user_id, $exam_id);
        echo json_encode(['success' => true, 'can_take' => $can_take]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
?>
