<?php  
session_start();
error_reporting(0);
include('includes/config.php');

if (isset($_POST['submit'])) {
    $nbid = $_GET['bookid'];
    $contactname = $_POST['contactname'];
    $contphonenum = $_POST['contphonenum'];
    $contemail = $_POST['contemail'];
    $fromdate = $_POST['fromdate'];
    $todate = $_POST['todate'];

    // Time duration handling
    $timeduration = isset($_POST['timeduration']) ? implode(", ", $_POST['timeduration']) : "No time slot selected";

    // Patient description combining checkboxes and textarea
    $selected_issues = isset($_POST['patient_issues']) ? implode(", ", $_POST['patient_issues']) : "";
    $other_details = trim($_POST['other_patientdesc']);
    $patientdesc = $selected_issues . ($other_details ? " | Additional: " . $other_details : "");

    $bookingid = mt_rand(100000000, 999999999);

    // Check for overlapping bookings
    $ret = mysqli_query($con, "SELECT * FROM tblbooking WHERE 
        ('$fromdate' BETWEEN DATE(FromDate) AND DATE(ToDate) 
        OR '$todate' BETWEEN DATE(FromDate) AND DATE(ToDate) 
        OR DATE(FromDate) BETWEEN '$fromdate' AND '$todate') 
        AND NurseID='$nbid' AND Status='Accepted'");

    if (mysqli_num_rows($ret) == 0) {
        $query = mysqli_query($con, "INSERT INTO tblbooking(BookingID, NurseID, ContactName, ContactNumber, ContactEmail, FromDate, ToDate, TimeDuration, PatientDescrition) 
        VALUES ('$bookingid', '$nbid', '$contactname', '$contphonenum', '$contemail', '$fromdate', '$todate', '$timeduration', '$patientdesc')");
        
        if ($query) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Sent!',
                        text: 'Your booking request has been sent. We will contact you soon.',
                    }).then(() => {
                        window.location.href = 'team.php';
                    });
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                });
            </script>";
        }
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Unavailable', 'This nurse is not available for these dates.', 'warning');
            });
        </script>";
    }
}

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nurse Booking | Online Nurse Hiring System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/fontawesome-all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .card-style {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .btn_apt {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn_apt:hover {
            background-color: #0056b3;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
        }
        textarea.form-control {
            min-height: 100px;
        }
        .form-check-inline {
            margin-right: 15px;
        }
        .form-check-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include_once("includes/navbar.php"); ?>
    <div class="inner-banner" id="home"></div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb container">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nurse Booking</li>
        </ol>
    </nav>

    <section class="team-agile py-5">
        <div class="container">
            <div class="title-section text-center pb-4">
                <h4>world of medicine</h4>
                <h3 class="w3ls-title text-capitalize">Nurse Booking</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-style">
                        <h4 class="text-center mb-4">Book a Nurse</h4>
                        <form method="post">
                            <div class="form-group">
                                <label>Contact Name</label>
                                <input type="text" class="form-control" name="contactname" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="contphonenum" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Email Address</label>
                                <input type="email" class="form-control" name="contemail" required>
                            </div>
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" class="form-control" name="fromdate" id="fromdate" min="<?php echo $today; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" class="form-control" name="todate" id="todate" min="<?php echo $today; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Time Duration</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="timeduration[]" value="9:00 AM - 12:00 PM">
                                    <label class="form-check-label">9:00 AM - 12:00 PM</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="timeduration[]" value="12:00 PM - 3:00 PM">
                                    <label class="form-check-label">12:00 PM - 3:00 PM</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="timeduration[]" value="3:00 PM - 6:00 PM">
                                    <label class="form-check-label">3:00 PM - 6:00 PM</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="timeduration[]" value="6:00 PM - 9:00 PM">
                                    <label class="form-check-label">6:00 PM - 9:00 PM</label>
                                </div>
                            </div>

                            <!-- Updated Patient Description Section -->
                            <div class="form-group">
                                <label>Patient Issues</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="patient_issues[]" value="Fever">
                                    <label class="form-check-label">Fever</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="patient_issues[]" value="Throat Pain">
                                    <label class="form-check-label">Throat Pain</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="patient_issues[]" value="Headache">
                                    <label class="form-check-label">Headache</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="patient_issues[]" value="Body Pain">
                                    <label class="form-check-label">Body Pain</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="patient_issues[]" value="Cough">
                                    <label class="form-check-label">Cough</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Other Details</label>
                                <textarea class="form-control" name="other_patientdesc" placeholder="Add any extra information..."></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn_apt" name="submit">Request Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once("includes/footer.php"); ?>

    <script src="js/jquery-2.2.3.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        document.getElementById('fromdate').addEventListener('change', function () {
            const fromDate = this.value;
            const toDate = document.getElementById('todate');
            toDate.min = fromDate;
            if (toDate.value < fromDate) {
                toDate.value = fromDate;
            }
        });
    </script>
</body>
</html>
