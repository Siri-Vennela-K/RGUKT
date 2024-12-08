<?php include 'dbconnection.php'; ?>
<?php
$userRole = isset($_SESSION['Role']) ? $_SESSION['Role'] : '';
if ($userRole == '') {
    header("Location: logout.php");
    exit();
}
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$Branch = isset($_SESSION['Branch']) ? $_SESSION['Branch'] : '';
$InchargeFor = isset($_SESSION['InchargeFor']) ? $_SESSION['InchargeFor'] : '';
$FacultyType = isset($_SESSION['FacultyType']) ? $_SESSION['FacultyType'] : '';

$sql = "SELECT o.RequestID, o.StudentId, s.FullName, s.Branch, s.Stream, DATE_FORMAT(InTime, '%Y-%m-%d %H:%i') AS start, 
            DATE_FORMAT(OutTime, '%Y-%m-%d %H:%i') AS end, o.Reason, o.Description 
            FROM OutPassRequests o
            JOIN StudentMaster s ON o.StudentId = s.StudentId 
			Where s.IsActive=1 and o.RequestStatus='Pending'";

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

$countQuery="SELECT SUM(CASE WHEN o.RequestStatus = 'Approved' THEN 1 ELSE 0 END) AS ApprovedCount,
                    SUM(CASE WHEN o.RequestStatus = 'Rejected' THEN 1 ELSE 0 END) AS RejectedCount,
					SUM(CASE WHEN o.RequestStatus = 'Pending' THEN 1 ELSE 0 END) AS PendingCount,
					SUM(CASE WHEN o.RequestID IS NOT NULL THEN 1 ELSE 0 END) AS TotalCount
                    FROM OutPassRequests o
                    JOIN StudentMaster s ON o.StudentId = s.StudentId
                    WHERE s.IsActive = 1 ";

if(!empty($Branch)){
    $countQuery = $countQuery." and s.Branch = '$Branch'";
}

if(!empty($InchargeFor)){
   $countQuery = $countQuery." and s.Gender = '$InchargeFor'";
}

if(!empty($FacultyType)){
    $countQuery = $countQuery." and o.CurrentLevel = '$FacultyType'";
}

$Countresult = mysqli_query($conn, $countQuery);

while ($row = $Countresult->fetch_assoc()) {
	$Approved = $row['ApprovedCount'];
	$Rejected = $row['RejectedCount'];
	$Pending = $row['PendingCount'];
	$Total = $row['TotalCount'];
}

?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the AJAX request
    $timeFrame = $_POST['timeFrame'];
    $startDate = '';
    $endDate = date('Y-m-d'); // Current date

    // Determine the date range based on the dropdown
    switch ($timeFrame) {
        case 'Last Week':
            $startDate = date('Y-m-d', strtotime('-1 week'));
            break;
        case 'Last Month':
            $startDate = date('Y-m-d', strtotime('-1 month'));
            break;
        case 'Last 6 Month':
            $startDate = date('Y-m-d', strtotime('-6 months'));
            break;
        case 'Last 1 year':
            $startDate = date('Y-m-d', strtotime('-1 year'));
            break;
    }

    // Execute the query
    $pdo = $conn->prepare("SELECT SUM(CASE WHEN o.Status = 'Approved' THEN 1 ELSE 0 END) AS ApprovedCount,
                            SUM(CASE WHEN o.Status = 'Rejected' THEN 1 ELSE 0 END) AS RejectedCount
                            FROM OutPassRequests o
                            JOIN StudentMaster s ON o.StudentId = s.StudentId
                            WHERE s.IsActive = 1 AND o.InTime BETWEEN :startDate AND :endDate");

    $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $conditionalcount = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the result as JSON
    echo json_encode($conditionalcount);
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8" />
	<title>Admin Dashboard</title>

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
	<link rel="stylesheet" type="text/css" href="src/plugins/sweetalert2/sweetalert2.css" />
	<link rel="stylesheet" type="text/css" href="src/plugins/toastr/toastr.min.css" />
</head>

<body>
<?php include 'Body.php'; ?>

	<div class="main-container">
		<div class="xs-pd-20-10 pd-ltr-20">
			<div class="title pb-20">
				<h2 class="text-blue h3">Dashboard Overview</h2>
			</div>

			<div class="row pb-10">
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo $Approved;?></div>
								<div class="font-14 text-secondary weight-500">
									Approved
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#00eccf">
									<i class="icon-copy dw dw-tick"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo $Rejected;?></div>
								<div class="font-14 text-secondary weight-500">
									Rejected
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#00eccf">
									<i class="icon-copy dw dw-cancel"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo $Pending;?></div>
								<div class="font-14 text-secondary weight-500">
									Pending Requests
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#ff5b5b">
								<i class="icon-copy dw dw-analytics-11"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">
						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo $Total;?></div>
								<div class="font-14 text-secondary weight-500">
									Total Requests
								</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#ff5b5b">
									<i class="icon-copy dw dw-analytics-3"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Export Datatable start -->
			<div class="card-box mb-30">
				<div class="pd-20">
					<h4 class="text-blue h4">Pending Requests</h4>
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
								<th>Description</th>
                                <th>Leaving Date & Time</th>
                                <th>Return Date & Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                                $sno = 1;
                                while ($row = $result->fetch_assoc()) {
									$RequestID = $row['RequestID'];
                                    $StudentId = $row['StudentId'];
                                    $FullName = $row['FullName'];
                                    $Stream = $row['Stream'];
                                    $start = $row['start'];
                                    $end = $row['end'];
                                    $Reason = $row['Reason'];
                                    $desc = $row['Description'];
                                
                                    echo "<tr>
                                                <td>$sno</td>
                                                <td>{$StudentId}</td>
                                                <td>{$FullName}</td>
                                                <td>{$Stream}</td>
                                                <td>{$Reason}</td>
												<td>{$desc}</td>
                                                <td>{$start}</td>
                                                <td>{$end}</td>
                                                <td>
												<div class='table-actions'>
													<a href='#' data-color='#265ed7' class='approve-btn' data-id='{$RequestID}'><i class='icon-copy dw dw-mail'></i></a>
												</div>
												</td>
                                            </tr>";
                                            $sno++;
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
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
	<script src="src/plugins/sweetalert2/sweetalert2.all.js"></script>
	<script src="src/plugins/sweetalert2/sweet-alert.init.js"></script>
	<script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
<script>
        $(document).ready(function () {
            // Fetch counts on dropdown change
            $('#time-frame').on('change', function () {
                const timeFrame = $(this).val();

                $.ajax({
                    url: '', // Same page
                    type: 'POST',
                    data: { timeFrame },
                    dataType: 'json',
                    success: function (data) {
                        // Update the counts in the UI
                        $('#approved-count').text(data.ApprovedCount);
                        $('#rejected-count').text(data.RejectedCount);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Trigger initial load
            $('#time-frame').trigger('change');
        });
    </script>
</body>

</html>