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


---

## ⚠️ A FAIRE — avec précaution

> Rappeler à chaque session de travail dans ce thème.

### 1. CSS — supprimer la duplication parent/enfant
Les fichiers dans `css/` copient en grande partie les fichiers du parent (`dc26-base/css/`). Le parent charge déjà ses versions via `dc26-front-styles` — ce thème recharge les mêmes règles en double.

**Quoi faire** : pour chaque fichier CSS du thème, le differ avec `dc26-base/css/` et ne garder que les sélecteurs qui changent réellement. Supprimer les fichiers sans delta réel.

**Risque** : moyen — procéder fichier par fichier, tester visuellement dans le browser après chaque changement.

### 2. JS — supprimer les réimports de modules parent
`scripts/dc26-front.js` réimporte probablement des modules déjà présents dans le bundle parent (`dc26-offcanvas.js`, `header-sticky.js`, `facet-sort-toggle.js`). Ces scripts sexécutent deux fois.

**Quoi faire** : comparer avec `dc26-base/scripts/dc26-front.js`, supprimer les imports de modules identiques. Ne garder que les modules propres à ce site.

**Risque** : faible si les modules sont identiques — tester le menu mobile et le header sticky après.
