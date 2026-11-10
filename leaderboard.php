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

// Pobranie rankingu graczy
$leaderboardQuery = mysqli_query($conn, 
    "SELECT id, username, klasa, punkty, created_at FROM users ORDER BY punkty DESC, id ASC LIMIT 50"
);

$players = [];
$myRank = null;
$rankIndex = 1;

if ($leaderboardQuery) {
    while ($row = mysqli_fetch_assoc($leaderboardQuery)) {
        if ($row['username'] === $username) {
            $myRank = $rankIndex;
        }
        $row['rank'] = $rankIndex++;
        $players[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyfronex &mdash; Ranking Graczy</title>
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
        }
        .leaderboard-link-btn {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            padding: 0.4rem 0.9rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all var(--transition);
        }
        .leaderboard-link-btn:hover {
            border-color: var(--accent);
            background: var(--bg-card-hover);
        }

        /* Podium Section */
        .podium-container {
            display: grid;
            grid-template-columns: 1fr 1.15fr 1fr;
            gap: 1.25rem;
            align-items: flex-end;
            margin: 2rem 0 3rem;
        }
        .podium-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 2rem 1.25rem 1.5rem;
            text-align: center;
            backdrop-filter: blur(16px);
            position: relative;
            transition: transform var(--transition), box-shadow var(--transition);
        }
        .podium-card:hover {
            transform: translateY(-4px);
        }
        .podium-card.first {
            order: 2;
            background: linear-gradient(180deg, rgba(255, 209, 102, 0.15) 0%, rgba(18, 24, 43, 0.8) 100%);
            border-color: rgba(255, 209, 102, 0.4);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(255, 209, 102, 0.2);
            padding-top: 2.5rem;
            padding-bottom: 2rem;
        }
        .podium-card.second {
            order: 1;
            background: linear-gradient(180deg, rgba(192, 192, 192, 0.1) 0%, rgba(18, 24, 43, 0.8) 100%);
            border-color: rgba(192, 192, 192, 0.3);
        }
        .podium-card.third {
            order: 3;
            background: linear-gradient(180deg, rgba(205, 127, 50, 0.1) 0%, rgba(18, 24, 43, 0.8) 100%);
            border-color: rgba(205, 127, 50, 0.3);
        }

        .podium-medal {
            font-size: 2.8rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .podium-card.first .podium-medal {
            font-size: 3.5rem;
            filter: drop-shadow(0 0 16px rgba(255, 209, 102, 0.6));
        }

        .podium-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.3rem;
            word-break: break-all;
        }
        .podium-card.first .podium-name {
            font-size: 1.45rem;
        }
        .podium-class {
            display: inline-block;
            background: rgba(108, 99, 255, 0.18);
            border: 1px solid rgba(108, 99, 255, 0.3);
            color: #c2bcff;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }
        .podium-points {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-gold);
            margin-bottom: 0.4rem;
        }
        .podium-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Leaderboard Table */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            backdrop-filter: blur(20px);
            overflow-x: auto;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
        }
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .leaderboard-table th {
            padding: 1rem 1.2rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
        }
        .leaderboard-table td {
            padding: 1rem 1.2rem;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-primary);
        }
        .leaderboard-table tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }
        .leaderboard-table tr.my-row td {
            background: rgba(0, 212, 170, 0.08);
            font-weight: 700;
        }
        .leaderboard-table tr.my-row td:first-child {
            border-left: 3px solid var(--accent-teal);
        }

        .rank-num {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }
        .rank-1 { color: #ffd166; background: rgba(255, 209, 102, 0.15); }
        .rank-2 { color: #c0c0c0; background: rgba(192, 192, 192, 0.15); }
        .rank-3 { color: #cd7f32; background: rgba(205, 127, 50, 0.15); }

        .player-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .player-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent) 0%, #9b59f5 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
        }
        .player-you-tag {
            background: rgba(0, 212, 170, 0.2);
            border: 1px solid rgba(0, 212, 170, 0.5);
            color: #6ffce8;
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .podium-container {
                grid-template-columns: 1fr;
            }
            .podium-card.first {
                order: 1;
            }
            .podium-card.second {
                order: 2;
            }
            .podium-card.third {
                order: 3;
            }
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

            <!-- Home Link -->
            <a href="home.php" class="leaderboard-link-btn">
                &larr; <span data-i18n="back_to_panel">Wroc do panelu</span>
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

        <!-- Welcome / Hero Header -->
        <section class="welcome-card" style="padding: 2rem 2.5rem;">
            <div class="welcome-text">
                <span class="badge">&#x1F3C6; <span data-i18n="leaderboard_title">Ranking Uczniow</span></span>
                <h1 style="font-size:clamp(1.8rem, 3.5vw, 2.3rem);">
                    <span data-i18n="leaderboard_headline">Najlepsi z najlepszych</span>
                </h1>
                <p class="welcome-desc" data-i18n="leaderboard_subdesc">
                    Rozwiazuj quizy, zdobywaj punkty za poprawne odpowiedzi i zdobywaj zaszczytne miejsce na podium!
                </p>
            </div>
            <div class="welcome-stats">
                <div class="stat-pill">
                    <span class="stat-icon">&#x1F3C5;</span>
                    <div>
                        <div class="stat-value">#<?php echo $myRank !== null ? $myRank : '-'; ?></div>
                        <div class="stat-label" data-i18n="your_ranking_pos">Twoja pozycja</div>
                    </div>
                </div>
                <div class="stat-pill">
                    <span class="stat-icon">&#x2B50;</span>
                    <div>
                        <div class="stat-value"><?php echo $userPoints; ?> <span data-i18n="pts_label">PKT</span></div>
                        <div class="stat-label" data-i18n="your_score">Twoj wynik</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TOP 3 PODIUM -->
        <?php if (count($players) >= 1): ?>
        <section class="podium-container">
            <!-- 2. Miejsce -->
            <?php if (isset($players[1])): 
                $p2 = $players[1];
                $d2 = date('d.m.Y', strtotime($p2['created_at']));
            ?>
            <div class="podium-card second">
                <span class="podium-medal">&#x1F948;</span>
                <div class="podium-name"><?php echo htmlspecialchars($p2['username']); ?></div>
                <span class="podium-class"><span data-i18n="class<?php echo $p2['klasa']; ?>">Klasa <?php echo $p2['klasa']; ?></span></span>
                <div class="podium-points"><?php echo intval($p2['punkty']); ?> <span data-i18n="pts_label" style="font-size:0.9rem;">PKT</span></div>
                <div class="podium-date"><span data-i18n="registered_on">Dolaczyl(a):</span> <?php echo $d2; ?></div>
            </div>
            <?php endif; ?>

            <!-- 1. Miejsce -->
            <?php if (isset($players[0])): 
                $p1 = $players[0];
                $d1 = date('d.m.Y', strtotime($p1['created_at']));
            ?>
            <div class="podium-card first">
                <span class="podium-medal">&#x1F947;</span>
                <div class="podium-name"><?php echo htmlspecialchars($p1['username']); ?></div>
                <span class="podium-class"><span data-i18n="class<?php echo $p1['klasa']; ?>">Klasa <?php echo $p1['klasa']; ?></span></span>
                <div class="podium-points"><?php echo intval($p1['punkty']); ?> <span data-i18n="pts_label" style="font-size:1rem;">PKT</span></div>
                <div class="podium-date"><span data-i18n="registered_on">Dolaczyl(a):</span> <?php echo $d1; ?></div>
            </div>
            <?php endif; ?>

            <!-- 3. Miejsce -->
            <?php if (isset($players[2])): 
                $p3 = $players[2];
                $d3 = date('d.m.Y', strtotime($p3['created_at']));
            ?>
            <div class="podium-card third">
                <span class="podium-medal">&#x1F949;</span>
                <div class="podium-name"><?php echo htmlspecialchars($p3['username']); ?></div>
                <span class="podium-class"><span data-i18n="class<?php echo $p3['klasa']; ?>">Klasa <?php echo $p3['klasa']; ?></span></span>
                <div class="podium-points"><?php echo intval($p3['punkty']); ?> <span data-i18n="pts_label" style="font-size:0.9rem;">PKT</span></div>
                <div class="podium-date"><span data-i18n="registered_on">Dolaczyl(a):</span> <?php echo $d3; ?></div>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- LEADERBOARD TABLE -->
        <section class="table-card">
            <h3 style="font-family:'Space Grotesk',sans-serif; font-size:1.3rem; margin-bottom:1rem; color:#fff;" data-i18n="full_ranking_title">
                Pelna tabela wynikow
            </h3>
            
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th style="width:70px;" data-i18n="th_rank">Pozycja</th>
                        <th data-i18n="th_student">Uczen</th>
                        <th style="width:140px;" data-i18n="th_class">Klasa</th>
                        <th style="width:150px;" data-i18n="th_points">Punkty</th>
                        <th style="width:160px;" data-i18n="th_registered">Data rejestracji</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($players)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:2rem; color:var(--text-muted);" data-i18n="no_players_yet">
                            Brak zarejestrowanych wynikow. Badz pierwszym, ktory zdobedzie punkty!
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($players as $p): 
                            $isMe = ($p['username'] === $username);
                            $createdDate = date('d.m.Y', strtotime($p['created_at']));
                            $rankCls = '';
                            if ($p['rank'] === 1) $rankCls = 'rank-1';
                            elseif ($p['rank'] === 2) $rankCls = 'rank-2';
                            elseif ($p['rank'] === 3) $rankCls = 'rank-3';
                        ?>
                        <tr class="<?php echo $isMe ? 'my-row' : ''; ?>">
                            <td>
                                <span class="rank-num <?php echo $rankCls; ?>">
                                    #<?php echo $p['rank']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="player-cell">
                                    <div class="player-avatar">
                                        <?php echo strtoupper(substr($p['username'], 0, 1)); ?>
                                    </div>
                                    <span style="font-weight:600;"><?php echo htmlspecialchars($p['username']); ?></span>
                                    <?php if ($isMe): ?>
                                        <span class="player-you-tag" data-i18n="you_tag">Ty</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="margin-bottom:0;">
                                    <span data-i18n="class<?php echo $p['klasa']; ?>">Klasa <?php echo $p['klasa']; ?></span>
                                </span>
                            </td>
                            <td>
                                <b style="color:var(--accent-gold); font-size:1.05rem; font-family:'Space Grotesk',sans-serif;">
                                    <?php echo intval($p['punkty']); ?>
                                </b>
                                <span data-i18n="pts_label" style="font-size:0.75rem; color:var(--text-muted);">PKT</span>
                            </td>
                            <td style="color:var(--text-muted); font-size:0.85rem;">
                                <?php echo $createdDate; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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

// Floating math symbols
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
