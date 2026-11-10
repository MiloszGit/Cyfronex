document.addEventListener("DOMContentLoaded", () => {

    window.currentTranslations = {};

    function applyTranslations(data) {
        window.currentTranslations = data;

        // 1. Obsluga atrybutow data-i18n
        document.querySelectorAll("[data-i18n]").forEach(el => {
            const key = el.getAttribute("data-i18n");
            if (data[key] !== undefined) {
                el.textContent = data[key];
            }
        });

        // 2. Obsluga atrybutow data-i18n-placeholder
        document.querySelectorAll("[data-i18n-placeholder]").forEach(el => {
            const key = el.getAttribute("data-i18n-placeholder");
            if (data[key] !== undefined) {
                el.setAttribute("placeholder", data[key]);
            }
        });

        // 3. Wsteczna kompatybilnosc z dotychczasowymi identyfikatorami
        const mapIds = [
            'content', 'userName', 'menuSettings', 'menuHelp', 'menuLogout',
            'buyCoffee', 'settings_tittle', 'saving', 'save', 'back',
            'matematic_quiz', 'start', 'score_quiz'
        ];

        mapIds.forEach(id => {
            const el = document.getElementById(id);
            if (el && data[id]) {
                el.textContent = data[id];
            }
        });

        for (let i = 1; i <= 8; i++) {
            const classEl = document.getElementById(`class${i}`);
            if (classEl && data[`class${i}`]) {
                classEl.textContent = data[`class${i}`];
            }
            const descEl = document.getElementById(`class${i}_desc`);
            if (descEl && data[`class${i}_desc`]) {
                descEl.textContent = data[`class${i}_desc`];
            }
        }

        // 4. Powiadomienie innych skryptow o zmianie jezyka
        window.dispatchEvent(new CustomEvent('langChanged', { detail: data }));
    }

    function loadLang(lang) {
        fetch(`lang/${lang}.json`)
            .then(response => {
                if (!response.ok) throw new Error(`Blad HTTP: ${response.status}`);
                return response.json();
            })
            .then(data => {
                applyTranslations(data);
            })
            .catch(error => {
                console.error('Blad ladowania pliku jezykowego:', error);
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
