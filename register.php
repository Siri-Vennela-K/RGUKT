<?php include 'dbconnection.php'; ?>
<?php
$userRole = isset($_SESSION['Role']) ? $_SESSION['Role'] : '';
if ($userRole == '') {
    header("Location: logout.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form data
        $student_id = $_POST['student_id'];
        $email = $_POST['email'];
        $branch = $_POST['branch'];
        $stream = $_POST['stream'];
        $full_name = $_POST['full_name'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $guardian_contact = $_POST['guardian_contact'];
        $address = $_POST['address'];
        $guardian = $_POST['guardian'];
        $guardian_name = $_POST['guardian_name'];
        $guardian_address = $_POST['guardian_address'];
    
        $sql = "SELECT * FROM StudentMaster WHERE Email = '$email' and IsActive=1";
        $result = $conn->query($sql);
	
        if ($result->num_rows > 0) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Email Already Exists.. Try sign in or else use a different email address');});</script>";
        }else{
            // Prepare SQL query
            $sql = "INSERT INTO StudentMaster (StudentID, Email, Branch, Stream, FullName, Gender, Phone,  StudentAddress, Guardian, GuardianName, GuardianContact, GuardianAddress) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssss", $student_id, $email, $branch, $stream, $full_name, $gender, $phone, $address, $guardian, $guardian_name, $guardian_contact, $guardian_address);

            // Execute query and check for errors
            if ($stmt->execute()) {
                $sql = "INSERT INTO UserMaster (Username, Email, UserType, UserRole, Branch) VALUES ('$full_name', '$email', 'Student', 'Student', '$branch')";
                if (mysqli_query($conn, $sql)){
                    $resetLink = "http://localhost/RGKUT-Outpass-Management-System/";
   			        $htmlMessage = "
   			        <!doctype html>
                    <html>
                    <head>
                    <meta content='text/html; charset=utf-8' http-equiv='Content-Type'/>
                    <meta name='description' content='Registration Successful'>
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
                    style='max-width:670px;background:#fff; border-radius:3px; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                    <tr>
                    <td style='padding:0 35px;text-align:center;'>
                    <h1>Registered Successfully</h1>
                    <span
                    style='display:inline-block; vertical-align:middle; margin:2px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                    </td>
                    </tr>
                    <tr>
                    <td style='height:40px;padding:0 35px;'><p>Hi <b>'".$full_name."'</b>,
                    <br> <br>
                    Your login details are as follows: <br><br>
                    - Email: <b>'".$email."'</b><br>
                    - Default Password: <b>SIRI2024</b> <br> <br> <br>
                    click the below button to login to the webiste</p>
                    <a href='" . $resetLink . "'
                    style='background:#092f57;text-decoration:none !important; font-weight:500; margin-top:20px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;'>Click Me</a>
                    <br><br>
                    <p>If the above button doesn't work, you can login by clicking the following link, <a href='" . $resetLink . "'>login</a>.</p>&nbsp;</td>
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
   
   			        $subject = "Registration Successfull";  // Subject of the email

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
                }
            } else {
                echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Something went wrong, try again..! \n'+'$stmt->error');});</script>";
            }
            // Close statement and connection
            $stmt->close();
        }
        $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Student Registration</title>

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
    <link rel="stylesheet" type="text/css" href="src/plugins/jquery-steps/jquery.steps.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/toastr/toastr.min.css" />
</head>

<body>
<?php include 'Body.php'; ?>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="pd-20 card-box mb-30">
                <div class="d-flex align-items-center">
                    <h4 class="text-blue h4">Register</h4>
                </div>
                <div class="wizard-content">
                    <form class="tab-wizard wizard-circle wizard" id="registration-form"
                        action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
                        <!-- Step 1: Account Credentials -->
                        <h5>Account Credentials</h5>
                        <section>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Student Id* :</label>
                                        <input type="text" name="student_id" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email Address* :</label>
                                        <input type="email" name="email" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch* :</label>
                                        <select name="branch" class="custom-select form-control" required>
                                            <option value="">Select Branch</option>
                                            <option value="Srikakulam">RGKUT - Srikakulam</option>
                                            <option value="Ongole">RGKUT - Ongole</option>
                                            <option value="Nuzividu">RGKUT - Nuzividu</option>
                                            <option value="RK Valley">RGKUT - RK Valley</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Stream* :</label>
                                        <select name="stream" class="custom-select form-control" required>
                                            <option value="">Select Stream</option>
                                            <option value="PUC - 1">PUC - 1</option>
                                            <option value="PUC - 2">PUC - 2</option>
                                            <option value="CSE">CSE</option>
                                            <option value="ECE">ECE</option>
                                            <option value="EEE">EEE</option>
                                            <option value="ME">ME</option>
                                            <option value="CE">CE</option>
                                            <option value="MME">MME</option>
                                            <option value="CHEM">CHEM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Step 2: Personal Info -->
                        <h5>Personal Info</h5>
                        <section>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name* :</label>
                                        <input type="text" name="full_name" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender* :</label>
                                        <div class="col-sm-8">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" value="Male" id="male" name="gender" class="custom-control-input"/>
                                                <label class="custom-control-label" for="male">Male</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" value="Female" name="gender" id="female"
                                                    class="custom-control-input" />
                                                <label class="custom-control-label" for="female">Female</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number* :</label>
                                        <input type="text" name="phone" class="form-control" required minlength="10"
                                            pattern="^[0-9]{10}$" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address* :</label>
                                            <textarea name="address" class="form-control h-25" required></textarea>
                                        </div>
                                    </div>
                            </div>
                        </section>

                        <!-- Step 3: Parent / Guardian Info -->
                        <h5>Parent / Guardian Info</h5>
                        <section>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Parent / Guardian* :</label>
                                        <select name="guardian" class="custom-select form-control" required>
                                            <option value="">Select</option>
                                            <option value="Parent">Parent</option>
                                            <option value="Guardian">Guardian</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Parent / Guardian Name* :</label>
                                        <input type="text" name="guardian_name" class="form-control" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Number* :</label>
                                        <input type="text" name="guardian_contact" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address* :</label>
                                        <textarea name="guardian_address" class="form-control h-25" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Step 4: Final Review -->
                        <h5>Final Review</h5>
                        <section>
                            <ul class="register-info" id="review-info">
                                <!-- Review details will be dynamically inserted here -->
                            </ul>
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input" name="customCheck1"
                                    id="customCheck1" required />
                                <label class="custom-control-label" for="customCheck1">I have read and agreed to the
                                    terms of services and privacy policy</label>
                            </div>
                        </section>
                    </form>

                </div>
            </div>

            <!-- success Popup html Start -->
            <button type="button" id="success-modal-btn" hidden data-toggle="modal" data-target="#success-modal"
                data-backdrop="static">
                Launch modal
            </button>
            <div class="modal fade" id="success-modal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered max-width-400" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center font-18">
                            <h3 class="mb-20">Registered Successfully!</h3>
                            <div class="mb-30 text-center">
                                <img src="vendors/images/success.png" />
                            </div>
                            You just have registered a student in RGKUT, Successfully.
                        </div>
                        <div class="modal-footer justify-content-center">
                            <a href="register.php" class="btn btn-primary">Done</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- success Popup html End -->

        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jquery-steps/jquery.steps.js"></script>
    <script src="src/plugins/jQuery-Validation/jquery-validation.min.js"></script>
    <script src="vendors/scripts/steps-setting.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
    <script>
        $(document).ready(function () {
            $.validator.addMethod("pattern", function (value, element, regex) {
                return this.optional(element) || regex.test(value);
            }, "Invalid format");
        });
    </script>
</body>

</html>