(() => {
  "use strict";

  const list = document.querySelector("#lessonList");
  const search = document.querySelector("#lessonSearch");
  const count = document.querySelector("#lessonCount");
  const languageButtons = [...document.querySelectorAll("[data-list-language]")];
  if (!list || !search || !count) return;

  let lessons = [];
  let language = 0;
  const slug = value => String(value ?? "")
    .normalize("NFKD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-zA-Z0-9]+/g, "-")
    .replace(/^-|-$/g, "")
    .toLowerCase()
    .slice(0, 80) || "conteudo";
  const textAt = (value, index) => Array.isArray(value) ? String(value[index] ?? "") : String(value ?? "");

  function render() {
    const query = search.value.trim().toLocaleLowerCase();
    const matches = lessons
      .map((lesson, index) => ({ lesson, index }))
      .filter(({ lesson }) => {
        const title = textAt(lesson.titulo, language);
        const label = textAt(lesson.licao, language);
        const searchText = `${title} ${label} ${textAt(lesson.leitura, language)} ${textAt(lesson.pergunta, language)}`;
        return searchText.toLocaleLowerCase().includes(query);
      });

    list.replaceChildren();
    count.textContent = `${matches.length} ${matches.length === 1 ? "lição encontrada" : "lições encontradas"}`;
    if (!matches.length) {
      const empty = document.createElement("p");
      empty.className = "page-muted";
      empty.textContent = "Não foram encontradas lições para esta pesquisa.";
      list.append(empty);
      return;
    }

    const chapters = new Map();
    matches.forEach(({ lesson, index }) => {
      const chapterNumber = String(lesson.capitulo ?? "—");
      if (!chapters.has(chapterNumber)) chapters.set(chapterNumber, []);
      chapters.get(chapterNumber).push({ lesson, index });
    });

    chapters.forEach((items, chapterNumber) => {
      const section = document.createElement("section");
      section.className = "lesson-chapter";
      const heading = document.createElement("h2");
      heading.textContent = language === 0 ? `Capítulo ${chapterNumber}` : `Ocilae ${chapterNumber}`;
      const grid = document.createElement("div");
      grid.className = "lesson-chapter-grid";

      items.forEach(({ lesson, index }) => {
        const title = textAt(lesson.titulo, language);
        const label = textAt(lesson.licao, language);
        const link = document.createElement("a");
        link.className = "lesson-link";
        link.href = `/fe-evangelica/licoes/${String(index + 1).padStart(3, "0")}-${slug(textAt(lesson.titulo, 0))}/`;
        const name = document.createElement("strong");
        name.textContent = title;
        const meta = document.createElement("span");
        meta.textContent = `${label} · ${textAt(lesson.leitura, language)}`;
        link.append(name, meta);
        grid.append(link);
      });

      section.append(heading, grid);
      list.append(section);
    });
  }

  languageButtons.forEach(button => {
    button.addEventListener("click", () => {
      language = Number(button.dataset.listLanguage);
      languageButtons.forEach(item => {
        const active = item === button;
        item.classList.toggle("active", active);
        item.setAttribute("aria-pressed", String(active));
      });
      render();
    });
  });
  search.addEventListener("input", render);

  fetch("/fe-evangelica/licoes.json")
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (!Array.isArray(data)) throw new TypeError("O ficheiro de lições deve conter uma lista.");
      lessons = data;
      render();
    })
    .catch(error => {
      console.error("[Fé Evangélica] Erro ao carregar as lições.", error);
      count.textContent = "Não foi possível carregar as lições.";
      list.textContent = "Verifica se fe-evangelica/licoes.json existe e contém JSON válido.";
    });
})();
