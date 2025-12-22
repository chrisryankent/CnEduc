# CnEduc User System - Quick Start Guide

## 🚀 Getting Started

### For End Users

#### 1. **Registration**
- Click "Register" in the top navigation
- Fill in your name, email, and password (minimum 6 characters)
- Confirm your password matches
- Click "Sign Up"
- You'll be redirected to login page

#### 2. **Login**
- Click "Login" in the top navigation
- Enter your registered email and password
- Click "Sign In"
- You're now logged in and will see your name in the header

#### 3. **View Dashboard**
- After login, click your name/avatar in the header
- Click "Dashboard" from the dropdown menu
- See your learning progress, completed topics/units, and earned badges

#### 4. **Track Learning Progress**
- While reading any topic or unit, look for the "Learning Progress" or "Your Progress" card
- Click "Mark Complete" button after finishing the content
- Your progress will update immediately
- Achievements are awarded automatically

#### 5. **View Your Certificate**
- Click your name in the header → "Certificate"
- See your achievement certificate
- Click "Print / Save as PDF" to download

#### 6. **Logout**
- Click your name in the header → "Logout"
- You'll be logged out and redirected to home page

---

## 🛠️ For Developers

### Database Setup

Before using the user system, ensure the new tables are created:

```bash
# Import the updated schema into your database
mysql -u [username] -p [database_name] < cneduc_schema.sql
```

**New Tables Created:**
- `user_topic_completion` - Tracks completed topics
- `user_unit_completion` - Tracks completed units
- `user_achievements` - Stores earned badges

### Using Authentication in PHP

#### Check if User is Logged In
```php
<?php
require_once __DIR__ . '/includes/functions.php';

if (is_user_logged_in()) {
    $user = get_current_user();
    echo "Welcome, " . $user['first_name'];
} else {
    echo "Please log in";
}
?>
```

#### Require Login for a Page
```php
<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_user_logged_in()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Page content here
?>
```

#### Mark Content as Complete
```php
<?php
require_once __DIR__ . '/includes/functions.php';

$user = get_current_user();
mark_topic_complete($user['id'], $topic_id);

// Check and award any new achievements
check_and_award_achievements($user['id']);
?>
```

#### Get User Progress
```php
<?php
$progress = get_user_progress_summary($user['id']);

echo "Topics completed: " . $progress['topics_completed'];
echo "Overall progress: " . $progress['overall_progress'] . "%";
?>
```

#### Get User Achievements
```php
<?php
$achievements = get_user_achievements($user['id']);

foreach ($achievements as $achievement) {
    echo $achievement['achievement_name']; // e.g., "First Step"
}
?>
```

---

## 📋 Available Functions

### Authentication Functions
```php
register_user($name, $email, $password)          // Returns ['success' => bool, 'error' => string]
login_user($email, $password)                    // Returns ['success' => bool, 'error' => string]
logout_user()                                    // Destroys session
is_user_logged_in()                             // Returns boolean
get_current_user()                              // Returns user array or null
get_user($user_id)                              // Returns user by ID
```

### Progress Tracking Functions
```php
mark_topic_complete($user_id, $topic_id)        // Returns boolean
is_topic_complete($user_id, $topic_id)          // Returns boolean
mark_unit_complete($user_id, $unit_id)          // Returns boolean
is_unit_complete($user_id, $unit_id)            // Returns boolean
get_user_progress_summary($user_id)             // Returns array with stats
```

### Achievement Functions
```php
award_achievement($user_id, $slug, $name, $description)
get_user_achievements($user_id)                 // Returns array of achievements
check_and_award_achievements($user_id)          // Auto-awards based on progress
```

---

## 🎯 Achievement System

### Auto-Awarded Achievements

Achievements are automatically awarded when users reach these milestones:

| Achievement | Trigger |
|---|---|
| First Step | Complete 1 topic |
| Getting Started | Complete 5 topics |
| Momentum | Complete 10 topics |
| Quarter Way | Reach 25% overall progress |
| Halfway There | Reach 50% overall progress |
| Almost Done | Reach 75% overall progress |
| Master Learner | Reach 100% overall progress |

### Manual Achievement Award
```php
<?php
award_achievement(
    $user_id, 
    'custom_achievement',      // slug (unique per user)
    'Custom Achievement Name',  // display name
    'Description of achievement' // description
);
?>
```

---

## 🔍 Troubleshooting

### User Can't Login
- Verify email and password are correct
- Check if account exists in database: `SELECT * FROM users WHERE email = '[email]';`
- Verify password using: `password_verify('[password]', '[password_hash]');`

### Progress Not Saving
- Verify user is logged in: `is_user_logged_in()` returns true
- Check `user_topic_completion` table: `SELECT * FROM user_topic_completion WHERE user_id = [id];`
- Ensure `mark_topic_complete()` returned true (no duplicate completion error)

### Achievements Not Appearing
- Verify function called: `check_and_award_achievements($user_id);`
- Check achievements table: `SELECT * FROM user_achievements WHERE user_id = [id];`
- Verify progress thresholds met: `get_user_progress_summary($user_id);`

### Session Not Starting
- Verify `session_start();` is at top of page before any output
- Check PHP error logs in `/xampp/apache/logs/`
- Ensure cookies enabled in browser

---

## 🔐 Security Notes

1. **Always use prepared statements** to prevent SQL injection
2. **Hash passwords** with `password_hash()` - never store plain text
3. **Validate user input** on both client and server side
4. **Escape output** with `htmlspecialchars()` to prevent XSS
5. **Use HTTPS** in production for secure transmission
6. **Validate sessions** - check `is_user_logged_in()` on sensitive pages
7. **Set secure cookies** - update php.ini for production:
   ```ini
   session.cookie_secure = 1
   session.cookie_httponly = 1
   session.cookie_samesite = Strict
   ```

---

## 📱 Testing with Sample Account

### Manual Database Insert (Testing Only)
```sql
INSERT INTO users (email, password_hash, first_name, last_name) 
VALUES ('test@example.com', '$2y$10$...', 'Test', 'User');
```

### Generate Hash for Testing
```php
<?php
$hash = password_hash('password123', PASSWORD_BCRYPT);
echo $hash; // Use in SQL above
?>
```

---

## 🚨 Common Issues & Solutions

| Issue | Solution |
|---|---|
| Blank page after login | Check error_reporting in php.ini, enable error logs |
| Redirect loop on login | Clear browser cookies, verify database connection |
| Progress not showing | Run `check_and_award_achievements()` after mark complete |
| CSS/styling broken | Verify Font Awesome CDN is accessible |
| Header dropdown not working | Check browser console for JavaScript errors |

---

## 📞 Support

For issues or questions:
1. Check error logs: `/xampp/apache/logs/error.log`
2. Review database tables: `SHOW TABLES; DESCRIBE user_achievements;`
3. Test functions individually in command line
4. Verify all required files exist: `register.php`, `login.php`, `dashboard.php`, etc.

---

**Version**: 1.0  
**Last Updated**: December 7, 2025  
**Status**: Production Ready ✅
