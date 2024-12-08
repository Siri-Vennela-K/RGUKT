<?php include 'dbconnection.php'; ?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values
	$token = $_POST['token'];
    $newPassword = trim($_POST['password']);
    $confirmPassword = trim($_POST['repeat']);

    // Check if passwords match
    if ($newPassword === $confirmPassword) {
		// Check if the token is valid
        $stmt = $conn->prepare("SELECT UserID FROM PasswordReset WHERE Token = ? AND ExpiryTime > NOW()");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
            $userID = $user['UserID'];
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE UserMaster SET Password = ?, IsFirstLogin = 0 WHERE UserID = ? and IsActive = 1");
            $stmt->bind_param('si', $hashedPassword, $userID);
            $stmt->execute();

			// Delete the token
            $stmt = $conn->prepare("DELETE FROM PasswordReset WHERE UserID = ?");
            $stmt->bind_param('i', $userID);
            $stmt->execute();
			$stmt->close();
			$conn->close();
			echo "<script>
    				document.addEventListener('DOMContentLoaded', function() {
        				AlertMessage('success','Password updated successfully');
        				setTimeout(function() {
        				    window.location.href = 'logout.php';
        				}, 10000); // 10000 milliseconds = 10 seconds
    				});
			</script>";
			          
		}else {
			echo "<script>
    				document.addEventListener('DOMContentLoaded', function() {
        				AlertMessage('error', 'Invalid or expired token, try again.');
        				setTimeout(function() {
        				    window.location.href = 'forgotpassword.php';
        				}, 10000); // 10000 milliseconds = 10 seconds
    				});
			</script>";
		}
        
    } else {
		echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error', 'Passwords do not match'); });</script>";
    }
}
else if (isset($_GET['token'])) {
    $token = $_GET['token'];
}
?>


<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8" />
	<title>Reset Password</title>

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
				<a href="login.html">
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
							<h2 class="text-center text-primary">Reset Password</h2>
						</div>
						<h6 class="mb-20">Enter your new password, confirm and submit</h6>
						<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
						<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
							<div class="input-group custom">
								<input type="text" name="password" class="form-control form-control-lg"
									placeholder="New Password" />
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
							</div>
							<div class="input-group custom">
								<input type="text" name="repeat" class="form-control form-control-lg"
									placeholder="Confirm New Password" />
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
							</div>
							<div class="row align-items-center">
								<div class="col-5">
									<div class="input-group mb-0">
										<input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
									</div>
								</div>
								<div class="col-2"></div>
								<div class="col-5">
									<div class="input-group mb-0">
										<a class="btn btn-danger btn-lg btn-block" href="logout.php">Back</a>
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
					password: {
						required: true,
						minlength: 6
					},
					repeat: {
						required: true,
						minlength: 6
					}
				},
				messages: {
					password: {
						required: "Please enter New Password",
						minlength: "Minimum length is 6"
					},
					repeat: {
						required: "Please enter Confirm New Password",
						minlength: "Minimum length is 6"
					}
				}
			});
		});
	</script>

</body>

</html>