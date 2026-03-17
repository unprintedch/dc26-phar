<?php
/**
 * Enqueue des assets pour le thème dc26-base
 *
 * @package dc26-base
 */

declare(strict_types=1);

/**
 * Récupère la version d'un asset basée sur filemtime pour le cache busting
 *
 * @param string $file_path Chemin relatif au répertoire du thème
 * @return string Version basée sur filemtime ou '1.0.0' en fallback
 */
function dc26_get_asset_version(string $file_path): string {
  $full_path = get_template_directory() . '/' . ltrim($file_path, '/');
  
  if (file_exists($full_path)) {
    return (string) filemtime($full_path);
  }
  
  return '1.0.0';
}


/**
 * Vérifie si un bloc slider est présent dans le contenu
 *
 * @param WP_Post|int|null $post Post object ou ID
 * @return bool True si un bloc slider est présent
 */
function dc26_has_slider_block($post = null): bool {
  if (!$post) {
    global $post;
  }
  
  if (!$post) {
    return false;
  }
  
  $slider_blocks = array(
    'dc26/slider',
    'dc26/slider-items',
    'dc26/slider-news',
    'dc26/slider-video',
    'dc26/slider-products',
  );
  
  foreach ($slider_blocks as $block_name) {
    if (has_block($block_name, $post)) {
      return true;
    }
  }
  
  return false;
}

/**
 * Enqueue Swiper assets (CSS et JS) depuis les fichiers locaux
 *
 * @return void
 */
function dc26_enqueue_swiper_assets(): void {
  $swiper_css_path = get_template_directory() . '/assets/vendor/swiper/swiper-bundle.min.css';
  $swiper_js_path = get_template_directory() . '/assets/vendor/swiper/swiper-bundle.min.js';
  
  // Enqueue Swiper CSS
  wp_enqueue_style(
    'swiper-css',
    get_template_directory_uri() . '/assets/vendor/swiper/swiper-bundle.min.css',
    array(),
    file_exists($swiper_css_path) ? (string) filemtime($swiper_css_path) : '11.0.0'
  );
  
  // Enqueue Swiper JS
  wp_enqueue_script(
    'swiper-js',
    get_template_directory_uri() . '/assets/vendor/swiper/swiper-bundle.min.js',
    array(),
    file_exists($swiper_js_path) ? (string) filemtime($swiper_js_path) : '11.0.0',
    true
  );
}

/**
 * Enqueue des styles et scripts principaux du thème
 */
function dc26_enqueue_styles(): void {
  $style_version = dc26_get_asset_version('build/style.css');
  $script_version = dc26_get_asset_version('build/app.js');
  
  wp_enqueue_style(
    'dc26-front-styles',
    get_template_directory_uri() . '/build/style.css',
    array(),
    $style_version
  );
  
  wp_enqueue_script(
    'dc26-front-scripts',
    get_template_directory_uri() . '/build/app.js',
    array('jquery'),
    $script_version,
    true
  );
  
  wp_localize_script(
    'dc26-front-scripts',
    'dc26Ajax',
    array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('dc26_ajax_nonce'),
    )
  );
}
add_action('wp_enqueue_scripts', 'dc26_enqueue_styles');


/**
 * Enqueue conditionnel des assets pour les blocs spécifiques (front-end)
 */
function dc26_enqueue_conditional_block_assets(): void {
  if (!dc26_has_slider_block()) {
    return;
  }
  
  dc26_enqueue_swiper_assets();
}
add_action('wp_enqueue_scripts', 'dc26_enqueue_conditional_block_assets', 20);

/**
 * Enqueue des assets pour l'éditeur de blocs (front + editor)
 * Le hook enqueue_block_assets s'exécute à la fois sur le front-end et dans l'éditeur
 */
function dc26_enqueue_block_editor_assets(): void {
  if (!dc26_has_slider_block()) {
    return;
  }
  
  dc26_enqueue_swiper_assets();
}
add_action('enqueue_block_assets', 'dc26_enqueue_block_editor_assets');

/**
 * Enqueue un script éditeur pour limiter les styles aux template parts header
 */
function dc26_enqueue_editor_template_part_styles(): void {
  $script_path = 'scripts/editor-template-part-styles.js';
  
  wp_enqueue_script(
    'dc26-editor-template-part-styles',
    get_template_directory_uri() . '/' . $script_path,
    array('wp-blocks', 'wp-data', 'wp-dom-ready'),
    dc26_get_asset_version($script_path),
    true
  );
}
add_action('enqueue_block_editor_assets', 'dc26_enqueue_editor_template_part_styles');

/**
 * Enqueue block variation for accordion tabs (editor only).
 */
function dc26_enqueue_accordion_tabs_variation(): void {
  $script_path = 'scripts/accordion-tabs-variation.js';

  wp_enqueue_script(
    'dc26-accordion-tabs-variation',
    get_template_directory_uri() . '/' . $script_path,
    array('wp-blocks', 'wp-dom-ready', 'wp-i18n'),
    dc26_get_asset_version($script_path),
    true
  );
}
add_action('enqueue_block_editor_assets', 'dc26_enqueue_accordion_tabs_variation');

/**
 * Enqueue accordion tabs behavior script (front).
 */
function dc26_enqueue_accordion_tabs_front(): void {
  $script_path = 'scripts/accordion-tabs-radio.js';

  wp_enqueue_script(
    'dc26-accordion-tabs-radio',
    get_template_directory_uri() . '/' . $script_path,
    array(),
    dc26_get_asset_version($script_path),
    true
  );
}
add_action('wp_enqueue_scripts', 'dc26_enqueue_accordion_tabs_front', 20);

