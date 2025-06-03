<?php
require_once 'config.php';  // Database connection configuration

$message = '';
$messageClass = '';

if (isset($_POST['login'])) {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password";
        $messageClass = "error";
    } else {
        try {
            $query = $dbh->prepare("SELECT * FROM nurses WHERE username = :username");
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->execute();
            
            if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['nurse_id'] = $row['id'];
                    $_SESSION['nurse_name'] = $row['username'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid credentials";
                    $messageClass = "error";
                }
            } else {
                $message = "Invalid credentials";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again later.";
            $messageClass = "error";
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
    <title>Nurse Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2f5;
        }

        .login-container {
            display: flex;
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 90%;
            max-width: 800px;
            padding: 0;
            position: relative;
        }

        .login-form {
            width: 50%;
            padding: 40px;
            position: relative;
            z-index: 1;
        }

        .illustration {
            width: 50%;
            background-color: #2b8379;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .illustration::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, #3ca399, #2b8379);
            border-radius: 50%;
            top: -50%;
            left: -50%;
            z-index: 0;
        }

        .nurse-image {
            width: 80%;
            height: auto;
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #2b8379;
        }

        button {
            background-color: #2b8379;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3ca399;
        }

        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-form, .illustration {
                width: 100%;
            }

            .illustration {
                order: -1;
                padding: 40px;
                min-height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h1>Nurse Login</h1>
            
            <?php if ($message): ?>
                <div class="<?php echo $messageClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="username">Name</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <div class="illustration">
            <svg class="nurse-image" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Nurse illustration content -->
                <!-- You can replace this with your own SVG nurse illustration or use an image -->
                <!-- For brevity, basic shapes are used here -->
                <circle cx="50" cy="35" r="20" fill="white"/>
                <rect x="30" y="55" width="40" height="45" fill="white"/>
                <rect x="45" cy="15" width="10" height="10" fill="#2b8379"/>
            </svg>
        </div>
    </div>
</body>
</html>