<?php
session_start();

// Redirect if session expired
if (!isset($_SESSION['email'])) {
    header("Location: signup.php");
    exit;
}

// Info & errors from session
$info = $_SESSION['info'] ?? '';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="verify.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .container { max-width: 400px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        input[type="number"], button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #007bff; color: #fff; border: none; cursor: pointer; transition: 0.3s; }
        button:hover { background: #0056b3; }
        .info { color: green; text-align: center; margin-bottom: 15px; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
        #toast { position: fixed; bottom: 20px; right: 20px; z-index: 1000; }
        .toast { background: #333; color: #fff; padding: 10px 20px; border-radius: 5px; margin-top: 5px; opacity: 0.9; }
    </style>
</head>
<body>
<div class="container">
    <h2>Verify OTP</h2>

    <?php if($info) echo "<p class='info'>$info</p>"; ?>
    <?php if($errors) { foreach($errors as $error) echo "<p class='error'>$error</p>"; } ?>

    <form id="otpForm">
        <input type="number" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>

    <button id="resendOtpBtn">Resend OTP</button>

    <div id="toast"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const otpForm = document.getElementById('otpForm');
    const resendBtn = document.getElementById('resendOtpBtn');
    const toastContainer = document.getElementById('toast');

    // Show toast message
    function showToast(message, type='success') {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerText = message;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // OTP submit
    otpForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(otpForm);

        fetch('verify-handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showToast(data.message, data.status);
            if (data.status === 'success' && data.redirect) {
                setTimeout(() => { window.location.href = data.redirect; }, 1500);
            }
        })
        .catch(err => showToast('Server error', 'error'));
    });

    // Resend OTP
    resendBtn.addEventListener('click', () => {
        fetch('resend-otp.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => showToast(data.message, data.status))
        .catch(err => showToast('Server error', 'error'));
    });
});
</script>
</body>
</html>
