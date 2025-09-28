<?php
session_start();
require 'connection.php';

if(isset($_POST['login'])){
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = [];

    if(!$email) $errors[] = "Email is required.";
    if(!$password) $errors[] = "Password is required.";

    if(count($errors) === 0){
        // Fetch user from database
        $stmt = $con->prepare("SELECT password, verified FROM devdata WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if(!$user){
            $errors[] = "Email not registered. Please signup first.";
        } else {
            if($user['verified'] == 0){
                $errors[] = "Account not verified. Please verify your email first.";
            } else if(!password_verify($password, $user['password'])){
                $errors[] = "Incorrect password.";
            } else {
                // Login successful
                $_SESSION['email'] = $email;
                $_SESSION['success'] = "Login successful!";
                header("Location: home.php");
                exit;
            }
        }
    }

    // If errors, redirect back with errors
    $_SESSION['login_errors'] = $errors;
    header("Location: login.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
