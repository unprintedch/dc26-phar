<?php

require_once get_template_directory() . '/functions/dc26-enqueue.php';
require_once get_template_directory() . '/functions/dc26-block-register.php';
require_once get_template_directory() . '/functions/dc26-menu-walker.php';
require_once get_template_directory() . '/functions/dc26-woocommerce.php';
require_once get_template_directory() . '/functions/dc26-facet.php';

/**
 * dc26-base functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage dc26-base
 * @since dc26-base 1.0
 */



// function register_theme_menus()
// {
//   register_nav_menus(array(
// 	 'primary' => __('Primary Menu'),
// 	 'footer' => __('Footer Menu'),
//   ));
// }
// add_action('init', 'register_theme_menus');

/**
 * Activer le support des largeurs de blocs (wide et full)
 * Les largeurs full sont disponibles nativement avec theme.json si contentSize et wideSize sont définis
 */
add_theme_support('align-wide');

add_filter( 'block_categories_all', function ( array $categories ): array {
    array_unshift( $categories, [
        'slug'  => 'dc26',
        'title' => __( 'DC26 Blocks', 'dc26-base' ),
        'icon'  => null,
    ] );
    return $categories;
} );

// Editor-only styles to improve block editor visibility.
add_theme_support('editor-styles');
add_editor_style('css/editor-style.css');

/**
 * Remove "Privé :" prefix from private page titles.
 *
 * @param string $format Default title format.
 * @return string
 */
function dc26_private_title_format(string $format): string {
    return '%s';
}
add_filter('private_title_format', 'dc26_private_title_format');

 
