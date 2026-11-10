<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';
if (!$conn) die("Blad polaczenia z baza danych");

$username = $_SESSION['user'];
$u = mysqli_real_escape_string($conn, $username);
$userQuery = mysqli_query($conn, "SELECT klasa, punkty FROM users WHERE username = '$u'");
$userData  = mysqli_fetch_assoc($userQuery);

$userKlasa  = $userData && isset($userData['klasa']) ? intval($userData['klasa']) : 1;
$userPoints = $userData && isset($userData['punkty']) ? intval($userData['punkty']) : 0;

$klasa = isset($_GET['klasa']) ? intval($_GET['klasa']) : $userKlasa;
if ($klasa < 1 || $klasa > 8) {
    $klasa = 1;
}

$query  = "SELECT tresc, opis, odpowiedz FROM zadania WHERE klasa = $klasa ORDER BY RAND() LIMIT 10";
$result = mysqli_query($conn, $query);
$zadania = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $zadania[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Quiz Matematyczny: Klasa <?php echo $klasa; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/logos.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
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
        .back-nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .back-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            transition: all var(--transition);
        }
        .back-nav-link:hover {
            color: #fff;
            border-color: var(--border-hover);
            background: var(--bg-card-hover);
        }
    </style>
</head>
<body>

<canvas id="confettiCanvas"></canvas>
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

    <!-- Main Content Area -->
    <main class="app-main">

        <div class="back-nav-bar">
            <a href="home.php" class="back-nav-link">
                &larr; <span data-i18n="back_to_panel">Wroc do panelu</span>
            </a>
            <div class="badge" style="margin-bottom:0;">
                &#x1F4DA; <span data-i18n="class<?php echo $klasa; ?>">Klasa <?php echo $klasa; ?></span> &bull; <span data-i18n="task_count">10 zadan</span>
            </div>
        </div>

        <div class="quiz-container-box">

            <!-- 1. START CARD -->
            <div id="start-container" class="quiz-card">
                <span class="badge" style="margin-bottom:1rem;">
                    &#x1F9EE; <span data-i18n="class<?php echo $klasa; ?>">Klasa <?php echo $klasa; ?></span>
                </span>
                
                <h1 id="matematic_quiz" class="welcome-card-h1" data-i18n="matematic_quiz" style="font-family:'Space Grotesk',sans-serif; font-size:clamp(1.8rem, 3.5vw, 2.4rem); font-weight:700; margin-bottom:1rem; color:#fff;">
                    Quiz matematyczny
                </h1>
                
                <p id="start" class="welcome-desc" data-i18n="start" style="margin-bottom:1.8rem; max-width:540px; margin-left:auto; margin-right:auto;">
                    Rozwiaz 10 losowych zadan dostosowanych do Twojego poziomu. Za kazda poprawna odpowiedz otrzymujesz +10 punktow do Twojego profilu!
                </p>

                <div class="quiz-perks">
                    <span class="perk-item">&#x2B50; <b data-i18n="perk_pts">+10 pkt za zadanie</b></span>
                    <span class="perk-item">&#x23F1;&#xFE0F; <b data-i18n="perk_no_time">Bez limitu czasu</b></span>
                    <span class="perk-item">&#x1F3AF; <b data-i18n="perk_instant">Natychmiastowa ocena</b></span>
                </div>

                <button id="startBtn" class="btn-quiz-primary">
                    <span data-i18n="start_challenge">Rozpocznij Wyzwanie</span> &rarr;
                </button>
            </div>

            <!-- 2. QUIZ ACTIVE CARD -->
            <div id="quiz-container" class="quiz-card" style="display:none;">
                
                <!-- Progress bar -->
                <div class="quiz-progress-section">
                    <div class="progress-header">
                        <span>
                            <span data-i18n="task_label">Zadanie</span> 
                            <span id="qCurrent" class="step-label">1</span> 
                            <span data-i18n="of_label">z</span> 
                            <span id="qTotal">10</span>
                        </span>
                        <span style="color:var(--accent-teal); font-weight:700;">
                            <span data-i18n="class<?php echo $klasa; ?>">Klasa <?php echo $klasa; ?></span>
                        </span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" id="progressBar"></div>
                    </div>
                </div>

                <!-- Question Box -->
                <div class="question-box">
                    <div id="tresc">Tresc zadania...</div>
                    <div id="tipBox" class="tip-box">
                        <span>&#x1F4A1;</span>
                        <span id="opis">Podpowiedz</span>
                    </div>
                </div>

                <!-- Answer Input Form -->
                <form id="quizForm" onsubmit="return false;">
                    <div class="answer-input-wrap">
                        <input type="text" id="odpowiedz" placeholder="Twoja odpowiedz..." data-i18n-placeholder="answer_placeholder" autocomplete="off">
                    </div>

                    <button id="checkBtn" type="button" class="btn-quiz-primary">
                        <span data-i18n="check_btn">Sprawdz odpowiedz</span> &#x2714;
                    </button>
                    <button id="nextBtn" type="button" class="btn-quiz-primary" style="display:none;">
                        <span data-i18n="next_btn">Nastepne zadanie</span> &rarr;
                    </button>
                </form>

                <!-- Feedback with icon -->
                <div id="feedback"></div>

                <!-- Floating points animation container -->
                <div id="particleZone"></div>
            </div>

            <!-- 3. RESULTS CARD -->
            <div id="wynik" class="quiz-card" style="display:none;">
                <div style="margin-bottom:1.5rem;">
                    <span class="trophy-icon">&#x1F3C6;</span>
                </div>

                <h2 id="score_quiz" data-i18n="score_quiz" style="font-family:'Space Grotesk',sans-serif; font-size:1.9rem; font-weight:700; color:#fff; margin-bottom:0.5rem;">
                    Gratulacje! Quiz ukonczony
                </h2>
                <p style="color:var(--text-muted); font-size:1rem;" id="resultCheer">
                    Swietna robota, trenuj dalej swoje umiejetnosci matematyczne!
                </p>

                <div class="results-stats-row">
                    <div class="res-stat-card">
                        <div class="res-stat-value score-val"><span id="score">0</span> / 10</div>
                        <div class="res-stat-label" data-i18n="correct_answers">Poprawne odpowiedzi</div>
                    </div>
                    <div class="res-stat-card">
                        <div class="res-stat-value points-val" id="finalPointsEarned">+0</div>
                        <div class="res-stat-label" data-i18n="earned_points">Zdobyte punkty</div>
                    </div>
                </div>

                <div class="quiz-finish-actions">
                    <a href="start.php?klasa=<?php echo $klasa; ?>" class="btn-secondary">
                        &#x1F504; <span data-i18n="play_again">Zagraj ponownie</span>
                    </a>
                    <a href="leaderboard.php" class="btn-secondary">
                        &#x1F3C6; <span data-i18n="leaderboard_nav">Zobacz ranking</span>
                    </a>
                    <a href="home.php" class="btn-quiz-primary">
                        &larr; <span data-i18n="back_to_panel">Wroc do panelu</span>
                    </a>
                </div>
            </div>

        </div>

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

let zadania = <?php echo json_encode($zadania); ?>;
let currentIndex = 0;
let score = 0;
let sessionPoints = 0;

const startContainer   = document.getElementById('start-container');
const quizContainer    = document.getElementById('quiz-container');
const checkBtn         = document.getElementById('checkBtn');
const nextBtn          = document.getElementById('nextBtn');
const feedback         = document.getElementById('feedback');
const trescEl          = document.getElementById('tresc');
const opisEl           = document.getElementById('opis');
const tipBox           = document.getElementById('tipBox');
const odpowiedzEl      = document.getElementById('odpowiedz');
const wynikEl          = document.getElementById('wynik');
const scoreEl          = document.getElementById('score');
const startBtn         = document.getElementById('startBtn');
const qCurrent         = document.getElementById('qCurrent');
const qTotal           = document.getElementById('qTotal');
const progressBar      = document.getElementById('progressBar');
const userPointsTotal  = document.getElementById('userPointsTotal');
const finalPointsEarned = document.getElementById('finalPointsEarned');
const particleZone     = document.getElementById('particleZone');

if (zadania.length > 0) {
    qTotal.innerText = zadania.length;
}

// Web Audio API
const audioCtx = (window.AudioContext || window.webkitAudioContext) ? new (window.AudioContext || window.webkitAudioContext)() : null;

function playSuccessSound() {
    if (!audioCtx) return;
    try {
        const now = audioCtx.currentTime;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(523.25, now);
        osc.frequency.exponentialRampToValueAtTime(659.25, now + 0.1);
        osc.frequency.exponentialRampToValueAtTime(783.99, now + 0.2);
        gain.gain.setValueAtTime(0.2, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.35);
    } catch(e) {}
}

function playWrongSound() {
    if (!audioCtx) return;
    try {
        const now = audioCtx.currentTime;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(220, now);
        osc.frequency.exponentialRampToValueAtTime(160, now + 0.2);
        gain.gain.setValueAtTime(0.2, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.25);
    } catch(e) {}
}

startBtn.onclick = () => {
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    startContainer.style.display = 'none';
    quizContainer.style.display = 'block';
    showZadanie();
};

function showZadanie() {
    if (currentIndex < zadania.length) {
        trescEl.innerText = zadania[currentIndex].tresc;
        const opisText = (zadania[currentIndex].opis || '').trim();
        if (opisText) {
            opisEl.innerText = opisText;
            tipBox.classList.remove('hidden');
        } else {
            tipBox.classList.add('hidden');
        }

        odpowiedzEl.value = '';
        odpowiedzEl.disabled = false;
        feedback.innerText = '';
        feedback.className = '';
        checkBtn.style.display = 'inline-flex';
        nextBtn.style.display = 'none';
        
        qCurrent.innerText = currentIndex + 1;
        progressBar.style.width = ((currentIndex + 1) / zadania.length * 100) + '%';
        odpowiedzEl.focus();
    } else {
        finishQuiz();
    }
}

function spawnPointsParticle(text) {
    const p = document.createElement('div');
    p.className = 'points-float-up';
    p.innerText = text;
    particleZone.appendChild(p);
    setTimeout(() => p.remove(), 1200);
}

function addPointsToAccount(pts) {
    sessionPoints += pts;
    let current = parseInt(userPointsTotal.innerText) || 0;
    userPointsTotal.innerText = current + pts;

    fetch('add_points.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'points=' + encodeURIComponent(pts)
    }).catch(err => console.log('Points sync error:', err));
}

function handleCheck() {
    let odp = odpowiedzEl.value.trim().toLowerCase();
    let poprawna = zadania[currentIndex].odpowiedz.trim().toLowerCase();
    const t = window.currentTranslations || {};

    if (odp === '') {
        feedback.innerHTML = '&#x26A0;&#xFE0F; ' + (t.empty_answer_warn || 'Wpisz swoja odpowiedz!');
        feedback.className = 'feedback-wrong';
        return;
    }

    let isCorrect = (odp === poprawna) || (odp.replace(/\s+/g, '') === poprawna.replace(/\s+/g, ''));

    if (isCorrect) {
        feedback.innerHTML = '&#x2705; ' + (t.correct_feedback || 'Brawo! Poprawna odpowiedz! (+10 PKT)');
        feedback.className = 'feedback-correct';
        score++;
        playSuccessSound();
        spawnPointsParticle('+10 PKT!');
        addPointsToAccount(10);
    } else {
        feedback.innerHTML = '&#x274C; ' + (t.wrong_feedback || 'Blad! Poprawny wynik:') + ' <b>' + zadania[currentIndex].odpowiedz + '</b>';
        feedback.className = 'feedback-wrong';
        playWrongSound();
    }

    odpowiedzEl.disabled = true;
    checkBtn.style.display = 'none';
    nextBtn.style.display = 'inline-flex';
    nextBtn.focus();
}

checkBtn.onclick = handleCheck;

odpowiedzEl.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        if (checkBtn.style.display !== 'none') {
            handleCheck();
        } else if (nextBtn.style.display !== 'none') {
            nextBtn.click();
        }
    }
});

nextBtn.onclick = () => {
    currentIndex++;
    showZadanie();
};

function finishQuiz() {
    quizContainer.style.display = 'none';
    wynikEl.style.display = 'block';
    scoreEl.innerText = score;
    finalPointsEarned.innerText = '+' + sessionPoints + ' PKT';

    const cheerEl = document.getElementById('resultCheer');
    const t = window.currentTranslations || {};

    if (score === 10) {
        cheerEl.innerText = t.cheer_perfect || 'Perfekcyjny wynik! 100% poprawnych zadan! 🏆';
        startConfetti();
    } else if (score >= 7) {
        cheerEl.innerText = t.cheer_great || 'Swietna robota! Matematyka nie ma przed Toba tajemnic! 🎉';
        startConfetti();
    } else if (score >= 4) {
        cheerEl.innerText = t.cheer_good || 'Dobry wynik! Cwicz dalej, a bedzie jeszcze lepiej! 💪';
    } else {
        cheerEl.innerText = t.cheer_keep_trying || 'Kazdy mistrz kiedys zaczynal. Sprobuj jeszcze raz! 📚';
    }
}

// Canvas Confetti
function startConfetti() {
    const canvas = document.getElementById('confettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = [];
    const colors = ['#6c63ff', '#00d4aa', '#ffd166', '#ff5e7e', '#fff'];

    for (let i = 0; i < 100; i++) {
        pieces.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            size: Math.random() * 8 + 4,
            speedY: Math.random() * 3 + 2,
            speedX: (Math.random() - 0.5) * 3,
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: Math.random() * 360,
            rotSpeed: (Math.random() - 0.5) * 6
        });
    }

    let frames = 0;
    function render() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            p.y += p.speedY;
            p.x += p.speedX;
            p.rotation += p.rotSpeed;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rotation * Math.PI / 180);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
            ctx.restore();
        });
        frames++;
        if (frames < 240) {
            requestAnimationFrame(render);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }
    render();
}

// Floating math symbols
const symbols = ['\u03A3','\u222B','\u221A','\u03C0','\u221E','\u2248','\u0394','\u2202','\u2207','\u03B1','\u03B2','\u03B3','\u03BB','\u03B8','\u2208','\u2200','\u2203','\u00B1','\u00D7','\u00F7','\u00B2','\u00B3'];
const mathBg = document.getElementById('mathBg');
for (let i = 0; i < 22; i++) {
    const span = document.createElement('span');
    span.textContent = symbols[Math.floor(Math.random() * symbols.length)];
    span.style.left = Math.random() * 100 + 'vw';
    span.style.animationDuration = (14 + Math.random() * 20) + 's';
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
