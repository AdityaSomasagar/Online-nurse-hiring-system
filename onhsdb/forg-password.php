<?php
session_start();
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes/config.php';

$message = '';
$messageClass = '';

// Function to generate a unique token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// --- 1. Password Reset Request ---
if (isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Please enter your email address.";
        $messageClass = "error";
    } else {
        try {
            // Check if the email exists in the database
            $query = $dbh->prepare("SELECT id, username FROM users WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a unique token
                $token = generateToken();
                $user_id = $user['id'];

                // Store the token in the database with an expiry time (e.g., 1 hour)
                $expiry_time = date('Y-m-d H:i:s', time() + 3600); // 1 hour
                $update_query = $dbh->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE id = :user_id");
                $update_query->bindParam(':token', $token, PDO::PARAM_STR);
                $update_query->bindParam(':expiry', $expiry_time, PDO::PARAM_STR);
                $update_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $update_query->execute();

                // Send reset email (replace with your actual email sending method)
                $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token; // CHANGE THIS URL
                $email_message = "Please click on the following link to reset your password: <a href='" . $reset_link . "'>" . $reset_link . "</a>";
                //  mail($email, "Password Reset Request", $email_message);  <--  Remove this line, it's insecure.  Use a proper mail function.

                // For testing purposes, display the reset link:  <-- Keep this for testing
                echo "Reset Link: <a href='" . $reset_link . "'>" . $reset_link . "</a><br>";

                $message = "Password reset link has been sent to your email. Please check your inbox.";
                $messageClass = "success";
            } else {
                $message = "Email address not found. Please check your email.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again.";
            error_log("Reset request error: " . $e->getMessage());
        }
    }
}

// --- 2. Password Reset ---
if (isset($_POST['reset_password'])) {
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
        $messageClass = "error";
    } else if ($new_password != $confirm_password) {
        $message = "Passwords do not match.";
        $messageClass = "error";
    } else if (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $messageClass = "error";
    } else {
        try {
            // Check if the token is valid and not expired
            $query = $dbh->prepare("SELECT id FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $user_id = $user['id'];
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password and clear the token
                $update_query = $dbh->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :user_id");
                $update_query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $update_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $update_query->execute();

                $message = "Password has been reset successfully. You can now login with your new password.";
                $messageClass = "success";
                //  header("Location: login.php"); // Redirect to login page  <-- Commented out for testing
                //  exit();
            } else {
                $message = "Invalid or expired reset token.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again.";
            error_log("Password reset error: " . $e->getMessage());
        }
    }
}

// --- 3.  Forgot Password Request Form ---
if (!isset($_GET['token'])) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <style>
            /* Styles remain largely the same, but adapted for password reset */
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

            .container {
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

            input[type="email"],
            input[type="password"] {
                width: calc(100% - 24px);
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 1em;
                margin-bottom: 5px;
                transition: border-color 0.3s ease;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: #2575FC;
                outline: none;
                box-shadow: 0 0 8px rgba(37, 117, 252, 0.2);
            }

            button {
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

            button:hover {
                background-color: #218838;
            }

            .error {
                color: #dc3545;
                font-size: 0.9em;
                margin-top: 10px;
                text-align: center;
            }

            .success {
                color: #28a745;
                font-size: 0.9em;
                margin-top: 10px;
                text-align: center;
            }
            .back-to-login {
                margin-top: 20px;
                font-size: 0.9em;
                color: #555;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .back-to-login:hover {
                color: #2575FC;
                text-decoration: underline;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <h1>Forgot Password</h1>
            <?php if ($message): ?>
                <div class="<?php echo $messageClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <p>Enter your email address to receive a password reset link.</p>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" name="reset_request">Send Reset Link</button>
            </form>
            <a href="login.php" class="back-to-login">Back to Login</a>
        </div>
    </body>
    </html>
<?php
}
// --- 4. Password Reset Form ---
else {
    $token = $_GET['token'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <style>
            /* Styles remain largely the same */
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

            .container {
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

            input[type="password"] {
                width: calc(100% - 24px);
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 1em;
                margin-bottom: 5px;
                transition: border-color 0.3s ease;
            }

            input[type="password"]:focus {
                border-color: #2575FC;
                outline: none;
                box-shadow: 0 0 8px rgba(37, 117, 252, 0.2);
            }

            button {
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

            button:hover {
                background-color: #218838;
            }

            .error {
                color: #dc3545;
                font-size: 0.9em;
                margin-top: 10px;
                text-align: center;
            }

            .success {
                color: #28a745;
                font-size: 0.9em;
                margin-top: 10px;
                text-align: center;
            }
             .back-to-login {
                margin-top: 20px;
                font-size: 0.9em;
                color: #555;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .back-to-login:hover {
                color: #2575FC;
                text-decoration: underline;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <h1>Reset Password</h1>
            <?php if ($message): ?>
                <div class="<?php echo $messageClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="reset_password">Reset Password</button>
            </form>
            <a href="login.php" class="back-to-login">Back to Login</a>
        </div>
    </body>
    </html>
    <?php
}
?>
