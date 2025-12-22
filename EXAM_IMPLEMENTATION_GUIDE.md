# CnEduc Exam System - Implementation Guide

## Overview
The exam system has been fully implemented with the following features:
- Admin exam creation interface
- Student exam-taking experience with shuffled questions
- Auto-marking system with keyword-based grading
- Cheat detection via tab switch monitoring
- Exam attempt tracking and results history
- Time management per exam

---

## Database Setup

### Step 1: Run Migration SQL
Run the `exam_system_migration.sql` file in your MySQL admin panel:

```bash
# Via command line:
mysql -u root -p cneduc < exam_system_migration.sql

# Via phpMyAdmin:
1. Select database "cneduc"
2. Go to "Import" tab
3. Upload exam_system_migration.sql
4. Click "Go"
```

**Tables Created:**
- `exams` - Exam definitions
- `exam_questions` - Questions linked to exams
- `exam_attempts` - User attempts tracking
- `exam_answers` - Individual answer storage
- `exam_tab_switches` - Cheat detection logs

**Fields Added to `topic_questions`:**
- `answer_keywords` - Keywords for auto-marking
- `model_answer` - Expected answer
- `marking_type` - Auto/Manual/AI-assisted

---

## System Architecture

### 1. Admin Panel - Create Exams
**File:** `admin/exam_create.php`

**Access:** Only admin users

**Features:**
- Select subject or unit
- Add existing questions from topic pool
- Set exam parameters:
  - Time limit
  - Passing percentage
  - Maximum attempts
  - Question shuffling
- Automatic time calculation based on question count

**How to Create an Exam:**
1. Go to `admin/exam_create.php`
2. Fill in exam title and description
3. Select exam type (Subject or Unit)
4. Choose questions from available pool
5. Configure exam settings
6. Click "Create Exam"

---

### 2. Student Exam Taking
**File:** `take_exam.php`

**Navigation Flow:**
```
Select Exam Type (Subject/Unit)
    ↓
Select Level/Course
    ↓
Select Class/Unit
    ↓
Select Subject/Unit
    ↓
Choose Specific Exam
    ↓
Start Exam
    ↓
Answer Questions
    ↓
Submit & View Results
```

**Features:**
- Guided multi-stage navigation
- Search/filter for 100+ items
- Questions shuffled uniquely per user using seed: `user_id * 1000 + exam_id * 100 + attempt_number`
- Real-time timer display
- Auto-save answers (background)
- Tab switch detection
- Automatic grading

**Student Flow:**
1. User clicks "Take Exam" from exams.php
2. System verifies:
   - User is logged in
   - User hasn't exceeded max attempts
   - No in-progress attempt exists
3. Questions are loaded and shuffled
4. Timer starts (countdown or elapsed)
5. User answers questions
6. On submit:
   - Auto-marking runs
   - Suspicious activity checked
   - Results displayed

---

### 3. Exam Attempts & Grading
**Database:** `exam_attempts` table

**Attempt Lifecycle:**
```
in_progress → submitted → graded
```

**Auto-Marking Process:**
1. User submits exam
2. For each answer:
   - Extract keywords from `topic_questions.answer_keywords`
   - Count matches in user's answer
   - Calculate percentage: (matches / total_keywords) × 100
   - Award points if ≥70% match
3. Calculate total score and percentage
4. Mark suspicious activity flags
5. Update attempt status to "graded"

**Example - Auto-Marking a Question:**
```php
Question: "What is 2+2?"
answer_keywords: "4, four"
User Answer: "2+2 equals 4"

Matches: "4" found = 1/2 keywords = 50% → No points
Expected: "4" found AND "four" found for full points

If user answered: "2 plus 2 equals 4 which is four"
Matches: "4" + "four" found = 2/2 keywords = 100% → Full points
```

---

### 4. Cheat Detection
**Files:**
- JavaScript in `take_exam.php`
- `api/exam_api.php` - Tab switch logging
- `exam_tab_switches` table - Suspicious activity records

**Monitoring:**
- Tab switches (when user leaves exam tab)
- Window blur events (when window loses focus)
- Exam completion time vs question count
- Suspiciously high scores (98-100%)

**Flagging Criteria:**
```php
if (tab_switches > 5) → Suspicious
if (time_taken < 2 minutes AND score > 80%) → Suspicious
if (score > 98%) → Suspicious
```

**Teacher Review:** Flagged exams appear with red "Flagged" badge

---

### 5. Question Shuffling Algorithm

**Problem:** All users see same questions in same order

**Solution:** Deterministic shuffling using user-specific seed

```php
$seed = ($user_id * 1000) + ($exam_id * 100) + ($attempt_number);
mt_srand($seed);
shuffle($questions);
```

**Benefits:**
- Each user gets different question order
- Same user's different attempts also differ
- Reproducible (same seed = same order)
- Prevents answer sharing

**Example:**
```
Exam ID: 5, Questions: [Q1, Q2, Q3, Q4, Q5]

User A (ID: 10), Attempt 1:
Seed: 10*1000 + 5*100 + 1 = 10501
Order: [Q3, Q1, Q4, Q2, Q5]

User A (ID: 10), Attempt 2:
Seed: 10*1000 + 5*100 + 2 = 10502
Order: [Q5, Q2, Q1, Q3, Q4] ← Different!

User B (ID: 15), Attempt 1:
Seed: 15*1000 + 5*100 + 1 = 15501
Order: [Q2, Q4, Q5, Q1, Q3] ← Different from User A!
```

---

### 6. Time Management

**Calculation:**
```
Total Time = (Question Count × Time Per Question) + 10% Buffer

Difficulty Levels:
- Easy: 3 min/question
- Intermediate: 5 min/question (default)
- Hard: 8 min/question

Example: 10 questions × 5 min = 50 min × 1.1 = 55 minutes
```

**Set in Admin Panel:**
- `time_limit_minutes` in exams table
- Can override default calculation

**During Exam:**
- Timer visible at top-right
- Shows elapsed time
- Prevents submission after timeout

---

## API Endpoints

**File:** `api/exam_api.php`

### Log Tab Switch
```http
POST /api/exam_api.php?action=log_tab_switch
Content-Type: application/json

{
  "attempt_id": 123,
  "switch_type": "left" | "returned" | "blur" | "focus"
}
```

### Save Answer (Auto-save)
```http
POST /api/exam_api.php?action=save_answer
Content-Type: application/x-www-form-urlencoded

attempt_id=123&question_id=456&answer=user_answer_text
```

### Submit Exam
```http
POST /api/exam_api.php?action=submit_exam
attempt_id=123
```

### Check Exam Eligibility
```http
GET /api/exam_api.php?action=can_take&exam_id=123
```

---

## User Flows

### Admin: Create Exam
```
Admin Login → admin/exam_create.php
↓
Fill Form (Title, Description, Type, Questions, Settings)
↓
Select Questions from Pool
↓
Submit → Exam Created
↓
Exam Available for Students
```

### Student: Take Exam
```
Student Login → take_exam.php
↓
Select Exam Type (Subject/Unit)
↓
Navigate: Level → Class → Subject → Exam
↓
Click "Start Exam"
↓
Create Attempt & Load Questions (Shuffled)
↓
Answer Questions (Auto-save in background)
↓
Submit
↓
Auto-Mark & Flag Suspicious Activity
↓
View Results (Score, Time, Flags)
↓
Option to Retake (if attempts remaining)
```

### View Results
```
Student Dashboard → "View Exam Results"
↓
Table of All Attempts with:
- Exam Name
- Attempt Number
- Date & Time
- Status (Graded/Submitted/In-Progress)
- Score %
- Time Taken
- Suspicious Flags
```

---

## Database Schema Highlights

### `exams` Table
```sql
id (PK)
subject_id (FK to subjects)
unit_id (FK to units)
title
description
total_questions
time_limit_minutes
passing_percentage (default: 50)
max_attempts (default: 3)
shuffle_questions (default: 1)
show_results (default: 1)
created_by (admin user)
created_at
updated_at
```

### `exam_attempts` Table
```sql
id (PK)
exam_id (FK)
user_id (FK)
attempt_number
started_at
submitted_at
time_taken_seconds
score
total_points
percentage
status (in_progress, submitted, graded)
tab_switches (count)
suspicious_activity (boolean)
ip_address
browser_info
```

### `exam_answers` Table
```sql
id (PK)
attempt_id (FK)
question_id (FK)
user_answer (LONGTEXT)
is_correct (boolean or null)
points_earned
answered_at
```

---

## File Structure
```
CnEduc/
├── api/
│   └── exam_api.php                 # API endpoints
├── admin/
│   └── exam_create.php              # Admin exam creation
├── take_exam.php                    # Student exam taking
├── exam_results.php                 # Results history
├── includes/
│   └── functions.php                # 20+ new exam functions
├── exam_system_migration.sql        # Database setup
└── EXAM_SYSTEM_DESIGN.md           # Design document
```

---

## Functions Available

### Exam Management
- `get_exam($exam_id)` - Get exam details
- `get_exams_by_subject($subject_id)` - Get subject exams
- `get_exams_by_unit($unit_id)` - Get unit exams

### Exam Taking
- `create_exam_attempt($exam_id, $user_id, $ip, $browser)` - Start exam
- `get_exam_attempt($attempt_id)` - Get attempt details
- `get_exam_questions($exam_id, $user_id, $attempt_num)` - Get shuffled questions
- `can_user_take_exam($user_id, $exam_id)` - Check eligibility

### Answering
- `save_exam_answer($attempt_id, $question_id, $answer)` - Auto-save
- `submit_exam($attempt_id)` - Grade and finalize

### Grading
- `auto_mark_answer($question_id, $user_answer, $points)` - Auto-mark
- `check_exam_suspicious_activity($attempt_id)` - Detect cheating

### Cheat Detection
- `log_exam_tab_switch($attempt_id, $type)` - Log tab switch

### User Results
- `get_user_exam_attempts($user_id, $exam_id=null)` - Get attempts

---

## Configuration

### Exam Settings (In Admin Panel)
| Setting | Default | Notes |
|---------|---------|-------|
| Time Limit | 60 min | Auto-calculated: questions × time_per_q |
| Passing % | 50% | Min % to pass exam |
| Max Attempts | 3 | How many times user can retake |
| Shuffle Questions | ON | Randomize question order |
| Show Results | ON | Display score immediately |

### Auto-Marking Threshold
- **Keyword Match Required:** 70%
- **Points Calculation:** (matched_keywords / total_keywords) × question_points

### Suspicious Activity Flags
- Tab switches: > 5
- Fast completion: < 2 min with score > 80%
- Extremely high score: > 98%

---

## Security Considerations

✅ **Implemented:**
- Session-based authentication required
- User-specific question shuffling prevents sharing
- IP address logging
- Browser fingerprinting
- Tab switch detection
- Server-side validation of all submissions

⚠️ **Recommendations:**
- Set answer_keywords for accurate auto-marking
- Teacher review flagged exams
- Monitor suspicious patterns
- Enforce time limits server-side
- Consider proctoring for high-stakes exams

---

## Testing Checklist

- [ ] Database tables created successfully
- [ ] Admin can create exams
- [ ] Student can navigate to exams
- [ ] Questions are shuffled per user
- [ ] Auto-marking works correctly
- [ ] Timer counts down/up correctly
- [ ] Tab switches are detected
- [ ] Results show correct scores
- [ ] Suspicious activity is flagged
- [ ] Users can't exceed max attempts
- [ ] Different attempt numbers show different question orders

---

## Troubleshooting

**Issue:** "No questions available for this exam"
- **Solution:** Check that questions exist and are linked in `exam_questions` table

**Issue:** All users see same question order
- **Solution:** Verify `shuffle_questions = 1` in exams table; check seed calculation

**Issue:** Auto-marking gives 0 points
- **Solution:** Set `answer_keywords` in topic_questions table; ensure 70%+ match

**Issue:** Suspicious activity not detected
- **Solution:** Verify tab switches are being logged in `exam_tab_switches` table

**Issue:** Timer not showing during exam
- **Solution:** Check JavaScript console; verify `attempt_id` is set correctly

---

## Next Steps / Future Enhancements

1. **Manual Marking Interface** - Teachers review and score open-ended questions
2. **AI-Assisted Marking** - Use ChatGPT API for similarity scoring
3. **Analytics Dashboard** - Question difficulty stats, student performance trends
4. **Question Bank** - Easier management of large question pools
5. **Randomized Question Pools** - Select random subset instead of all questions
6. **Multiple Choice Support** - Auto-mark MCQs automatically
7. **Proctoring Integration** - Camera monitoring during exam
8. **Certificates** - Award certificates on passing exams
9. **Leaderboards** - Compare student performance
10. **Exam Scheduling** - Set exam availability windows

---

## Support

For issues or questions, please refer to:
1. `EXAM_SYSTEM_DESIGN.md` - Comprehensive system design
2. Function comments in `includes/functions.php`
3. Code comments in admin/exam_create.php and take_exam.php

