function AlertMessage(errorType, errorMessage, isHideAfter) {//  errorType => 'info', 'warning', 'success', 'error'

    var errorTypes = ['info', 'warning', 'success', 'error'];
    if (typeof (isHideAfter) !== 'boolean')
        isHideAfter = false;

    errorMessage = $.trim(errorMessage) || '';

    if (errorMessage != '') {

        if (jQuery.inArray(errorType.toString().toLowerCase(), errorTypes) != '-1') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "600",
                "hideDuration": "1000",
                "timeOut": "7000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "opacity": 1
            }
            toastr[errorType](errorMessage);
            return false;
        }
    }
}