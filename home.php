<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Cyfronex</title>

    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/logos.svg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sixtyfour+Convergence&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="hero">
    <nav>

        <div class="menu-container">
            <div class="hamburger" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>

        <img src="img/logo.svg" class="logo">

        
        <h1 class="centertext" style="color:red;">
            Cyfronex
        </h1>
        

        <img src="img/user/user.svg" class="user-pic" onclick="toggleUserMenu()">

        <div class="sub-menu-wrap" id="subMenu">
            <div class="sub-menu">

                <div class="user-info" id="user">
                    <img src="img/user/people.svg">
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                </div>

                <hr>

                <a href="settings.php" class="sub-menu-link">
                    <img src="img/settings.svg">
                    <p id="menuSettings" >Settings & Privacy</p>
                </a>

                <a href="help.html" class="sub-menu-link">
                    <img src="img/help.svg">
                    <p id="menuHelp">Help & Support</p>
                </a>

                <a href="logout.php" class="sub-menu-link">
                    <img src="img/logout.svg">
                    <p id="menuLogout">Logout</p>
                </a>

            </div>
        </div>

    </nav>
</div>

<nav id="menu" class="menu">
    <ul>
        <li><a id="class1" href="start.php?klasa=1">Class 1</a></li>
        <li><a id="class2" href="start.php?klasa=2">Class 2</a></li>
        <li><a id="class3" href="start.php?klasa=3">Class 3</a></li>
        <li><a id="class4" href="start.php?klasa=4">Class 4</a></li>
        <li><a id="class5" href="start.php?klasa=5">Class 5</a></li>
        <li><a id="class6" href="start.php?klasa=6">Class 6</a></li>
        <li><a id="class7" href="start.php?klasa=7">Class 7</a></li>
        <li><a id="class8" href="start.php?klasa=8">Class 8</a></li>
    </ul>
</nav>

<div class="contex">
    <h1>Cyfronex</h1>
</div>

<div class="contex">
    <select id="lang">
        <option value="pl">🇵🇱 Polski</option>
        <option value="en">🇬🇧 English</option>
        <option value="de">🇩🇪 Deutsch</option>
    </select>
</div>

<div class="contex">
    <div id="content">data loading...</div>
</div>

<div class="buycoffe">
    <h1 id="buyCoffee">Buy us a coffee</h1>
    <button>
        <a href="https://ko-fi.com/Cyfronex">
            <img src="img/caffe.svg">
        </a>
    </button>
</div>

<footer>
    <div class="footerBottom">
        <p>Copyright &copy;2026 
            <span class="designer">Radosław Mazur Miłosz Biniek</span>
        </p>
    </div>
</footer>

<script>
function toggleMenu() {
    document.getElementById("menu").classList.toggle("open-menu");
}

function toggleUserMenu() {
    document.getElementById("subMenu").classList.toggle("open-menu");
}
</script>

<script src="script.js"></script>
<script src="load_lang.js"></script>

</body>
</html>