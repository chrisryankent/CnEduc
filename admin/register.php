<?php
// admin/register.php - Create new admin user (temporary utility)
require_once __DIR__ . '/../includes/db.php';
session_start();
require_once __DIR__ . '/../includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    
    // Validation
    if (empty($username)) {
        $error = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($username) > 100) {
        $error = 'Username must not exceed 100 characters.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username already exists
        $username_esc = $mysqli->real_escape_string($username);
        $res = $mysqli->query("SELECT id FROM admin_users WHERE username = '$username_esc'");
        if ($res && $res->num_rows > 0) {
            $error = 'Username already exists.';
        } else {
            // Hash password and insert
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $password_hash_esc = $mysqli->real_escape_string($password_hash);
            
            $sql = "INSERT INTO admin_users (username, password_hash) VALUES ('$username_esc', '$password_hash_esc')";
            if ($mysqli->query($sql)) {
                $message = "✓ Admin user '<strong>$username</strong>' created successfully!<br>";
                $message .= "Password hash: <code style='background: #f0f0f0; padding: 4px; font-family: monospace; word-break: break-all;'>$password_hash</code><br><br>";
                $message .= "You can now log in with:<br>";
                $message .= "<strong>Username:</strong> $username<br>";
                $message .= "<strong>Password:</strong> " . htmlspecialchars($password) . "<br><br>";
                $message .= "<a href='login.php?username=" . urlencode($username) . "' style='color: #0066cc; text-decoration: none;'>> Go to Login</a>";
            } else {
                $error = 'Failed to create user: ' . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register Admin User - CnEduc</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
        }
        .register-container input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            margin-top: 16px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .register-container button:hover {
            background: #0052a3;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            border-left: 4px solid #28a745;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Create Admin User</h1>
        <p style="color: #666; font-size: 14px;">Use this page to register a new admin account. After creating an account, go to <a href="login.php">login.php</a> to test it.</p>
        
        <?php if (!empty($message)): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="e.g., admin" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
            
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Repeat password" required>
            
            <button type="submit">Create Admin User</button>
        </form>
        
        <hr style="margin-top: 30px; border: none; border-top: 1px solid #ddd;">
        
        <h3>Current Admin Users</h3>
        <?php
        $res = $mysqli->query("SELECT id, username, created_at FROM admin_users ORDER BY created_at DESC");
        if ($res && $res->num_rows > 0) {
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background: #f0f0f0;'><th style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>ID</th><th style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>Username</th><th style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>Created</th></tr>";
            while ($user = $res->fetch_assoc()) {
                echo "<tr><td style='padding: 8px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td style='padding: 8px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td style='padding: 8px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($user['created_at']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: #999;'>No admin users found.</p>";
        }
        ?>
        
        <p style="margin-top: 20px; font-size: 12px; color: #999;">
            <strong>Important:</strong> This registration page is for testing purposes. In production, remove this file or protect it with authentication.
        </p>
    </div>
</body>
</html>
