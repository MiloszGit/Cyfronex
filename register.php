<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm-password'];
    $klasa    = isset($_POST['klasa']) ? intval($_POST['klasa']) : 1;

    if ($klasa < 1 || $klasa > 8) {
        $klasa = 1;
    }

    if ($password !== $confirm) {
        header("Location: register.php?error=hasla");
        exit;
    }

    $u = mysqli_real_escape_string($conn, $username);
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$u'");
    if (mysqli_fetch_assoc($check)) {
        header("Location: register.php?error=exists");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $e = mysqli_real_escape_string($conn, $email);
    $h = mysqli_real_escape_string($conn, $hashed);

    $result = mysqli_query($conn,
        "INSERT INTO users (username, email, password, klasa, punkty) VALUES ('$u', '$e', '$h', $klasa, 0)"
    );

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
    <link rel="icon" href="img/logo.svg" type="image/svg+xml">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Rejestracja</title>
    <link rel="stylesheet" href="login.css">
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

<div class="auth-page">

    <div class="auth-left">
        <a class="brand-logo" href="index.php">
            <img src="img/logo.svg" alt="Cyfronex logo">
            <span class="brand-name">Cyfronex</span>
        </a>

        <h1 class="brand-tagline">
            Zacznij swoja<br>
            przygode z<br>
            <span class="hl-purple">matematyka</span>
        </h1>

        <p class="brand-desc">
            Dolacz do tysiecy uczniow! Wybierz swoja klase podczas rejestracji, rozwiazuj zadania, zdobywaj punkty i wspinaj sie na szczyt rankingu.
        </p>

        <div class="feature-pills">
            <span class="pill"><span class="pill-icon">&#x2B50;</span> Punkty za poprawne odpowiedzi</span>
            <span class="pill"><span class="pill-icon">&#x1F393;</span> Zadania dopasowane do Twojej klasy</span>
            <span class="pill"><span class="pill-icon">&#x1F4CA;</span> Sledzenie rozwoju</span>
            <span class="pill"><span class="pill-icon">&#x1F9EE;</span> 100+ zadan w bazie</span>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-card">
            <div class="card-header">
                <h2>Utworz konto &#x2728;</h2>
                <p>Wybierz swoja klase i zacznij zbierac punkty!</p>
            </div>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'hasla'): ?>
            <div class="error-msg">
                <span>&#x26A0;&#xFE0F;</span>
                <span>Hasla sie nie zgadzaja. Sprawdz i sprobuj ponownie.</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
            <div class="error-msg">
                <span>&#x26A0;&#xFE0F;</span>
                <span>Ta nazwa uzytkownika jest juz zajeta.</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'db'): ?>
            <div class="error-msg">
                <span>&#x26A0;&#xFE0F;</span>
                <span>Blad bazy danych. Sprobuj ponownie pozniej.</span>
            </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Nazwa uzytkownika</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x1F464;</span>
                        <input type="text" id="username" name="username" placeholder="Wybierz unikalna nazwe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Adres e-mail</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x2709;&#xFE0F;</span>
                        <input type="email" id="email" name="email" placeholder="twoj@email.com" required>
                    </div>
                </div>

                <!-- Wybor klasy -->
                <div class="class-selector-group">
                    <label>Do ktorej klasy chodzisz?</label>
                    <div class="class-pills-wrap">
                        <div class="class-radio-item">
                            <input type="radio" id="klasa1" name="klasa" value="1" checked>
                            <label for="klasa1">Klasa 1</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa2" name="klasa" value="2">
                            <label for="klasa2">Klasa 2</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa3" name="klasa" value="3">
                            <label for="klasa3">Klasa 3</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa4" name="klasa" value="4">
                            <label for="klasa4">Klasa 4</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa5" name="klasa" value="5">
                            <label for="klasa5">Klasa 5</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa6" name="klasa" value="6">
                            <label for="klasa6">Klasa 6</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa7" name="klasa" value="7">
                            <label for="klasa7">Klasa 7</label>
                        </div>
                        <div class="class-radio-item">
                            <input type="radio" id="klasa8" name="klasa" value="8">
                            <label for="klasa8">Klasa 8</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Haslo</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x1F512;</span>
                        <input type="password" id="password" name="password" placeholder="Min. 8 znakow" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Potwierdz haslo</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x1F511;</span>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Powtorz haslo" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Zarejestruj sie &rarr;</button>
            </form>

            <div class="divider">juz masz konto?</div>

            <p class="switch-link">
                <a href="index.php">&larr; Wroc do logowania</a>
            </p>
        </div>
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
</body>
</html>
