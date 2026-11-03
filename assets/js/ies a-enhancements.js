
(() => {
    "use strict";

    const categories = document.getElementById("iesCategories");
    const grid = document.getElementById("hymnGrid");

    if (!categories || !grid) {
        console.warn("[IESA] Elementos de navegação não encontrados.");
        return;
    }

    let currentCategory = "all";

    const normalize = value => String(value || "")
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();

    function getCards() {
        return Array.from(grid.children).filter(el =>
            el.nodeType === 1
        );
    }

    function filterCards(category) {
        currentCategory = category;

        const cards = getCards();
        let visible = 0;

        cards.forEach(card => {
            const text = normalize(card.innerText || card.textContent);

            const matches = category === "all" ||
                text.includes(normalize(category));

            card.hidden = !matches;

            if (matches) visible++;
        });

        const message = document.getElementById("iesFilterMessage");

        if (message) {
            message.textContent = category === "all"
                ? `${cards.length} elementos disponíveis.`
                : `${visible} resultados encontrados para "${category}".`;
        }

        categories.querySelectorAll(".ies-category").forEach(button => {
            const active = button.dataset.category === category;
            button.classList.toggle("active", active);
            button.setAttribute("aria-pressed", String(active));
        });

        const empty = document.getElementById("emptyState");

        if (empty && category !== "all") {
            empty.hidden = visible !== 0;
        }
    }

    categories.addEventListener("click", event => {
        const button = event.target.closest(".ies-category");
        if (!button) return;

        const category = button.dataset.category;

        if (category === "all") {
            const search = document.getElementById("searchInput");
            if (search) search.value = "";
        }

        filterCards(category);
    });

    const message = document.createElement("div");
    message.id = "iesFilterMessage";
    message.className = "ies-filter-message";
    categories.after(message);

    // A aplicação original pode reconstruir os cartões.
    // Reaplica o filtro quando isso acontecer.
    const observer = new MutationObserver(() => {
        if (currentCategory !== "all") {
            filterCards(currentCategory);
        }
    });

    observer.observe(grid, {
        childList: true,
        subtree: false
    });

    console.info("[IESA] Navegação complementar carregada.");
})();
