<?php session_start();
error_reporting(0);
// Database Connection
include('includes/config.php');
//Validating Session
if(strlen($_SESSION['aid'])==0)
  { 
header('location:index.php');
}
else{
  // Process the form if it's submitted
  if(isset($_POST['accept'])) {
    $requestid = intval($_POST['requestid']);
    $status = 'Accepted';
    $query = mysqli_query($con, "update tblbooking set Status='$status' where ID='$requestid'");
    if($query) {
      echo "<script>alert('Request has been accepted');</script>";
      echo "<script>window.location.href='pending-requests.php'</script>";
    } else {
      echo "<script>alert('Something went wrong. Please try again');</script>";
    }
  }
  
  if(isset($_POST['reject'])) {
    $requestid = intval($_POST['requestid']);
    $status = 'Rejected';
    $query = mysqli_query($con, "update tblbooking set Status='$status' where ID='$requestid'");
    if($query) {
      echo "<script>alert('Request has been rejected');</script>";
      echo "<script>window.location.href='pending-requests.php'</script>";
    } else {
      echo "<script>alert('Something went wrong. Please try again');</script>";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Online Nurse Hiring System | Pending Requests</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
<?php include_once("includes/navbar.php");?>
  <!-- /.navbar -->

 <?php include_once("includes/sidebar.php");?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Pending Requests</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Pending Requests</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Pending Requests</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                   <th>S.No</th>
                   <th>Booking Number</th>
                   <th>Contact Name</th>
                   <th>Contact Email</th>
                   <th>Contact Number</th>
                   <th>Status</th>
                   <th>Booking Date</th>
                   <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
<?php
$ret=mysqli_query($con,"select * from tblbooking where Status='' or Status IS NULL");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {
?>
                  <tr>
                    <td><?php echo $cnt;?></td>
                    <td><?php echo $row['BookingID'];?></td>
                    <td><?php echo $row['ContactName'];?></td>
                    <td><?php echo $row['ContactEmail'];?></td>
                    <td><?php echo $row['ContactNumber'];?></td>
                    <td>
                      <?php if($row['Status']=="" || $row['Status']==NULL){ ?>
                        <span class="badge badge-warning">Pending</span>
                      <?php } else { ?>
                        <?php echo $row['Status'];?>
                      <?php } ?>
                    </td>
                    <td>
                      <span class="badge badge-primary"><?php echo $row['BookingDate'];?></span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <form method="post">
                          <input type="hidden" name="requestid" value="<?php echo $row['ID'];?>">
                          <button type="submit" name="accept" class="btn btn-success btn-sm mr-1">
                            <i class="fa fa-check"></i> Accept
                          </button>
                          <button type="submit" name="reject" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Reject
                          </button>
                        </form>
                        <a href="view-request.php?viewid=<?php echo $row['ID'];?>" class="btn btn-info btn-sm ml-1">
                          <i class="fa fa-eye"></i> View
                        </a>
                      </div>
                    </td>
                  </tr>
<?php 
$cnt++;
} ?>
                  </tbody>
                  <tfoot>
                  <tr>
                   <th>S.No</th>
                   <th>Booking Number</th>
                   <th>Contact Name</th>
                   <th>Contact Email</th>
                   <th>Contact Number</th>
                   <th>Status</th>
                   <th>Booking Date</th>
                   <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include_once('includes/footer.php');?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>
<?php } ?>