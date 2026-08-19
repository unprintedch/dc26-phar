# Changelog — dc26-phar

## 2026-08-19 — Déploiement plugins + refonte header sticky

### Infra / déploiement (découverte critique)

- **`~/web/` sur le serveur `phar` n'est PAS la prod.** C'est une install WordPress abandonnée (ACF gratuit, Wordfence, Avada/Fusion, WPML 3.2.7 jamais mis à jour depuis 2016-2019). La vraie prod est **`~/sites/prod.phar.swiss/`** — confirmé par la présence d'ACF Pro, Gravity Forms, FacetWP (stack requis par le thème) et par les timestamps de modification récents.
- **`dev.phar.swiss` n'existe plus** — il pointe vers un serveur totalement différent (IP différente). L'environnement `sites/prod.phar.swiss` est désormais le seul environnement phar.
- `sites/phar/deploy.sh` a été corrigé :
  - `PROD`/`PROD_MU` pointent maintenant vers `sites/prod.phar.swiss/wp-content/...` (au lieu de `web/...`)
  - Suppression de la logique staging/prod à deux cibles — un seul environnement
  - **Ajout du build de `dc26-base`** avant le sync — le script ne rebuildait auparavant que `dc26-phar`, donc les modifs CSS/JS du thème parent n'étaient jamais recompilées avant déploiement (bug à l'origine de plusieurs allers-retours ce jour)
- `.env` : `PHAR_SSH_PATH` ajouté/corrigé → `sites/prod.phar.swiss/wp-content/themes`
- La clé d'hôte SSH du serveur `phar` (`mqyp.ftp.infomaniak.com`) a changé en cours de session (rotation côté Infomaniak, vérifiée via `ssh-keyscan` — fingerprint cohérent sur requêtes indépendantes, pas d'indice de MITM). `known_hosts` mis à jour.

### Plugins

- **WPML** (core `sitepress-multilingual-cms` 4.9.5 + `wpml-string-translation` 3.5.3) et **Google Site Kit** (1.182.0) copiés depuis le site local `benk` (versions les plus à jour disponibles) et uploadés sur `sites/prod.phar.swiss/wp-content/plugins/`.
- **Non activés** — à faire manuellement dans wp-admin (WPML lancera son assistant de config/migration DB à l'activation).
- Les copies uploadées par erreur dans `~/web/` ont été supprimées.

### Bugs header — dc26-base (thème parent, partagé avec dc26-oav etc.)

Tous ces bugs étaient déjà présents dans le code depuis le refactor `position:fixed → position:sticky` du 18 juin, mais n'avaient **jamais été vus en prod** car `sites/prod.phar.swiss` tournait sur un ancien build (cf. bug deploy.sh ci-dessus). Premier vrai déploiement de dc26-base en prod = premier moment où ils sont devenus visibles.

- **`css/_header-sticky.css`**
  - Header transparent en haut de page (`is-top`) ne montrait plus rien de lisible depuis le passage à `position:sticky` (il ne chevauche plus le hero comme avant avec `position:fixed`). Fix : `margin-bottom: calc(-1 * var(--site-header-height, 0px))` scopé à `.home`/`.front-page` pour recréer le chevauchement.
  - `justify-content: space-between` était forcé en dur sur le groupe racine du header, écrasant le réglage réel du bloc (`is-content-justification-left` visible dans l'éditeur mais ignoré en front). Supprimé — le front respecte maintenant le réglage du bloc, identique à l'éditeur.
  - `.wp-block-navigation { flex: 1 1 auto; }` forçait le nav à grandir et bouffer tout l'espace disponible, cassant l'alignement naturel avec les éléments voisins (bouton Marketplace). Supprimé (trouvé via DevTools par David).
- **`blocks/scroll-image/style.css`** — Le crossfade du logo au scroll ne fonctionnait nulle part : le JS GSAP prévu (`scripts/scroll-image.js`) n'est jamais importé dans le bundle, et `view.js` du bloc suppose qu'une règle CSS `.is-scrolled` gère le crossfade — règle qui n'existait pas. Ajoutée (transition opacity pure CSS, pas de dépendance GSAP).

### Bugs header — dc26-phar (spécifique au site)

- **`css/_header-sticky.css`**
  - Switcher de langue (EN/FR/DE, liste `.horizontal-list` distincte du menu principal) : le texte est dans un `<span class="has-primary-color">` imbriqué (classe utilitaire WP en `!important`), qui suivait la règle générale "nav → couleur primary au scroll" — illisible sur fond givré/clair selon le contenu derrière. Forcé en blanc permanent, langue courante (pas de `<a>`, juste texte) en gras.
  - Bouton **Marketplace** : même souci de lisibilité (couleur primary sur fond givré translucide qui laisse voir des photos derrière). Forcé en blanc permanent (texte + bordure) + `white-space: nowrap` (le texte "MARKETPLACE" wrappait sur 2 lignes dans la largeur du bouton).

### Non résolu / à surveiller

- Le bouton Marketplace est masqué en dessous de 992px de large par le plugin **Block Visibility** (`hide-medium-screen`, `hide-small-screen`) — comportement existant, pas touché.
- Les fixes dans `dc26-base` (chevauchement header, justify-content, flex nav, scroll-image) **ne sont pas encore backportés/vérifiés sur dc26-oav** ou les autres sites utilisant le même thème parent — à vérifier si le même bug de header sticky s'y manifeste.
- Padding asymétrique du header (`spacing--40` vs `spacing--50` gauche/droite) repéré dans le template part **"header light"** (`header-copy`, utilisé par la page d'accueil réelle via `pages-no-title.html`) — pas corrigé, signalé à David pour arbitrage éditeur.
