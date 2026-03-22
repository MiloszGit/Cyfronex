<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = $1";
$result = pg_query_params($conn, $query, array($username));

$user = pg_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $username;
    header("Location: home.php");
    exit;
} else {
    header("Location: index.php?error=1");
    exit;
}