# CnEduc (Tutorial site)

This is a small PHP/MySQL scaffolding for a tutorial site modeled like W3Schools / TutorialsPoint but organised by school levels (Primary, Secondary, University).

Files added:
- `includes/db.php` — PDO connection configuration (edit credentials).
- `includes/functions.php` — helper functions to query the DB.
- `levels.php` — choose a study level.
- `subjects.php` — list subjects for a selected level.
- `topics.php` — list topics for a selected subject.
- `read_topic.php` — read the selected topic content.
- `courses.php` — list university courses.
- `units.php` — list course units for a selected course.
- `read_unit.php` — read the selected course unit.
- `cneduc_schema.sql` — database schema and seed data.

Setup
1. Create the database and tables by importing `cneduc_schema.sql` into your MySQL server (phpMyAdmin or CLI):

```powershell
mysql -u root -p < cneduc_schema.sql
```

2. Edit `includes/db.php` to match your DB credentials.

3. Place the project in your web server root (e.g., XAMPP `htdocs`) or configure your server to serve this folder.

4. Visit `levels.php` in your browser to start, or `index.php` if you already have a homepage.

Notes & next steps
- Add authentication, admin UI for editing content, richer content formatting (HTML stored in `content`), and search.
- You can integrate these pages into your existing `index.php` or navigation.
