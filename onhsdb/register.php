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
        $messageClass = "error-message";
    } else {
        try {
            // Check if email or phone already exists
            $query = $dbh->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $message = "Email or phone number already registered.";
                $messageClass = "error-message";
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
                        $mail->setFrom('nrsanjaykumar2004@gmail.com', 'SanjayKumar'); // Replace with your domain
                        $mail->addAddress( $email,$username);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Registration';
                        $mail->Body    = "Thank you for registering! Your OTP code is: <b>" . htmlspecialchars($otp) . "</b>. Please use this to verify your account within 10 minutes.";
                        $mail->AltBody = "Thank you for registering! Your OTP code is: " . htmlspecialchars($otp) . ". Please use this to verify your account within 10 minutes.";

                        $mail->send();
                        // Store email in session for OTP verification page
                        session_start();
                        $_SESSION['reg_email'] = $email;
                        header("Location: verify_otp.php");
                        exit();

                    } catch (Exception $e) {
                        $message = "Error sending OTP email. Please try again later. Mailer Error: {$mail->ErrorInfo}";
                        $messageClass = "error-message";
                        // If email fails, you might want to delete the user record or handle it accordingly
                        $deleteQuery = $dbh->prepare("DELETE FROM users WHERE email = :email");
                        $deleteQuery->bindParam(':email', $email, PDO::PARAM_STR);
                        $deleteQuery->execute();
                    }
                } else {
                    $message = "Something went wrong during registration. Please try again.";
                    $messageClass = "error-message";
                }
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again later.";
            $messageClass = "error-message";
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8; /* Light grey background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .registration-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            width: 80%;
            max-width: 900px;
            overflow: hidden;
        }

        .form-section {
            padding: 40px;
            width: 60%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .illustration-section {
            background-color: #007bff; /* Blue accent color */
            color: white;
            padding: 40px;
            width: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #007bff, #66aaff); /* Gradient background */
        }

        .illustration-image {
            max-width: 100%;
            height: auto;
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.2em;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
            font-size: 1em;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            margin-bottom: 10px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.2);
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .success-message {
            color: #28a745;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .login-link {
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
            text-align: center;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .registration-container {
                flex-direction: column;
                width: 95%;
            }

            .form-section, .illustration-section {
                width: 100%;
                padding: 30px;
            }

            .illustration-section {
                order: -1; /* Move illustration to the top on smaller screens */
                min-height: 200px;
            }

            h2 {
                font-size: 2em;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
<?php
    $userType = $_POST['user_type'] ?? null;
    ?>
    <div class="registration-container">
        <?php if (!$userType): ?>
            <!-- Selection Panel -->
            <div class="form-section" style="width: 100%; align-items: center;">
                <h2>Choose Registration Type</h2>
                <form method="POST" action="">
                    <button type="submit" name="user_type" value="user" style="margin-bottom: 20px;">Patient Register</button>
                </form>
                <form method="GET" action="nurse-register.php">
                    <button type="submit">Nurse Register</button>
                </form>
            </div>
        <?php else: ?>
            <!-- User Registration Form -->
            <div class="form-section">
                <h2>Patient Registration</h2>

                <?php if ($message): ?>
                    <div class="<?php echo $messageClass; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="user_type" value="user">

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
                <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        <?php endif; ?>

        <!-- Illustration -->
        <div class="illustration-section">
            <img src="images/register.png" alt="Registration Illustration" class="illustration-image">
        </div>
    </div>
</body>
</html>