<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$currentUser = $_SESSION['user'];

$result = pg_query_params(
    $conn,
    "SELECT * FROM users WHERE username = $1",
    array($currentUser)
);

$user = pg_fetch_assoc($result);

if (!$user) {
    die("User not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];

    if (!empty($newPassword)) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = "UPDATE users SET username=$1, email=$2, password=$3 WHERE username=$4";
        $params = array($newUsername, $newEmail, $hashed, $currentUser);
    } else {
        $update = "UPDATE users SET username=$1, email=$2 WHERE username=$3";
        $params = array($newUsername, $newEmail, $currentUser);
    }

    $res = pg_query_params($conn, $update, $params);

    if ($res) {

        $_SESSION['user'] = $newUsername;

        header("Location: settings.php?saved=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Ustawienia</title>
    <link rel="stylesheet" href="settings.css">
</head>

<body>

<div class="hero">

    <div class="login-box">
<div class="contex">
    <select id="lang">
        <option value="pl">🇵🇱 Polski</option>
        <option value="en">🇬🇧 English</option>
        <option value="de">🇩🇪 Deutsch</option>
    </select>
</div>
        <h1 id="settings_tittle">Ustawienia</h1>

        <?php if (isset($_GET['saved'])): ?>
            <p id="saving" style="color:#00ff88;">Zapisano</p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="username"
                value="<?php echo htmlspecialchars($user['username']); ?>">

            <input type="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>">

            <input type="password" name="password"
                placeholder="Nowe hasło (opcjonalnie)">

            <button id="save" type="submit">Zapisz</button>

        </form>

        <a id="back" href="home.php">Powrót</a>

    </div>

</div>
<script src="script.js"></script>
<script src="load_lang.js"></script>
</body>
</html>