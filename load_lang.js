function loadLang(lang) {
    fetch(`lang/${lang}.json`)
        .then(response => {
            if (!response.ok) throw new Error(`Błąd HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const contentDiv = document.getElementById('content');
            const select = document.getElementById('lang');
            const selectedOption = select.options[select.selectedIndex];
            const flag = selectedOption.getAttribute('data-flag') || '';
            contentDiv.textContent = `${flag} ${data.message}`;

            const userName = document.getElementById('userName');
            if (userName) userName.textContent = data.user;

            const menuSettings = document.getElementById('menuSettings');
            const menuHelp = document.getElementById('menuHelp');
            const menuLogout = document.getElementById('menuLogout');
            if (menuSettings) menuSettings.textContent = data.settings;
            if (menuHelp) menuHelp.textContent = data.help;
            if (menuLogout) menuLogout.textContent = data.logout;

            const buyCoffee = document.getElementById('buyCoffee');
            if (buyCoffee) buyCoffee.textContent = data.buyCoffee;

            for (let i = 1; i <= 8; i++) {
                const classEl = document.getElementById(`class${i}`);
                if (classEl && data[`class${i}`]) {
                    classEl.textContent = data[`class${i}`];
                }
            }

        })
        .catch(error => {
            console.error('Błąd ładowania json:', error);
            document.getElementById('content').textContent = "Nie udało się załadować danych.";
            if (document.getElementById('userName')) document.getElementById('userName').textContent = "User";
        });
}

const savedLang = localStorage.getItem("lang") || "pl";
document.getElementById('lang').value = savedLang;
loadLang(savedLang);

document.getElementById('lang').addEventListener('change', (e) => {
    const lang = e.target.value;
    loadLang(lang);
    localStorage.setItem("lang", lang);
});
