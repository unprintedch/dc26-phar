<?php
declare(strict_types=1);

/**
 * Render a grid of items using WP_Query.
 * Called both from render.php (initial render) and from the FacetWP template
 * callback (AJAX refresh).
 *
 * @param array{
 *   post_type: string,
 *   posts_per_page: int,
 *   tax_query: array,
 *   show_title: bool,
 *   columns: int
 * } $settings
 */
function dc26_gc_render_items(array $settings): void {
    $args = [
        'post_type'           => $settings['post_type'],
        'post_status'         => 'publish',
        'posts_per_page'      => $settings['posts_per_page'],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    ];

    if (!empty($settings['tax_query'])) {
        $args['tax_query'] = $settings['tax_query'];
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        echo '<p class="dc26-grid-content__empty">'
           . esc_html__('Aucun résultat.', 'dc26-phar')
           . '</p>';
        wp_reset_postdata();
        return;
    }

    while ($query->have_posts()) {
        $query->the_post();
        dc26_gc_card((int) get_the_ID(), $settings);
    }

    wp_reset_postdata();
}

/**
 * Render a single card.
 * Image: ACF `logo` field → featured image fallback.
 * Link:  ACF `lien` field  → permalink fallback.
 *
 * @param int   $post_id
 * @param array $settings
 */
function dc26_gc_card(int $post_id, array $settings): void {
    // ── Image URL ──────────────────────────────────────────────────────────────
    $img_url  = '';
    $logo_raw = get_field('logo', $post_id);
    $attach_id = 0;

    if (!empty($logo_raw)) {
        if (is_array($logo_raw)) {
            $attach_id = (int) ($logo_raw['ID'] ?? $logo_raw['id'] ?? 0);
        } elseif (is_numeric($logo_raw)) {
            $attach_id = (int) $logo_raw;
        }
    }

    if ($attach_id > 0) {
        $src = wp_get_attachment_image_src($attach_id, 'full');
        if ($src) {
            $img_url = $src[0];
        }
    } elseif (has_post_thumbnail($post_id)) {
        $img_url = (string) get_the_post_thumbnail_url($post_id, 'full');
    }

    // ── Link ───────────────────────────────────────────────────────────────────
    $lien = (string) (get_field('lien', $post_id) ?: '');
    $href = ($lien && filter_var($lien, FILTER_VALIDATE_URL))
          ? $lien
          : (string) get_permalink($post_id);

    $title = get_the_title($post_id);

    // ── Output ─────────────────────────────────────────────────────────────────
    echo '<article class="dc26-gc-card">';

    if ($img_url) {
        $mode = $settings['image_mode'] ?? 'cover';

        if ($mode === 'contain') {
            $img_tag = wp_get_attachment_image($attach_id > 0 ? $attach_id : get_post_thumbnail_id($post_id), 'large', false, [
                'loading'  => 'lazy',
                'decoding' => 'async',
                'alt'      => $title,
            ]);
            if (!$img_tag) {
                $img_tag = '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($title) . '" loading="lazy" decoding="async">';
            }
            if ($href) {
                printf(
                    '<a class="dc26-gc-card__img-wrap" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a>',
                    esc_url($href), esc_attr($title), $img_tag
                );
            } else {
                echo '<div class="dc26-gc-card__img-wrap">' . $img_tag . '</div>';
            }
        } else {
            $bg_style = 'background-image:url(' . esc_url($img_url) . ')';
            if ($href) {
                printf(
                    '<a class="dc26-gc-card__img-wrap" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s" style="%s"></a>',
                    esc_url($href), esc_attr($title), $bg_style
                );
            } else {
                printf('<div class="dc26-gc-card__img-wrap" style="%s"></div>', $bg_style);
            }
        }
    }

    if ($settings['show_title'] && $title) {
        echo '<h3 class="dc26-gc-card__title">';
        if ($href) {
            printf(
                '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                esc_url($href),
                esc_html($title)
            );
        } else {
            echo esc_html($title);
        }
        echo '</h3>';
    }

    echo '</article>';
}

/**
 * Shared helper: build choices array of all show_ui taxonomies.
 */
function dc26_gc_tax_choices(): array {
    $choices = [];
    foreach (get_taxonomies(['show_ui' => true], 'objects') as $tax) {
        $choices[$tax->name] = $tax->label . ' (' . $tax->name . ')';
    }
    return $choices;
}

/**
 * Shared helper: build choices array of all terms from show_ui taxonomies.
 */
function dc26_gc_term_choices(): array {
    $choices = [];
    foreach (get_taxonomies(['show_ui' => true], 'objects') as $tax) {
        $terms = get_terms(['taxonomy' => $tax->name, 'hide_empty' => false]);
        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }
        foreach ($terms as $term) {
            $choices[(string) $term->term_id] = $term->name . ' (' . $tax->label . ')';
        }
    }
    return $choices;
}

/**
 * Populate the gc_tax select with all public registered taxonomies.
 */
add_filter('acf/load_field/key=field_gc_tax', function (array $field): array {
    $field['choices'] = dc26_gc_tax_choices();
    return $field;
});

add_filter('acf/load_field/key=field_gc_terms', function (array $field): array {
    $field['choices'] = dc26_gc_term_choices();
    return $field;
});

// ── Slider Partners — mêmes filtres ──────────────────────────────────────────
add_filter('acf/load_field/key=field_sp_filter_tax', function (array $field): array {
    $field['choices'] = dc26_gc_tax_choices();
    return $field;
});

add_filter('acf/load_field/key=field_sp_filter_terms', function (array $field): array {
    $field['choices'] = dc26_gc_term_choices();
    return $field;
});

/**
 * Register FacetWP PHP template so the grid refreshes correctly on AJAX.
 *
 * The template name is "dc26_gc_{page_id}" — unique per page.
 * Block settings are persisted in a transient (set in render.php).
 */
add_filter('facetwp_templates', function (array $templates): array {
    // Resolve page ID: from FacetWP AJAX POST or from current query
    $page_id = 0;

    if (!empty($_POST['data']['http_params']['uri'])) {
        $raw_uri = sanitize_text_field(wp_unslash((string) $_POST['data']['http_params']['uri']));
        $path    = (string) (parse_url($raw_uri, PHP_URL_PATH) ?: '');
        $full    = trailingslashit(home_url()) . ltrim($path, '/');
        $page_id = (int) url_to_postid($full);
    }

    if (!$page_id) {
        $page_id = (int) get_queried_object_id();
    }

    if (!$page_id) {
        return $templates;
    }

    $settings = get_transient('dc26_gc_' . $page_id);

    if (!is_array($settings)) {
        return $templates;
    }

    $templates[] = [
        'name'     => 'dc26_gc_' . $page_id,
        'label'    => 'Grid Content',
        'template' => static function () use ($settings): void {
            dc26_gc_render_items($settings);
        },
    ];

    return $templates;
});
