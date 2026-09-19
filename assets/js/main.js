/**
 * Paytrack — Global JS Utilities
 * Shared across all pages.
 */

window.Paytrack = window.Paytrack || {};

Paytrack.Utils = (function () {

    /** Format number as Philippine Peso */
    function peso(amount) {
        return '\u20b1' + parseFloat(amount).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    /** Show a SweetAlert2 success toast (top-right) */
    function toastSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    }

    /** Show a SweetAlert2 error toast */
    function toastError(message) {
        Swal.fire({
            icon: 'error',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }

    /** Confirm dialog — returns Promise<boolean> */
    function confirm(title, text) {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#e5e5e7',
            confirmButtonText: 'Yes, proceed',
            reverseButtons: true,
        }).then(result => result.isConfirmed);
    }

    return { peso, toastSuccess, toastError, confirm };

})();
