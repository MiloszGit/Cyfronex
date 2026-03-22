<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm-password'];

    if ($password !== $confirm) {
        header("Location: register.php?error=hasla");
        exit;
    }

    $check = pg_query_params($conn, "SELECT id FROM users WHERE username = $1", array($username));
    if (pg_fetch_assoc($check)) {
        header("Location: register.php?error=exists");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, array($username, $email, $hashed));

    if ($result) {
        header("Location: index.php?success=1");
        exit;
    } else {
        header("Location: register.php?error=db");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Registration panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container"> 
    <h2>Registration</h2>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'hasla'): ?>
        <p style="color:red;">Hasła się nie zgadzają!</p>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
        <p style="color:red;">Taki użytkownik już istnieje!</p>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'db'): ?>
        <p style="color:red;">Błąd bazy danych</p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label>User name</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Email address</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="confirm-password" required>
        </div>

        <button type="submit" class="login-button">Sign up</button>
    </form>

    <p>Already have an account? <a href="index.php">Log in</a></p>

    <div class="footerBottom">
        <p>Copyright &copy;2026 <span class="designer">Radosław Mazur Miłosz Biniek</span></p>
    </div>
</div>

</body>
</html>