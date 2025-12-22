# CnEduc Exam System Design

## 1. EXAM SETUP & CREATION

### Admin Panel Requirements:
- **Create Exam**: Admin creates exam with:
  - Target subject/unit
  - Question pool (select from existing topic_questions or add new)
  - Difficulty levels (basic, intermediate, advanced)
  - Time limit (e.g., 60 minutes for 10 questions = 6 min/question)
  - Passing score (e.g., 50%)
  - Attempt limits (e.g., max 3 attempts per user)
  - Randomization settings
  - Marking scheme (points per question, auto-marking rules)

### Database Table Needed:
```sql
CREATE TABLE exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT,
    unit_id INT,
    title VARCHAR(255),
    description TEXT,
    total_questions INT,
    time_limit_minutes INT,
    passing_percentage INT DEFAULT 50,
    max_attempts INT DEFAULT 3,
    shuffle_questions BOOLEAN DEFAULT 1,
    shuffle_answers BOOLEAN DEFAULT 1,
    show_results BOOLEAN DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

CREATE TABLE exam_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    question_id INT,
    question_order INT,
    points INT DEFAULT 1,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES topic_questions(id)
);
```

---

## 2. QUESTION RANDOMIZATION (DIFFERENT FOR EACH USER)

### Current Problem:
- All users see same questions in same order
- Easy to share answers

### Solution: Question Pooling & Randomization

```php
// When exam starts, create unique question set for each attempt
function get_exam_questions_for_user($exam_id, $user_id, $attempt_number) {
    global $mysqli;
    
    // Get all questions for this exam
    $questions = $mysqli->query(
        "SELECT eq.id, eq.question_order, eq.points, tq.* 
         FROM exam_questions eq
         JOIN topic_questions tq ON eq.question_id = tq.id
         WHERE eq.exam_id = $exam_id
         ORDER BY eq.question_order"
    )->fetch_all(MYSQLI_ASSOC);
    
    // Shuffle if enabled
    $exam = get_exam($exam_id);
    if ($exam['shuffle_questions']) {
        // Use seed based on user_id + attempt to ensure consistent randomization
        // but different for each user
        mt_srand($user_id * 1000 + $exam_id * 100 + $attempt_number);
        shuffle($questions);
    }
    
    return $questions;
}
```

### Key Points:
- **Seeding**: Use `user_id + exam_id + attempt_number` as seed
- Each user gets different question order
- Same user's different attempts also get different orders
- Questions are truly randomized but reproducible if needed

---

## 3. EXAM ATTEMPT TRACKING

### New Database Table:
```sql
CREATE TABLE exam_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    user_id INT,
    attempt_number INT,
    started_at TIMESTAMP,
    submitted_at TIMESTAMP NULL,
    time_taken_minutes INT,
    score INT,
    total_points INT,
    percentage DECIMAL(5,2),
    status ENUM('in_progress', 'submitted', 'graded') DEFAULT 'in_progress',
    tab_switches INT DEFAULT 0,
    suspicious_activity BOOLEAN DEFAULT 0,
    FOREIGN KEY (exam_id) REFERENCES exams(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE exam_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT,
    question_id INT,
    user_answer TEXT,
    is_correct BOOLEAN,
    points_earned INT,
    answered_at TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES exam_attempts(id),
    FOREIGN KEY (question_id) REFERENCES topic_questions(id)
);
```

---

## 4. TIME ALLOCATION SYSTEM

### Formula:
```
Total Time = (Number of Questions × Average Time per Question) + Buffer Time

Examples:
- 10 questions × 6 min/question = 60 minutes total
- 20 questions × 5 min/question = 100 minutes total
- 5 questions × 8 min/question = 40 minutes total
```

### Implementation:
```php
function calculate_exam_time($total_questions, $difficulty = 'intermediate') {
    // Base time per question by difficulty
    $base_times = [
        'easy' => 3,        // 3 min per question
        'intermediate' => 5, // 5 min per question
        'hard' => 8         // 8 min per question
    ];
    
    $time_per_question = $base_times[$difficulty] ?? 5;
    $total_time = $total_questions * $time_per_question;
    
    // Add 10% buffer for reading instructions
    $total_time = (int)($total_time * 1.1);
    
    return $total_time;
}
```

---

## 5. AUTO-MARKING SYSTEM

### For Q&A Style Questions:
Since current system has user-submitted Q&A questions, we need:

**Option A: Keyword-Based Auto-Marking**
```php
function auto_mark_answer($question_id, $user_answer) {
    global $mysqli;
    
    // Get expected keywords/model answer
    $question = get_topic_question($question_id);
    $keywords = explode(',', $question['answer_keywords'] ?? '');
    
    $user_answer_lower = strtolower(trim($user_answer));
    $matches = 0;
    
    foreach ($keywords as $keyword) {
        if (strpos($user_answer_lower, trim(strtolower($keyword))) !== false) {
            $matches++;
        }
    }
    
    // Award points based on keyword matches
    $percentage = ($matches / count($keywords)) * 100;
    $points = ($percentage / 100) * $total_points;
    
    return [
        'is_correct' => $percentage >= 70,
        'points' => (int)$points,
        'percentage' => $percentage
    ];
}
```

**Option B: Manual Marking + AI Assistance**
```
- Auto-mark obvious answers (yes/no, multiple choice)
- Flag answers for teacher review
- Use AI (ChatGPT API) for similarity to model answer
- Teacher approves/adjusts scores
```

**Option C: Hybrid (Recommended)**
```
- 40% auto-marking from keywords
- 60% manual review by teacher
- AI provides confidence score
- Flag suspicious/low-confidence answers
```

### Update Database Table:
```sql
ALTER TABLE topic_questions ADD COLUMN answer_keywords VARCHAR(500);
ALTER TABLE topic_questions ADD COLUMN model_answer TEXT;
ALTER TABLE topic_questions ADD COLUMN marking_type ENUM('auto', 'manual', 'ai_assisted') DEFAULT 'manual';
```

---

## 6. TAB/WINDOW FOCUS DETECTION (CHEAT DETECTION)

### JavaScript Implementation:
```javascript
let tabSwitches = 0;
let isTabActive = true;

// Track when user switches to another tab
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        isTabActive = false;
        console.warn('User left exam tab');
        // Send warning to server
        logTabSwitch('left');
    } else {
        isTabActive = true;
        console.log('User returned to exam tab');
        logTabSwitch('returned');
    }
});

// Track when window loses focus
window.addEventListener('blur', function() {
    isTabActive = false;
    logTabSwitch('blur');
});

window.addEventListener('focus', function() {
    isTabActive = true;
    logTabSwitch('focus');
});

function logTabSwitch(type) {
    tabSwitches++;
    
    // Send to server
    fetch('api/log_tab_switch.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            attempt_id: <?php echo $attempt_id; ?>,
            type: type,
            timestamp: new Date()
        })
    });
}

// Before submitting exam
document.getElementById('examForm').addEventListener('submit', function(e) {
    if (tabSwitches > 3) {
        const proceed = confirm(
            'We detected ' + tabSwitches + ' tab switches. This might indicate cheating. Continue?'
        );
        if (!proceed) {
            e.preventDefault();
            return false;
        }
    }
});
```

### Server-Side Logging:
```sql
CREATE TABLE exam_tab_switches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT,
    switch_type ENUM('left', 'returned', 'blur', 'focus'),
    logged_at TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES exam_attempts(id)
);
```

### Flag Suspicious Activity:
```php
function check_suspicious_activity($attempt_id) {
    global $mysqli;
    
    $tab_switches = $mysqli->query(
        "SELECT COUNT(*) as count FROM exam_tab_switches 
         WHERE attempt_id = $attempt_id"
    )->fetch_assoc()['count'];
    
    $attempt = get_exam_attempt($attempt_id);
    
    // Flags:
    $suspicious = false;
    $reasons = [];
    
    if ($tab_switches > 5) {
        $suspicious = true;
        $reasons[] = "Too many tab switches ($tab_switches)";
    }
    
    if ($attempt['time_taken_minutes'] < 2) {
        $suspicious = true;
        $reasons[] = "Exam completed too quickly";
    }
    
    if ($attempt['percentage'] > 95 && $attempt['percentage'] < 100) {
        $suspicious = true;
        $reasons[] = "Suspiciously high score";
    }
    
    return [
        'is_suspicious' => $suspicious,
        'flags' => $reasons,
        'risk_level' => $suspicious ? 'high' : 'low'
    ];
}
```

---

## 7. COMPLETE EXAM FLOW

### Step 1: User Selects Exam
```
1. User navigates to take_exam.php
2. Selects subject/unit
3. System checks:
   - User logged in? ✓
   - Has attempts remaining? ✓
   - Not currently taking exam? ✓
```

### Step 2: Start Exam
```
1. Create exam_attempt record with status='in_progress'
2. Generate unique question set (shuffled based on user_id)
3. Get time limit from exam + questions
4. Start timer
5. Display questions
6. Initialize tab-switch tracking
```

### Step 3: During Exam
```
1. Questions displayed one-by-one or all at once (configurable)
2. Timer counts down
3. Monitor tab switches
4. Auto-save answers every 30 seconds (in background)
5. Warn when time running out
6. Prevent submission if time expired
```

### Step 4: Submit Exam
```
1. Validate all answers received
2. Record submission time
3. Calculate attempt duration
4. Check tab switches
5. Auto-mark answers
6. Calculate score
7. Update exam_attempt with status='submitted'
8. Flag suspicious activity
9. Show results to user
```

### Step 5: Grading
```
Auto-mark: 30 seconds
Manual review: Teacher reviews within 24hrs
Final grade: Combined auto + manual score
```

---

## 8. IMPLEMENTATION PRIORITY

### Phase 1 (Critical):
- [ ] Exam creation admin panel
- [ ] exam & exam_questions tables
- [ ] Question shuffling per user
- [ ] exam_attempts table
- [ ] Auto-marking engine
- [ ] Basic timer

### Phase 2 (Important):
- [ ] Tab switch detection
- [ ] Suspicious activity flagging
- [ ] Answer history
- [ ] Exam results display
- [ ] Attempt limits

### Phase 3 (Enhancement):
- [ ] AI-assisted marking
- [ ] Manual marking interface
- [ ] Analytics dashboard
- [ ] Question difficulty analysis
- [ ] Student performance reports

---

## 9. SECURITY CONSIDERATIONS

1. **Prevent Re-examination**: Once submitted, mark as complete
2. **Time Validation**: Server validates time, not just client
3. **Answer Integrity**: Use hashing for answer integrity check
4. **IP Logging**: Log IP address for each attempt
5. **Rate Limiting**: Prevent multiple submissions from same IP
6. **Session Validation**: Ensure user session valid during entire exam

---

## 10. EXAMPLE: Complete Exam Journey

```
User A takes Math exam (Subject ID: 5):
- Questions pool: [Q1, Q2, Q3, Q4, Q5]
- Shuffled for User A: [Q3, Q1, Q4, Q2, Q5]
- Time allocated: 5 questions × 5 min = 30 minutes
- Timer starts

Same time, User B takes same exam:
- Questions pool: [Q1, Q2, Q3, Q4, Q5]
- Shuffled for User B: [Q5, Q2, Q1, Q3, Q4] (DIFFERENT ORDER!)
- Time allocated: 30 minutes
- Timer starts

User A finishes in 25 minutes:
- Auto-marked immediately
- Score: 80/100
- Tab switches: 2 (normal)
- Status: PASSED

User B finishes in 3 minutes:
- Auto-marked immediately
- Score: 95/100
- Tab switches: 8 (flagged)
- Status: FLAGGED FOR REVIEW
```

---

## Questions for Clarification:

1. **Question Type**: Do you want Q&A questions only, or add multiple choice?
2. **Teacher Review**: Will teachers review auto-marked answers?
3. **Retake Policy**: Can users retake exams? After how long?
4. **Passing Score**: Fixed 50% or per-exam configurable?
5. **Certificate**: Award certificates after passing?
6. **Analytics**: Want to track question difficulty stats?

