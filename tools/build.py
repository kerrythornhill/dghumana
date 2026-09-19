from pathlib import Path
import re, json, html, shutil, base64
ROOT=Path(__file__).resolve().parent.parent
c=json.loads((ROOT/'theme/config.json').read_text())
out=ROOT/'preview';out.mkdir(exist_ok=True)
for src in (ROOT/'content').glob('*.html'):
    shutil.copy2(src,ROOT/'theme/content'/src.name)
def tokens(s):
    return re.sub(r'\[\[(asset:)?([\w./-]+)\]\]',lambda m: ('../theme/assets/'+m[2]) if m[1] else ('index.html' if m[2]=='home' else m[2]+'.html'),s)
for key,p in c['pages'].items():
    header=(ROOT/'theme/partials/header.html').read_text().replace('data-route="'+key+'"','data-route="'+key+'" aria-current="page"')
    body=header+(ROOT/'content'/f'{key}.html').read_text()+(ROOT/'theme/partials/footer.html').read_text()
    page='<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>'+html.escape(p['title'])+'</title><meta name="description" content="'+html.escape(p['description'],quote=True)+'"><link rel="icon" href="../theme/assets/favicon.svg"><link rel="stylesheet" href="../theme/assets/site.css"></head><body class="'+c['body_class']+'">'+tokens(body)+'</body></html>'
    (out/('index.html' if key=='home' else key+'.html')).write_text(page)
print(f"Built {len(c['pages'])} preview pages and synchronized theme content.")
