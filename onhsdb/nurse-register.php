<?php
// nurse_registration_page.php
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes/config.php';

if (isset($_POST['submit'])) {
    // 1.  Handle File Upload
    $uploadDir = "uploads/"; // Create this directory
    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $profilePicName = $_FILES['ProfilePic']['name'];
    $profilePicTmpName = $_FILES['ProfilePic']['tmp_name'];
    $profilePicPath = $uploadDir . $profilePicName;  //store this in db
     $profilePicExtension = strtolower(pathinfo($profilePicName, PATHINFO_EXTENSION));

     // Validate file type
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    if (!in_array($profilePicExtension, $allowedExtensions)) {
        echo "Error: Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        exit(); // Stop processing
    }
     // Validate file size (2MB limit)
    if ($_FILES['ProfilePic']['size'] > 2 * 1024 * 1024) {
        echo "Error: File size too large. Maximum size is 2MB.";
        exit();
    }

    if (move_uploaded_file($profilePicTmpName, $profilePicPath)) {
         // File upload successful
    } else {
        echo "Error uploading file.";
        exit();
    }

    // 2.  Get Form Data
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $mobileNo = $_POST['mobileNo'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $languages = isset($_POST['languages']) ? implode(",", $_POST['languages']) : '';
    $NursingExp = $_POST['NursingExp'];
    $NursingCertificate = $_POST['NursingCertificate'];
    $EducationDescription = $_POST['EducationDescription'];
    // $ProfilePic = $profilePicPath;  //already set

    // 3. Insert into nurse_requests
    try {
        $sql = "INSERT INTO nurse_requests (name, gender, email, password, mobileNo, address, city, state, languages, NursingExp, NursingCertificate, EducationDescription, ProfilePic)
                VALUES (:name, :gender, :email, :password, :mobileNo, :address, :city, :state, :languages, :NursingExp, :NursingCertificate, :EducationDescription, :ProfilePic)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':mobileNo', $mobileNo, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':city', $city, PDO::PARAM_STR);
        $stmt->bindParam(':state', $state, PDO::PARAM_STR);
        $stmt->bindParam(':languages', $languages, PDO::PARAM_STR);
        $stmt->bindParam(':NursingExp', $NursingExp, PDO::PARAM_STR);
        $stmt->bindParam(':NursingCertificate', $NursingCertificate, PDO::PARAM_STR);
        $stmt->bindParam(':EducationDescription', $EducationDescription, PDO::PARAM_STR);
        $stmt->bindParam(':ProfilePic', $profilePicPath, PDO::PARAM_STR); // Use the path
        $stmt->execute();

        // Redirect to a "thank you" page or back to the registration page with a message
        header("Location: register.php?message=Registration request submitted. Admin will review.");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); //  Handle this more gracefully (e.g., show user-friendly message)
    }
} else {
    //  The form was not submitted
    //header("Location: nurse_registration_page.php"); // Removed this line.  The page should display the form.
    //exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nurse Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .registration-container {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-section {
            flex: 1;
            padding: 40px;
        }
        .illustration-section {
            flex: 1;
            background: #eaf0f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .illustration-section img {
            width: 80%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group label {
            font-weight: 600;
        }
        .btn-primary {
            width: 100%;
        }
        .back-link {
            margin-top: 15px;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="registration-container">
    <div class="form-section">
        <h2>Nurse Registration</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Gender</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="Male" required>
                    <label class="form-check-label">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="Female" required>
                    <label class="form-check-label">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="Other" required>
                    <label class="form-check-label">Other</label>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobileNo" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required>
            </div>

            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" required>
            </div>

            <div class="form-group">
                <label>State</label>
                <input type="text" name="state" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Languages Known</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="English">
                    <label class="form-check-label">English</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="Hindi">
                    <label class="form-check-label">Hindi</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="Tamil">
                    <label class="form-check-label">Tamil</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="Kannada">
                    <label class="form-check-label">Kannada</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="Telugu">
                    <label class="form-check-label">Telugu</label>
                </div>
            </div>

            <div class="form-group">
                <label>Nursing Experience</label>
                <input type="text" name="NursingExp" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nursing Certificate</label>
                <input type="text" name="NursingCertificate" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Education Description</label>
                <textarea name="EducationDescription" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="ProfilePic" accept="image/*" class="form-control" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Register Nurse</button>

            <a class="back-link" href="register.php">← Back to Registration Options</a>
        </form>
    </div>

    <div class="illustration-section">
        <img src="images/register.png" alt="Nurse Registration Illustration">
    </div>
</div>

</body>
</html>
