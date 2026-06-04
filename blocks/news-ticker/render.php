<?php
/**
 * Block: dc26/news-ticker
 *
 * @param array    $block
 * @param string   $content
 * @param bool     $is_preview
 * @param int      $post_id
 * @param WP_Block $wp_block
 */
declare(strict_types=1);

$items       = get_field('nt_items') ?: [];
$label       = (string) (get_field('nt_label') ?: '');
$speed       = max(5, min(120, (int) (get_field('nt_speed') ?: 30)));
$separator   = (string) (get_field('nt_separator') ?: '·');
$direction   = get_field('nt_direction') === 'right' ? 'right' : 'left';
$pause_hover = (bool) get_field('nt_pause_hover');

// Placeholder éditeur si aucun item
if ($is_preview && empty($items)) : ?>
<div class="dc26-ticker dc26-ticker--empty-preview">
    <span style="padding:1rem;opacity:.5;font-size:.875rem;">News Ticker — ajouter des textes dans les champs</span>
</div>
<?php return; endif;

if (empty($items)) {
    return;
}

// ── Attributs block supports — $wp_block->attributes est la source fiable en ACF v3
$wp_attrs   = (isset($wp_block) && $wp_block instanceof WP_Block) ? ($wp_block->attributes ?? []) : [];
$block_style = $wp_attrs['style'] ?? ($block['style'] ?? []);
if (is_string($block_style)) {
    $block_style = json_decode($block_style, true) ?: [];
}

$bg_color   = $wp_attrs['backgroundColor'] ?? ($block['backgroundColor'] ?? '');
$text_color = $wp_attrs['textColor']       ?? ($block['textColor']       ?? '');
$gradient   = $wp_attrs['gradient']        ?? ($block['gradient']        ?? '');
$font_size  = $wp_attrs['fontSize']        ?? ($block['fontSize']        ?? '');

// ── Classes ────────────────────────────────────────────────────────────────
$classes = ['dc26-ticker', 'dc26-ticker--' . $direction];
if ($pause_hover) {
    $classes[] = 'dc26-ticker--pause-hover';
}
if (!empty($block['align'])) {
    $classes[] = 'align' . $block['align'];
}
if (!empty($block['className'])) {
    $classes[] = $block['className'];
}

// Couleurs preset
if ($bg_color) {
    $classes[] = 'has-' . $bg_color . '-background-color';
    $classes[] = 'has-background';
}
if ($text_color) {
    $classes[] = 'has-' . $text_color . '-color';
    $classes[] = 'has-text-color';
}
if ($gradient) {
    $classes[] = 'has-' . $gradient . '-gradient-background';
    $classes[] = 'has-background';
}
if ($font_size) {
    $classes[] = 'has-' . $font_size . '-font-size';
}

// ── Styles inline ──────────────────────────────────────────────────────────
$styles = ['--nt-duration:' . $speed . 's'];

// Spacing (padding / margin)
foreach (['padding', 'margin'] as $prop) {
    if (!empty($block_style['spacing'][$prop]) && is_array($block_style['spacing'][$prop])) {
        foreach ($block_style['spacing'][$prop] as $side => $value) {
            if ($value) {
                $styles[] = $prop . '-' . $side . ':' . $value;
            }
        }
    }
}

// Couleurs custom (non-preset)
if (!empty($block_style['color']['text'])) {
    $styles[] = 'color:' . $block_style['color']['text'];
}
if (!empty($block_style['color']['background'])) {
    $styles[] = 'background-color:' . $block_style['color']['background'];
}
if (!empty($block_style['color']['gradient'])) {
    $styles[] = 'background:' . $block_style['color']['gradient'];
}

// Typographie custom
$typo_map = [
    'fontSize'      => 'font-size',
    'lineHeight'    => 'line-height',
    'fontWeight'    => 'font-weight',
    'letterSpacing' => 'letter-spacing',
    'textTransform' => 'text-transform',
    'fontStyle'     => 'font-style',
];
foreach ($typo_map as $js_key => $css_prop) {
    if (!empty($block_style['typography'][$js_key])) {
        $styles[] = $css_prop . ':' . $block_style['typography'][$js_key];
    }
}

$block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

$sep_html = $separator
    ? '<span class="dc26-ticker__sep" aria-hidden="true">' . esc_html($separator) . '</span>'
    : '';
?>

<div
    <?php if ($block_id) : ?>id="<?php echo esc_attr($block_id); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    style="<?php echo esc_attr(implode(';', $styles)); ?>"
    role="marquee"
    aria-live="off">

    <?php if ($label) : ?>
    <span class="dc26-ticker__label"><?php echo esc_html($label); ?></span>
    <?php endif; ?>

    <div class="dc26-ticker__viewport">
        <?php for ($s = 0; $s < 2; $s++) : ?>
        <ul class="dc26-ticker__track" aria-hidden="<?php echo $s > 0 ? 'true' : 'false'; ?>">
            <?php foreach ($items as $item) :
                $text = (string) ($item['nt_text'] ?? '');
                $href = (string) ($item['nt_link'] ?? '');
                if (!$text) continue;
            ?>
            <li class="dc26-ticker__item">
                <?php if ($href && filter_var($href, FILTER_VALIDATE_URL)) : ?>
                    <a href="<?php echo esc_url($href); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($text); ?></a>
                <?php else : ?>
                    <?php echo esc_html($text); ?>
                <?php endif; ?>
                <?php echo $sep_html; ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
    </div>

</div>
