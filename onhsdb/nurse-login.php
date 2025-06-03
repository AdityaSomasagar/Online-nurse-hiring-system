<?php
session_start();
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes/config.php'; // Your DB config path

$message = '';
$messageClass = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "All fields are required!";
        $messageClass = "error";
    } else {
        try {
            $query = $dbh->prepare("SELECT * FROM nurses WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $nurse = $query->fetch(PDO::FETCH_ASSOC);

            if ($nurse && password_verify($password, $nurse['password'])) {
                $_SESSION['nurse_id'] = $nurse['id'];
                $_SESSION['nurse_email'] = $nurse['email'];

                header("Location: nurse_dashboard.php"); // Change to your nurse dashboard page
                exit();
            } else {
                $message = "Invalid email or password.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again.";
            error_log("Nurse login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: linear-gradient(to right, #6a11cb, #2575fc); 
            color: white;
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.9); 
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }
        h1 { 
            font-size: 30px; 
            color: #333; 
            margin-bottom: 30px; 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            font-size: 16px; 
            color: #555; 
            margin-bottom: 8px; 
        }
        input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 16px; 
            background-color: #f9f9f9; 
            transition: border-color 0.3s ease;
        }
        input:focus { 
            border-color: #2b8379; 
            outline: none;
        }
        button { 
            background-color: #2b8379; 
            color: white; 
            padding: 14px; 
            border: none; 
            border-radius: 8px; 
            width: 100%; 
            cursor: pointer; 
            font-size: 18px; 
            transition: background-color 0.3s ease;
        }
        button:hover { 
            background-color: #3ca399; 
        }
        .error { 
            color: #e74c3c; 
            font-size: 14px; 
            margin-top: 15px; 
        }
        .success { 
            color: #2ecc71; 
            font-size: 14px; 
            margin-top: 15px; 
        }
        .forgot-password {
            font-size: 14px;
            color: #2575fc;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Nurse Login</h1>

        <?php if ($message): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login">Login</button>
        </form>

        <a href="forg-password.php" class="forgot-password">Forgot Password?</a>
    </div>
</body>
</html>
