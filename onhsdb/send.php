<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

require 'vendor/autoload.php'; // Use Composer's autoloader
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes\config.php';

$message = '';
$messageClass = '';

// Function to generate a random numeric OTP
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

if (isset($_POST['register'])) {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $otp = generateOTP(); // Generate 6-digit OTP
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP expires in 10 minutes

    if (empty($username) || empty($email) || empty($phone) || empty($_POST['password'])) {
        $message = "All fields are required!";
        $messageClass = "error";
    } else {
        try {
            // Check if email or phone already exists
            $query = $dbh->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $message = "Email or phone number already registered.";
                $messageClass = "error";
            } else {
                // Insert user data with OTP
                $query = $dbh->prepare("INSERT INTO users (username, email, phone, password, otp_code, otp_expiry, is_verified)
                                        VALUES (:username, :email, :phone, :password, :otp, :otp_expiry, 0)"); // is_verified defaults to 0
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':phone', $phone, PDO::PARAM_STR);
                $query->bindParam(':password', $password, PDO::PARAM_STR);
                $query->bindParam(':otp', $otp, PDO::PARAM_STR);
                $query->bindParam(':otp_expiry', $otp_expiry, PDO::PARAM_STR);

                if ($query->execute()) {
                    // Send OTP via email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'nrsanjaykumar2004@gmail.com'; // Replace with your email address
                        $mail->Password   = 'tpbdgknbjuhtkors'; // Replace with your email password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port       = 465;

                        // Recipients
                        $mail->setFrom('nrsanjaykumar2004@gmail.com', 'Sanjaykumar'); // Replace with your domain
                        $mail->addAddress($email, $username);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Registration';
                        $mail->Body    = "Thank you for registering! Your OTP code is: <b>" . htmlspecialchars($otp) . "</b>. Please use this to verify your account within 10 minutes.";
                        $mail->AltBody = "Thank you for registering! Your OTP code is: " . htmlspecialchars($otp) . ". Please use this to verify your account within 10 minutes.";

                        $mail->send();
                        // Store email in session for OTP verification page
                        session_start();
                        $_SESSION['reg_email'] = $email;
                        header("Location: verify-otp.php");
                        exit();

                    } catch (Exception $e) {
                        $message = "Error sending OTP email. Please try again later. Mailer Error: {$mail->ErrorInfo}";
                        $messageClass = "error";
                        // If email fails, you might want to delete the user record or handle it accordingly
                        $deleteQuery = $dbh->prepare("DELETE FROM users WHERE email = :email");
                        $deleteQuery->bindParam(':email', $email, PDO::PARAM_STR);
                        $deleteQuery->execute();
                    }
                } else {
                    $message = "Something went wrong during registration. Please try again.";
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
    <title>User Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;}

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2f5;
        }

        .register-container {
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

        .register-form {
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

        .success {
            color: #2ecc71;
            font-size: 14px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }

            .register-form, .illustration {
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
    <div class="register-container">
        <div class="register-form">
            <h1>User Registration</h1>

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
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
        <div class="illustration">
            <svg class="nurse-image" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="35" r="20" fill="white"/>
                <rect x="30" y="55" width="40" height="45" fill="white"/>
                <rect x="45" y="15" width="10" height="10" fill="#2b8379"/>
            </svg>
        </div>
    </div>
</body>
</html>