document.addEventListener('DOMContentLoaded', () => {
    const otpForm = document.querySelector('#otpForm');
    const resendBtn = document.querySelector('#resendOtpBtn');
    const toastContainer = document.querySelector('#toastContainer');

    // Helper: show toast messages
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerText = message;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // OTP form submit
    if (otpForm) {
        otpForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(otpForm);

            fetch('verify-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json()) // Expect JSON from PHP
            .then(data => {
                if(data.status === 'success'){
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => showToast('Server error', 'error'));
        });
    }

    // Resend OTP
    if (resendBtn) {
        resendBtn.addEventListener('click', () => {
            fetch('resend-otp.php', { method: 'POST' })
            .then(res => res.json()) // Expect JSON from PHP
            .then(data => {
                if(data.status === 'success'){
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => showToast('Server error', 'error'));
        });
    }
});
