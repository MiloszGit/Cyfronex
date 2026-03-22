<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
    <title>Login panel</title>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if(isset($_GET['error'])): ?>
        <p style="color:red;">Zły login lub hasło</p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>User name</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="login-button">log in</button>
    </form>

    <p>You don't have an account? <a href="register.php">sign up</a></p>

    <div class="footerBottom">
        <p>Copyright &copy;2026 <span class="designer">Radosław Mazur Miłosz Biniek</span></p>
    </div>
</div>

</body>
</html>