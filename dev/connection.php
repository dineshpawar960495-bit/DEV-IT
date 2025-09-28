<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'dev';

$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
    die(json_encode(['status'=>'error','message'=>'Database connection failed: ' . $con->connect_error]));
}
// REMOVE any echo like "Database connected successfully!"
?>
