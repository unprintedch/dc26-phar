<?php
/**
 * Slider Partners block template — powered by Swiper.
 *
 * @param array  $block      Block settings and attributes.
 * @param string $content    Unused.
 * @param bool   $is_preview True in block editor.
 */

if ($is_preview) : ?>
<div class="dc26-partners-slider dc26-partners-slider--preview">
    <p class="dc26-partners-preview-label">⟵ Partenaires Slider ⟶</p>
</div>
<?php return;
endif;

$query = new WP_Query(array(
    'post_type'      => 'partenaire',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
    'no_found_rows'  => true,
));

$partners = array();
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $logo_raw = get_field('logo') ?: get_post_meta(get_the_ID(), 'logo', true);
        if (empty($logo_raw)) {
            continue;
        }
        if (is_array($logo_raw)) {
            $logo = $logo_raw;
        } else {
            $src = wp_get_attachment_image_src((int) $logo_raw, 'full');
            if (!$src) {
                continue;
            }
            $logo = array(
                'url'    => $src[0],
                'alt'    => get_post_meta((int) $logo_raw, '_wp_attachment_image_alt', true),
            );
        }
        $partners[] = array(
            'logo' => $logo,
            'lien' => get_field('lien') ?: get_post_meta(get_the_ID(), 'lien', true) ?: '',
            'name' => get_the_title(),
        );
    }
    wp_reset_postdata();
}

if (empty($partners)) {
    return;
}

$block_id   = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
$class_name = 'dc26-partners-slider';
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $class_name .= ' align' . $block['align'];
}
?>

<div
    <?php if ($block_id) : ?>id="<?php echo esc_attr($block_id); ?>"<?php endif; ?>
    class="<?php echo esc_attr($class_name); ?>">

    <button class="dc26-partners-btn dc26-partners-btn--prev" aria-label="<?php echo esc_attr__('Précédent', 'dc26-phar'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="swiper dc26-partners-swiper">
        <div class="swiper-wrapper">
            <?php foreach ($partners as $partner) :
                $logo   = $partner['logo'];
                $lien   = $partner['lien'];
                $imgAlt = esc_attr($logo['alt'] ?: $partner['name']);
            ?>
                <div class="swiper-slide dc26-partners-item">
                    <?php if ($lien) : ?>
                        <a href="<?php echo esc_url($lien); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php echo esc_attr($partner['name']); ?>">
                    <?php endif; ?>
                    <img
                        src="<?php echo esc_url($logo['url']); ?>"
                        alt="<?php echo $imgAlt; ?>"
                        loading="lazy"
                        decoding="async">
                    <?php if ($lien) : ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="dc26-partners-btn dc26-partners-btn--next" aria-label="<?php echo esc_attr__('Suivant', 'dc26-phar'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
