<?php
include 'db.php';
if (!$conn) die("Błąd połączenia z bazą danych");

$klasa = isset($_GET['klasa']) ? intval($_GET['klasa']) : 1;

$query = "SELECT tresc, opis, odpowiedz FROM zadania WHERE klasa = $1 ORDER BY RANDOM() LIMIT 10";
$result = pg_query_params($conn, $query, array($klasa));
$zadania = array();
while ($row = pg_fetch_assoc($result)) {
    $zadania[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Quiz matematyczny – klasa <?php echo $klasa; ?></title>
<style>
    body { font-family: Arial, sans-serif; margin: 30px; }
    button { margin-top: 10px; padding: 5px 15px; }
    #wynik { font-weight: bold; margin-top: 20px; }
</style>
<link rel="stylesheet" href="zadania.css">
</head>
<body>
    <div class="contex">
    <select id="lang">
        <option value="pl">🇵🇱 Polski</option>
        <option value="en">🇬🇧 English</option>
        <option value="de">🇩🇪 Deutsch</option>
    </select>
</div>
<h1 id="matematic_quiz">Quiz matematyczny – klasa <?php echo $klasa; ?></h1>

<div id="start-container">
    <p id="start">Kliknij START, aby rozpocząć quiz z 10 losowych zadań.</p>
    <button id="startBtn">START</button>
</div>

<div id="quiz-container" style="display:none;">
    <p id="tresc"></p>
    <p id="opis"></p>
    <input type="text" id="odpowiedz" placeholder="Twoja odpowiedź">
    <br>
    <button id="checkBtn">SPRAWDŹ</button>
    <button id="nextBtn" style="display:none;">DALEJ</button>
    <p id="feedback"></p>
</div>

<div id="wynik" style="display:none;">
    <h id="score_quiz">Twój wynik<h> <span id="score"></span>/10
</div>

<script>
let zadania = <?php echo json_encode($zadania); ?>;
let currentIndex = 0;
let score = 0;

const startContainer = document.getElementById('start-container');
const quizContainer = document.getElementById('quiz-container');
const checkBtn = document.getElementById('checkBtn');
const nextBtn = document.getElementById('nextBtn');
const feedback = document.getElementById('feedback');
const trescEl = document.getElementById('tresc');
const opisEl = document.getElementById('opis');
const odpowiedzEl = document.getElementById('odpowiedz');
const wynikEl = document.getElementById('wynik');
const scoreEl = document.getElementById('score');
const startBtn = document.getElementById('startBtn');

startBtn.onclick = () => {
    startContainer.style.display = 'none';
    quizContainer.style.display = 'block';
    showZadanie();
};

function showZadanie() {
    if(currentIndex < zadania.length){
        trescEl.innerText = zadania[currentIndex].tresc;
        opisEl.innerText = zadania[currentIndex].opis;
        odpowiedzEl.value = '';
        feedback.innerText = '';
        checkBtn.style.display = 'inline';
        nextBtn.style.display = 'none';
    } else {
        quizContainer.style.display = 'none';
        wynikEl.style.display = 'block';
        scoreEl.innerText = score;
    }
}

checkBtn.onclick = () => {
    let odp = odpowiedzEl.value.trim();
    let poprawna = zadania[currentIndex].odpowiedz.trim();

    if(odp === ''){
        feedback.innerText = 'Wpisz odpowiedź!';
        return;
    }

    if(odp === poprawna){
        feedback.innerText = 'Poprawnie!';
        score++;
    } else {
        feedback.innerText = 'Błędnie! Poprawna odpowiedź: ' + poprawna;
    }

    checkBtn.style.display = 'none';
    nextBtn.style.display = 'inline';
};

nextBtn.onclick = () => {
    currentIndex++;
    showZadanie();
};
</script>

<script src="script.js"></script>
<script src="load_lang.js"></script>

</body>
</html>