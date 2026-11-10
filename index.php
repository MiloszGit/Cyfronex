<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Zaloguj sie</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<!-- Floating math symbols background -->
<div class="math-bg" id="mathBg"></div>

<div class="auth-page">

    <!-- ===== Left Panel - Branding ===== -->
    <div class="auth-left">
        <a class="brand-logo" href="#">
            <img src="img/logo.svg" alt="Cyfronex logo">
            <span class="brand-name">Cyfronex</span>
        </a>

        <h1 class="brand-tagline">
            Naucz sie matematyki<br>
            <span class="hl-purple">szybciej</span> i
            <span class="hl-teal">madrzej</span>
        </h1>

        <p class="brand-desc">
            Interaktywne zadania, natychmiastowe wyjasnienia i spersonalizowane sciezki nauki &mdash; wszystko w jednym miejscu. Matematyka jeszcze nigdy nie byla tak przystepna.
        </p>

        <div class="feature-pills">
            <span class="pill"><span class="pill-icon">&#x1F9EE;</span> Setki zadan</span>
            <span class="pill"><span class="pill-icon">&#x1F4C8;</span> Sledzenie postepu</span>
            <span class="pill"><span class="pill-icon">&#x26A1;</span> Natychmiastowa pomoc</span>
            <span class="pill"><span class="pill-icon">&#x1F3C6;</span> Rankingi i osiagniecia</span>
        </div>
    </div>

    <!-- ===== Right Panel - Login Form ===== -->
    <div class="auth-right">
        <div class="auth-card">
            <div class="card-header">
                <h2>Witaj z powrotem &#x1F44B;</h2>
                <p>Zaloguj sie, aby kontynuowac nauke</p>
            </div>

            <?php if(isset($_GET['error'])): ?>
            <div class="error-msg">
                <span>&#x26A0;&#xFE0F;</span>
                <span>Nieprawidlowy login lub haslo. Sprobuj ponownie.</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['success'])): ?>
            <div class="error-msg" style="background:rgba(0,212,170,.12);border-color:rgba(0,212,170,.35);color:#6ffce8;">
                <span>&#x2705;</span>
                <span>Konto zostalo utworzone! Mozesz sie teraz zalogowac.</span>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Nazwa uzytkownika</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x1F464;</span>
                        <input type="text" id="username" name="username" placeholder="Wpisz nazwe uzytkownika" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Haslo</label>
                    <div class="input-wrap">
                        <span class="input-icon">&#x1F512;</span>
                        <input type="password" id="password" name="password" placeholder="Wpisz haslo" required>
                    </div>
                </div>

                <a class="forgot-link" href="Forgot-password.html">Zapomniales hasla?</a>

                <button type="submit" class="btn-primary">Zaloguj sie &rarr;</button>
            </form>

            <div class="divider">lub</div>

            <p class="switch-link">
                Nie masz konta? <a href="register.php">Zarejestruj sie</a>
            </p>
        </div>
    </div>

</div>

<p class="auth-footer">Copyright &copy;2026 <span>Radoslaw Mazur &amp; Milosz Biniek</span></p>

<script>
    const symbols = ['\u03A3','\u222B','\u221A','\u03C0','\u221E','\u2248','\u0394','\u2202','\u2207','\u03B1','\u03B2','\u03B3','\u03BB','\u03B8','\u2208','\u2200','\u2203','\u00B1','\u00D7','\u00F7','\u00B2','\u00B3'];
    const container = document.getElementById('mathBg');
    const count = 22;

    for (let i = 0; i < count; i++) {
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
