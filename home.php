<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';

$username = $_SESSION['user'];
$u = mysqli_real_escape_string($conn, $username);
$userQuery = mysqli_query($conn, "SELECT klasa, punkty FROM users WHERE username = '$u'");
$userData  = mysqli_fetch_assoc($userQuery);

$userKlasa  = $userData && isset($userData['klasa']) ? intval($userData['klasa']) : 1;
$userPoints = $userData && isset($userData['punkty']) ? intval($userData['punkty']) : 0;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Panel Glowny</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/logos.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        .points-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: linear-gradient(135deg, rgba(255, 184, 0, 0.15) 0%, rgba(255, 107, 107, 0.15) 100%);
            border: 1px solid rgba(255, 184, 0, 0.35);
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffd166;
            box-shadow: 0 0 12px rgba(255, 184, 0, 0.2);
            text-decoration: none;
        }
        .leaderboard-btn {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(108, 99, 255, 0.15);
            border: 1px solid rgba(108, 99, 255, 0.4);
            color: #c2bcff;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all var(--transition);
        }
        .leaderboard-btn:hover {
            background: rgba(108, 99, 255, 0.3);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 0 14px var(--accent-glow);
        }
        .user-assigned-badge {
            background: rgba(0, 212, 170, 0.2);
            border: 1px solid rgba(0, 212, 170, 0.5);
            color: #6ffce8;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-left: auto;
        }
        .class-card.my-class {
            border: 2px solid var(--accent-teal);
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.12) 0%, rgba(108, 99, 255, 0.1) 100%);
            box-shadow: 0 0 25px rgba(0, 212, 170, 0.25);
        }
    </style>
</head>
<body>

<div class="math-bg" id="mathBg"></div>

<div class="app-layout">

    <!-- Top Navigation Bar -->
    <header class="app-header">
        <div class="header-left">
            <a href="home.php" class="brand-logo">
                <img src="img/logo.svg" alt="Cyfronex logo" class="app-logo">
                <span class="brand-name">Cyfronex</span>
            </a>
        </div>

        <div class="header-right">
            <!-- Points Counter -->
            <div class="points-badge" title="Twoje punkty">
                <span>&#x2B50;</span>
                <span id="userPointsTotal"><?php echo $userPoints; ?></span>
                <span data-i18n="pts_label" style="font-size:0.75rem; opacity:0.8;">PKT</span>
            </div>

            <!-- Leaderboard Link -->
            <a href="leaderboard.php" class="leaderboard-btn" title="Ranking">
                <span>&#x1F3C6;</span>
                <span data-i18n="leaderboard_nav">Ranking</span>
            </a>

            <!-- Language Selector -->
            <div class="lang-selector-wrap">
                <select id="lang">
                    <option value="pl">&#x1F1F5;&#x1F1F1; Polski</option>
                    <option value="en">&#x1F1EC;&#x1F1E7; English</option>
                    <option value="de">&#x1F1E9;&#x1F1EA; Deutsch</option>
                </select>
            </div>

            <!-- User Menu Trigger -->
            <div class="user-menu-container">
                <div class="user-avatar-btn" onclick="toggleUserMenu()" title="Profil">
                    <span class="avatar-letter"><?php echo strtoupper(substr($username, 0, 1)); ?></span>
                    <span class="username-label"><?php echo htmlspecialchars($username); ?></span>
                    <span class="dropdown-chevron">&#x25BE;</span>
                </div>

                <!-- Dropdown -->
                <div class="sub-menu-wrap" id="subMenu">
                    <div class="sub-menu">
                        <div class="user-info-box">
                            <div class="user-avatar-large">
                                <?php echo strtoupper(substr($username, 0, 1)); ?>
                            </div>
                            <div class="user-details">
                                <h3 id="userName"><?php echo htmlspecialchars($username); ?></h3>
                                <p class="user-role">
                                    <span data-i18n="student_account">Konto ucznia</span> &bull; 
                                    <span data-i18n="class<?php echo $userKlasa; ?>">Klasa <?php echo $userKlasa; ?></span>
                                </p>
                            </div>
                        </div>

                        <hr class="menu-divider">

                        <a href="leaderboard.php" class="sub-menu-link">
                            <span class="menu-icon">&#x1F3C6;</span>
                            <span data-i18n="leaderboard_nav">Ranking graczy</span>
                        </a>

                        <a href="settings.php" class="sub-menu-link">
                            <span class="menu-icon">&#x2699;&#xFE0F;</span>
                            <span id="menuSettings" data-i18n="settings">Ustawienia</span>
                        </a>

                        <a href="help.html" class="sub-menu-link">
                            <span class="menu-icon">&#x2753;</span>
                            <span id="menuHelp" data-i18n="help">Pomoc i kontakt</span>
                        </a>

                        <a href="logout.php" class="sub-menu-link logout-link">
                            <span class="menu-icon">&#x1F6AA;</span>
                            <span id="menuLogout" data-i18n="logout">Wyloguj sie</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="app-main">

        <!-- Welcome Banner -->
        <section class="welcome-card">
            <div class="welcome-text">
                <span class="badge">&#x1F4DA; <span data-i18n="edu_panel">Panel edukacyjny</span> &bull; <span data-i18n="your_class">Twoja klasa</span>: <?php echo $userKlasa; ?></span>
                <h1><span data-i18n="welcome_title">Witaj,</span> <span class="highlight"><?php echo htmlspecialchars($username); ?></span>!</h1>
                <p id="content" class="welcome-desc" data-i18n="message">Rozwiazuj zadania matematyczne ze swojej klasy, zbieraj punkty i szlifuj swoje umiejetnosci!</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-pill">
                    <span class="stat-icon">&#x2B50;</span>
                    <div>
                        <div class="stat-value"><?php echo $userPoints; ?> <span data-i18n="pts_label">PKT</span></div>
                        <div class="stat-label" data-i18n="your_score">Twoj wynik</div>
                    </div>
                </div>
                <div class="stat-pill">
                    <span class="stat-icon">&#x1F393;</span>
                    <div>
                        <div class="stat-value"><span data-i18n="class<?php echo $userKlasa; ?>">Klasa <?php echo $userKlasa; ?></span></div>
                        <div class="stat-label" data-i18n="your_profile">Twoj profil</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Classes Grid -->
        <section class="classes-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
                <h2 class="section-title" style="margin-bottom:0;" data-i18n="select_level">Wybierz poziom zadan</h2>
                <span style="font-size:0.9rem; color:var(--text-muted);">
                    <span data-i18n="pts_reward_info">Za kazda poprawna odpowiedz otrzymujesz</span> <b style="color:#ffd166;">+10 <span data-i18n="pts_label">PKT</span></b>
                </span>
            </div>
            
            <div class="classes-grid">
                <?php for($i = 1; $i <= 8; $i++): 
                    $isMy = ($i === $userKlasa);
                    $icons = [
                        1 => '&#x1F522;', 2 => '&#x2795;', 3 => '&#x2716;&#xFE0F;', 4 => '&#x1F9E9;',
                        5 => '&#x1F4CA;', 6 => '&#x2696;&#xFE0F;', 7 => '&#x1F50E;', 8 => '&#x1F393;'
                    ];
                ?>
                <a href="start.php?klasa=<?php echo $i; ?>" class="class-card <?php echo $isMy ? 'my-class' : ''; ?> <?php echo ($i === 8) ? 'highlight-card' : ''; ?>">
                    <div class="class-card-header">
                        <span class="class-number">0<?php echo $i; ?></span>
                        <?php if ($isMy): ?>
                            <span class="user-assigned-badge">&#x2714; <span data-i18n="your_class">Twoja klasa</span></span>
                        <?php endif; ?>
                        <span class="class-icon"><?php echo $icons[$i]; ?></span>
                    </div>
                    <h3 id="class<?php echo $i; ?>" data-i18n="class<?php echo $i; ?>">Klasa <?php echo $i; ?></h3>
                    <p class="class-desc" id="class<?php echo $i; ?>_desc" data-i18n="class<?php echo $i; ?>_desc"></p>
                    <span class="start-badge"><span data-i18n="start_quiz_btn">Rozpocznij quiz</span> &rarr;</span>
                </a>
                <?php endfor; ?>
            </div>
        </section>

        <!-- Hidden menu for script.js compatibility -->
        <nav id="menu" style="display:none;"></nav>

        <!-- Coffee Support Banner -->
        <section class="coffee-card">
            <div class="coffee-content">
                <span class="coffee-icon">&#x2615;</span>
                <div>
                    <h3 id="buyCoffee" data-i18n="buyCoffee">Wesprzyj projekt Cyfronex</h3>
                    <p data-i18n="coffee_desc">Podoba Ci sie ta aplikacja? Mozesz postawic nam wirtualna kawe i pomoc w jej dalszym rozwoju!</p>
                </div>
            </div>
            <a href="https://ko-fi.com/Cyfronex" target="_blank" rel="noopener noreferrer" class="coffee-btn" data-i18n="buy_coffee_btn">
                Postaw kawe &rarr;
            </a>
        </section>

    </main>

    <!-- Footer -->
    <footer class="app-footer">
        <p>Copyright &copy;2026 <span class="designer">Radoslaw Mazur &amp; Milosz Biniek</span> &mdash; Cyfronex Platforma Edukacyjna</p>
    </footer>

</div>

<script>
function toggleUserMenu() {
    const subMenu = document.getElementById("subMenu");
    subMenu.classList.toggle("open-menu");
}

document.addEventListener('click', function(e) {
    const container = document.querySelector('.user-menu-container');
    const subMenu = document.getElementById("subMenu");
    if (container && !container.contains(e.target)) {
        subMenu.classList.remove("open-menu");
    }
});

const symbols = ['\u03A3','\u222B','\u221A','\u03C0','\u221E','\u2248','\u0394','\u2202','\u2207','\u03B1','\u03B2','\u03B3','\u03BB','\u03B8','\u2208','\u2200','\u2203','\u00B1','\u00D7','\u00F7','\u00B2','\u00B3'];
const mathBg = document.getElementById('mathBg');
for (let i = 0; i < 24; i++) {
    const span = document.createElement('span');
    span.textContent = symbols[Math.floor(Math.random() * symbols.length)];
    span.style.left = Math.random() * 100 + 'vw';
    span.style.animationDuration = (16 + Math.random() * 20) + 's';
    span.style.animationDelay = (Math.random() * -30) + 's';
    span.style.fontSize = (1 + Math.random() * 1.8) + 'rem';
    span.style.opacity = Math.random() * 0.45 + 0.05;
    mathBg.appendChild(span);
}
</script>

<script src="script.js"></script>
<script src="load_lang.js"></script>

</body>
</html>
