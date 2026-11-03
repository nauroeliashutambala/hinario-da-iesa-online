#!/usr/bin/env python3
"""Gera páginas SEO estáticas por hino a partir dos JSON preservados."""
import json, html, re
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
DOMAIN="https://hinario-da-iesa-online.vercel.app"
DB=ROOT/"db"
LANGS=[("portugues","Português"),("kimbundo","Kimbundo"),("umbundo","Umbundo"),("kikongo","Kikongo"),("cokwe","Cokwe"),("fiote","Fiote"),("kwanyama","Kwanyama"),("nianeka","Nyaneka"),("nganguela","Nganguela")]
def slug(s):
    import unicodedata
    s=unicodedata.normalize("NFKD",s).encode("ascii","ignore").decode().lower()
    return re.sub(r"[^a-z0-9]+","-",s).strip("-")[:80] or "hino"
def esc(s): return html.escape(str(s or ""),quote=True)
urls=[DOMAIN+"/"]
pages=[]
for lang,label in LANGS:
    p=DB/f"{lang}.json"
    if not p.exists(): continue
    try: data=json.loads(p.read_text(encoding="utf-8"))
    except Exception as e: print("JSON ignorado:",p,e); continue
    if not isinstance(data,list): continue
    for idx,h in enumerate(data):
        title=str(h.get("titulo") or h.get("title") or f"Hino {h.get('id',idx+1)}")
        hymn=str(h.get("hino") or h.get("letra") or h.get("texto") or "")
        number=str(h.get("id",idx+1))
        slugpart=f"{int(number):03d}-{slug(title)}" if number.isdigit() else f"{slug(number)}-{slug(title)}"
        rel=f"hino/{lang}/{slugpart}/"
        out=ROOT/rel/"index.html";out.parent.mkdir(parents=True,exist_ok=True)
        canonical=f"{DOMAIN}/{rel}"
        description=f"Leia a letra do hino {title} ({label}) no Hinário da IESA Online."
        body=f"""<!doctype html><html lang="pt-AO"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="index,follow"><title>{esc(title)} – Hinário da IESA Online</title><meta name="description" content="{esc(description)}"><link rel="canonical" href="{esc(canonical)}"><link rel="icon" href="/favicon.ico"><link rel="stylesheet" href="/assets/css/app.css"><meta property="og:type" content="article"><meta property="og:title" content="{esc(title)} – Hinário da IESA"><meta property="og:description" content="{esc(description)}"><meta property="og:url" content="{esc(canonical)}"><script type="application/ld+json">{json.dumps({"@context":"https://schema.org","@type":"MusicComposition","name":title,"inLanguage":"pt","url":canonical},ensure_ascii=False)}</script></head><body><div class="ambient ambient-one"></div><header class="topbar glass"><a class="brand" href="/"><span class="brand-mark">♫</span><span><strong>Hinário</strong><small>DA IESA · ONLINE</small></span></a><nav><a href="/">Biblioteca</a></nav></header><main class="hymn-page"><a class="back-link" href="/">← Voltar ao hinário</a><article class="hymn-detail glass"><span class="eyebrow">HINO {esc(number)} · {esc(label)}</span><h1>{esc(title)}</h1><div class="lyrics">{esc(hymn)}</div><a class="button primary" href="/">Explorar outros hinos ↗</a></article></main><footer><span>© Hinário da IESA Online</span><span>Hinos · Letras · Louvor</span></footer></body></html>"""
        out.write_text(body,encoding="utf-8")
        urls.append(canonical);pages.append((rel,title))
(ROOT/"sitemap.xml").write_text('<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'+"\n".join(f"  <url><loc>{esc(u)}</loc></url>" for u in sorted(set(urls)))+"\n</urlset>\n",encoding="utf-8")
print(f"Sitemap criado com {len(set(urls))} URLs.")
print(f"Páginas SEO geradas: {len(pages)}")
