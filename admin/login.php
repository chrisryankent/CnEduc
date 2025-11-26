<?php
// Configure session BEFORE starting it
require_once __DIR__ . '/../includes/db.php';
session_start();
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$attempt_file = sys_get_temp_dir() . '/cneduc_login_attempts.json';

// Check rate limiting
$max_attempts = 5;
$lockout_seconds = 600; // 10 minutes
$attempts = [];

if (file_exists($attempt_file)) {
    $attempts = json_decode(file_get_contents($attempt_file), true) ?: [];
}

$ip = $_SERVER['REMOTE_ADDR'];
$now = time();

// Clean old entries
foreach ($attempts as $stored_ip => $data) {
    if ($now - $data['last_attempt'] > $lockout_seconds) {
        unset($attempts[$stored_ip]);
    }
}

// Check if IP is locked out
if (isset($attempts[$ip]) && $attempts[$ip]['count'] >= $max_attempts) {
    if ($now - $attempts[$ip]['last_attempt'] < $lockout_seconds) {
        $remaining = $lockout_seconds - ($now - $attempts[$ip]['last_attempt']);
        $error = "Too many failed login attempts. Please try again in " . ceil($remaining / 60) . " minute(s).";
    }
}

if (isset($_POST['username']) && empty($error)) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    if (login_admin($user, $pass)) {
        // Clear attempts on successful login
        unset($attempts[$ip]);
        file_put_contents($attempt_file, json_encode($attempts));
        header('Location: dashboard.php');
        exit;
    } else {
        // Record failed attempt
        if (!isset($attempts[$ip])) {
            $attempts[$ip] = ['count' => 0, 'last_attempt' => $now];
        }
        $attempts[$ip]['count']++;
        $attempts[$ip]['last_attempt'] = $now;
        file_put_contents($attempt_file, json_encode($attempts));
        
        $error = 'Invalid username or password';
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>
<div class="card">
  <h1>Admin Login</h1>
  <?php if (!empty($error)): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  <form method="post">
    <div><label>Username<br><input type="text" name="username" required></label></div>
    <div><label>Password<br><input type="password" name="password" required></label></div>
    <div><input type="submit" value="Login"></div>
  </form>
  <p>Demo credentials: <strong>admin / password</strong></p>
  <p style="color:#999; font-size:12px;">This uses secure password hashing (bcrypt). Change credentials after first login.</p>
</div>
<?php include __DIR__ . '/footer.php'; ?>
