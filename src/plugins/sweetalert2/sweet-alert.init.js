!function ($) {
    "use strict";

    var SweetAlert = function () {
    };

    SweetAlert.prototype.init = function () {

        //Parameter
        $('.approve-btn').click(function () {
            const requestId = $(this).data('id');
            swal({
                title: 'Are you sure, to grant the permission to leave?',
                //text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve it!',
                cancelButtonText: 'No, Deny!',
                confirmButtonClass: 'btn btn-success margin-5',
                cancelButtonClass: 'btn btn-danger margin-5',
                buttonsStyling: false
            }).then(function (result) {
                if (result.value === true) {
                    $.ajax({
                        url: 'sendRequest.php',
                        type: 'POST',
                        data: { id: requestId, page: 'AdminDashboard', status: 'Approved' },
                        success: function (response) {
                            if (response) {
                                swal('Approved!', 'Request has been approved.', 'success')
                            } else {
                                AlertMessage('error', 'Failed to escalate request: ' + response.message);
                            }
                        },
                        error: function () {
                            AlertMessage('error', 'An error occurred while escalating the request.');
                        }
                    });

                } else if (result.dismiss === 'cancel') {
                    $.ajax({
                        url: 'sendRequest.php',
                        type: 'POST',
                        data: { id: requestId, page: 'AdminDashboard', status: 'Rejected' },
                        success: function (response) {
                            if (response) {
                                swal('Rejected!', 'Request has been rejected.', 'error')
                            } else {
                                AlertMessage('error', 'Failed to escalate request: ' + response.message);
                            }
                        },
                        error: function () {
                            AlertMessage('error', 'An error occurred while escalating the request.');
                        }
                    });
                }
            });
        });

        $('.dw-delete-3').click(function () {
            swal({
                title: 'Are you sure, to delete the student info?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'No, Keep!',
                confirmButtonClass: 'btn btn-success margin-5',
                cancelButtonClass: 'btn btn-danger margin-5',
                buttonsStyling: false
            }).then(function (result) {
                if (result.value === true) {
                    swal(
                        'Deleted!',
                        'Student Data Deleted Successfully',
                        'success'
                    );
                }
                else if (result.dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'No changes were made',
                        'error'
                    );
                }
            });
        });

        $('.delete-request-btn').click(function () {
            const requestId = $(this).data('id');
            swal({
                title: 'Are you sure, to the request?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'No, Keep!',
                confirmButtonClass: 'btn btn-success margin-5',
                cancelButtonClass: 'btn btn-danger margin-5',
                buttonsStyling: false
            }).then(function (result) {
                if (result.value === true) {
                    $.ajax({
                        url: 'deleteRequest.php',
                        type: 'POST',
                        data: { id: requestId },
                        success: function (response) {
                            if (response) {
                                swal('Deleted!', 'Request Deleted Successfully', 'success');
                                setTimeout(function () {
                                    location.reload();
                                }, 10000); // 10000 milliseconds = 10 seconds
                            } else {
                                swal('Cancelled!', 'Failed to delete request', 'error');
                            }
                        },
                        error: function () {
                            swal('Cancelled!', 'An error occurred while deleting the request.', 'error');
                        }
                    });

                }
                else if (result.dismiss === 'cancel') {
                    swal('Cancelled!', 'No changes were made', 'error');
                }
            });
        });

    },
        //init
        $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
}(window.jQuery),

    //initializing
    function ($) {
        "use strict";
        $.SweetAlert.init()
    }(window.jQuery);