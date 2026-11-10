<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include 'db.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

$query  = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$user   = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['username'];
    header("Location: home.php");
    exit;
} else {
    header("Location: index.php?error=1");
    exit;
}
