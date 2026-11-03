# Hinário da IESA — SEO e execução

## Pastas preservadas
- `db/`: JSON originais do hinário
- `assets/`: imagens e recursos
- `favicon.ico`: ícone original

## Executar localmente
Na pasta do projecto:
```bash
python3 -m http.server 8080
```
Abrir http://localhost:8080

## Gerar páginas SEO estáticas
```bash
python3 scripts/generate-seo.py
```
O script gera páginas individuais em `hino/<idioma>/<numero-titulo>/` e atualiza `sitemap.xml`.

## Publicar no Vercel
Importar esta pasta/repositório como projecto estático. O `vercel.json` define URLs limpas e cache de assets.

## Observações
- As letras são carregadas dos JSON originais, sem inventar conteúdo.
- Confirma direitos de publicação e autorização dos hinos antes de publicar.
- Sitemap contém URLs geradas a partir dos dados presentes.
