<?php
// Include necessary files and establish database connection (assuming you have a config.php)
require_once 'C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes/config.php';

// Initialize variables to hold data
$totalNurses = 0;
$totalNewRequests = 0;
$totalAcceptedRequests = 0;
$totalRejectedRequests = 0;
$nurseRequests = []; // Array to hold nurse request data
$patients = []; // Array to hold patient data

try {
    // 1. Fetch Dashboard Data (Nurse Counts)
    $nurseCountQuery = $dbh->query("SELECT COUNT(*) FROM nurses");
    $totalNurses = $nurseCountQuery->fetchColumn();

    $newRequestCountQuery = $dbh->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'");
    $totalNewRequests = $newRequestCountQuery->fetchColumn();

    $acceptedRequestCountQuery = $dbh->query("SELECT COUNT(*) FROM requests WHERE status = 'accepted'");
    $totalAcceptedRequests = $acceptedRequestCountQuery->fetchColumn();

    $rejectedRequestCountQuery = $dbh->query("SELECT COUNT(*) FROM requests WHERE status = 'rejected'");
    $totalRejectedRequests = $rejectedRequestCountQuery->fetchColumn();

    // 2. Fetch Nurse Requests with Patient Details
    $requestQuery = $dbh->prepare("SELECT r.id, r.request_date, r.status, p.id as patient_id, p.name as patient_name, p.gender as patient_gender, p.age as patient_age, n.name as nurse_name
                                  FROM requests r
                                  JOIN patients p ON r.patient_id = p.id
                                  LEFT JOIN nurses n ON r.nurse_id = n.id  -- Join to get nurse name, might be null
                                  ORDER BY r.request_date DESC"); // Order by request date
    $requestQuery->execute();
    $nurseRequests = $requestQuery->fetchAll(PDO::FETCH_ASSOC);

     // 3. Fetch Patient Details
    $patientQuery = $dbh->query("SELECT id, name, gender, age, address, phone FROM patients");
    $patients = $patientQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Database Error: " . $e->getMessage(); //  Don't display the actual error to the user in a production environment.  Log it.
    error_log("Database error: " . $e->getMessage());
    $message = "Failed to fetch data. Please try again later.";
    $messageClass = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        /* Custom styles for the Nurse Dashboard */
        body {
            font-family: 'Poppins', sans-serif; /* More modern font */
            background-color: #f0f4f8; /* Light background */
            color: #333;
        }
        .navbar {
            background-color: #3498db; /* Blue navbar */
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            display: flex; /* Use flexbox for alignment */
            align-items: center;
            font-weight: 600; /* Use 600 for semi-bold */
            font-size: 1.4rem;
        }
        .navbar-brand i {
            margin-right: 0.5rem; /* Space between icon and text */
            font-size: 1.6rem; /* Larger icon */
        }
        .nav-link {
            color: white;
            font-weight: 500; /* Medium font weight for nav links */
            margin-right: 1rem;
            transition: color 0.3s ease; /* Smooth transition */
        }
        .nav-link:hover, .nav-link.active {
            color: #f1c40f; /* Highlight on hover/active */
        }
        .container-fluid {
            padding-left: 2rem;
            padding-right: 2rem;
        }
        h1 {
            color: #2c3e50; /* Darker heading */
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 2.2rem;
        }
        .dashboard-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* Smooth transition */
            display: flex; /* Use flexbox for vertical alignment */
            flex-direction: column;
            justify-content: space-between; /* Distribute space between content */
            height: 100%; /* Ensure cards are of equal height */
        }
        .summary-card:hover {
            transform: translateY(-5px); /* Slight lift on hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Increased shadow on hover */
        }
        .summary-card-title {
            font-size: 1.1rem;
            color: #7f8c8d; /* Muted title color */
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .summary-card-value {
            font-size: 2rem; /* Large value */
            color: #2c3e50; /* Darker value color */
            font-weight: 600;
        }
        .summary-card-link {
            color: #3498db;
            font-size: 1rem;
            margin-top: 1rem;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-flex; /* Use inline-flex for alignment */
            align-items: center;
        }
        .summary-card-link:hover {
            color: #217dbb; /* Darker blue on hover */
        }
        .summary-card-link i {
            margin-left: 0.5rem; /* Space for the arrow */
            font-size: 1.2rem; /* Larger arrow */
        }

        .nurse-requests-section, .patient-details-section {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0; /* Add a border */
            padding-bottom: 0.5rem; /* Space between title and border */
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 1rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-bottom: 1rem;
            border-radius: 8px; /* Rounded corners for the table */
            overflow: hidden;  /* hide the overflowed rounded corners */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02); /* Subtle shadow for the table */
        }
        .table thead th {
            background-color: #ecf0f1; /* Light header background */
            color: #2c3e50; /* Darker header text */
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e0e0e0; /* Stronger bottom border */
            font-weight: 600; /* Bold header text */
            font-size: 1.1rem;
        }
        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0; /* Lighter row borders */
            color: #555; /* Medium gray text */
        }
        .table tbody tr:hover {
            background-color: #f5f5f5; /* Slight background change on row hover */
        }
        .table tbody td a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-flex; /* Use inline-flex for alignment */
            align-items: center;
        }
        .table tbody td a:hover {
            color: #217dbb;
        }
        .table tbody td a i {
            margin-left: 0.5rem; /* Space for the arrow */
            font-size: 1rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.2);
        }
        .status-pending {
            background-color: #f39c12; /* Orange */
        }
        .status-accepted {
            background-color: #2ecc71; /* Green */
        }
        .status-rejected {
            background-color: #e74c3c; /* Red */
        }

        .pagination {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }
        .page-item .page-link {
            border: none;
            border-radius: 5px;
            color: #3498db;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .page-item .page-link:hover,
        .page-item.active .page-link {
            background-color: #3498db;
            color: white;
        }
        .page-item.disabled .page-link {
            color: #bdc3c7;
            background-color: transparent;
            cursor: not-allowed;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .dashboard-summary {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on medium screens */
            }
        }
        @media (max-width: 768px) {
            .dashboard-summary {
                grid-template-columns: 1fr; /* 1 column on small screens */
            }
            .navbar-brand {
                font-size: 1.2rem; /* Smaller brand on small screens */
            }
            .nav-link {
                margin-right: 0.5rem; /* Less margin on small screens */
            }
            h1 {
                font-size: 2rem; /* Smaller heading on small screens */
            }
            .section-title {
                font-size: 1.2rem; /* Smaller section title */
            }
            .table thead th, .table tbody td {
                padding: 0.75rem; /* Less padding in table cells on small screens */
                font-size: 0.9rem;
            }
            .status-badge {
                font-size: 0.8rem; /* Smaller badge on small screens */
                padding: 0.25rem 0.75rem;
            }
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem; /* Even smaller brand on very small screens */
            }
            .nav-link {
                font-size: 0.9rem; /* Smaller nav links on very small screens */
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hospital-user"></i> Nurse Konnekt Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Nurses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <h1>Dashboard</h1>

        <div class="dashboard-summary">
            <div class="summary-card">
                <h2 class="summary-card-title">Total Nurses</h2>
                <div class="summary-card-value"><?php echo $totalNurses; ?></div>
                <a href="#" class="summary-card-link">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="summary-card">
                <h2 class="summary-card-title">Total New Requests</h2>
                <div class="summary-card-value"><?php echo $totalNewRequests; ?></div>
                <a href="#" class="summary-card-link">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="summary-card">
                <h2 class="summary-card-title">Total Accepted Requests</h2>
                <div class="summary-card-value"><?php echo $totalAcceptedRequests; ?></div>
                <a href="#" class="summary-card-link">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="summary-card">
                <h2 class="summary-card-title">Total Rejected Requests</h2>
                <div class="summary-card-value"><?php echo $totalRejectedRequests; ?></div>
                <a href="#" class="summary-card-link">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <section class="nurse-requests-section">
            <h2 class="section-title">Nurse Requests</h2>
            <?php if ($message && $messageClass == 'error'): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Patient Name</th>
                            <th>Patient Gender</th>
                            <th>Patient Age</th>
                            <th>Nurse Name</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($nurseRequests) > 0): ?>
                            <?php foreach ($nurseRequests as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td><?php echo $request['patient_name']; ?></td>
                                    <td><?php echo $request['patient_gender']; ?></td>
                                    <td><?php echo $request['patient_age']; ?></td>
                                     <td><?php echo $request['nurse_name'] ?? 'Not Assigned'; ?></td>
                                    <td><?php echo date('F j, Y, g:i a', strtotime($request['request_date'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php
                                            switch ($request['status']) {
                                                case 'pending': echo 'status-pending'; break;
                                                case 'accepted': echo 'status-accepted'; break;
                                                case 'rejected': echo 'status-rejected'; break;
                                                default: echo 'status-pending';
                                            }
                                        ?>"><?php echo ucfirst($request['status']); ?></span>
                                    </td>
                                    <td><a href="#"><i class="fas fa-arrow-right"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No nurse requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
             <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </section>

        <section class="patient-details-section">
            <h2 class="section-title">Patient Details</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($patients) > 0): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?php echo $patient['id']; ?></td>
                                    <td><?php echo $patient['name']; ?></td>
                                    <td><?php echo $patient['gender']; ?></td>
                                    <td><?php echo $patient['age']; ?></td>
                                    <td><?php echo $patient['address']; ?></td>
                                    <td><?php echo $patient['phone']; ?></td>
                                    <td><a href="#"><i class="fas fa-arrow-right"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No patients found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add active class to the current page link
            $('.navbar-nav .nav-link').each(function() {
                if (window.location.href.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>
