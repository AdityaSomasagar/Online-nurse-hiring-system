<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database Connection (PDO)
include('C:\xampp\htdocs\Online-Nurse-Hiring-System-PHP\onhsdb\includes\config.php');

// Error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pagination logic
$page_no = isset($_GET['page_no']) && is_numeric($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
$total_records_per_page = 5;
$offset = ($page_no - 1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = 2;

// Fetch total records (PDO)
$stmt = $dbh->prepare("SELECT COUNT(ID) AS total_records FROM tblnurse");
$stmt->execute();
$total_records = $stmt->fetch(PDO::FETCH_ASSOC)['total_records'];
$total_no_of_pages = ceil($total_records / $total_records_per_page);
$second_last = $total_no_of_pages - 1;

// Fetch nurse data (PDO)
$stmt = $dbh->prepare("SELECT * FROM tblnurse LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $total_records_per_page, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Online Nurse Hiring System | Our Team</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="Online Nurse Hiring System, Hire Nurse, Medical Staff, Healthcare Professionals">
    <link href="css/bootstrap.css" type="text/css" rel="stylesheet">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <link href="css/fontawesome-all.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">
    <style>
        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .pagination {
            display: inline-block;
            border-radius: 5px;
            overflow: hidden;
        }

        .pagination li {
            display: inline;
        }

        .pagination li a,
        .pagination li span {
            color: #007bff;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }

        .pagination li a:hover:not(.active) {
            background-color: #f1f1f1;
        }

        .pagination li.active a,
        .pagination li.active span {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination li.disabled a,
        .pagination li.disabled span {
            color: #ccc;
            pointer-events: none;
            background-color: #eee;
            border-color: #ddd;
        }

        /* Team Section Enhancements */
        .team-agile {
            background-color: #f8f9fa; /* Light background for the section */
            padding: 60px 0;
        }

        .title-section h4 {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .w3ls-title {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
            display: inline-block;
        }

        .w3ls-title::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 50px;
            height: 2px;
            background-color: #007bff;
            transform: translateX(-50%);
        }

        .team-agile .row {
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .team-agile .col-md-4 {
            padding: 0;
        }

        .team-agile .col-md-4 img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease-in-out;
        }

        .team-agile .col-md-4:hover img {
            transform: scale(1.05);
        }

        .team-text {
            padding: 30px;
        }

        .team-text h4 {
            color: #007bff;
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .team-text p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .team-text .list-group {
            margin-bottom: 25px;
        }

        .team-text .list-group-item {
            color: #555;
            padding: 8px 0;
            display: flex;
            align-items: center;
        }

        .team-text .list-group-item i {
            color: #007bff;
            margin-right: 10px;
            font-size: 1.1em;
        }

        .btn_apt {
            display: inline-block;
            padding: 12px 25px;
            background-color: #28a745; /* Green for appointment button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn_apt:hover {
            background-color: #218838;
        }

        /* No nurses found message */
        .no-nurses {
            text-align: center;
            padding: 30px;
            font-size: 1.2em;
            color: #777;
        }
    </style>
</head>
<body>

<?php include_once("includes/navbar.php"); ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Our Team</li>
    </ol>
</nav>

<section class="team-agile py-lg-5">
    <div class="container py-sm-5 pt-5 pb-0">
        <div class="title-section text-center pb-lg-5">
            <h4>Dedicated Professionals</h4>
            <h3 class="w3ls-title text-center text-capitalize">Meet Our Medical Staff</h3>
        </div>

        <?php if (count($results) > 0): ?>
            <?php foreach ($results as $result): ?>
                <div class="row mt-5 pb-lg-5">
                    <div class="col-md-4 border p-0 my-auto">
                        <img src="admin/images/<?php echo htmlspecialchars($result['ProfilePic']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($result['Name']); ?>'s Profile" />
                    </div>
                    <div class="col-md-8 team-text mt-md-0 mt-5">
                        <h4><?php echo htmlspecialchars($result['Name']); ?></h4>
                        <p class="my-3"><?php echo htmlspecialchars($result['EducationDescription']); ?></p>
                        <ul class="list-group mb-3">
                            <li class="list-group-item border-0"><i class="fas fa-map-marker-alt mr-3"></i><?php echo htmlspecialchars($result['Address']); ?></li>
                            <li class="list-group-item border-0 py-0"><i class="fas fa-briefcase mr-3"></i>Experience: <?php echo htmlspecialchars($result['NursingExp']); ?> years</li>
                            <li class="list-group-item border-0"><i class="fas fa-hospital mr-3"></i>State: <?php echo htmlspecialchars($result['State']); ?></li>
                            <li class="list-group-item border-0 py-0"><i class="fas fa-city mr-3"></i>City: <?php echo htmlspecialchars($result['City']); ?></li>
                            <li class="list-group-item border-0"><i class="fas fa-language mr-3"></i>Languages Known: <?php echo htmlspecialchars($result['LanguagesKnown']); ?></li>
                            <li class="list-group-item border-0"><i class="fas fa-certificate mr-3"></i>Certificate: <?php echo htmlspecialchars($result['NursingCertificate']); ?></li>
                        </ul>
                        <a href="book-nurse.php?bookid=<?php echo htmlspecialchars($result['ID']); ?>" class="btn_apt">Book Appointment</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-nurses">No nurses found at the moment. Please check back later.</p>
        <?php endif; ?>

        <?php if ($total_no_of_pages > 1): ?>
            <div class="pagination-container">
                <ul class="pagination">
                    <li <?php if ($page_no <= 1) echo "class='disabled'"; ?>>
                        <a <?php if ($page_no > 1) echo "href='?page_no=$previous_page'"; ?>>Previous</a>
                    </li>

                    <?php
                    if ($total_no_of_pages <= 10) {
                        for ($counter = 1; $counter <= $total_no_of_pages; $counter++) {
                            echo $counter == $page_no ?
                                "<li class='active'><a>$counter</a></li>" :
                                "<li><a href='?page_no=$counter'>$counter</a></li>";
                        }
                    } elseif ($total_no_of_pages > 10) {
                        if ($page_no <= 4) {
                            for ($counter = 1; $counter < 8; $counter++) {
                                echo $counter == $page_no ?
                                    "<li class='active'><a>$counter</a></li>" :
                                    "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
                            echo "<li class='disabled'><span>...</span></li><li><a href='?page_no=$second_last'>$second_last</a></li><li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        } elseif ($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                            echo "<li><a href='?page_no=1'>1</a></li><li><a href='?page_no=2'>2</a></li><li class='disabled'><span>...</span></li>";
                            for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {
                                echo $counter == $page_no ?
                                    "<li class='active'><a>$counter</a></li>" :
                                    "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
                            echo "<li class='disabled'><span>...</span></li><li><a href='?page_no=$second_last'>$second_last</a></li><li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        } else {
                            echo "<li><a href='?page_no=1'>1</a></li><li><a href='?page_no=2'>2</a></li><li class='disabled'><span>...</span></li>";
                            for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                                echo $counter == $page_no ?
                                    "<li class='active'><a>$counter</a></li>" :
                                    "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
                        }
                    }
                    ?>

                    <li <?php if ($page_no >= $total_no_of_pages) echo "class='disabled'"; ?>>
                        <a <?php if ($page_no < $total_no_of_pages) echo "href='?page_no=$next_page'"; ?>>Next</a>
                    </li>
                    <?php if ($page_no < $total_no_of_pages) echo "<li><a href='?page_no=$total_no_of_pages'>Last &raquo;</a></li>"; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once("includes/footer.php"); ?>

<script src="js/jquery-2.2.3.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
    $(function () {
        $("#datepicker,#datepicker1").datepicker();
    });
</script>
</body>
</html>