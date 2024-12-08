<?php include 'dbconnection.php'; ?>
<?php
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
       $Toemail = trim($_POST['email']);
       
       // Check if email exists in the database
       $stmt = $conn->prepare("SELECT UserID FROM UserMaster WHERE Email = ? and IsActive = 1");
       $stmt->bind_param('s', $Toemail);
       $stmt->execute();
       $result = $stmt->get_result();
       
       if ($result->num_rows > 0) {
           $user = $result->fetch_assoc();
           $userID = $user['UserID'];
           
           // Generate a unique token
           $token = bin2hex(random_bytes(32));

           date_default_timezone_set('Asia/Kolkata'); 
		   $expiryTime = date("Y-m-d H:i:s", strtotime('+1 hour'));

           // Store the token in a password_reset table
           $stmt = $conn->prepare("INSERT INTO PasswordReset (UserID, Token, ExpiryTime) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Token=?, ExpiryTime=?");
           $stmt->bind_param('issss', $userID, $token, $expiryTime, $token, $expiryTime);
           $stmt->execute();
           
           // Send password reset email
           $resetLink = "http://localhost/RGKUT-Outpass-Management-System/resetpassword.php?token=" . $token;
          
   			$htmlMessage = "
   			<!doctype html>
   			<html>
   			<head>
   			    <meta content='text/html; charset=utf-8' http-equiv='Content-Type'/>
   			    <title>Reset Password Email Template</title>
   			    <meta name='description' content='Reset Password Email Template.'>
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
   			                                        <h1>Reset Password</h1>
   			                                        <span
   			                                            style='display:inline-block; vertical-align:middle; margin:2px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
   			                                        <p>
   			                                          Click the button below to reset your password, the reset password link is only valid for 1 hour.
   			                                        </p>
   			                                        <a href='" . $resetLink . "'
   			                                            style='background:#092f57;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;'>Reset
   			                                            Password</a>
   			                                      <br><br>
   			                                        <p>If the above button doesn't work, you can reset your password by clicking the following link, <a href='" . $resetLink . "'>Reset password</a>.</p>
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
   
   			$subject = "Password Reset Request";  // Subject of the email

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
			
   		}else{
			echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Email not found. Please check your registered email address'); });</script>";
		}
	}
?>
<!DOCTYPE html>
<html>
   <head>
      <!-- Basic Page Info -->
      <meta charset="utf-8" />
      <title>Forgot Password</title>
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
      <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
	  <link rel="stylesheet" type="text/css" href="src/plugins/toastr/toastr.min.css" />
   </head>
   <body>
      <div class="login-header box-shadow">
         <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
               <a href="#">
               <img src="vendors/images/rgukt-logo.png" alt="" /><b>RGUKT</b>
               </a>
            </div>
         </div>
      </div>
      <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-md-6">
                  <img src="vendors/images/forgot-password.png" alt="" />
               </div>
               <div class="col-md-6">
                  <div class="login-box bg-white box-shadow border-radius-10">
                     <div class="login-title">
                        <h2 class="text-center text-primary">Forgot Password</h2>
                     </div>
                     <h6 class="mb-20">
                        Enter your email address to reset your password
                     </h6>
                     <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
                        <div class="input-group custom">
                           <input type="text" name="email" class="form-control form-control-lg"
                              placeholder="Email" />
                           <div class="input-group-append custom">
                              <span class="input-group-text"><i class="fa fa-envelope-o"
                                 aria-hidden="true"></i></span>
                           </div>
                        </div>
                        <div class="row align-items-center">
                           <div class="col-5">
                              <div class="input-group mb-0">
                                 <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="font-16 weight-600 text-center" data-color="#707373">
                                 OR
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="input-group mb-0">
                                 <a class="btn btn-outline-primary btn-lg btn-block" href="logout.php">Login</a>
                              </div>
                           </div>
                        </div>
                     </form>
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
      <script src="src/plugins/jQuery-Validation/jquery-validation.min.js"></script>
	<script src="src/plugins/toastr/toastr.min.js"></script>
	<script src="vendors/scripts/toastr.js"></script>
      <script>
         $(document).ready(function () {
         
         	$("form").validate({
         		errorPlacement: function (error, element) {
         			error.insertBefore(element.closest('.input-group'));
         		},
         		rules: {
         			email: {
         				required: true,
         				email: true
         			}
         		},
         		messages: {
         			email: {
         				required: "Please enter your registered email",
         				email: "Enter a valid email address"
         			}
         		}
         	});
         });
      </script>
   </body>
</html>