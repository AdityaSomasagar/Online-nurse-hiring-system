<?php
// Previous PHP code remains exactly the same up to the DOCTYPE
// Database connection configuration
require_once 'config.php';  // Create this file separately with database credentials

// Initialize variables for error/success messages
$message = '';
$messageClass = '';

if (isset($_POST['register'])) {
    // Sanitize and validate input
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required";
        $messageClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $messageClass = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long";
        $messageClass = "error";
    } else {
        try {
            // Check if email already exists
            $query = $dbh->prepare("SELECT id FROM users WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $message = "Email already registered. Please try logging in.";
                $messageClass = "error";
            } else {
                // Hash password with strong algorithm
                $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

                // Insert user data
                $query = $dbh->prepare("INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())");
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':password', $passwordHash, PDO::PARAM_STR);

                if ($query->execute()) {
                    $message = "Registration successful! You can now <a href='login.php'>log in</a>.";
                    $messageClass = "success";
                } else {
                    $message = "Registration failed. Please try again later.";
                    $messageClass = "error";
                }
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again later.";
            $messageClass = "error";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('https://img.freepik.com/free-photo/blur-hospital_1203-7957.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            max-width: 400px;
            width: 90%;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #34495e;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            background-color: #2980b9;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3498db;
        }

        .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success {
            color: #27ae60;
            background: rgba(39, 174, 96, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success a {
            color: #2980b9;
            text-decoration: none;
        }

        .success a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .form-container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Hospital User Registration</h2>
        
        <?php if ($message): ?>
            <p class="<?php echo $messageClass; ?>"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                       required pattern="[A-Za-z0-9_]{3,20}"
                       title="Username must be between 3-20 characters and can only contain letters, numbers, and underscores">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" 
                       required minlength="8"
                       pattern="(?=.\d)(?=.[a-z])(?=.*[A-Z]).{8,}"
                       title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters">
            </div>

            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>