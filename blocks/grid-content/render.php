<?php
/**
 * Block: dc26/grid-content — Grille filtrée
 *
 * @param array  $block
 * @param string $content
 * @param bool   $is_preview
 * @param int    $post_id
 */
declare(strict_types=1);

// ── Champs ACF ────────────────────────────────────────────────────────────────
$cpt            = get_field('gc_post_type') ?: 'partenaire';
$posts_per_page = max(1, (int) (get_field('gc_posts_per_page') ?: 12));
$columns        = max(2, min(4, (int) (get_field('gc_columns') ?: 3)));
$show_title     = (bool) get_field('gc_show_title');
$image_mode     = in_array(get_field('gc_image_mode'), ['cover', 'contain'], true) ? get_field('gc_image_mode') : 'cover';
$tax_query_rows = get_field('gc_tax_query') ?: [];
$facets_config  = get_field('gc_facets') ?: [];
$pager_facet    = sanitize_key((string) (get_field('gc_pager_facet') ?: ''));

// ── Preview ───────────────────────────────────────────────────────────────────
if ($is_preview) : ?>
<div class="dc26-grid-content dc26-grid-content--preview">
    <span class="dc26-grid-content__preview-label">
        Grille filtrée — <strong><?php echo esc_html($cpt); ?></strong>
        · <?php echo $columns; ?> col · <?php echo $posts_per_page; ?>/page
        <?php if ($facets_config) : ?>
         · <?php echo count($facets_config); ?> facet(s)
        <?php endif; ?>
    </span>
</div>
<?php return; endif;

// ── Build tax_query ───────────────────────────────────────────────────────────
$tax_query = [];
foreach ($tax_query_rows as $row) {
    $tax      = sanitize_key((string) ($row['gc_tax'] ?? ''));
    $term_ids = array_values(array_filter(array_map('intval', (array) ($row['gc_terms'] ?? []))));
    $op       = in_array($row['gc_operator'] ?? '', ['IN', 'NOT IN', 'AND'], true)
              ? $row['gc_operator'] : 'IN';

    if ($tax && $term_ids) {
        $tax_query[] = [
            'taxonomy' => $tax,
            'field'    => 'term_id',
            'terms'    => $term_ids,
            'operator' => $op,
        ];
    }
}

// ── Settings — persistés pour le template FacetWP AJAX ───────────────────────
$settings = [
    'post_type'      => sanitize_key($cpt),
    'posts_per_page' => $posts_per_page,
    'tax_query'      => $tax_query,
    'show_title'     => $show_title,
    'columns'        => $columns,
    'image_mode'     => $image_mode,
];
set_transient('dc26_gc_' . $post_id, $settings, DAY_IN_SECONDS);

$template_name = 'dc26_gc_' . $post_id;

// ── Wrapper ───────────────────────────────────────────────────────────────────
$block_id   = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
$class_name = 'dc26-grid-content dc26-grid-content--col-' . $columns . ' dc26-grid-content--img-' . $image_mode;
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $class_name .= ' align' . $block['align'];
}
?>

<div id="<?php echo esc_attr($block_id); ?>"
     class="<?php echo esc_attr($class_name); ?>">

    <?php if ($facets_config && function_exists('facetwp_display')) : ?>
    <div class="dc26-grid-content__filters">
        <?php foreach ($facets_config as $f) :
            $slug  = sanitize_key((string) ($f['gc_facet_slug'] ?? ''));
            $label = (string) ($f['gc_facet_label'] ?? '');
            if (!$slug) continue;
        ?>
        <div class="dc26-grid-content__facet">
            <?php if ($label) : ?>
                <span class="dc26-grid-content__facet-label"><?php echo esc_html($label); ?></span>
            <?php endif; ?>
            <?php echo facetwp_display('facet', $slug); ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="facetwp-template dc26-grid-content__grid"
         data-name="<?php echo esc_attr($template_name); ?>">
        <?php dc26_gc_render_items($settings); ?>
    </div>

    <?php if ($pager_facet && function_exists('facetwp_display')) : ?>
    <div class="dc26-grid-content__pager">
        <?php echo facetwp_display('facet', $pager_facet); ?>
    </div>
    <?php endif; ?>

</div>
