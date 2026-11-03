(() => {
  "use strict";

  const buttons = [...document.querySelectorAll("[data-language]")];
  const panels = [...document.querySelectorAll("[data-language-panel]")];
  if (!buttons.length || !panels.length) return;

  buttons.forEach(button => {
    button.addEventListener("click", () => {
      const language = button.dataset.language;
      buttons.forEach(item => {
        const active = item === button;
        item.classList.toggle("active", active);
        item.setAttribute("aria-pressed", String(active));
      });
      panels.forEach(panel => {
        panel.hidden = panel.dataset.languagePanel !== language;
      });
    });
  });
})();
