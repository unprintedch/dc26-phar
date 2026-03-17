# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
npm install          # Install dependencies (first time)
npm run dev          # Watch CSS + bundle JS (development)
npm run build        # Compile CSS + JS for production
npm run build:main   # CSS only (postcss css/style.css → build/style.css)
npm run build:js     # JS only (esbuild scripts/dc26-front.js → build/app.js)
```

The `build/` directory is gitignored — always run `npm run build` after cloning.

## Architecture

### CSS Pipeline
- Entry: `css/style.css` (imports all partials via `@import`)
- Output: `build/style.css` (loaded on frontend via `dc26-enqueue.php`)
- Editor styles: `css/editor-style.css` (loaded separately in block editor)
- PostCSS plugins: `postcss-import`, `postcss-nested`, `autoprefixer`
- Block-specific styles live in `blocks/{name}/style.css` — auto-registered by `dc26-block-register.php`

### JS Pipeline
- Main bundle: `scripts/dc26-front.js` → `build/app.js` (esbuild, minified)
- Standalone scripts loaded separately by `dc26-enqueue.php`: `accordion-tabs-radio.js`, `editor-template-part-styles.js`, `accordion-tabs-variation.js`
- Swiper is loaded **conditionally** only when a `dc26/slider*` block is present in the page

### Block Registration
`functions/dc26-block-register.php` auto-scans the `blocks/` directory — any subfolder with a `block.json` is registered automatically. To add a new block:
1. Create `blocks/{block-name}/block.json` (use `"category": "dc26"`)
2. Create `blocks/{block-name}/render.php` for ACF/PHP rendering
3. Optionally add `style.css` (auto-enqueued) and `view.js` (declared in `block.json` as `"script": "file:./view.js"`)

All blocks use ACF (`"acf": { "mode": "preview", "renderTemplate": "render.php", "blockVersion": 3 }`).

### Functions
| File | Responsibility |
|------|---------------|
| `dc26-enqueue.php` | Enqueue CSS/JS, Swiper conditional loading |
| `dc26-block-register.php` | Auto-register all blocks + block styles |
| `dc26-menu-walker.php` | Custom nav walker |
| `dc26-woocommerce.php` | WooCommerce hooks |
| `dc26-facet.php` | FacetWP sort options and index row filters |

### Design Tokens
All palette colors, font sizes, spacing scale, and layout widths (`contentSize: 960px`, `wideSize: 1360px`) are defined in `theme.json`. Use `var(--wp--preset--color--{slug})` and `var(--wp--preset--spacing--{slug})` in CSS.

### Required Plugins
- Advanced Custom Fields (ACF) — all custom blocks depend on it
- Gravity Forms
- FacetWP (for `dc26-facet.php` filters)

## WordPress / PHP Conventions

- PHP 7.4+, always use `declare(strict_types=1)` in function files
- Extend via hooks (`add_action`, `add_filter`) — never modify core or plugin files
- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`; sanitize all input: `sanitize_text_field()` etc.
- Nonce verification required on all form submissions and AJAX handlers
- Database queries via `$wpdb->prepare()` or `WP_Query` — no raw SQL
- AJAX: use `admin-ajax.php` with nonce check, or REST API with `permission_callback`
- Use `wp_enqueue_script()` / `wp_enqueue_style()` — never inline or hardcode asset URLs
- Store config via Options API (`get_option` / `update_option`) or transients for cached data
- WooCommerce: use `wc_get_product()`, `WC()->session`, `wc_add_notice()` — prefer WC APIs over raw WP equivalents
- WooCommerce template overrides go in `woocommerce/` inside the theme folder
