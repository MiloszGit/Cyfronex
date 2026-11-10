<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
require_once "db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($stmt);

    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_admin']) {
            $error = "Nie masz uprawnien admina";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: admin.php");
            exit();
        }
    } else {
        $error = "Bledne dane logowania";
    }
}

$is_admin = false;

if (isset($_SESSION['user_id'])) {
    $my_id = (int) $_SESSION['user_id'];
    $stmt  = mysqli_query($conn, "SELECT is_admin FROM users WHERE id = $my_id");
    $user  = mysqli_fetch_assoc($stmt);
    if ($user && $user['is_admin'] == 1) {
        $is_admin = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Panel admina</title>
<style>
body { font-family: Arial; background: #2c2f33; color: white; text-align: center; }
.box { background: #23272a; padding: 30px; border-radius: 10px; width: 300px; margin: 100px auto; }
input { width: 100%; padding: 10px; margin: 10px 0; }
button { padding: 10px; width: 100%; background: #5865f2; color: white; border: none; cursor: pointer; }
table { margin: auto; margin-top: 40px; border-collapse: collapse; width: 80%; }
td, th { padding: 12px; border: 1px solid #444; }
a { color: #00d1ff; text-decoration: none; }
.admin { color: #2ecc71; font-weight: bold; }
</style>
</head>
<body>

<?php if (!$is_admin): ?>

<div class="box">
    <h2>&#x1F510; Admin Login</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Login" required>
        <input type="password" name="password" placeholder="Haslo" required>
        <button name="login">Zaloguj</button>
    </form>
</div>

<?php else: ?>

<?php
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM messages WHERE sender_id = $id OR receiver_id = $id");
        mysqli_query($conn, "DELETE FROM friends WHERE user_id = $id OR friend_id = $id");
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    }
    header("Location: admin.php");
    exit();
}

if (isset($_GET['make_admin'])) {
    $id = (int) $_GET['make_admin'];
    mysqli_query($conn, "UPDATE users SET is_admin = 1 WHERE id = $id");
    header("Location: admin.php");
    exit();
}

if (isset($_GET['remove_admin'])) {
    $id = (int) $_GET['remove_admin'];
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "UPDATE users SET is_admin = 0 WHERE id = $id");
    }
    header("Location: admin.php");
    exit();
}

$res = mysqli_query($conn, "SELECT id, username, email, is_admin FROM users ORDER BY id DESC");
?>

<h2>&#x1F451; Panel admina</h2>

<table>
<tr>
    <th>ID</th><th>Nick</th><th>Email</th><th>Rola</th><th>Akcje</th>
</tr>

<?php while ($u = mysqli_fetch_assoc($res)): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td>
        <?php if ($u['is_admin'] == 1): ?>
            <span class="admin">ADMIN</span>
        <?php else: ?>
            user
        <?php endif; ?>
    </td>
    <td>
        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Usunac?')">&#x1F5D1;&#xFE0F;</a>
        <?php if ($u['is_admin'] != 1): ?>
            <a href="?make_admin=<?= $u['id'] ?>">&#x2B50;</a>
        <?php else: ?>
            <a href="?remove_admin=<?= $u['id'] ?>">&#x274C;</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

<br><br>
<a href="?logout=1">&#x1F6AA; Wyloguj</a>

<?php endif; ?>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>

</body>
</html>
