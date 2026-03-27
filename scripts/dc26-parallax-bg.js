import { gsap } from 'gsap';

export default function parallaxBg() {
    const el = document.querySelector('#bg-pattern-layer');
    if (!el) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const factor = parseFloat(el.dataset.factor ?? '0.2');
    const mode   = el.dataset.mode ?? 'pattern';
    const tileW  = parseFloat(el.dataset.tileW) || 400;
    const tileH  = parseFloat(el.dataset.tileH) || 400;

    // Opacity is handled via CSS variable --bg-opacity injected by PHP.
    // No JS needed — immune to GSAP overwrites.

    // Center + initial position
    gsap.set(el, { xPercent: -50, y: 0 });

    // Height — must cover full document for parallax travel
    function updateHeight() {
        const docH = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
        el.style.height = `${docH + docH * (1 - factor)}px`;
        if (mode === 'pattern') rebuildTiles();
    }

    // Ensure enough tiles to fill the layer height (PHP outputs 100, may need more)
    function rebuildTiles() {
        const tiles = el.querySelectorAll('.pattern-tile');
        if (!tiles.length) return;
        const cols   = Math.ceil((el.offsetWidth || 4000) / tileW) + 1;
        const rows   = Math.ceil(parseInt(el.style.height) / tileH) + 1;
        const needed = cols * rows;
        if (needed <= tiles.length) return;
        const frag = document.createDocumentFragment();
        for (let i = tiles.length; i < needed; i++) {
            frag.appendChild(tiles[0].cloneNode(true));
        }
        el.appendChild(frag);
    }

    updateHeight();

    // Inertia parallax — each scroll fires a tween toward the new target.
    // expo.out decelerates exponentially after scroll stops, giving a natural heavy feel.
    window.addEventListener('scroll', () => {
        gsap.to(el, {
            y: -(window.scrollY * factor),
            duration: 2,
            ease: 'expo.out',
            overwrite: true,
        });
    }, { passive: true });

    new ResizeObserver(updateHeight).observe(document.body);
}
