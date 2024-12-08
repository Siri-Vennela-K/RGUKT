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
   
   $sql = "SELECT Id, StudentID, Email, Branch, FullName, Stream, Gender, Phone, StudentAddress, 
           Guardian, GuardianName, GuardianContact, GuardianAddress
               FROM StudentMaster  Where IsActive=1";
   
   if(!empty($Branch)){
       $sql = $sql." and Branch = '$Branch'";
   }
   
   if(!empty($InchargeFor)){
      $sql = $sql." and Gender = '$InchargeFor'";
   }
   
   $sql = $sql." order by StudentID desc";
   
   $result = mysqli_query($conn, $sql);
   
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateRequest'])) {
    $Id = $_POST['Id'];
    $previousEmail = $_POST['previousemail'];
    $studentId = trim(mysqli_real_escape_string($conn, $_POST['student_id'])); 
    $email = trim(mysqli_real_escape_string($conn, $_POST['email'])); 
    $branch = trim(mysqli_real_escape_string($conn, $_POST['branch'])); 
    $stream = trim(mysqli_real_escape_string($conn, $_POST['stream'])); 
    $fullName = trim(mysqli_real_escape_string($conn, $_POST['full_name'])); 
    $gender = trim(mysqli_real_escape_string($conn, $_POST['gender'])); 
    $phone = trim(mysqli_real_escape_string($conn, $_POST['phone'])); 
    $address = trim(mysqli_real_escape_string($conn, $_POST['address'])); 
    $guardian = trim(mysqli_real_escape_string($conn, $_POST['guardian'])); 
    $guardianName = trim(mysqli_real_escape_string($conn, $_POST['guardian_name'])); 
    $guardianContact = trim(mysqli_real_escape_string($conn, $_POST['guardian_contact'])); 
    $guardianAddress = trim(mysqli_real_escape_string($conn, $_POST['guardian_address'])); 

    if($previousEmail === $email)
    {
        $update = 1;
    }
    else{
        $EmailExists = "SELECT * FROM UserMaster WHERE Email = '$email'";
        $EmailExistsresult = $conn->query($EmailExists);
    
        if ($EmailExistsresult->num_rows > 0) {
            $update = 0;
            echo "<script>document.addEventListener('DOMContentLoaded', function() { AlertMessage('error', 'Email Already Exists..' + '$email'); }); </script>";
        } else {
            $update = 1;
        }
    }
    
    if($update == 1){
        $sql = "UPDATE StudentMaster SET StudentID = ?, Email = ?, Branch = ?, Stream = ?, FullName = ?, Gender = ?, Phone = ?, 
                StudentAddress = ?, Guardian = ?, GuardianName = ?, GuardianContact = ?, GuardianAddress = ? 
                WHERE ID = ?";

        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("isssssssssssi", $studentId, $email, $branch, $stream, $fullName, $gender, $phone, $address, 
        $guardian, $guardianName, $guardianContact, $guardianAddress, $Id); 
        
        
        if ($stmt->execute()) {
            $sql = "UPDATE UserMaster set Email='$email', Username='$fullName', Branch='$branch', IsFirstLogin = 1 WHERE Email='$previousEmail'";
            if (mysqli_query($conn, $sql)){
                echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('success','Updated Successfully'); location.reload(); });</script>";
            }
        } else {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {AlertMessage('error','Failed to update. Try Again..!'); });</script>";
        }
        $stmt->close(); 
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html>
   <head>
      <!-- Basic Page Info -->
      <meta charset="utf-8" />
      <title>Student Info</title>
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
      <style>
         @media (min-width: 992px) {
         .modal-lg, .modal-xl {
         max-width: 90% !important;
         }
         }
      </style>
   </head>
   <body>
      <?php include 'Body.php'; ?>
      <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
         <!-- Export Datatable start -->
         <div class="card-box mb-30">
            <div class="pd-20">
               <h4 class="text-blue h4">Students</h4>
            </div>
            <div class="pb-20">
               <table class="table hover data-table-export">
                  <thead>
                     <tr>
                        <th>S.no</th>
                        <th>Student Id</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Stream</th>
                        <th>FullName</th>
                        <th>Gender</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Action</th>
                        <th>Parent / Guardian</th>
                        <th>Parent / Guardian Name</th>
                        <th>Parent / Guardian Number</th>
                        <th>Parent / Guardian Address</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        if ($result->num_rows > 0) {
                            $sno = 1; // Serial number
                            while ($row = $result->fetch_assoc()) {
                                $id=$row['Id'];
                                $StudentID = $row['StudentID'];
                                $Email = $row['Email'];
                                $Branch = $row['Branch'];
                                $FullName = $row['FullName'];
                                $Stream = $row['Stream'];
                                $Gender = $row['Gender'];
                                $Phone = $row['Phone'];
                                $Address = $row['StudentAddress'];
                                $Guardian = $row['Guardian'];
                                $GuardianName = $row['GuardianName'];
                                $GuardianContact = $row['GuardianContact'];
                                $GuardianAddress = $row['GuardianAddress'];
                        
                            
                                echo "<tr>
                                <td>{$sno}</td>
                                <td>{$StudentID}</td>
                                <td>{$Email}</td>
                                <td>{$Branch}</td>
                                <td>{$Stream}</td>
                                <td>{$FullName}</td>
                                <td>{$Gender}</td>
                                <td>{$Phone}</td>
                                <td>{$Address}</td>
                                <td>
                                    <div class='table-actions'>
                                        <a href='#' data-toggle='modal' data-target='#bd-example-modal-lg' class='edit-btn' data-studentid='{$StudentID}' 
                                            data-email='{$Email}' data-branch='{$Branch}' data-stream='{$Stream}' data-name='{$FullName}' 
                                            data-gender='{$Gender}' data-phone='{$Phone}' data-address='{$Address}' data-guardian='{$Guardian}'
                                            data-guardianname='{$GuardianName}' data-guardiancontact='{$GuardianContact}' data-guardianaddress='{$GuardianAddress}'
                                            data-id='{$id}' '>
                                            <i class='icon-copy dw dw-edit2'></i>
                                        </a>
                                        <a href='#' class='delete-request-btn' data-id='{$id}' data-color='#e95959'>
                                            <span class='icon-copy ti-trash'></span>
                                        </a> 
                                    </div>
                                </td>
                                <td>{$Guardian}</td>
                                <td>{$GuardianName}</td>
                                <td>{$GuardianContact}</td>
                                <td>{$GuardianAddress}</td>
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
         <div class="modal fade bs-example-modal-lg" id="bd-example-modal-lg" tabindex="-1"
            role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered"">
               <div class="modal-content">
                  <div class="modal-header">
                     <h4 class="modal-title" id="myLargeModalLabel">
                        Edit Student Details
                     </h4>
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                     ×
                     </button>
                  </div>
                  <div class="modal-body">
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                  <input type="hidden" name="Id" id="studentmasterId">
                  <input type="hidden" name="previousemail" id="previousemail"> 
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Student Id* :</label>
                              <input type="text" name="student_id" class="form-control" required/>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Email Address* :</label>
                              <input type="email" name="email" class="form-control" required />
                           </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Full Name* :</label>
                              <input type="text" name="full_name" class="form-control" required />
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Gender* :</label>
                              <div class="col-sm-12">
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
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Phone Number* :</label>
                              <input type="text" name="phone" class="form-control" required minlength="10"
                                 pattern="^[0-9]{10}$" />
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Student Address* :</label>
                              <textarea name="address" class="form-control h-25" required></textarea>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Parent / Guardian* :</label>
                              <select name="guardian" class="custom-select form-control" required>
                                 <option value="">Select</option>
                                 <option value="Parent">Parent</option>
                                 <option value="Guardian">Guardian</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Parent / Guardian Name* :</label>
                              <input type="text" name="guardian_name" class="form-control" required />
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Contact Number* :</label>
                              <input type="text" name="guardian_contact" class="form-control" required />
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label>Parent / Guardian Address* :</label>
                              <textarea name="guardian_address" class="form-control h-25" required></textarea>
                           </div>
                        </div>
                     </div>
                     <div>
                        <div class="text-center">
                            <input class="btn btn-primary" type="submit" value="Save" name="updateRequest">
                        </div>
                     </div> 
                    </form>                  
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
                const Id = $(this).data('id');
                const studentId = $(this).data('studentid');
                const email = $(this).data('email');
                const branch = $(this).data('branch');
                const fullname = $(this).data('name');
                const stream = $(this).data('stream');
                const gender = $(this).data('gender');
                const phone = $(this).data('phone');
                const address = $(this).data('address');
                const guardian = $(this).data('guardian');
                const guardianName = $(this).data('guardianname');
                const guardianContact = $(this).data('guardiancontact');
                const guardianAddress = $(this).data('guardianaddress');

                // Set form values in the modal
                $('#studentmasterId').val(Id);
                $('#previousemail').val(email);
                $('input[name="student_id"]').val(studentId);
                $('input[name="email"]').val(email);
                $('select[name="branch"]').val(branch);
                $('select[name="stream"]').val(stream);
                $('input[name="full_name"]').val(fullname);
                $('input[name="gender"][value="' + gender + '"]').prop('checked', true);
                $('input[name="phone"]').val(phone);
                $('textarea[name="address"]').val(address);
                $('select[name="guardian"]').val(guardian);
                $('input[name="guardian_name"]').val(guardianName);
                $('input[name="guardian_contact"]').val(guardianContact);
                $('textarea[name="guardian_address"]').val(guardianAddress);
            });
         });
      </script>
   </body>
</html>