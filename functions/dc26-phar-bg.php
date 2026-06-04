<?php
declare(strict_types=1);

// ACF Options page
add_action('acf/init', function (): void {
    if (!function_exists('acf_add_options_page')) return;
    acf_add_options_sub_page([
        'page_title'  => 'Fond animé',
        'menu_title'  => 'Fond animé',
        'parent_slug' => 'themes.php',
        'capability'  => 'manage_options',
        'option_key'  => 'dc26_phar_bg',
    ]);
});

// Render the layer
add_action('wp_body_open', function (): void {
    if (!function_exists('get_field')) return;
    if (!get_field('bg_enabled', 'option')) return;

    $mode = get_field('bg_mode', 'option') ?: 'pattern';

    // get_field returns false (not null) when unsaved — ?? does not catch false
    $raw_opacity = get_field('bg_opacity', 'option');
    $opacity = ($raw_opacity !== false && $raw_opacity !== null)
        ? (float) $raw_opacity / 100
        : 0.1;

    $raw_factor = get_field('bg_parallax_factor', 'option');
    $factor = ($raw_factor !== false && $raw_factor !== null)
        ? (float) $raw_factor
        : 0.2;

    $svg_upload = get_field('bg_svg_upload', 'option');
    $svg_inline = get_field('bg_svg_inline', 'option');

    // Build SVG HTML — same as the original working approach
    $svg_html = '';
    $tile_w   = null;
    $tile_h   = null;

    if ($svg_upload) {
        // ACF file field returns an array; use ['url'] directly (most reliable)
        $svg_url = is_array($svg_upload) ? $svg_upload['url'] : wp_get_attachment_url((int) $svg_upload);
        if ($svg_url) {
            $svg_html = '<img src="' . esc_url($svg_url) . '" alt="" aria-hidden="true">';

            // Parse SVG dimensions so JS can set the correct grid/tile size
            $attachment_id = is_array($svg_upload) ? (int) $svg_upload['id'] : (int) $svg_upload;
            $svg_path = get_attached_file($attachment_id);
            if ($svg_path) {
                $raw_svg = @file_get_contents($svg_path); // phpcs:ignore
                if ($raw_svg) {
                    [$tile_w, $tile_h] = dc26_phar_parse_svg_dimensions($raw_svg);
                }
            }
        }
    } elseif ($svg_inline) {
        $svg_html = wp_kses($svg_inline, dc26_phar_svg_kses_tags());
        [$tile_w, $tile_h] = dc26_phar_parse_svg_dimensions($svg_inline);
    }

    if (!$svg_html) return;

    // CSS variables injected by PHP — opacity + tile dimensions, immune to JS/GSAP overwrites
    $css_vars = '--bg-opacity: ' . esc_attr((string) $opacity) . ';';
    if ($tile_w && $tile_h) {
        $css_vars .= ' --tile-w: ' . esc_attr((string) $tile_w) . 'px;';
        $css_vars .= ' --tile-h: ' . esc_attr((string) $tile_h) . 'px;';
    }
    $tile_style = ' style="' . $css_vars . '"';

    echo '<div id="bg-pattern-layer"'
        . ' data-mode="' . esc_attr($mode) . '"'
        . ' data-factor="' . esc_attr((string) $factor) . '"'
        . ' data-opacity="' . esc_attr((string) $opacity) . '"'
        . ($tile_w ? ' data-tile-w="' . esc_attr((string) $tile_w) . '"' : '')
        . ($tile_h ? ' data-tile-h="' . esc_attr((string) $tile_h) . '"' : '')
        . $tile_style
        . '>';

    if ($mode === 'pattern') {
        for ($i = 0; $i < 100; $i++) {
            echo '<div class="pattern-tile" aria-hidden="true">' . $svg_html . '</div>';
        }
    } else {
        echo '<div class="bg-full-svg" aria-hidden="true">' . $svg_html . '</div>';
    }

    echo '</div>';
});

/**
 * Extract width/height from an SVG string.
 * Reads viewBox first (most reliable), then falls back to width/height attributes.
 *
 * @return array{0: float|null, 1: float|null}
 */
function dc26_phar_parse_svg_dimensions(string $svg): array {
    if (preg_match('/viewBox=["\']([^"\']+)["\']/', $svg, $m)) {
        $parts = preg_split('/[\s,]+/', trim($m[1]));
        if (count($parts) === 4) {
            return [(float) $parts[2], (float) $parts[3]];
        }
    }
    $w = preg_match('/\bwidth=["\']([0-9.]+)/', $svg, $m) ? (float) $m[1] : null;
    $h = preg_match('/\bheight=["\']([0-9.]+)/', $svg, $m) ? (float) $m[1] : null;
    return [$w, $h];
}

function dc26_phar_svg_kses_tags(): array {
    $svg_tags = ['svg', 'g', 'path', 'circle', 'rect', 'line', 'polyline', 'polygon',
                 'ellipse', 'text', 'tspan', 'defs', 'use', 'symbol', 'clipPath',
                 'linearGradient', 'radialGradient', 'stop', 'mask', 'pattern',
                 'image', 'title', 'desc'];
    $allowed = [];
    foreach ($svg_tags as $tag) {
        $allowed[$tag] = array_fill_keys(
            ['id', 'class', 'style', 'xmlns', 'viewBox', 'width', 'height', 'fill', 'stroke',
             'stroke-width', 'd', 'cx', 'cy', 'r', 'x', 'y', 'x1', 'y1', 'x2', 'y2',
             'points', 'transform', 'opacity', 'data-name', 'aria-hidden',
             'fill-rule', 'clip-rule', 'href', 'xlink:href'],
            true
        );
    }
    return $allowed;
}
