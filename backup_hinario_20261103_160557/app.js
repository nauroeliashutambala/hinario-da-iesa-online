(() => {
  "use strict";
  const $ = (s) => document.querySelector(s);
  const grid = $("#hymnGrid"), favGrid = $("#favoriteGrid"), input = $("#searchInput");
  const dialog = $("#hymnDialog"), lyrics = $("#dialogLyrics");
  const PAGE_SIZE = 18;
  let all = [], filtered = [], visible = PAGE_SIZE, lang = "all", current = null;
  let favorites = new Set(JSON.parse(localStorage.getItem("iesa-favorites") || "[]"));
  const sources = [
    ["Português","/db/portugues.json"],["Kimbundo","/db/kimbundo.json"],
    ["Umbundo","/db/umbundo.json"],["Kikongo","/db/kikongo.json"],
    ["Cokwe","/db/cokwe.json"],["Fiote","/db/fiote.json"],
    ["Kwanyama","/db/kwanyama.json"],["Nyaneka","/db/nianeka.json"],
    ["Nganguela","/db/nganguela.json"]
  ];
  const norm = s => String(s ?? "").normalize("NFD").replace(/[\u0300-\u036f]/g,"").toLowerCase();
  const esc = s => String(s ?? "").replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
  const idKey = h => `${h.lang}:${h.id}`;
  const persist = () => localStorage.setItem("iesa-favorites",JSON.stringify([...favorites]));
  const getFav = h => favorites.has(idKey(h));
  function toast(msg){const t=document.createElement("div");t.className="toast";t.textContent=msg;document.body.append(t);setTimeout(()=>t.remove(),2200);}
  function titleOf(h){return h.titulo || h.title || `Hino ${h.id}`;}
  function textOf(h){return h.hino || h.letra || h.texto || h.lyrics || "";}
  function numberOf(h){return String(h.id ?? "").padStart(3,"0");}
  function createCard(h){
    const card=document.createElement("article");card.className="hymn-card";card.tabIndex=0;
    card.innerHTML=`<div class="card-top"><span class="hymn-number">HINO ${esc(numberOf(h))} · ${esc(h.langName)}</span><button class="favorite-button ${getFav(h)?"saved":""}" aria-label="${getFav(h)?"Remover dos favoritos":"Adicionar aos favoritos"}" title="Favorito">${getFav(h)?"♥":"♡"}</button></div><h3>${esc(titleOf(h))}</h3><div class="hymn-preview">${esc(textOf(h).replace(/\s+/g," ").slice(0,115) || "Consultar hino")}</div><div class="card-footer"><span>ABRIR LETRA</span><span class="card-arrow">↗</span></div>`;
    card.querySelector(".favorite-button").addEventListener("click",e=>{e.stopPropagation();toggleFav(h);});
    card.addEventListener("click",()=>openHymn(h));card.addEventListener("keydown",e=>{if(e.key==="Enter"||e.key===" "){e.preventDefault();openHymn(h);}});
    return card;
  }
  function render(){
    grid.replaceChildren();const slice=filtered.slice(0,visible);
    slice.forEach(h=>grid.append(createCard(h)));
    $("#resultLabel").textContent=`${filtered.length} ${filtered.length===1?"hino":"hinos"}`;
    $("#totalCount").textContent=all.length.toLocaleString("pt-AO");
    $("#emptyState").hidden=filtered.length!==0;$("#loadMore").hidden=visible>=filtered.length;
    renderFavorites();
  }
  function renderFavorites(){
    favGrid.replaceChildren();const fs=all.filter(getFav);
    if(!fs.length){favGrid.innerHTML='<div class="muted-card">Marca o coração de um hino para o guardar aqui.</div>';return;}
    fs.slice(0,6).forEach(h=>favGrid.append(createCard(h)));
  }
  function applyFilter(){
    const q=norm(input.value.trim());
    filtered=all.filter(h=>(lang==="all"||h.lang===lang)&&(!q||norm(`${h.id} ${titleOf(h)} ${textOf(h)}`).includes(q)));
    visible=PAGE_SIZE;render();
  }
  function toggleFav(h){const k=idKey(h);getFav(h)?favorites.delete(k):favorites.add(k);persist();render();toast(getFav(h)?"Adicionado aos favoritos":"Removido dos favoritos");}
  function openHymn(h){
    current=h;$("#dialogNumber").textContent=`HINO ${numberOf(h)} · ${h.langName}`;
    $("#dialogTitle").textContent=titleOf(h);lyrics.textContent=textOf(h)||"A letra deste hino não está disponível.";
    $("#dialogFavorite").textContent=getFav(h)?"♥ Favorito guardado":"♡ Guardar favorito";
    dialog.showModal();
    document.title=`${titleOf(h)} – Hinário da IESA Online`;
  }
  function closeDialog(){dialog.close();document.title="Hinário da IESA Online — Hinos, letras e louvor";}
  async function load(){
    const results=await Promise.allSettled(sources.map(async ([name,url])=>{
      const r=await fetch(url);if(!r.ok)throw new Error(url);const data=await r.json();
      if(!Array.isArray(data))return [];
      return data.map((h,i)=>({...h,id:String(h.id??i+1),lang:url.split("/").pop().replace(".json",""),langName:name}));
    }));
    all=results.flatMap(r=>r.status==="fulfilled"?r.value:[]);
    // De-duplicate only exact language/id pairs; preserve original records.
    const seen=new Set();all=all.filter(h=>{const k=idKey(h);if(seen.has(k))return false;seen.add(k);return true;});
    buildFilters();applyFilter();
    if(!all.length){grid.innerHTML='<div class="empty-state glass"><h3>Dados não encontrados</h3><p>Confirma que a pasta db/ foi preservada no projecto.</p></div>';}
  }
  function buildFilters(){
    const box=$("#languageFilters");box.replaceChildren();
    [["all","Todos"],...sources.map(([name,url])=>[url.split("/").pop().replace(".json",""),name])].forEach(([key,label])=>{
      const b=document.createElement("button");b.className="filter"+(key===lang?" active":"");b.dataset.lang=key;b.textContent=label;
      b.addEventListener("click",()=>{lang=key;box.querySelectorAll(".filter").forEach(x=>x.classList.toggle("active",x===b));applyFilter();});box.append(b);
    });
  }
  input.addEventListener("input",applyFilter);
  $("#loadMore").addEventListener("click",()=>{visible+=PAGE_SIZE;render();});
  $("#clearSearch").addEventListener("click",()=>{input.value="";applyFilter();});
  $("#closeDialog").addEventListener("click",closeDialog);
  dialog.addEventListener("click",e=>{if(e.target===dialog)closeDialog();});
  $("#dialogFavorite").addEventListener("click",()=>current&&toggleFav(current));
  $("#copyLyrics").addEventListener("click",async()=>{try{await navigator.clipboard.writeText(`${titleOf(current)}\n\n${textOf(current)}`);toast("Letra copiada");}catch{toast("Não foi possível copiar neste navegador");}});
  $("#themeToggle").addEventListener("click",()=>{document.body.classList.toggle("light");localStorage.setItem("iesa-theme",document.body.classList.contains("light")?"light":"dark");});
  if(localStorage.getItem("iesa-theme")==="light")document.body.classList.add("light");
  document.addEventListener("keydown",e=>{if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==="k"){e.preventDefault();input.focus();}if(e.key==="Escape"&&dialog.open)closeDialog();});
  $("#year").textContent=new Date().getFullYear();
  load().catch(err=>{console.error(err);grid.innerHTML='<div class="empty-state glass"><h3>Erro ao carregar</h3><p>Verifica se estás a executar o site num servidor HTTP.</p></div>';});
})();
