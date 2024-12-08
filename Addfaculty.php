<?php include 'dbconnection.php'; ?>
<?php
$userRole = isset($_SESSION['Role']) ? $_SESSION['Role'] : '';
if ($userRole == '') {
    header("Location: logout.php");
    exit();
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $branch = trim(mysqli_real_escape_string($conn, $_POST['branch']));
    $stream = trim(mysqli_real_escape_string($conn, $_POST['stream']));
    $number = trim(mysqli_real_escape_string($conn, $_POST['number']));
    $facultytype = trim(mysqli_real_escape_string($conn, $_POST['facultytype']));
    $InchargeFor = trim(mysqli_real_escape_string($conn, $_POST['InchargeFor']));

    if (empty($name) || empty($email) || empty($branch) || empty($stream) || empty($number) || empty($facultytype)) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','All fields are mandatory..!'); });</script>";
    } else {
        $sql = "SELECT * FROM facultymaster WHERE Email = '$email' and IsActive=1";
        $result = $conn->query($sql);
	
        if ($result->num_rows > 0) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Email Already Exists.. Try to use a different email address');});</script>";
        }else{
            $stmt = $conn->prepare("INSERT INTO FacultyMaster (FullName, Email, Branch, Stream, Phone, FacultyType, InchargeFor) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssss", $name, $email, $branch, $stream, $number, $facultytype, $InchargeFor);

            // Execute query and check for errors
            if ($stmt->execute()) {
                $sql = "INSERT INTO UserMaster (Username, Email, UserType, UserRole, Branch) VALUES ('$name', '$email', 'Faculty', 'Faculty','$branch')";
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
   			        if(mail($email, $subject, $htmlMessage, $headers)) {
			        	echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('success', 'Faculty Registered, email sent successfully!'); });</script>";
			        } else {
			        	$lastError = error_get_last();
			        	echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error', 'Failed to send email. Please try again later. " . (isset($lastError['message']) ? $lastError['message'] : '') . "'); });</script>";
			        }
                }
            } else {
                echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Something went wrong, try again..! \n'+'$stmt->error');});</script>";
            }
            $stmt->close();
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Add Faculty</title>

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
            <div class="pd-20 card-box mb-30">
                <div class="title pb-20">
                    <h2 class="text-blue h2">Add Faculty</h2>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Full name* :</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>E-mail Id* :</label>
                                <input type="text" class="form-control" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Branch* :</label>
                                <select name="branch" class="custom-select form-control">
                                    <option value="">Select Branch</option>
                                    <option value="Srikakulam">RGKUT - Srikakulam</option>
                                    <option value="Ongole">RGKUT - Ongole</option>
                                    <option value="Nuzividu">RGKUT - Nuzividu</option>
                                    <option value="RK Valley">RGKUT - RK Valley</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Stream* :</label>
                                <select name="stream" class="custom-select form-control">
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
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Phone number* :</label>
                                <input type="text" class="form-control" name="number">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Faculty Type* :</label>
                                <select class="custom-select form-control" name="facultytype" id="facultytype">
                                    <option value="">Select Type</option>
                                    <option value="Care Taker">Care Taker</option>
                                    <option value="Warden">Warden</option>
                                    <option value="Student Welfare">Student Welfare</option>
                                    <option value="Director">Director</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="incharge" style="display: none;">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Incharge Of* :</label>
                                <div class="col-sm-8">
                                    <div class="custom-control custom-radio custom-control-inline pb-0">
                                        <input type="radio" id="Male" name="InchargeFor" value="Male" class="custom-control-input" />
                                        <label class="custom-control-label" for="Male">Male</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline pb-0">
                                        <input type="radio" id="Female" name="InchargeFor" value="Female" class="custom-control-input" />
                                        <label class="custom-control-label" for="Female">Female</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 text-center">
                            <input class="btn btn-primary" type="submit" value="Submit">
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jQuery-Validation/jquery-validation.min.js"></script>
	<script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>

    <script>
        $(document).ready(function () {
            $.validator.addMethod("pattern", function (value, element, regex) {
                return this.optional(element) || regex.test(value);
            }, "Invalid format");

            $('#facultytype').change(function () {
                let value = $('#facultytype').val();
                if (value == 'Care Taker' || value == 'Warden') {
                    $('#incharge').show();
                } else {
                    $('#incharge').hide();
                }
            });

            $("form").validate({
                errorPlacement: function (error, element) {
                    error.insertBefore(element.closest('.form-group'));
                },
                rules: {
                    name: {
                        required: true,
                        pattern: /^[a-zA-Z ]+$/,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    branch: {
                        required: true
                    },
                    stream: {
                        required: true
                    },
                    number: {
                        required: true,
                        pattern: /^[0-9]+$/,
                        minlength: 10
                    },
                    facultytype: {
                        required: true
                    },
                    InchargeFor: {
                        required: true

                    }
                },
                messages: {
                    name: {
                        required: "Enter full name",
                        minlength: "Minimum length is 3",
                        pattern: "Only alphabet are allowed"
                    },
                    email: {
                        required: "Enter your E-mail Id",
                        email: "Enter a valid email address"
                    },
                    branch: {
                        required: "Select your branch"
                    },
                    stream: {
                        required: "Select your stream"
                    },
                    number: {
                        required: "Enter your Phone number",
                        pattern: "Only numbers are allowed",
                        minlength: "Minimum length is 10"
                    },
                    facultytype: {
                        required: "Select your faculty type"
                    },
                    InchargeFor: {
                        required: "select the Incharge Of"

                    }

                }
            });
        });
    </script>
</body>

</html>