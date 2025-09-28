<?php
session_start();

// --- 1️⃣ Include DB connection ---
require 'connection.php'; // $con = new mysqli(...);

// --- 2️⃣ Include PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- 3️⃣ Get POST data ---
$fullname = trim($_POST['fullname'] ?? '');
$age      = trim($_POST['age'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$cpassword= trim($_POST['cpassword'] ?? '');

$errors = [];

// --- 4️⃣ Validation ---
if (!$fullname || !$age || !$phone || !$email || !$password || !$cpassword) {
    $errors[] = "All fields are required.";
}

if ($password !== $cpassword) {
    $errors[] = "Passwords do not match.";
}

// --- 5️⃣ Check if email already exists ---
$stmt = $con->prepare("SELECT id FROM devdata WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $errors[] = "Email already exists! Please login or verify OTP.";
}

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
    header("Location: signup.php");
    exit();
}

// --- 6️⃣ Generate OTP ---
$otp = rand(1000, 9999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
$verified = 0;
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// --- 7️⃣ Insert user into DB ---
$stmt = $con->prepare("INSERT INTO devdata (fullname, age, phone, email, password, otp, otp_expiry, verified) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssssi", $fullname, $age, $phone, $email, $hashedPassword, $otp, $expiry, $verified);

if (!$stmt->execute()) {
    die("Database Error: " . $stmt->error);
}
$stmt->close();

// Send OTP via PHPMailer
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dineshpawar960495@gmail.com'; // Your Gmail
    $mail->Password   = 'cteshllolplvxxjk';            // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('dineshpawar960495@gmail.com', 'DEV Project');
    $mail->addAddress($email, $fullname);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP for DEV Project';
    $mail->Body    = "Hello <b>$fullname</b>,<br>Your OTP is: <b>$otp</b><br>Valid for 5 minutes.";

    $mail->send();

    // Store email in session for verification
    $_SESSION['email'] = $email;
    $_SESSION['info']  = "OTP sent to your email - $email";
    header("Location: verify-otp.php");
    exit();
} catch (Exception $e) {
    // Do not expose sensitive info to users
    $_SESSION['errors'] = ["Mailer Error: Could not send OTP. Try again later."];
    header("Location: signup.php");
    exit();
}
?>
