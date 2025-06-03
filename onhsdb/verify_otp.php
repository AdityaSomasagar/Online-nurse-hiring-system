<?php
session_start();
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes\config.php';

if (!isset($_SESSION['reg_email'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['reg_email'];
$message = '';
$messageClass = '';

if (isset($_POST['verify_otp'])) {
    $otp_entered = trim($_POST['otp']); // Remove spaces

    if (empty($otp_entered)) {
        $message = "Please enter the OTP.";
        $messageClass = "error";
    } else {
        try {
            // Get OTP and expiry from DB
            $query = $dbh->prepare("SELECT otp_code, otp_expiry FROM users WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $storedOtp = strval($row['otp_code']);
                $otpExpiry = strtotime($row['otp_expiry']);

                if ($otp_entered === $storedOtp && $otpExpiry > time()) {
                    // OTP is valid and not expired
                    $updateQuery = $dbh->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = :email");
                    $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
                    $updateQuery->execute();

                    $message = "✅ Your account has been successfully verified! You can now <a href='login.php'>login</a>.";
                    $messageClass = "success";
                    unset($_SESSION['reg_email']);
                } elseif ($otpExpiry <= time()) {
                    $message = "❌ OTP has expired. Please request a new one.";
                    $messageClass = "error";
                } else {
                    $message = "❌ Invalid OTP. Please try again.";
                    $messageClass = "error";
                }
            } else {
                $message = "❌ Email not found. Please register again.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again later.";
            $messageClass = "error";
            error_log("OTP verification error: " . $e->getMessage());
        }
    }
}

if (isset($_POST['resend_otp'])) {
    $otp = rand(100000, 999999);
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    try {
        // Update new OTP in database
        $updateQuery = $dbh->prepare("UPDATE users SET otp_code = :otp, otp_expiry = :otp_expiry WHERE email = :email");
        $updateQuery->bindParam(':otp', $otp);
        $updateQuery->bindParam(':otp_expiry', $otp_expiry);
        $updateQuery->bindParam(':email', $email);
        $updateQuery->execute();

        // Send OTP via email
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        require 'vendor/autoload.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nrsanjaykumar2004@gmail.com';
        $mail->Password   = 'tpbdgknbjuhtkors'; // Use app-specific password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('nrsanjaykumar2004@gmail.com', 'Sanjaykumar');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Resend OTP - Online Nurse Hiring';
        $mail->Body    = "Here is your new OTP: <b>$otp</b>. It will expire in 10 minutes.";

        $mail->send();

        $message = "✅ A new OTP has been sent to your email.";
        $messageClass = "success";

    } catch (Exception $e) {
        $message = "❌ Failed to resend OTP. Please try again later.";
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .otp-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #2b8379;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        button:hover {
            background-color: #3ca399;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .success {
            color: #2ecc71;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="otp-container">
    <h2>Verify Your Email</h2>

    <?php if ($message): ?>
        <div class="<?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="otp">Enter the OTP sent to your email</label>
            <input type="text" name="otp" id="otp" required>
        </div>
        <button type="submit" name="verify_otp">Verify OTP</button>
    </form>

    <form method="POST">
        <button type="submit" name="resend_otp">Resend OTP</button>
    </form>
</div>
</body>
</html>