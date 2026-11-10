<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Niezalogowany']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Bledna metoda']);
    exit;
}

$points = isset($_POST['points']) ? intval($_POST['points']) : 0;

if ($points <= 0 || $points > 500) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidlowa liczba punktow']);
    exit;
}

$username = mysqli_real_escape_string($conn, $_SESSION['user']);

$update = mysqli_query($conn, "UPDATE users SET punkty = punkty + $points WHERE username = '$username'");

if ($update) {
    $res = mysqli_query($conn, "SELECT punkty FROM users WHERE username = '$username'");
    $row = mysqli_fetch_assoc($res);
    $total = $row ? intval($row['punkty']) : 0;

    echo json_encode([
        'success' => true,
        'added'   => $points,
        'total'   => $total
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Blad bazy danych']);
}
