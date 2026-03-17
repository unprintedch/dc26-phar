<?php
// Register blocks
add_action( 'init', 'dc26_acf_blocks' );
function dc26_acf_blocks() {
    $blocks_dir = get_template_directory() . '/blocks/';
    $blocks = scandir( $blocks_dir );

    foreach ($blocks as $block) {
        // Skip if it's not a directory or if it's a system directory (like . or ..)
        if ( ! is_dir( $blocks_dir . $block ) || in_array( $block, array( '.', '..' ) ) ) {
            continue;
        }

        $block_path = $blocks_dir . $block;
        $block_json_path = $block_path . '/block.json';
        
        // Check if block.json exists
        if ( ! file_exists( $block_json_path ) ) {
            continue;
        }
        
        $args = array();

        // Ajout du style si présent
        if ( file_exists( $block_path . '/style.css' ) ) {
            wp_register_style(
                "dc26-block-{$block}-style",
                get_template_directory_uri() . "/blocks/{$block}/style.css",
                array(),
                filemtime( $block_path . '/style.css' )
            );
            $args['style'] = "dc26-block-{$block}-style";
        }

        // Ajout du script si présent dans block.json ou fichiers spécifiques
        $block_json = json_decode( file_get_contents( $block_json_path ), true );
        
        if ( isset( $block_json['script'] ) ) {
            // Extract script filename from the file:./path format.
            $script_path = str_replace( 'file:./', '', $block_json['script'] );
            $script_full_path = $block_path . '/' . $script_path;

            if ( file_exists( $script_full_path ) ) {
                $script_deps = array( 'wp-blocks', 'wp-element', 'wp-editor', 'swiper-js' );
                if ( preg_match( '/(^|\/)view(\.min)?\.js$/', $script_path ) ) {
                    $script_deps = array();
                }

                wp_register_script(
                    "dc26-block-{$block}-script",
                    get_template_directory_uri() . "/blocks/{$block}/{$script_path}",
                    $script_deps,
                    filemtime( $script_full_path ),
                    true
                );
                $args['script'] = "dc26-block-{$block}-script";
            }
        } elseif ( file_exists( $block_path . '/script.js' ) ) {
            wp_register_script(
                "dc26-block-{$block}-script",
                get_template_directory_uri() . "/blocks/{$block}/script.js",
                array( 'wp-blocks', 'wp-element', 'wp-editor', 'swiper-js' ),
                filemtime( $block_path . '/script.js' ),
                true
            );
            $args['script'] = "dc26-block-{$block}-script";
        }

        // Register the block
        register_block_type( $block_path, $args );
    }
}

add_action( 'init', function () {
	register_block_style(
		'core/template-part',
		[
			'name'  => 'sticky-header',
			'label' => __( 'Sticky header', 'dc26-oav' ),
		]
	);

	register_block_style(
		'core/button',
		[
			'name'  => 'dc26-ghost-arrow',
			'label' => __( 'Sans fond + fleche', 'dc26-oav' ),
		]
	);

	register_block_style(
		'core/button',
		[
			'name'  => 'dc26-ghost-download',
			'label' => __( 'Sans fond + download', 'dc26-oav' ),
		]
	);

	register_block_style(
		'core/buttons',
		[
			'name'  => 'dc26-buttons-doc-list',
			'label' => __( 'Liste docs alignee', 'dc26-oav' ),
		]
	);
} );




// require_once( get_template_directory() . '/blocks/block-variation.php' );
// remove_theme_support( 'core-block-patterns' );

