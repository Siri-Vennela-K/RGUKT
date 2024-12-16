<?php include 'dbconnection.php'; ?>
<?php
$requestId = $_POST['id'] ?? '';
$pageName = $_POST['page'] ?? '';
$Status = $_POST['status'] ?? '';

if ($requestId && $pageName == "StudentRequests") {
    // Prepare and execute the update query
    $sql = "UPDATE OutpassRequests
            SET CurrentLevel = CASE 
                WHEN CurrentLevel = 'Caretaker' THEN 'Warden'
                WHEN CurrentLevel = 'Warden' THEN 'HOD'
                WHEN CurrentLevel = 'HOD' THEN 'Director'
                ELSE 'Director' 
            END,
            EscalationStatus = 'Yes'
            WHERE RequestID = ? AND RequestStatus = 'Rejected'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to escalate request']);
    }

    $stmt->close();
    $conn->close();
} 
else if ($requestId && $pageName == "AdminDashboard"){
    // Prepare and execute the update query
    $sql = "UPDATE OutpassRequests
    SET RequestStatus = ?
    WHERE RequestID = ? AND RequestStatus = 'Pending'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $Status, $requestId);

    $query="SELECT DATE_FORMAT(InTime, '%Y-%m-%d %H:%i') AS start, 
            DATE_FORMAT(OutTime, '%Y-%m-%d %H:%i') AS end, o.Reason, s.email 
            FROM OutPassRequests o
            JOIN StudentMaster s ON o.StudentId = s.StudentId
            WHERE s.IsActive = 1 and o.RequestID = $requestId";

    $result = mysqli_query($conn, $query);

    while ($row = $result->fetch_assoc()) {
    	$reason = $row['Reason'];
    	$OutTime = $row['start'];
    	$ReturnTime = $row['end'];
        $Toemail = $row['email'];
    }

    if ($stmt->execute()) {
        // Permission request email
        $htmlMessage = "
        <!doctype html>
        <html>
        <head>
            <meta content='text/html; charset=utf-8' http-equiv='Content-Type'/>
            <title>Reset Password Email Template</title>
            <meta name='description' content='Permission Request'>
            <style type='text/css'>
                a {text-decoration: none !important;}
            </style>
        </head>

        <body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
            <table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
                style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: \"Open Sans\", sans-serif;'>
                <tr>
                    <td>
                        <table style='background-color: #f2f3f8; max-width:670px; margin:0 auto;' width='100%' border='0'
                            align='center' cellpadding='0' cellspacing='0'>
                            <tr>
                                <td style='height:10px;'>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>
                                    <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                                        style='max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                                        <tr>
                                            <td style='padding:0 35px;'>
                                                <h1>Outpass Permission Request</h1>
                                                <span
                                                    style='display:inline-block; vertical-align:middle; margin:2px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                                                <p>
                                                  Dear Parent, <br>  <br>
                                                  Your Son/Daughter had requested for an Outpass from the college. <br> <br>
                                                  The timings and reason are as follows: <br> <br>
                                                  
                                                  <b>Reason :</b> '" . $reason . "' <br> <br>
                                                  <b>Out Time :</b> '" . $OutTime . "' <br> <br>
                                                  <b>Return Time:</b> '" . $ReturnTime . "' <br> <br>
                                                   <br>
                                                  <b>NOTE:</b> <br>
                                                  <p>
                                                    In case if you are having any questions, Please contact the help desk.
                                                  </p>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td style='height:40px;'>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            <tr>
                                <td style='height:20px;'>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $subject = "Outpass Permission Request";  // Subject of the email

        // Set the headers to send an HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@/rguktn.ac.in" . "\r\n";  // Sender's email address

        // Send the email
        if(mail($Toemail, $subject, $htmlMessage, $headers)) {
         echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('success', 'Email sent successfully!'); });</script>";
     } else {
         $lastError = error_get_last();
         echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error', 'Failed to send email. Please try again later. " . (isset($lastError['message']) ? $lastError['message'] : '') . "'); });</script>";
     }

    echo json_encode(['success' => true]);
    } else {
    echo json_encode(['success' => false, 'message' => 'Failed to escalate request']);
    }

    $stmt->close();
    $conn->close();
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request ID']);
}
?>
