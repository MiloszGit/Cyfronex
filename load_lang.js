document.addEventListener("DOMContentLoaded", () => {

    function loadLang(lang) {
        fetch(`lang/${lang}.json`)
            .then(response => {
                if (!response.ok) throw new Error(`Błąd HTTP: ${response.status}`);
                return response.json();
            })
            .then(data => {

                const contentDiv = document.getElementById('content');
                const select = document.getElementById('lang');

                if (contentDiv && select) {
                    const selectedOption = select.options[select.selectedIndex];
                    const flag = selectedOption?.getAttribute('data-flag') || '';
                    contentDiv.textContent = `${flag} ${data.message || ""}`;
                }

                const userName = document.getElementById('userName');
                if (userName) userName.textContent = data.user || "";

                const menuSettings = document.getElementById('menuSettings');
                const menuHelp = document.getElementById('menuHelp');
                const menuLogout = document.getElementById('menuLogout');

                if (menuSettings) menuSettings.textContent = data.settings || "";
                if (menuHelp) menuHelp.textContent = data.help || "";
                if (menuLogout) menuLogout.textContent = data.logout || "";

                const buyCoffee = document.getElementById('buyCoffee');
                if (buyCoffee) buyCoffee.textContent = data.buyCoffee || "";

                for (let i = 1; i <= 8; i++) {
                    const classEl = document.getElementById(`class${i}`);
                    if (classEl && data[`class${i}`]) {
                        classEl.textContent = data[`class${i}`];
                    }
                }

                const settingsTitle = document.getElementById('settings_tittle');
                if (settingsTitle) settingsTitle.textContent = data.settings_tittle || "Settings";

                const saveBtn = document.getElementById('save');
                if (saveBtn) saveBtn.textContent = data.save || "Save";

                const backBtn = document.getElementById('back');
                if (backBtn) backBtn.textContent = data.back || "Back";

                const saving = document.getElementById('saving');
                if (saving) saving.textContent = data.saving || "Saved";

                const matematic_quiz = document.getElementById('matematic_quiz');
                if (matematic_quiz) matematic_quiz.textContent = data.matematic_quiz || "Math Quiz - Class";

                const start = document.getElementById('start');
                if (start) start.textContent = data.start || "Click START to begin the quiz with 10 random tasks";

                const score_quiz = document.getElementById('score_quiz');
                if (score_quiz) score_quiz.textContent = data.score_quiz || "Your score"

            })
            .catch(error => {
                console.error('Błąd ładowania json:', error);

                const contentDiv = document.getElementById('content');
                if (contentDiv) {
                    contentDiv.textContent = "Nie udało się załadować danych.";
                }
            });
    }

    const select = document.getElementById('lang');

    if (!select) return;

    const savedLang = localStorage.getItem("lang") || "pl";

    select.value = savedLang;

    loadLang(savedLang);

    select.addEventListener('change', (e) => {
        const lang = e.target.value;
        localStorage.setItem("lang", lang);
        loadLang(lang);
    });

});