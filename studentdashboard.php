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

    $sql = "SELECT RequestID, Reason AS title, Description AS description, 
    DATE_FORMAT(InTime, '%Y-%m-%dT%H:%i:%s') AS start, 
    DATE_FORMAT(OutTime, '%Y-%m-%dT%H:%i:%s') AS end,  
    ColorCode AS className, IconName AS icon 
    FROM OutpassRequests WHERE StudentID = '$studentid'";

    $result = mysqli_query($conn, $sql);
    $events_json = '[]';
    $events = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = [
                "id" => $row["RequestID"],
                "title" => $row["title"],
                "description" => $row["description"],
                "start" => $row["start"],
                "end" => $row["end"],
                "className" => $row["className"],
                "icon" => $row["icon"],
            ];
        }
    }

    //header('Content-Type: application/json');
    $events_json = json_encode($events);
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Kolkata');

    $reason = trim(mysqli_real_escape_string($conn, $_POST['reason']));
    $fromdate = trim(mysqli_real_escape_string($conn, $_POST['fromdate']));
    $returndate = trim(mysqli_real_escape_string($conn, $_POST['returndate']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $ecolor = trim(mysqli_real_escape_string($conn, $_POST['ecolor']));
    $eicon = trim(mysqli_real_escape_string($conn, $_POST['eicon']));

    if (empty($reason) || empty($fromdate) || empty($returndate) || empty($description) || empty($ecolor) || empty($eicon)) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','All fields are mandatory..!'); });</script>";
    } else {
        $datetime = DateTime::createFromFormat('d F Y h:i a', $fromdate);
        $datetime1 = DateTime::createFromFormat('d F Y h:i a', $returndate);

        if ($datetime && $datetime1) {
            $from_datetime = $datetime->format('Y-m-d H:i:s');
            $return_datetime = $datetime1->format('Y-m-d H:i:s');
        } else {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Invalid Date Format'); });</script>";
        }

        if ($datetime < $datetime1) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','From datetime should be less than return datetime'); });</script>";
        }

        $studentid = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : '';

        $stmt = $conn->prepare("INSERT INTO OutpassRequests (StudentID, Reason, Description, InTime, OutTime, ColorCode, IconName) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssss", $studentid, $reason, $description, $from_datetime, $return_datetime, $ecolor, $eicon);

        if ($stmt->execute()) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('success','Request submitted successfully!'); });</script>";
        } else {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','" . mysqli_error($conn) . "'); });</script>";
        }

        $stmt->close();
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Student Dashboard</title>

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
    <link rel="stylesheet" type="text/css" href="src/plugins/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/sweetalert2/sweetalert2.css" />
	<link rel="stylesheet" type="text/css" href="src/plugins/toastr/toastr.min.css" />
</head>

<body>
    <?php include 'Body.php'; ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>Student Dashboard</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pd-20 card-box mb-30">
                    <div class="calendar-wrap">
                        <div id="calendar"></div>
                    </div>
                    <!-- calendar modal -->
                    <div id="modal-view-event" class="modal modal-top fade calendar-modal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <h4 class="h4">
                                        <span class="event-icon weight-400 mr-3"></span><span
                                            class="event-title"></span>
                                    </h4>
                                    <div class="event-body"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="modal-view-event-add" class="modal modal-top fade calendar-modal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="add-event" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"
                                    method="POST">
                                    <div class="modal-body">
                                        <h4 class="text-blue h4 mb-10">Edit Outpass Request Detail</h4>

                                        <div class="form-group">
                                            <label>Reason*</label>
                                            <input type="text" class="form-control" name="reason" maxlength="50" autocomplete="off" />
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>From Date & Time*</label>
                                                    <input type="text" class="datetimepicker form-control"
                                                        name="fromdate" id="fromdate" autocomplete="off" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Return Date & Time*</label>
                                                    <input type="text" class="datetimepicker form-control"
                                                        name="returndate" id="returndate" autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Reason Description*</label>
                                            <textarea class="form-control h-25" name="description" maxlength="2000" autocomplete="off"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Reason Color*</label>
                                                    <select class="form-control" name="ecolor">
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
                                                    <label>Reason Icon*</label>
                                                    <select class="form-control" name="eicon">
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
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-primary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/fullcalendar/fullcalendar.min.js"></script>
    <!-- <script src="vendors/scripts/calendar-setting.js"></script> -->
    <script src="src/plugins/sweetalert2/sweetalert2.all.js"></script>
    <script src="src/plugins/sweetalert2/sweet-alert.init.js"></script>
    <script src="src/plugins/jQuery-Validation/jquery-validation.min.js"></script>
    <script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
    <script>
        $(document).ready(function () {
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
            
        });
        
        (function () {
	"use strict";
	jQuery(function () {
    // page is ready
    jQuery("#calendar").fullCalendar({
        themeSystem: "bootstrap4",
        // emphasizes business hours
        businessHours: false,
        defaultView: "month",
        // event dragging & resizing
        editable: true,
        // header
        header: {
            left: "title",
            center: "month,agendaWeek,agendaDay",
            right: "today prev,next",
        },
        events: <?php echo $events_json ?? '[]'; ?>,
        dayClick: function (date) {
            jQuery("#modal-view-event-add").modal();
        },
        eventClick: function (event, jsEvent, view) {
            jQuery(".event-icon").html("<i class='fa fa-" + event.icon + "'></i>");
            jQuery(".event-title").html(event.title);
            jQuery(".event-body").html(event.description);
            jQuery("#modal-view-event").modal();
        },
    });

});

})(jQuery);

    </script>
</body>

</html>