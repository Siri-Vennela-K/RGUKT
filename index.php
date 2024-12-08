<?php include 'dbconnection.php'; ?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Protect against SQL Injection
    $email = $conn->real_escape_string($email);
    $role = $conn->real_escape_string($role);

    // Check if user exists
    $sql = "SELECT U.UserID, U.Username, U.Email, U.Password, U.UserType, U.UserRole, U.Branch, F.InchargeFor, U.IsFirstLogin, 
	f.FacultyType FROM usermaster U 
	LEFT JOIN FacultyMaster f ON u.Email = f.Email 
	WHERE U.Email = '$email' and U.UserType='$role' and U.IsActive=1";

    $result = $conn->query($sql);
	
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Set session variables
            $_SESSION['UserID'] = $user['UserID'];
			$_SESSION['UserName']= $user['Username'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['Role'] = $user['UserRole'];
			$_SESSION['Branch'] = $user['Branch'];
			$_SESSION['InchargeFor'] = $user['InchargeFor'];
			$_SESSION['FacultyType'] = $user['FacultyType'];

			if($user['IsFirstLogin'] == 1){
				header("Location: forgotpassword.php");
				exit;
			}

            // Redirect to the appropriate dashboard based on role
            if ($user['UserType'] === 'Admin' || $role === 'Faculty') {
                header("Location: AdminDashboard.php");
            } elseif ($role === 'Student') {
                header("Location: studentdashboard.php");
            }
            exit;
        } else {
			echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Invalid Password'); });</script>";
        }
    } else {
		echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','User does not exists, contact administrator'); });</script>";

    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8" />
	<title>RGUKT</title>

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

<body class="login-page">
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
				<div class="col-md-6 col-lg-7">
					<img src="vendors/images/login-page-img.png" alt="" />
				</div>
				<div class="col-md-6 col-lg-5">
					<div class="login-box bg-white box-shadow border-radius-10">
						<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
							<div class="select-role">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn active">
										<input type="radio" name="role" value="Faculty" />
										<div class="icon">
											<img src="vendors/images/briefcase.svg" class="svg" alt="" />
										</div>
										<span>I'm</span>
										Faculty
									</label>
									<label class="btn">
										<input type="radio" name="role" value="Student" />
										<div class="icon">
											<img src="vendors/images/person.svg" class="svg" alt="" />
										</div>
										<span>I'm</span>
										Student
									</label>
								</div>
							</div>

							<div class="input-group custom">
								<input type="text" name="email" class="form-control form-control-lg"
									placeholder="email address" />
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
								</div>
							</div>
							<div class="input-group custom">
								<input type="password" name="password" class="form-control form-control-lg"
									placeholder="**********" />
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
							</div>
							<div class="row pb-30">
								<div class="col-6">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="customCheck1" />
										<label class="custom-control-label" for="customCheck1">Remember</label>
									</div>
								</div>
								<div class="col-6">
									<div class="forgot-password">
										<a href="forgotpassword.php">Forgot Password?</a>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="input-group mb-0">
										<input class="btn btn-primary btn-lg btn-block" type="submit" value="Sign In">
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

					if (element.attr("name") === "role") {
						error.insertBefore(".select-role");
					} else {
						error.insertBefore(element.closest('.input-group'));
					}
				},
				rules: {
					role: {
						required: true
					},
					email: {
						required: true,
						email:true
					},
					password: {
						required: true,
						minlength: 6
					}

				},
				messages: {
					role: {
						required: "Please select a role"
					},
					email: {
						required: "Please enter your email",
						email: "Please enter valid email address"
					},
					password: {
						required: "Enter your password",
						minlength: "Minimum length is 6"
					}

				}
			});
		});
	</script>
</body>

</html>