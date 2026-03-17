<?php

/**
 * Header Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
    $anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = '';
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $class_name .= ' align' . $block['align'];
}

$post_id = get_the_ID();
$is_admin = is_admin();

// Récupérer le logo depuis les options ACF
$logo_id = get_field('logo_white', 'option');

// Récupérer les données de l'image si l'ID existe
$logo = null;
if ($logo_id) {
    $logo = wp_get_attachment_image_src($logo_id, 'full');
    $logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true);
}


?>

<?php if ($is_admin) : ?>
    <div class="admin-view-only flex items-center justify-center bg-slate-200 p-12 hover:bg-slate-300 transition-all">
        <h3>Custom header</h3>
    </div>
    <?php return; ?>
<?php endif; ?>

<div id="menu-container" <?php echo esc_attr($anchor); ?> class="alignfull w-full top-0 fixed z-50">


    <!-- Scrolled: Small logo + navigation (appears on scroll) -->
    <div class="header-scrolled  w-full  py-3 bg-white relative">
        <div class="container mx-auto">
            <div class="flex flex-col items-center gap-2">
                <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-link">
                    <?php $logo_compass_url = get_template_directory_uri() . '/assets/img/THMG_logo_long_color.svg'; ?>
                    <img
                        src="<?php echo esc_url($logo_compass_url); ?>"
                        alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                        class="logo-image"
                        loading="eager">
                </a>

                <!-- Menu -->
                <nav class="hidden lg:flex items-center gap-8 ">
                    <?php wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'flex gap-8 items-center',
                        'fallback_cb' => false
                    )); ?>
                </nav>
            </div>

            <!-- Social -->
            <div class="hidden lg:block absolute right-0 top-0">
                <?php block_template_part('social-nav'); ?>
            </div>

            <!-- Burger -->
            <button id="burger-icon" class="burger-menu lg:hidden z-50 w-12 h-12" aria-expanded="false" aria-controls="offcanvas">
                <span class="bg-primary block"></span>
                <span class="bg-primary block"></span>
                <span class="bg-primary block"></span>
            </button>
        </div>
    </div>
</div>

<!-- Off-canvas menu -->
<div id="overlay" class="fixed inset-0 z-30 hidden backdrop-blur-sm bg-white/30"></div>
<div id="offcanvas" class="fixed z-40 w-[500px] h-full top-0 -right-[500px] bg-white flex flex-col p-8 pt-64 transition-all drop-shadow-md bg-light-gray">
    <?php wp_nav_menu(array(
        'container_id'    => 'offcanvas-menu',
        'container_class' => '',
        'menu_class'      => '',
        'li_class'        => 'mb-6',
        'fallback_cb'     => false
    )); ?>
</div>
