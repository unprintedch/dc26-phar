# CLAUDE.md — dc26-phar

Thème enfant pour PHAR (phar.swiss) — site pharmaceutique/médical.
Stack spécifique : FacetWP, WooCommerce.
URL locale : phar.local | Staging : dev.phar.swiss

> Architecture complète, philosophie ACF blocks, pipeline CSS/JS → [../dc26-base/CLAUDE.md](../dc26-base/CLAUDE.md)

## CSS overrides

`css/style.css` importe uniquement les fichiers phar-spécifiques — le CSS du parent est chargé automatiquement par WordPress.

| Fichier | Rôle |
|---------|------|
| `_header.css` | Header custom phar (design propre au site) |
| `_header-sticky.css` | Override : frosted glass 0.25 (base = 0.4) + nav links en primary au scroll |
| `_block-style.css` | Overrides : `dc26-hex-check` (SVG asset) + `media-text` overflow |
| `_bg-animated.css` | Animated background phar-specific |

## Functions (phar-only)

| Fichier | Rôle |
|---------|------|
| `dc26-phar-enqueue.php` | Enqueue CSS/JS child (dépend de `dc26-front-styles`) |
| `dc26-phar-bg.php` | ACF options page pour les paramètres du fond animé |
| `dc26-grid-content.php` | Rendu items + carte pour `dc26/grid-content`, template FacetWP AJAX via transient |
| `dc26-facet.php` | Override base : tri "Par étude", orderby numérique DESC |

## Blocks (phar-only)

| Bloc | Description |
|------|-------------|
| `dc26/grid-content` | Grille filtrée générique — CPT configurable, facets FacetWP, pré-filtres taxo, Load More |
| `dc26/news-ticker` | Ticker actualités défilant |

## Scripts (phar-only)

| Fichier | Rôle |
|---------|------|
| `dc26-parallax-bg.js` | Effet parallaxe sur le fond animé |
