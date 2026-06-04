# STATUS — dc26-phar

## Infos
- **Statut** : prod
- **Site** : dev.phar.swiss (staging) / phar.swiss (prod)
- **Parent** : dc26-base
- **SSH alias** : `phar`
- **Spécificité** : partenaires WooCommerce, FacetWP

## Blocs
- `slider-partners` — marquee Swiper + nav + logos

## Functions
- `facet`
- `menu-walker`
- `phar-bg` — fond animé GSAP
- `phar-enqueue`
- `woocommerce`

## Deploy
- `sites/phar/deploy.sh` — `./deploy.sh` → staging | `./deploy.sh prod` → production (confirmation requise)

## Open tasks
- [ ] Activer le thème sur dev.phar.swiss (WP Admin → Apparence)
- [ ] Syncer les ACF JSON (Admin → ACF → Sync)
- [ ] Vérifier slider-partners sur dev.phar.swiss
- [ ] Importer les 9 partenaires manquants
- [ ] **Fond animé** — `data-opacity` présent dans le DOM mais opacity ne change pas visuellement
- [ ] **Fond animé** — parallaxe pas assez souple (lerp smooth=0.07, revoir easing)
- [ ] Préparer déploiement production phar.swiss
