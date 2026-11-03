(() => {
  "use strict";

  const grid = document.querySelector("#favoriteGrid");
  const count = document.querySelector("#favoriteCount");
  const clearButton = document.querySelector("#clearFavorites");
  if (!grid || !count || !clearButton) return;

  const sources = [
    ["Português", "/db/portugues.json"],
    ["Kimbundo", "/db/kimbundo.json"],
    ["Umbundo", "/db/umbundo.json"],
    ["Kikongo", "/db/kikongo.json"],
    ["Cokwe", "/db/cokwe.json"],
    ["Fiote", "/db/fiote.json"],
    ["Kwanyama", "/db/kwanyama.json"],
    ["Nyaneka", "/db/nianeka.json"],
    ["Nganguela", "/db/nganguela.json"]
  ];

  let hymns = [];
  let favorites;

  const titleOf = hymn => hymn.titulo || hymn.title || `Hino ${hymn.id}`;
  const lyricsOf = hymn => hymn.hino || hymn.letra || hymn.texto || hymn.lyrics || "";
  const keyOf = hymn => `${hymn.lang}:${hymn.id}`;
  const slug = value => String(value ?? "")
    .normalize("NFKD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-zA-Z0-9]+/g, "-")
    .replace(/^-|-$/g, "")
    .toLowerCase()
    .slice(0, 80) || "conteudo";

  function readFavorites() {
    const stored = localStorage.getItem("iesa-favorites") || "[]";
    const parsed = JSON.parse(stored);
    if (!Array.isArray(parsed) || parsed.some(item => typeof item !== "string")) {
      throw new TypeError("A lista de favoritos guardada não tem o formato esperado.");
    }
    return new Set(parsed);
  }

  function hymnPath(hymn) {
    const number = /^\d+$/.test(hymn.id)
      ? String(Number(hymn.id)).padStart(3, "0")
      : slug(hymn.id);
    const index = String(hymn.sourceIndex + 1).padStart(3, "0");
    return `/hino/${hymn.lang}/${number}-${slug(titleOf(hymn))}-${index}/`;
  }

  function createCard(hymn) {
    const card = document.createElement("article");
    card.className = "hymn-card";

    const top = document.createElement("div");
    top.className = "card-top";
    const label = document.createElement("span");
    label.className = "hymn-number";
    label.textContent = `HINO ${hymn.id.padStart(3, "0")} · ${hymn.langName}`;

    const remove = document.createElement("button");
    remove.type = "button";
    remove.className = "favorite-button saved";
    remove.setAttribute("aria-label", `Remover ${titleOf(hymn)} dos favoritos`);
    remove.title = "Remover dos favoritos";
    remove.textContent = "♥";
    remove.addEventListener("click", () => toggleFavorite(hymn));
    top.append(label, remove);

    const link = document.createElement("a");
    link.className = "favorite-card-link";
    link.href = hymnPath(hymn);
    const heading = document.createElement("h2");
    heading.textContent = titleOf(hymn);
    const preview = document.createElement("p");
    preview.className = "hymn-preview";
    preview.textContent = lyricsOf(hymn).replace(/\s+/g, " ").slice(0, 115) || "Consultar hino";
    link.append(heading, preview);

    const footer = document.createElement("div");
    footer.className = "card-footer";
    const caption = document.createElement("span");
    caption.textContent = "ABRIR LETRA";
    const arrow = document.createElement("span");
    arrow.className = "card-arrow";
    arrow.setAttribute("aria-hidden", "true");
    arrow.textContent = "↗";
    footer.append(caption, arrow);

    card.append(top, link, footer);
    return card;
  }

  function render() {
    const saved = hymns.filter(hymn => favorites.has(keyOf(hymn)));
    grid.replaceChildren();
    count.textContent = `${saved.length} ${saved.length === 1 ? "hino guardado" : "hinos guardados"}`;
    clearButton.hidden = saved.length === 0;

    if (!saved.length) {
      const empty = document.createElement("div");
      empty.className = "muted-card";
      empty.textContent = "Ainda não guardaste hinos. Usa o coração na biblioteca para os encontrar aqui.";
      const link = document.createElement("a");
      link.className = "button primary";
      link.href = "/hino/";
      link.textContent = "Explorar hinos";
      grid.append(empty, link);
      return;
    }

    saved.forEach(hymn => grid.append(createCard(hymn)));
  }

  function persistFavorites() {
    try {
      localStorage.setItem("iesa-favorites", JSON.stringify([...favorites]));
    } catch (error) {
      console.error("[Hinário] Não foi possível atualizar os favoritos guardados.", error);
      count.textContent = "Não foi possível guardar a alteração neste dispositivo.";
      return false;
    }
    return true;
  }

  function toggleFavorite(hymn) {
    const key = keyOf(hymn);
    const wasSaved = favorites.has(key);
    wasSaved ? favorites.delete(key) : favorites.add(key);
    if (!persistFavorites()) {
      wasSaved ? favorites.add(key) : favorites.delete(key);
      return;
    }
    render();
  }

  async function load() {
    favorites = readFavorites();
    const results = await Promise.all(sources.map(async ([langName, url]) => {
      const response = await fetch(url);
      if (!response.ok) throw new Error(`Não foi possível carregar ${url}: HTTP ${response.status}`);
      const records = await response.json();
      if (!Array.isArray(records)) throw new TypeError(`${url} deve conter uma lista de hinos.`);
      const lang = url.split("/").pop().replace(".json", "");
      return records.map((hymn, sourceIndex) => ({
        ...hymn,
        id: String(hymn.id ?? sourceIndex + 1),
        lang,
        langName,
        sourceIndex
      }));
    }));

    const seen = new Set();
    hymns = results.flat().filter(hymn => {
      const key = keyOf(hymn);
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
    render();
  }

  clearButton.addEventListener("click", () => {
    if (!window.confirm("Remover todos os hinos dos favoritos neste dispositivo?")) return;
    const previous = favorites;
    favorites = new Set();
    if (!persistFavorites()) {
      favorites = previous;
      return;
    }
    render();
  });

  load().catch(error => {
    console.error("[Hinário] Não foi possível carregar os favoritos.", error);
    count.textContent = "Ocorreu um erro ao carregar os favoritos.";
    grid.textContent = "Verifica a ligação ou tenta novamente quando estiveres online.";
  });
})();
