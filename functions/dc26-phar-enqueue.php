<?php
/**
 * Enqueue des assets pour le thème dc26-phar (child theme)
 * Remplace les assets du parent par ceux compilés depuis dc26-phar.
 *
 * @package dc26-phar
 */

declare(strict_types=1);

/**
 * Enqueue CSS/JS pour phar.
 *
 * CSS : le parent (dc26-base) garde son handle dc26-front-styles.
 *       phar charge ses overrides en tant que dc26-phar-styles, après le parent.
 *
 * JS  : phar remplace le bundle du parent — son entry point importe
 *       tout ce dont il a besoin (y compris les modules de dc26-base).
 *
 * Priorité 20 > priorité 10 du parent.
 */
function dc26_phar_enqueue_styles(): void {
    $style_path  = get_stylesheet_directory() . '/build/style.css';
    $script_path = get_stylesheet_directory() . '/build/app.js';

    // CSS — charger les overrides phar après le CSS parent
    wp_enqueue_style(
        'dc26-phar-styles',
        get_stylesheet_directory_uri() . '/build/style.css',
        array('dc26-front-styles'),
        file_exists($style_path) ? (string) filemtime($style_path) : '1.0.0'
    );

    // JS — remplace le bundle du parent (phar a son propre entry point)
    wp_dequeue_script('dc26-front-scripts');
    wp_deregister_script('dc26-front-scripts');
    wp_enqueue_script(
        'dc26-front-scripts',
        get_stylesheet_directory_uri() . '/build/app.js',
        array('jquery'),
        file_exists($script_path) ? (string) filemtime($script_path) : '1.0.0',
        true
    );

    wp_localize_script(
        'dc26-front-scripts',
        'dc26Ajax',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('dc26_ajax_nonce'),
        )
    );
}
add_action('wp_enqueue_scripts', 'dc26_phar_enqueue_styles', 20);

/**
 * Charge Swiper et fixe la dépendance du view.js quand slider-partners est présent.
 * Le registrar dc26-block-register.php donne des deps vides à view.js (pattern WordPress),
 * ce qui ne garantit pas que Swiper soit chargé avant. On re-déclare la dépendance ici.
 */
function dc26_phar_enqueue_slider_partners(): void {
    if (!has_block('dc26/slider-partners')) {
        return;
    }
    dc26_enqueue_swiper_assets();

    // Ajoute swiper-js comme dépendance du script du bloc
    $script = wp_scripts()->registered['dc26-block-slider-partners-script'] ?? null;
    if ($script && !in_array('swiper-js', $script->deps, true)) {
        $script->deps[] = 'swiper-js';
    }
}
add_action('wp_enqueue_scripts', 'dc26_phar_enqueue_slider_partners', 25);

/**
 * Remplace le script éditeur du parent (editor-template-part-styles)
 * par la version phar (syntaxe wp.domReady).
 */
function dc26_phar_enqueue_editor_template_part_styles(): void {
    $script_path = 'scripts/editor-template-part-styles.js';
    $full_path    = get_stylesheet_directory() . '/' . $script_path;

    wp_dequeue_script('dc26-editor-template-part-styles');
    wp_deregister_script('dc26-editor-template-part-styles');
    wp_enqueue_script(
        'dc26-editor-template-part-styles',
        get_stylesheet_directory_uri() . '/' . $script_path,
        array('wp-blocks', 'wp-dom-ready'),
        file_exists($full_path) ? (string) filemtime($full_path) : '1.0.0',
        true
    );
}
add_action('enqueue_block_editor_assets', 'dc26_phar_enqueue_editor_template_part_styles', 20);

/**
 * Register custom block style variations for Gutenberg.
 */
function dc26_phar_register_block_styles(): void {
    register_block_style(
        'core/list',
        array(
            'name'  => 'dc26-hex-check',
            'label' => __('Hexagone check', 'dc26-phar'),
        )
    );
}
add_action('init', 'dc26_phar_register_block_styles');
