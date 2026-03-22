<?php
session_start();
require_once "db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = pg_query_params(
        $conn,
        "SELECT * FROM users WHERE username = $1",
        [$username]
    );

    $user = pg_fetch_assoc($stmt);

    if ($user && password_verify($password, $user['password'])) {

        if (!$user['is_admin']) {
            $error = "❌ Nie masz uprawnień admina";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: admin.php");
            exit();
        }

    } else {
        $error = "❌ Błędne dane logowania";
    }
}

$is_admin = false;

if (isset($_SESSION['user_id'])) {
    $my_id = (int) $_SESSION['user_id'];

    $stmt = pg_query_params(
        $conn,
        "SELECT is_admin FROM users WHERE id = $1",
        [$my_id]
    );

    $user = pg_fetch_assoc($stmt);

    if ($user && $user['is_admin'] === 't') {
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
body {
    font-family: Arial;
    background: #2c2f33;
    color: white;
    text-align: center;
}

.box {
    background: #23272a;
    padding: 30px;
    border-radius: 10px;
    width: 300px;
    margin: 100px auto;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
}

button {
    padding: 10px;
    width: 100%;
    background: #5865f2;
    color: white;
    border: none;
    cursor: pointer;
}

table {
    margin: auto;
    margin-top: 40px;
    border-collapse: collapse;
    width: 80%;
}

td, th {
    padding: 12px;
    border: 1px solid #444;
}

a {
    color: #00d1ff;
    text-decoration: none;
}

.admin {
    color: #2ecc71;
    font-weight: bold;
}
</style>

</head>
<body>

<?php if (!$is_admin): ?>

<div class="box">
    <h2>🔐 Admin Login</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Login" required>
        <input type="password" name="password" placeholder="Hasło" required>
        <button name="login">Zaloguj</button>
    </form>
</div>

<?php else: ?>

<?php
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id != $_SESSION['user_id']) {

        pg_query_params($conn,
            "DELETE FROM messages WHERE sender_id = $1 OR receiver_id = $1",
            [$id]
        );

        pg_query_params($conn,
            "DELETE FROM friends WHERE user_id = $1 OR friend_id = $1",
            [$id]
        );

        pg_query_params($conn,
            "DELETE FROM users WHERE id = $1",
            [$id]
        );
    }

    header("Location: admin.php");
    exit();
}

if (isset($_GET['make_admin'])) {
    $id = (int) $_GET['make_admin'];

    pg_query_params(
        $conn,
        "UPDATE users SET is_admin = TRUE WHERE id = $1",
        [$id]
    );

    header("Location: admin.php");
    exit();
}

if (isset($_GET['remove_admin'])) {
    $id = (int) $_GET['remove_admin'];

    if ($id != $_SESSION['user_id']) {
        pg_query_params(
            $conn,
            "UPDATE users SET is_admin = FALSE WHERE id = $1",
            [$id]
        );
    }

    header("Location: admin.php");
    exit();
}

$res = pg_query(
    $conn,
    "SELECT id, username, email, is_admin FROM users ORDER BY id DESC"
);
?>

<h2>👑 Panel admina</h2>

<table>
<tr>
    <th>ID</th>
    <th>Nick</th>
    <th>Email</th>
    <th>Rola</th>
    <th>Akcje</th>
</tr>

<?php while ($u = pg_fetch_assoc($res)): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>

    <td>
        <?php if ($u['is_admin'] === 't'): ?>
            <span class="admin">ADMIN</span>
        <?php else: ?>
            user
        <?php endif; ?>
    </td>

    <td>
        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Usunąć?')">🗑️</a>

        <?php if ($u['is_admin'] !== 't'): ?>
            <a href="?make_admin=<?= $u['id'] ?>">⭐</a>
        <?php else: ?>
            <a href="?remove_admin=<?= $u['id'] ?>">❌</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

<br><br>
<a href="?logout=1">🚪 Wyloguj</a>

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