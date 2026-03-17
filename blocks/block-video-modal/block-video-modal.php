<?php
/**
 * Video Modal Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
    $anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'video-modal-block';
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $class_name .= ' align' . $block['align'];
}

// Get ACF fields
$video_url = get_field('video_url') ?: '';
$button_text = get_field('button_text') ?: 'Regarder la vidéo';
$thumbnail_url = get_field('thumbnail_url') ?: '';
$video_title = get_field('video_title') ?: '';

// Generate unique ID for this modal instance
$modal_id = 'video-modal-' . uniqid();

// Check if we're in admin (editor preview)
$is_admin = is_admin();
?>

<div <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>">
    
    <?php if ($is_admin) : ?>
        <!-- Prévisualisation dans l'éditeur -->
        <div class="bg-gray-100 p-6 rounded-lg border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="text-gray-500 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-heading font-bold mb-2">Video Modal</h3>
                <?php if ($video_url) : ?>
                    <p class="text-sm text-gray-600 mb-4">✅ URL configurée</p>
                    <?php if ($thumbnail_url) : ?>
                        <div class="relative inline-block">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="Preview" class="w-48 h-auto rounded-lg">
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-lg">
                                <div class="w-12 h-12 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <button class="bg-primary text-white px-4 py-2 rounded-none uppercase font-body font-medium">
                            <?php echo esc_html($button_text); ?>
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="text-sm text-gray-600">Configurez l'URL de la vidéo dans les paramètres du bloc.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif (empty($video_url)) : ?>
        <!-- Message d'erreur sur le front-end -->
        <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
            <p class="text-red-600 text-sm">⚠️ URL de vidéo manquante</p>
        </div>
    <?php else : ?>
        
        <!-- Bouton de déclenchement -->
        <button 
            type="button" 
            class="video-modal-trigger"
            data-modal-target="<?php echo esc_attr($modal_id); ?>"
            data-video-src="<?php echo esc_url($video_url); ?>"
            aria-label="<?php echo esc_attr($button_text); ?>"
        >
            <?php if ($thumbnail_url) : ?>
                <div class="relative group">
                    <img 
                        src="<?php echo esc_url($thumbnail_url); ?>" 
                        alt="<?php echo esc_attr($video_title ?: $button_text); ?>"
                        class="w-full h-auto transition-transform duration-300 group-hover:scale-105"
                    >
                    <div class="video-modal-overlay">
                        <div class="video-modal-overlay__icon">
                            <img
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/play.svg'); ?>"
                                alt=""
                                class="video-modal-overlay__icon-image"
                                aria-hidden="true"
                            >
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <span class="video-modal-trigger__icon">
                    <img
                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/play.svg'); ?>"
                        alt=""
                        class="video-modal-trigger__icon-image"
                        aria-hidden="true"
                    >
                </span>
            <?php endif; ?>
        </button>

        <!-- Modale -->
        <div 
            id="<?php echo esc_attr($modal_id); ?>" 
            class="video-modal fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="<?php echo esc_attr($modal_id); ?>-title"
        >
            <div class="video-modal-content bg-white max-w-4xl w-full max-h-[90vh] overflow-hidden">
                
                <!-- Header de la modale -->
                <div class="flex items-center justify-between p-4 border-b">
                    <?php if ($video_title) : ?>
                        <h3 id="<?php echo esc_attr($modal_id); ?>-title" class="text-lg font-heading font-bold">
                            <?php echo esc_html($video_title); ?>
                        </h3>
                    <?php endif; ?>
                    <button 
                        type="button" 
                        class="video-modal-close text-gray-500 hover:text-gray-700 transition-colors duration-200"
                        aria-label="Fermer la modale"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenu vidéo -->
                <div class="video-container relative w-full" style="padding-bottom: 56.25%;">
                    <iframe 
                        class="absolute top-0 left-0 w-full h-full"
                        src=""
                        data-src="<?php echo esc_url($video_url); ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php if (!$is_admin) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour tous les boutons de modale vidéo
    document.querySelectorAll('.video-modal-trigger').forEach(function(trigger) {
        const modalId = trigger.getAttribute('data-modal-target');
        const modal = document.getElementById(modalId);
        const closeBtn = modal.querySelector('.video-modal-close');
        const iframe = modal.querySelector('iframe');

        // Ouvrir la modale
        trigger.addEventListener('click', function() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Charger la vidéo seulement à l'ouverture
            const baseSrc = trigger.getAttribute('data-video-src') || iframe.getAttribute('data-src') || '';
            if (baseSrc) {
                iframe.setAttribute(
                    'src',
                    baseSrc + (baseSrc.includes('?') ? '&' : '?') + 'autoplay=1'
                );
            }
        });

        // Fermer la modale
        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            
            // Arrêter la vidéo en vidant le src
            iframe.setAttribute('src', '');
        }

        closeBtn.addEventListener('click', closeModal);

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Fermer en cliquant sur l'overlay
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
});
</script>
<?php endif; ?>
