# CLAUDE.md — dc26-phar

Thème pour PHAR — site pharmaceutique/médical avec FacetWP et WooCommerce.

## Commands

```bash
npm install       # First time
npm run dev       # Watch CSS + JS
npm run build     # Production build
```

`build/` est gitignored — toujours lancer `npm run build` après le clone.

## Functions

| Fichier | Rôle |
|---------|------|
| `dc26-enqueue.php` | Enqueue CSS/JS, chargement conditionnel Swiper |
| `dc26-block-register.php` | Auto-register blocs + styles de blocs |
| `dc26-menu-walker.php` | Custom nav walker accordion |
| `dc26-facet.php` | FacetWP — tri "Par étude", normalisation dates en année, orderby numérique DESC |
| `dc26-woocommerce.php` | Placeholder WooCommerce |

## Blocks

| Bloc | Description |
|------|-------------|
| `block-video-modal` | Vidéo en modal — chargement iframe lazy, ARIA complet |
| `toggle-panel` | Interface deux panneaux avec toggle, LocalStorage, `view.js` front-end |
| `slider-partners` | Marquee infini CSS de logos partenaires (ACF repeater: logo + lien) |

> Philosophie ACF Blocks v3 → voir `CLAUDE.md` racine (`CLAUDE_base/CLAUDE.md`).

## Scripts

| Fichier | Rôle |
|---------|------|
| `dc26-front.js` | Entry point (importe offcanvas, facet-sort, header-sticky) |
| `dc26-offcanvas.js` | Menu mobile burger + overlay + Escape |
| `accordion-tabs-radio.js` | Comportement accordion/tabs front-end |
| `accordion-tabs-variation.js` | Variation bloc éditeur (Sur place, Téléphone, En ligne) |
| `header-sticky.js` | Header sticky au scroll (seuil 64px), variable CSS `--site-header-height` |
| `facet-sort-toggle.js` | Convertit le select FacetWP `sort_firm` en boutons toggle |
| `editor-template-part-styles.js` | Limite le style `sticky-header` aux header template parts |
| `scroll-behavior.js` | Module scroll avancé (non importé dans le bundle — désactivé) |

## Block Styles enregistrés

- `sticky-header` — template-part blocks
- `dc26-ghost-arrow` — button blocks
- `dc26-ghost-download` — button blocks
- `dc26-buttons-doc-list` — buttons group
