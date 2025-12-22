# CnEduc Exam System - Quick Start Guide

## 🚀 Get Up and Running in 5 Minutes

### Prerequisites
- XAMPP with MySQL running
- CnEduc platform installed
- Admin user account

---

## Step 1: Create Database Tables (2 minutes)

**Option A: Using phpMyAdmin (Easiest)**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `cneduc`
3. Click "Import" tab
4. Browse and upload: `exam_system_migration.sql`
5. Click "Go"
6. ✅ All tables created

**Option B: Using Command Line**
```bash
mysql -u root -p cneduc < exam_system_migration.sql
# Press Enter, type password, done
```

**Verify Success:**
```sql
-- In phpMyAdmin SQL tab, run:
SHOW TABLES LIKE 'exam%';
-- Should show: exams, exam_questions, exam_attempts, exam_answers, exam_tab_switches
```

---

## Step 2: Create Your First Exam (3 minutes)

1. **Login as Admin**
   - Go to `http://localhost/Cneduc/admin/`
   - Login with admin credentials

2. **Create Exam**
   - Go to `admin/exam_create.php`
   - Fill in:
     - **Title:** "Mathematics Final Exam"
     - **Description:** "Test your math skills"
     - **Type:** Subject
     - **Subject:** Select Mathematics
     - **Time:** 60 minutes (or auto-calculated)
     - **Passing:** 50%
     - **Max Attempts:** 3

3. **Add Questions**
   - Scroll down to "Available Questions"
   - Click questions to select them (at least 5)
   - They appear in "Selected Questions" on the right
   - Can remove by clicking "Remove"

4. **Submit**
   - Click "Create Exam"
   - ✅ Exam created!

---

## Step 3: Student Takes Exam (1 minute)

1. **Student Login**
   - Go to `http://localhost/Cneduc/`
   - Login as student

2. **Navigate to Exam**
   - Click "Exams" in navbar
   - Click "Start Exam Now"
   - Select: Subject → Level → Class → Subject
   - Click exam title → "Start Exam"

3. **Take Exam**
   - Answer questions in text boxes
   - Timer shows elapsed time
   - Click "Submit Exam" when done

4. **View Results**
   - Immediate score display
   - Shows % passing/failing
   - Option to take another exam (if attempts remaining)

---

## Step 4: Review Results (1 minute)

**Student View:**
- Go to `exam_results.php`
- See all attempts in table:
  - Exam name
  - Date & time
  - Score %
  - Status
  - Suspicious flags

**Admin View:**
- Check database: `exam_attempts` table
- See all student attempts across exams
- Identify flagged attempts for review

---

## Key Features to Know

### ✅ Questions are Shuffled
- Each student gets different question order
- Each attempt gets different order
- Prevents cheating/answer sharing

### ✅ Auto-Marking Works
- Questions with "answer_keywords" are auto-marked
- 70% keyword match = correct answer
- Other questions stay as "pending manual review"

### ✅ Time Limits Enforced
- Timer visible during exam
- Can't submit after time expires
- Time taken is recorded

### ✅ Cheating Detection
- Tab switches tracked
- Flags if > 5 switches
- Also flags if exam completed too fast
- Admins see "Flagged" badge on suspicious attempts

### ✅ Attempt Limits Work
- Each student can take exam max N times
- Set during admin exam creation
- System prevents taking after limit reached

---

## Testing Checklist

Run through this to verify everything works:

- [ ] Database tables exist
- [ ] Admin can create exam
- [ ] Admin can select questions
- [ ] Admin can set time limit
- [ ] Student can navigate to exam
- [ ] Student can start exam
- [ ] Questions appear in different order for different users
- [ ] Timer shows and counts up
- [ ] Student can submit exam
- [ ] Score appears immediately after submit
- [ ] Results appear in exam_results.php
- [ ] Attempt count prevents taking exam after limit
- [ ] Tab switches are detected (if user leaves tab)

---

## Common Issues & Fixes

### Issue: "No exams available for this subject"
**Cause:** You created exam but didn't add any questions
**Fix:** Create exam again, make sure to select at least 5 questions

### Issue: Questions are in same order for all students
**Cause:** shuffle_questions is disabled
**Fix:** When creating exam, check "Shuffle Questions" checkbox

### Issue: Auto-marking not working (all questions pending)
**Cause:** Questions don't have answer_keywords set
**Fix:** 
1. Go to phpmyadmin
2. Find topic_questions table
3. Edit a question
4. Add answer_keywords: "keyword1, keyword2, keyword3"
5. Retake exam - now it will auto-mark

### Issue: "Can't take exam - attempt limit reached"
**Cause:** Student already took exam max times
**Fix:** Either increase max_attempts when creating exam, or reset user attempts

### Issue: Timer not showing
**Cause:** Attempt not created properly
**Fix:** Check browser console (F12 → Console) for errors

---

## File Locations Quick Reference

```
Admin Panel
├─ Create Exam: /admin/exam_create.php
└─ View Exams: /admin/exams_list.php (optional)

Student Interface
├─ Take Exam: /take_exam.php
├─ View Results: /exam_results.php
└─ From Navbar: Exams → Start Exam Now

API
└─ /api/exam_api.php (auto-called, don't visit directly)

Documentation
├─ EXAM_SYSTEM_SUMMARY.md (overview)
├─ EXAM_IMPLEMENTATION_GUIDE.md (detailed)
├─ EXAM_SYSTEM_DESIGN.md (architecture)
└─ exam_system_migration.sql (database)

Database Functions
└─ includes/functions.php (20+ exam functions)
```

---

## Example: Complete User Journey

### Admin: Create Math Exam
```
1. Go to admin/exam_create.php
2. Title: "Algebra Basics"
3. Select: Subject > Mathematics
4. Select 10 questions about algebra
5. Set: 45 minutes, 60% passing, 3 attempts
6. Click Create
✅ Exam ready
```

### Student 1: Take Exam
```
1. Login as student
2. Click Exams → Start Exam
3. Select: Subject → Primary → P4 → Mathematics
4. Click "Algebra Basics" exam
5. See questions in random order (e.g., Q3, Q7, Q1, Q9, Q2...)
6. Answer all questions
7. Click Submit
8. See score: 70% PASSED ✓
9. Can take again (2 attempts left)
```

### Student 2: Take Same Exam (Different Time)
```
1. Login as different student
2. Click Exams → Start Exam
3. Select same exam: Mathematics > Algebra Basics
4. See questions in DIFFERENT order (e.g., Q8, Q2, Q4, Q1, Q6...)
   ← Different from Student 1!
5. Answer all questions
6. Click Submit
7. See score: 75% PASSED ✓
```

### Admin: Review Results
```
1. Go to exam_results.php (as admin or see in DB)
2. See both students' attempts:
   - Student 1: 70%, no flags
   - Student 2: 75%, no flags
3. Both have 2 attempts remaining
```

---

## Advanced Options

### Auto-Marking Setup (For Better Grading)

**Add Keywords to Questions:**
```
Example Question: "What is the capital of France?"
answer_keywords: "Paris, Paris France"

If student answers: "The capital of France is Paris"
→ Matches "Paris" = 1/2 keywords = 50% (no points)

If student answers: "Paris, which is in France"
→ Matches "Paris" + "Paris France" = 2/2 = 100% (full points)
```

**How to Add Keywords:**
1. phpMyAdmin → topic_questions table
2. Edit question you want to auto-mark
3. Set `answer_keywords` field: "keyword1, keyword2, keyword3"
4. Set `marking_type` to "auto"
5. Now this question will auto-mark when exam is submitted

### Time Calculation
- Auto: questions × 5 min/question + 10% = time limit
- Manual: Set custom time when creating exam
- Example: 10 questions × 5 = 50 × 1.1 = 55 minutes

### Passing Score
- Default: 50%
- Can change per exam
- Example: Difficult exam set to 60% passing

### Attempt Limits
- Default: 3 times
- Can change per exam
- Student can't retake after limit

---

## Database Schema Overview

**exams** (What exam, when, for who)
- title, description, subject_id, unit_id
- time_limit_minutes, passing_percentage, max_attempts
- shuffle_questions, show_results

**exam_questions** (Which questions in exam)
- exam_id, question_id, question_order, points

**exam_attempts** (Each time someone takes exam)
- exam_id, user_id, attempt_number
- started_at, submitted_at, time_taken_seconds
- score, percentage, status
- tab_switches, suspicious_activity, ip_address

**exam_answers** (Each answer given)
- attempt_id, question_id, user_answer
- is_correct, points_earned

**exam_tab_switches** (Cheat detection logs)
- attempt_id, switch_type, logged_at

---

## Performance Tips

### For Large Question Pools (500+ questions):
- Use search feature in exam creation
- Add questions in batches of 50
- Consider using filters by subject/topic

### For Many Exams:
- Create exam naming convention (YearTermLevel)
- Example: "2024Term1P4Math", "2024Term1P4English"
- Makes finding and organizing easier

### For Large Classes (500+ students):
- Database has proper indexes
- No performance issues expected
- Monitor database size (~1MB per 100 attempts)

---

## Security Reminders

✅ **Always:**
- Require student login before exam
- Set reasonable attempt limits
- Review flagged exams
- Change admin password after setup

⚠️ **Don't:**
- Share exam details before test date
- Let students see answer_keywords
- Leave admin panel logged in unattended

---

## Congratulations! 🎉

You now have a fully functional exam system:
- ✅ Admins can create exams
- ✅ Students can take exams
- ✅ Exams are automatically graded
- ✅ Questions are randomized per student
- ✅ Cheating is detected
- ✅ Results are tracked

**Ready to deploy!**

For more details, see:
- `EXAM_SYSTEM_SUMMARY.md` - Overview
- `EXAM_IMPLEMENTATION_GUIDE.md` - Complete guide
- `EXAM_SYSTEM_DESIGN.md` - Technical design

