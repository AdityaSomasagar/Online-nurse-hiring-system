<?php
session_start();
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes/config.php';

$message = '';
$messageClass = '';

if (isset($_POST['user_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
        $messageClass = "error";
    } else {
        try {
            $query = $dbh->prepare("SELECT * FROM users WHERE username = :username");
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = 'patient'; // Renamed to 'patient' for clarity
                header("Location: team.php"); // Redirect to team.php for user registration
                exit();
            } else {
                $message = "Invalid username or password.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An unexpected error occurred. Please try again later.";
            error_log("Patient login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Online Nurse Konnekt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8BC6EC, #2575FC);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 450px;
            text-align: center;
        }

        h1 {
            color: #2575FC;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .login-options {
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .login-options button {
            background-color: #2575FC;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        .login-options button:hover {
            background-color: #1A52BE;
        }

        .login-form-wrapper {
            border-top: 1px solid #eee;
            padding-top: 30px;
        }

        h2 {
            color: #555;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 1em;
            color: #555;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 24px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            margin-bottom: 5px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2575FC;
            outline: none;
            box-shadow: 0 0 8px rgba(37, 117, 252, 0.2);
        }

        .login-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .login-button:hover {
            background-color: #218838;
        }

        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: center;
        }

        .forgot-password {
            display: block;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #2575FC;
            text-decoration: underline;
        }

        .no-account {
            margin-top: 25px;
            font-size: 0.9em;
            color: #777;
        }

        .no-account a {
            color: #2575FC;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .no-account a:hover {
            color: #1A52BE;
            text-decoration: underline;
        }

        .login-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1><span style="color: #28a745;">Online</span> Nurse <span style="color: #2575FC;">Connekt</span></h1>

        <div class="login-options">
            <button onclick="showPatientLoginForm()">Patient Login</button>
            <button onclick="window.location.href='nurse-login.php'">Nurse Login</button>
        </div>

        <div id="patientLoginForm" class="login-form login-form-wrapper">
            <h2>Patient Login</h2>
            <?php if ($message): ?>
                <div class="<?php echo $messageClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="user_login" class="login-button">Log In</button>
            </form>

            <a href="forg-password.php" class="forgot-password">Forgot Password?</a>
            <p class="no-account">Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        function showPatientLoginForm() {
            document.getElementById('patientLoginForm').style.display = 'block';
        }

        // Show the patient login form by default on page load
        document.addEventListener('DOMContentLoaded', function() {
            showPatientLoginForm();
        });
    </script>
</body>
</html>