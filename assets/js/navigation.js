(() => {
  "use strict";

  const toggle = document.querySelector("#navToggle");
  const nav = document.querySelector("#mainNav");
  if (toggle && nav) {
    const generalLink = nav.querySelector('a[href="/geral/"]');
    if (generalLink && !nav.querySelector(".nav-dropdown")) {
      const dropdown = document.createElement("details");
      dropdown.className = "nav-dropdown";

      const summary = document.createElement("summary");
      summary.append("Geral ");
      const caret = document.createElement("span");
      caret.setAttribute("aria-hidden", "true");
      caret.textContent = "⌄";
      summary.append(caret);

      const menu = document.createElement("div");
      menu.className = "nav-dropdown-menu";
      menu.setAttribute("aria-label", "Secções gerais");

      [
        ["Todas as secções", "/geral/"],
        ["Pai Nosso", "/geral/painosso/"],
        ["Chamada", "/geral/chamada/"],
        ["Credo dos Apóstolos", "/geral/credo/"],
        ["Súplica Geral", "/geral/suplica/"],
        ["Litanias", "/geral/litania/"],
      ].forEach(([label, href]) => {
        const link = document.createElement("a");
        link.href = href;
        link.textContent = label;
        menu.append(link);
      });

      dropdown.append(summary, menu);
      generalLink.replaceWith(dropdown);
    }

    const closeMenu = () => {
      toggle.setAttribute("aria-expanded", "false");
      toggle.setAttribute("aria-label", "Abrir menu");
      nav.classList.remove("is-open");
    };

    toggle.addEventListener("click", () => {
      const isOpen = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!isOpen));
      toggle.setAttribute("aria-label", isOpen ? "Abrir menu" : "Fechar menu");
      nav.classList.toggle("is-open", !isOpen);
    });

    nav.addEventListener("click", event => {
      if (event.target.closest("a")) closeMenu();
    });

    document.addEventListener("keydown", event => {
      if (event.key === "Escape") closeMenu();
    });

    document.addEventListener("click", event => {
      if (!event.target.closest(".topbar")) closeMenu();
    });
  }

  const themeToggle = document.querySelector("#themeToggle");
  if (localStorage.getItem("iesa-theme") === "light") {
    document.body.classList.add("light");
  }
  themeToggle?.addEventListener("click", () => {
    document.body.classList.toggle("light");
    localStorage.setItem(
      "iesa-theme",
      document.body.classList.contains("light") ? "light" : "dark",
    );
  });
})();
