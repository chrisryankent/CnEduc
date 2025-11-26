# CnEduc - Educational Content Management System
## Uganda Curriculum-Based Learning Platform

### 🎯 System Overview
CnEduc is a W3Schools/TutorialsPoint-style educational website built for Uganda's school curriculum. It provides structured learning content organized by **Primary classes (P1-P7)**, **Secondary classes (S1-S6)**, and **University courses**.

**Key Feature:** Each class has different topics within the same subject (e.g., P1 Math differs from P3 Math), reflecting realistic curriculum progression.

---

## 📊 Database Architecture

### Core Tables

**1. levels** (3 rows)
- Primary, Secondary, University
- Top-level organizational unit

**2. classes** (13 rows)
- P1-P7 for Primary level
- S1-S6 for Secondary level
- Linked to levels table

**3. subjects**
- Per-class organization (not per-level)
- E.g., P1 has Math, English, Science, Social Studies
- E.g., S1 has Math, English, Biology, Chemistry, Physics, History, Geography, Social Studies

**4. topics**
- Specific lessons within subjects
- E.g., "Numbers 1-10", "Addition Basics" (P1 Math), "Cell Structure", "Classification of Organisms" (S1 Biology)

**5. courses** (University level)
- E.g., "BSc Computer Science", "BA Education"
- Top-level university programs

**6. units** (University level)
- Course units/modules with code and title
- E.g., "CS101 - Introduction to Programming"

**7. admin_users**
- Secure login with bcrypt password hashing
- Demo: username=`admin`, password=`password`

---

## 🌍 Navigation Hierarchy

### School Curriculum (Primary & Secondary)
```
Levels
  └─ Classes (P1-P7, S1-S6)
       └─ Subjects (Math, English, Science, etc.)
            └─ Topics (specific lessons)
```

### University
```
Courses
  └─ Units (with code and title)
```

---

## 📁 Project Structure

### Public Pages (Root Level)
- `index.php` — Homepage with level/course navigation and quick search
- `levels.php` — Lists Primary, Secondary, University
- `classes.php` — Lists P1-P7 or S1-S6 based on selected level
- `subjects.php` — Lists subjects for selected class
- `topics.php` — Lists topics for selected subject with breadcrumb
- `read_topic.php` — Display individual topic content
- `courses.php` — Lists university courses
- `units.php` — Lists units for selected course
- `read_unit.php` — Display individual unit content
- `search.php` — Full-text search with class/level context

### Admin Pages (`/admin`)

**Authentication:**
- `login.php` — Secure login form (bcrypt password verification)
- `logout.php` — Session termination
- `_auth.php` — Centralized session check (included in all admin pages)
- `header.php` & `footer.php` — Admin-specific layout
- `dashboard.php` — Admin control panel

**School Curriculum Management:**
- **Classes** (4 pages): `classes_list.php`, `class_add.php`, `class_edit.php`, `class_delete.php`
- **Subjects** (4 pages): `subjects_list.php`, `subject_add.php`, `subject_edit.php`, `subject_delete.php`
- **Topics** (4 pages): `topics_list.php`, `topic_add.php`, `topic_edit.php`, `topic_delete.php`

**University Management:**
- **Courses** (4 pages): `courses_list.php`, `course_add.php`, `course_edit.php`, `course_delete.php`
- **Units** (4 pages): `units_list.php`, `unit_add.php`, `unit_edit.php`, `unit_delete.php`

### Core Files

**Includes (`/includes`):**
- `db.php` — MySQL/MySQLi connection initialization
- `functions.php` — 30+ helper functions for CRUD and authentication
- `header.php` — Public site header with navigation
- `footer.php` — Footer template

**Database:**
- `cneduc_schema.sql` — Complete schema with sample data (P1, P2, S1 classes populated)

**Styling:**
- `assets/style.css` — Responsive grid layout, breadcrumbs, responsive design

---

## 🔐 Security Features

1. **Authentication**: Bcrypt password hashing (`PASSWORD_BCRYPT`)
2. **CSRF Protection**: Token-based validation on all forms using `hash_equals()`
3. **SQL Injection Prevention**: `real_escape_string()` for text inputs, integer casting for IDs
4. **Session Management**: Server-side sessions with centralized auth check in `_auth.php`
5. **Access Control**: All admin pages require login; public pages are accessible to all

---

## 🛠️ Core Helper Functions (in `includes/functions.php`)

### Query Functions
- `get_levels()` — All levels
- `get_level($id)` — Single level
- `get_classes_by_level($level_id)` — Classes in a level
- `cned_get_class($id)` — Single class
- `get_subjects_by_class($class_id)` — Subjects in a class
- `get_subject($id)` — Single subject
- `get_topics_by_subject($subject_id)` — Topics in a subject
- `get_topic($id)` — Single topic
- `get_courses()` — All university courses
- `get_course($id)` — Single course
- `get_units_by_course($course_id)` — Units in a course
- `get_unit($id)` — Single unit

### CRUD Functions
- `add_topic($subject_id, $title, $content, $position)` → Boolean
- `update_topic($id, $subject_id, $title, $content, $position)` → Boolean
- `delete_topic($id)` → Boolean
- `add_unit($course_id, $code, $title, $content, $position)` → Boolean
- `update_unit($id, $course_id, $code, $title, $content, $position)` → Boolean
- `delete_unit($id)` → Boolean

### Authentication Functions
- `login_admin($username, $password)` → User record or null
- `is_admin_logged_in()` → Boolean
- `get_admin_user($id)` → User record or null

### Security Functions
- `generate_csrf_token()` → String token
- `verify_csrf_token($token)` → Boolean

---

## 🚀 Quick Start

### 1. Database Setup
```sql
-- Import the schema
mysql -u root -p cneduc < cneduc_schema.sql
```

### 2. Configuration
Update `includes/db.php` with your database credentials:
```php
$mysqli = new mysqli('localhost', 'username', 'password', 'cneduc');
```

### 3. Access the System
- **Public Site**: `http://localhost/cneduc/`
- **Admin Panel**: `http://localhost/cneduc/admin/login.php`
- **Demo Login**: username=`admin`, password=`password`

---

## 📋 Sample Data Included

### Primary Classes
- P1: Math (3 topics), English (3 topics)
- P2: Math (0), English (0), Science, Social Studies

### Secondary Classes
- S1: Math, English, Biology (3 topics), Chemistry, Physics, History, Geography, Social Studies

### University
- BSc Computer Science (2 units)
- BA Education (1 unit)

---

## 🔄 URL Parameters

**Important:** The system uses standardized query parameters:
- Topic pages: `?id=` (not `?topic_id=`)
- Unit pages: `?id=` (not `?unit_id=`)
- Courses: `?course_id=`
- Classes: `?class_id=` and `?level_id=`
- Subjects: `?class_id=`
- Topics list: `?subject_id=`

---

## 🎨 Frontend Features

1. **Responsive Grid Layout** — Works on mobile, tablet, desktop
2. **Breadcrumb Navigation** — Shows full path: Level > Class > Subject > Topic
3. **Search with Context** — Results show class/level/subject hierarchy
4. **Sidebar Navigation** — Quick access to search and admin login
5. **Card-Based UI** — Clean, organized content presentation

---

## 📝 Next Steps & Future Enhancements

### Completed ✅
- Class-based hierarchy for Primary/Secondary
- Full CRUD for all entities
- Bcrypt password hashing and CSRF protection
- Search functionality with context
- Responsive design
- Complete admin interface

### Potential Enhancements 🚀
1. User accounts and progress tracking
2. Quiz/assessment functionality
3. Rich text editor (TinyMCE, CKEditor) for content entry
4. PDF export for topics/units
5. Discussion forums or Q&A system
6. Media uploads (images, videos)
7. Email notifications for admin
8. Bulk import of topics/units via CSV
9. Analytics dashboard showing popular content
10. Mobile app version

---

## 🐛 Troubleshooting

**"Subject not found" error**
- Verify subject is linked to a class, not a level directly

**"Database connection failed"**
- Check db.php credentials match your MySQL setup
- Verify database exists: `SHOW DATABASES;`

**Admin pages redirect to login**
- Session expired: log in again
- Browser cookies may be disabled

**Search returns no results**
- Ensure topics have been added to at least one subject
- Check that subject is linked to a class

---

## 📄 License
This system is designed for educational use in Uganda's school curriculum context.

---

## 📞 Support
For issues or feature requests, verify:
1. Database schema imported correctly
2. All PHP files present in correct directories
3. Admin authenticated before accessing admin pages
4. Parameter names match (use `?id=` not `?topic_id=`)

