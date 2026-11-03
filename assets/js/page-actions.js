(() => {
  "use strict";

  const button = document.querySelector("[data-copy-page]");
  if (!button) return;

  button.addEventListener("click", async () => {
    const lyrics = document.querySelector(".detail-card .lyrics");
    const sections = [...document.querySelectorAll(".detail-card .language-section")];
    const text = lyrics
      ? `${document.querySelector(".detail-card h1")?.textContent ?? ""}\n\n${lyrics.textContent ?? ""}`
      : sections.map(section => `${section.querySelector("h2")?.textContent ?? ""}\n${section.querySelector(".page-copy")?.textContent ?? ""}`).join("\n\n");

    try {
      await navigator.clipboard.writeText(text.trim());
      button.textContent = "Conteúdo copiado";
    } catch (error) {
      console.error("[Hinário] Não foi possível copiar o conteúdo.", error);
      button.textContent = "Cópia indisponível neste navegador";
    }
  });
})();
