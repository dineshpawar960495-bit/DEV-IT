<?php
session_start();
header('Content-Type: application/json'); // Always send JSON header before output

require 'connection.php'; // DB connection

// PHPMailer not needed here unless you resend OTP
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

// -------------------- Check session --------------------
if (!isset($_SESSION['email'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired. Please signup again.'
    ]);
    exit;
}

$email = $_SESSION['email'];
$otp   = trim($_POST['otp'] ?? '');

// -------------------- Validate OTP --------------------
if (!$otp) {
    echo json_encode([
        'status' => 'error',
        'message' => 'OTP is required!'
    ]);
    exit;
}

// -------------------- Fetch user --------------------
$stmt = $con->prepare("SELECT otp, otp_expiry, verified FROM devdata WHERE email=?");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $con->error
    ]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found.'
    ]);
    exit;
}

// -------------------- Already verified --------------------
if ($user['verified'] == 1) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Your account is already verified! Redirecting to login...',
        'redirect' => 'login.php'
    ]);
    exit;
}

// -------------------- Check OTP correctness --------------------
if ($user['otp'] != $otp) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid OTP!'
    ]);
    exit;
}

// -------------------- Check OTP expiry --------------------
if (strtotime($user['otp_expiry']) < time()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'OTP has expired! Please resend OTP.'
    ]);
    exit;
}

// -------------------- OTP is valid → update verified --------------------
$stmt = $con->prepare("UPDATE devdata SET verified=1, otp=NULL, otp_expiry=NULL WHERE email=?");
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update verification: ' . $stmt->error
    ]);
    exit;
}
$stmt->close();

// -------------------- Success response --------------------
echo json_encode([
    'status'   => 'success',
    'message'  => 'Account verified successfully! Redirecting to login...',
    'redirect' => 'login.php'
]);
exit;
