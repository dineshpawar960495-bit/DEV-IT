<?php
session_start();
header('Content-Type: application/json'); // IMPORTANT for AJAX

require 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Check if email exists in session
if (!isset($_SESSION['email'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired. Please signup again.'
    ]);
    exit();
}

$email = $_SESSION['email'];

// Fetch user info
$stmt = $con->prepare("SELECT fullname, verified FROM devdata WHERE email=?");
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
    exit();
}

if ($user['verified'] == 1) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Your account is already verified! Please login.',
        'redirect' => 'login.php'
    ]);
    exit();
}

// Generate new OTP
$otp = rand(1000, 9999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Update OTP in DB
$stmt = $con->prepare("UPDATE devdata SET otp=?, otp_expiry=? WHERE email=?");
$stmt->bind_param("sss", $otp, $expiry, $email);
$stmt->execute();
$stmt->close();

// Send OTP via PHPMailer
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dineshpawar960495@gmail.com'; // your Gmail
    $mail->Password   = 'cteshllolplvxxjk';            // your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('dineshpawar960495@gmail.com', 'CARSMODIN');
    $mail->addAddress($email, $user['fullname']);

    $mail->isHTML(true);
    $mail->Subject = 'Your new OTP for CARSMODIN';
    $mail->Body    = "Hello <b>{$user['fullname']}</b>,<br>Your new OTP is: <b>$otp</b><br>Valid for 5 minutes.";

    $mail->send();

    echo json_encode([
        'status' => 'success',
        'message' => 'A new OTP has been sent to your email.'
    ]);
    exit();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mailer Error: ' . $mail->ErrorInfo
    ]);
    exit();
}
