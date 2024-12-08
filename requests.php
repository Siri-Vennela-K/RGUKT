<?php include 'dbconnection.php'; ?>
<?php
$userRole = isset($_SESSION['Role']) ? $_SESSION['Role'] : '';
if ($userRole == '') {
    header("Location: logout.php");
    exit();
}

$Branch = isset($_SESSION['Branch']) ? $_SESSION['Branch'] : '';
$InchargeFor = isset($_SESSION['InchargeFor']) ? $_SESSION['InchargeFor'] : '';
$FacultyType = isset($_SESSION['FacultyType']) ? $_SESSION['FacultyType'] : '';

$sql = "SELECT o.StudentId, s.FullName, s.Branch, s.Stream, DATE_FORMAT(InTime, '%Y-%m-%d %H:%i') AS start, 
            DATE_FORMAT(OutTime, '%Y-%m-%d %H:%i') AS end, o.Reason, o.RequestStatus 
            FROM OutPassRequests o
            JOIN StudentMaster s ON o.StudentId = s.StudentId Where s.IsActive=1";

if(!empty($Branch)){
    $sql = $sql." and s.Branch = '$Branch'";
}

if(!empty($InchargeFor)){
   $sql = $sql." and s.Gender = '$InchargeFor'";
}

if(!empty($FacultyType)){
    $sql = $sql." and o.CurrentLevel = '$FacultyType'";
}

$sql = $sql." order by o.RequestID desc";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Requests History</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png" />

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/toastr/toastr.min.css" />
</head>

<body>
    <?php include 'Body.php'; ?>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">

            <!-- Export Datatable start -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Requests History</h4>
                </div>
                <div class="pb-20">
                    <table class="table hover data-table-export">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Student Id</th>
                                <th>Name</th>
                                <th>Stream</th>
                                <th>Reason</th>
                                <th>Leaving Date & Time</th>
                                <th>Return Date & Time</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                                $sno = 1;
                                while ($row = $result->fetch_assoc()) {
                                    $StudentId = $row['StudentId'];
                                    $FullName = $row['FullName'];
                                    $Branch = $row['Branch'];
                                    $Stream = $row['Stream'];
                                    $start = $row['start'];
                                    $end = $row['end'];
                                    $Reason = $row['Reason'];
                                    $status = $row['RequestStatus'];
                                    $statusBadge = ""; 
                                
                                    // Define badge based on status (approved, pending, rejected)
                                    switch ($row['RequestStatus']) {
                                        case 'Approved':
                                            $statusBadge = '<span class="badge badge-pill badge-success">Approved</span>';
                                            break;
                                        case 'Rejected':
                                            $statusBadge = '<span class="badge badge-pill badge-danger">Rejected</span>';
                                            break;
                                        default:
                                            $statusBadge = '<span class="badge badge-pill badge-primary">Pending</span>';
                                            break;
                                    }
                                
                                    echo "<tr>
                                                <td>$sno</td>
                                                <td>{$StudentId}</td>
                                                <td>{$FullName}</td>
                                                <td>{$Stream}</td>
                                                <td>{$Reason}</td>
                                                <td>{$start}</td>
                                                <td>{$end}</td>
                                                <td>{$statusBadge}</td>
                                            </tr>";
                                            $sno++;
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Export Datatable End -->
        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>

    <!-- buttons for Export datatable -->
    <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
    <!-- Datatable Setting js -->
    <script src="vendors/scripts/datatable-setting.js"></script>
	<script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
</body>

</html>