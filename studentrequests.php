<?php include 'dbconnection.php'; ?>
<?php
$userRole = isset($_SESSION['Role']) ? $_SESSION['Role'] : '';
if ($userRole == '') {
    header("Location: logout.php");
    exit();
}

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$sql = "SELECT * FROM StudentMaster WHERE Email = '$email' AND IsActive = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $_SESSION['StudentID'] = $student['StudentID'];
    $studentid = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : '';

    $sql = "SELECT RequestID, Reason AS title, Description AS description, RequestStatus AS status,
    DATE_FORMAT(InTime, '%Y-%m-%d %H:%i') AS start, 
    DATE_FORMAT(OutTime, '%Y-%m-%d %H:%i') AS end,  
    ColorCode AS className, IconName AS icon 
    FROM OutpassRequests WHERE StudentID = '$studentid' order by RequestID";

    $result = mysqli_query($conn, $sql);
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateRequest'])) {
    // Fetch form data
    $requestId = $_POST['requestId'];
    $reason = $_POST['reason'];
    $description = $_POST['description'];
    $fromdate = $_POST['fromdate'];
    $returndate = $_POST['returndate'];
    $color = $_POST['color'];
    $icon = $_POST['icon'];

    // SQL update query
    $updateQuery = "UPDATE OutpassRequests 
                    SET Reason = ?, Description = ?, InTime = ?, OutTime = ?, ColorCode = ?, IconName = ?
                    WHERE RequestID = ?";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssi", $reason, $description, $fromdate, $returndate, $color, $icon, $requestId);

    if ($stmt->execute()) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('success','Updated Successfully'); });</script>";
    } else {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Failed to update. Try Again..!'); });</script>";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>My Requests</title>

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
        <div class="pd-ltr-20 xs-pd-20-10">
            <!-- Export Datatable start -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Requests History</h4>
                </div>
                <div class="pb-20">
                    <table class="table hover data-table-export nowrap">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Reason</th>
                                <!-- <th>Description</th> -->
                                <th>Leaving Date & Time</th>
                                <th>Return Date & Time</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $sno = 1; // Serial number
                                while ($row = $result->fetch_assoc()) {
                                    $requestId = $row['RequestID'];
                                    $reason = $row['title'];
                                    $description = $row['description'];
                                    $start = $row['start'];
                                    $end = $row['end'];
                                    $status = $row['status'];
                                    $statusBadge = ""; // Badge class and text
                                
                                    // Define badge based on status (approved, pending, rejected)
                                    switch ($row['status']) {
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
    <td>{$sno}</td>
    <td>{$reason}</td>
    <td>{$start}</td>
    <td>{$end}</td>
    <td>{$statusBadge}</td>
    <td>
        <div class='table-actions'>
            <a href='#' data-toggle='modal' data-target='#modal-view-event-add' class='edit-btn' data-id='{$requestId}' 
                data-reason='{$reason}' data-description='{$description}' data-start='{$start}' data-end='{$end}' 
                data-color='{$row['className']}' data-icon='{$row['icon']}''>
                <i class='icon-copy dw dw-edit2'></i>
            </a>
            <a href='#' class='delete-request-btn' data-id='{$requestId}' data-color='#e95959'>
                <span class='icon-copy ti-trash'></span>
            </a>";

if ($statusBadge == 'Rejected' && $currentLevel != 'Director') {
    echo "<a href='#' class='send-request-btn' data-id='{$requestId}' data-color='#265ed7'>
                <span class='icon-copy ti-loop'></span>
          </a>";
}

echo "  </div>
    </td>
</tr>";

                                    $sno++;
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Export Datatable End -->

            <!-- Modal start -->
            <div id="modal-view-event-add" class="modal modal-top fade calendar-modal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="add-event" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <input type="hidden" name="requestId" id="requestId">
                            <div class="modal-body">
                                <h4 class="text-blue h4 mb-10">Edit Outpass Request Detail</h4>
                                <div class="form-group">
                                    <label>Reason*</label>
                                    <input type="text" class="form-control" name="reason" id="reason">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>From Date & Time*</label>
                                            <input type="text" class="datetimepicker form-control" name="fromdate" id="fromdate">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Return Date & Time*</label>
                                            <input type="text" class="datetimepicker form-control" name="returndate" id="returndate">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Reason Description*</label>
                                    <textarea class="form-control h-25" name="description" id="description"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Reason Color</label>
                                            <select class="form-control" name="color" id="color">
                                                <option value="fc-bg-default">fc-bg-default</option>
                                                <option value="fc-bg-blue">fc-bg-blue</option>
                                                <option value="fc-bg-lightgreen">fc-bg-lightgreen</option>
                                                <option value="fc-bg-pinkred">fc-bg-pinkred</option>
                                                <option value="fc-bg-deepskyblue">fc-bg-deepskyblue</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Reason Icon</label>
                                            <select class="form-control" name="icon" id="icon">
                                                <option value="circle">circle</option>
                                                <option value="cog">cog</option>
                                                <option value="group">group</option>
                                                <option value="suitcase">suitcase</option>
                                                <option value="calendar">calendar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input class="btn btn-primary" type="submit" value="Save" name="updateRequest">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal end -->

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
    <script src="src/plugins/jQuery-Validation/jquery-validation.min.js"></script>
    <script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
    <script>
        $(document).ready(function () {
        
            $(document).on('click', '.edit-btn', function () {
    // Fetch data attributes from the clicked edit button
    const requestId = $(this).data('id');
    const reason = $(this).data('reason');
    const description = $(this).data('description');

    // Fetch date-time values from the data attributes
    const start = $(this).data('start');
    const end = $(this).data('end');
    const color = $(this).data('color');
    const icon = $(this).data('icon');

    // Function to format date and time
    function formatDateTime(dateTime) {
        const date = new Date(dateTime);
        const day = String(date.getDate()).padStart(2, '0');
        const month = date.toLocaleString('default', { month: 'long' }); // e.g., November
        const year = date.getFullYear();

        // Format hours and minutes
        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12 || 12; // Convert to 12-hour format

        return `${day} ${month} ${year} ${hours}:${minutes} ${ampm}`;
    }

    // Format start and end date-time values
    const formattedStart = formatDateTime(start);
    const formattedEnd = formatDateTime(end);

    // Set form values in the modal
    $('#requestId').val(requestId);
    $('input[name="reason"]').val(reason);
    $('textarea[name="description"]').val(description);
    $('input[name="fromdate"]').val(formattedStart);
    $('input[name="returndate"]').val(formattedEnd);
    $('select[name="color"]').val(color);
    $('select[name="icon"]').val(icon);
});

$(document).on('click', '.send-request-btn', function (e) {
    e.preventDefault();
    const requestId = $(this).data('id');

    // AJAX call for sending request
    $.ajax({
        url: 'sendRequest.php',
        type: 'POST',
        data: { id: requestId, page: 'StudentRequests' },
        success: function (response) {
            if (response.success) {
                AlertMessage('success','Request escalated successfully!');
                // Optionally, reload the table or update the row status dynamically
                location.reload();
            } else {
                AlertMessage('error','Failed to escalate request: ' + response.message);
            }
        },
        error: function () {
            AlertMessage('error','An error occurred while escalating the request.');
        }
    });
});

            $('#add-event').validate({
                errorPlacement: function (error, element) {
                    error.insertBefore(element.closest('.form-group'));
                },
                rules: {
                    reason: {
                        required: true
                    },
                    fromdate: {
                        required: true
                    },
                    returndate: {
                        required: true
                    },
                    description: {
                        required: true
                    }
                },
                messages: {
                    reason: {
                        required: "Please enter a reason"
                    },
                    fromdate: {
                        required: "Please select from date & time"
                    },
                    returndate: {
                        required: "Please select return date & time"
                    },
                    description: {
                        required: "Please provide a description"
                    }
                },
                submitHandler: function (form) {
                    form.submit();
                },
                invalidHandler: function (event, validator) {
                    event.preventDefault();
                }
            });

            // Prevent modal from closing on validation error
            $('input[type="submit"]').on('click', function (e) {
                if (!$('#add-event').valid()) {
                    e.preventDefault(); // Prevent submission
                }
            });
        });

    </script>


</body>

</html>