<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];

    // Allowed file types
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        $_SESSION['errors'] = ["Invalid file type. Only JPG, PNG, GIF allowed."];
        header("Location: home.php");
        exit();
    }

    // Save file
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . "." . $ext;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Update DB
        $stmt = $con->prepare("UPDATE devdata SET profile_image=? WHERE email=?");
        $stmt->bind_param("ss", $filePath, $email);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Profile image updated!";
    } else {
        $_SESSION['errors'] = ["Failed to upload image."];
    }
}

header("Location: home.php");
exit();
