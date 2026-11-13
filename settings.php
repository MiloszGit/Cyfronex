<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$currentUser = $_SESSION['user'];

$u = mysqli_real_escape_string($conn, $currentUser);
$result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
$user   = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found");
}

$currentKlasa = isset($user['klasa']) ? intval($user['klasa']) : 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $newPassword = $_POST['password'];
    $newKlasa = isset($_POST['klasa']) ? intval($_POST['klasa']) : $currentKlasa;
    if ($newKlasa < 1 || $newKlasa > 8) {
        $newKlasa = 1;
    }

    $nu = mysqli_real_escape_string($conn, $newUsername);
    $ne = mysqli_real_escape_string($conn, $newEmail);
    $cu = mysqli_real_escape_string($conn, $currentUser);

    if (!empty($newPassword)) {
        $hashed = mysqli_real_escape_string($conn, password_hash($newPassword, PASSWORD_DEFAULT));
        $res = mysqli_query($conn,
            "UPDATE users SET username='$nu', email='$ne', klasa=$newKlasa, password='$hashed' WHERE username='$cu'"
        );
    } else {
        $res = mysqli_query($conn,
            "UPDATE users SET username='$nu', email='$ne', klasa=$newKlasa WHERE username='$cu'"
        );
    }

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Ustawienia</title>
    <link rel="stylesheet" href="login.css">
    <link rel="shortcut icon" href="img/logos.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo.svg" type="image/svg+xml">    
    <style>
        .class-selector-group {
            margin-bottom: 1.2rem;
        }
        .class-selector-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }
        .class-pills-wrap {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }
        .class-radio-item input[type="radio"] {
            display: none;
        }
        .class-radio-item label {
            display: block;
            text-align: center;
            padding: 0.6rem 0.2rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: var(--radius-input);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all var(--transition);
            margin-bottom: 0;
            text-transform: none;
            letter-spacing: normal;
        }
        .class-radio-item input[type="radio"]:checked + label {
            background: rgba(108, 99, 255, 0.25);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 0 12px rgba(108, 99, 255, 0.4);
        }
        .class-radio-item label:hover {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>

<div class="math-bg" id="mathBg"></div>

<div class="auth-page" style="grid-template-columns: 1fr; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem 1rem;">

    <div class="auth-card" style="max-width: 500px; width: 100%;">
        
        <!-- Header with Language selector -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <a class="brand-logo" href="home.php">
                <img src="img/logo.svg" alt="Cyfronex logo" style="width: 44px; height: 44px;">
                <span class="brand-name" style="font-size: 1.3rem;">Cyfronex</span>
            </a>
            <div>
                <select id="lang" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.4rem 0.7rem; border-radius: 8px; font-size: 0.85rem; outline: none;">
                    <option value="pl" style="background:#111;">&#x1F1F5;&#x1F1F1; PL</option>
                    <option value="en" style="background:#111;">&#x1F1EC;&#x1F1E7; EN</option>
                    <option value="de" style="background:#111;">&#x1F1E9;&#x1F1EA; DE</option>
                </select>
            </div>
        </div>

        <div class="card-header" style="text-align: left; margin-bottom: 1.5rem;">
            <h2 id="settings_tittle" data-i18n="settings_tittle">Ustawienia profilu</h2>
            <p data-i18n="settings_subtitle">Edytuj swoje dane, zmien klase lub ustaw nowe haslo</p>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="error-msg" style="background:rgba(0,212,170,.12);border-color:rgba(0,212,170,.35);color:#6ffce8;">
                <span>&#x2705;</span>
                <span id="saving" data-i18n="saving">Zmiany zostaly pomyslnie zapisane!</span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username" data-i18n="username_label">Nazwa uzytkownika</label>
                <div class="input-wrap">
                    <span class="input-icon">&#x1F464;</span>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email" data-i18n="email_label">Adres e-mail</label>
                <div class="input-wrap">
                    <span class="input-icon">&#x2709;&#xFE0F;</span>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>

            <!-- Zmiana klasy -->
            <div class="class-selector-group">
                <label data-i18n="change_class_label">Twoja klasa</label>
                <div class="class-pills-wrap">
                    <?php for($i = 1; $i <= 8; $i++): ?>
                    <div class="class-radio-item">
                        <input type="radio" id="klasa<?php echo $i; ?>" name="klasa" value="<?php echo $i; ?>" <?php echo ($currentKlasa === $i) ? 'checked' : ''; ?>>
                        <label for="klasa<?php echo $i; ?>" data-i18n="class<?php echo $i; ?>">Klasa <?php echo $i; ?></label>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="password" data-i18n="password_change_label">Nowe haslo (pozostaw puste, aby nie zmieniac)</label>
                <div class="input-wrap">
                    <span class="input-icon">&#x1F512;</span>
                    <input type="password" id="password" name="password" placeholder="Wpisz nowe haslo">
                </div>
            </div>

            <button id="save" type="submit" class="btn-primary" style="margin-top: 0.5rem;" data-i18n="save">Zapisz zmiany &rarr;</button>
        </form>

        <p class="switch-link" style="margin-top: 1.5rem;">
            <a id="back" href="home.php">&larr; <span data-i18n="back_to_panel">Powrot do panelu glownego</span></a>
        </p>

    </div>

</div>

<p class="auth-footer">Copyright &copy;2026 <span> Milosz Biniek</span></p>

<script>
    const symbols = ['\u03A3','\u222B','\u221A','\u03C0','\u221E','\u2248','\u0394','\u2202','\u2207','\u03B1','\u03B2','\u03B3','\u03BB','\u03B8','\u2208','\u2200','\u2203','\u00B1','\u00D7','\u00F7','\u00B2','\u00B3'];
    const container = document.getElementById('mathBg');
    for (let i = 0; i < 22; i++) {
        const span = document.createElement('span');
        span.textContent = symbols[Math.floor(Math.random() * symbols.length)];
        span.style.left = Math.random() * 100 + 'vw';
        span.style.animationDuration = (14 + Math.random() * 20) + 's';
        span.style.animationDelay = (Math.random() * -30) + 's';
        span.style.fontSize = (1 + Math.random() * 1.8) + 'rem';
        span.style.opacity = Math.random() * 0.5 + 0.05;
        container.appendChild(span);
    }
</script>

<script src="script.js"></script>
<script src="load_lang.js"></script>
</body>
</html>
