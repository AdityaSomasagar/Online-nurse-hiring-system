<?php
session_start();
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\Online-Nurse-Hiring-System-PHP\onhs\includes/config.php'; // Ensure this file has the correct PDO database connection

$message = '';
$messageClass = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = "All fields are required!";
        $messageClass = "error";
    } else {
        try {
            // Prepare the SQL query
            $query = $dbh->prepare("SELECT * FROM users WHERE username = :username");
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->execute();

            // Fetch user data
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Password matches, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to dashboard or home page
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Invalid credentials";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f0f2f5; }
        .login-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); width: 90%; max-width: 400px; }
        h1 { font-size: 28px; color: #333; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 14px; color: #555; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        input:focus { border-color: #2b8379; outline: none; }
        button { background-color: #2b8379; color: white; padding: 10px; border: none; border-radius: 6px; width: 100%; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #3ca399; }
        .error { color: #e74c3c; font-size: 14px; margin-top: 8px; text-align: center; }
        .success { color: #2ecc71; font-size: 14px; margin-top: 8px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>User Login</h1>

        <?php if ($message): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="username">Name</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
