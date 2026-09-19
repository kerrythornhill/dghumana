# WordPress installation and maintenance

Target: https://dghumana.com only.

## Current installation — September 19, 2026

The approved website is live. The active theme is `dg-humana-common-horizon`; Home page 155 is published. The static homepage is page 155. The owner confirmed completion of a DigitalOcean snapshot before publication. The previous theme remains installed. Existing legacy pages and wiki content remain published and unchanged.

The instructions below describe a fresh installation or future review cycle. Do not reimport existing pages or treat the original draft theme as the current active theme.

Take a complete files and database backup before installation. The owner’s Droplet handoff also requires a database export outside the web root before changes.

The existing site has a custom theme, a wiki, and several published pages. This redesign replaces the public homepage presentation; it does not authorize deletion of those pages or their content. Review legacy pages before activating a new site-wide theme. The theme includes ordinary page and archive fallbacks, but existing custom styling may need preservation.

## Preview

Use WPVibe’s current plugin and draft-theme workflow to create **DG Humana — Common Horizon**. Read the scaffold before applying this source. Preserve the plugin’s draft-preview hook if using a hybrid scaffold. The source theme is plain PHP/CSS with self-hosted fonts and optimized WebP artwork.

The homepage can render from bundled content without creating or changing database pages. WPVibe previews receive noindex/nofollow and no-cache headers. Do not expose a preview token in this repository.

The original review draft was `dg-humana-common-horizon-wpvibe-draft`. The theme retains WPVibe's preview stylesheet hook and uses the portable CSS in `assets/site.css`. `config.json` maps the possibility artwork to Media Library attachment 154 on the configured domain; WordPress generates its responsive image sources. On a different installation, update the media mapping or use the bundled files.

## Optional editable page

From this domain’s WordPress directory, after backing up:

```sh
wp eval-file /absolute/path/to/this-repository/tools/import-drafts.php
```

The importer checks the domain and creates one draft page tagged `_humana_route=home`. It preserves any existing imported page, refuses a conflicting slug, and does not change site options, publish, or activate a theme. Use the native code editor to update the HTML, preserving section IDs and the `main` element. Export WordPress edits back into `content/home.html` before rebuilding.

Home page 155 is already imported and published under `humana-home`, using one Gutenberg Custom HTML block. Do not import a duplicate. Contact details are maintained in `theme/partials/footer.html`.

## Publication

Review the homepage at desktop and phone widths. Confirm a recent complete backup and explicit launch approval before activation. Publish and select the imported home page as the static front page, activate the reviewed theme, then check anchors, images, metadata, indexing, 404 handling, and existing archive URLs. Any retirement or redirection of old pages is a separate editorial decision.

## Source updates

Edit `content/home.html` and run `python tools/build.py`. The source and static preview share `theme/assets/site.css`. Metadata is in `theme/config.json`. No JavaScript, third-party fonts, forms, or analytics are needed.
