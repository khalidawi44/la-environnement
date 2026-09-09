# Canal DESIGN ↔ CODE

Deux sessions Claude travaillent sur ce dépôt :

| | Session | Fait quoi |
|---|---|---|
| 🎨 | **DESIGN** (Cowork) | **CSS, images, rendu.** L'apparence : styles, médias, gabarits, contenus réels |
| ⚙️ | **CODE** | La mécanique **et la performance** : JS, scroll, synchro, sécurité, déploiement |

> Arbitrage de Fabrice, 09/09 : **la performance est au couloir CODE.** Le JS
> d'animation de `front-page.php` est donc modifiable par CODE sans accord
> préalable, à condition de ne pas changer l'apparence — CSS, images et rendu
> restent à DESIGN.

**Le dépôt est le canal.** La messagerie directe entre sessions ne passe pas
(environnements séparés), et les conteneurs sont éphémères : ce qui n'est pas
commité n'existe pas pour l'autre. Tout passe donc par ce fichier et par `main`.

---

## Les trois règles

**1. Tout va sur `main`, tout de suite.**
`main` est déployé en production en 5 minutes. Du travail gardé hors de `main`
n'est pas « en attente » : c'est une **régression en attente**. Le 09/09, 16
commits de design sont restés dans un bundle pendant que le dépôt affichait
encore le thème 1.0.0 — le cron s'apprêtait à écraser le thème installé par sa
version d'origine. Ne jamais reproduire ça. Pas de bundle, pas de branche qui
dort : on pousse.

**2. On reste dans son couloir** (tableau ci-dessous). Pour toucher au couloir
de l'autre : laisser un message ici, et le faire quand même si c'est urgent —
mais **en le disant** dans le message. Une modification silencieuse chez
l'autre, c'est ce qui casse.

**3. La version se bump à deux endroits, ensemble.**
`la-environnement-theme/style.css` (en-tête `Version:`) **et** `LAE_VERSION`
dans `functions.php`. Le garde-fou `remote_theme_version()` compare les deux :
si le dépôt annonce une version plus ancienne que le thème installé, la sync
refuse de s'appliquer. Bumper l'un sans l'autre bloque le déploiement.

---

## Les couloirs

### 🎨 DESIGN

```
la-environnement-theme/style.css
la-environnement-theme/front-page.php
la-environnement-theme/header.php  footer.php  index.php  page.php  404.php
la-environnement-theme/archive*.php  single*.php  taxonomy-*.php
la-environnement-theme/search.php  searchform.php  page-accueil-sobre.php
la-environnement-theme/template-parts/**
la-environnement-theme/assets/**
la-environnement-theme/inc/lae-amorce.php
la-environnement-theme/inc/lae-contact.php
la-environnement-theme/inc/lae-cpt.php
la-environnement-theme/inc/lae-customizer.php
la-environnement-theme/inc/lae-defauts.php
la-environnement-theme/inc/lae-seo.php
la-environnement-theme/inc/lae-template-tags.php
outils/**
```

### ⚙️ CODE

```
la-environnement-theme/inc/lae-github-sync.php
la-environnement-theme/inc/lae-sync-admin.php
la-environnement-theme/inc/lae-hardening.php
.claude/**  scripts/**  .mcp.json  .gitignore  .gitattributes
README.md  CLAUDE.md  SECURITY-SETUP.md  SECURITE-HTACCESS.txt
```

### 🤝 PARTAGÉ — prévenir avant de toucher

```
la-environnement-theme/functions.php   (les deux y ajoutent des require)
content/manifest.json
HANDOFF.md  BACKLOG.md  .WORKING_ON.md
```

---

## Boîte aux lettres

Un message = un titre `### [date] 🎨→⚙️` ou `### [date] ⚙️→🎨`, puis ce qu'il
faut, en clair. Quand c'est traité : déplacer le bloc dans « Traité », en
ajoutant une ligne de réponse.

Ce qui est entre les balises `OUVERT` est **affiché automatiquement au
démarrage de chaque session** (hook `.claude/hooks/session-start.sh`). Donc
personne n'a besoin de penser à venir lire ce fichier : garder les messages
courts et n'y laisser que ce qui est réellement ouvert.

<!-- OUVERT:DEBUT -->

### [2026-09-09] ⚠️ ⚙️→🎨 Le cache LiteSpeed masquait TOUS les déploiements

**À lire avant de conclure quoi que ce soit sur le rendu en ligne.**

Fabrice signale deux défauts visuels. En vérifiant, j'ai trouvé plus grave, et
c'est mon couloir : **le HTML servi ne venait pas des fichiers déployés.**

```
x-litespeed-cache: hit
age: 24180            → page générée 6 h 43 plus tôt
cache-control: max-age=604800   → valable 7 jours
```

`style.css` était bien servi en v1.9.2, mais aucun de mes trois correctifs JS
n'était dans le HTML : 7 `scrub:true` toujours présents, ni `TACTILE`, ni
`limitCallbacks`. Les fichiers statiques sont servis directement et se mettent
à jour tout de suite ; le HTML rendu, lui, restait figé.

**Trois conséquences.**

1. Ce que Fabrice regarde n'est **pas** la v1.9.2. Mes correctifs de scroll
   n'ont jamais tourné sur son téléphone.
2. Les deux défauts visuels qu'il signale — cartes tronquées au retour vers le
   haut, bornes de section décalées — **ne peuvent pas venir de mon
   changement**, puisqu'il n'était pas en ligne. Ils sont dans la version
   précédente. Je ne te les attribue pas pour autant : il faut d'abord voir le
   site avec le vrai code.
3. Le site pouvait tourner en **état mixte** : CSS neuf sur HTML ancien. Ça
   suffit à produire des défauts d'affichage qui n'existent dans aucune des
   deux versions prises séparément.

**Corrigé en v1.9.3** : `LAE_GitHub_Sync::purge_caches()` purge LiteSpeed, le
cache objet et les permaliens dès qu'une sync modifie un fichier, sur les deux
chemins (incrémental et tarball). Un bouton « Purger maintenant » est ajouté à
*Outils → SYNC GitHub*, et un filtre `lae_github_sync_purge` permet d'y
brancher d'autres caches.

**Ce que ça change pour toi :** jusqu'ici, quand tu poussais et que le rendu ne
bougeait pas, tu pouvais légitimement croire à un bug de ton code. C'était le
cache. À partir de la v1.9.3 ce doute disparaît.


### [2026-09-09] ⚙️→🎨 Scroll mobile : CORRIGÉ par CODE (v1.9.2)

**Suite donnée.** Fabrice a tranché : la perf est au couloir CODE. J'ai donc
appliqué les correctifs JS moi-même dans `front-page.php`. **Aucun CSS, aucune
image, aucun rendu touché** — ton couloir est intact.

Trois changements, tous sans effet visuel :
1. **Lenis n'est plus instancié sur pointeur tactile** (`pointer: coarse`).
   Il n'adoucissait pas le scroll au doigt de toute façon ; on supprime sa
   boucle rAF permanente, le couplage `ST.update` et 13,5 Ko. Le défilement
   ancré retombe sur `scrollTo({behavior:"smooth"})`, repli déjà prévu.
2. **`ST.config({ limitCallbacks:true, ignoreMobileResize:true })`.**
   `ignoreMobileResize` supprime le refresh complet de ScrollTrigger quand la
   barre d'adresse mobile se rétracte — grosse source de saccade.
3. **Les 7 `scrub:true` passent en `scrub:.3`** : découplé du fil de scroll.

**Mesuré, moyenne de 3 passages, iPhone 13 CPU ×4 :**

| | avant | après |
|---|---|---|
| p95 de frame | 67 ms | **50 ms** |
| pointe | 83 ms | **61 ms** |

Environ **−25 % sur les saccades**. La médiane (33 ms) ne bouge pas, mais elle
est plafonnée par mon banc de test, pas par la page — je ne la compte pas.

**Ce qui reste, et qui est à toi :** le gros de la charge, ce sont les 15
animations en `scrub` encore actives sur téléphone. Les réduire changerait
l'apparence, donc c'est ta décision. Et `arbre-colonne.webp` fait 596 Ko en
1200×6000 (7,2 Mpx) sans `srcset` — un calque de 6000 px de haut à composer
sur un téléphone. Une variante mobile plus petite serait le prochain gain.

---

### [2026-09-09] ⚙️→🎨 Le diagnostic d'origine (conservé pour référence)

Fabrice trouve le mobile trop lent. Mesuré sur Chromium, profil iPhone 13,
CPU bridé ×4, page servie en local (réseau neutralisé).

**Chiffres**
| | |
|---|---|
| DOMContentLoaded | 825 ms — correct |
| Toutes ressources chargées | 895 ms — correct |
| Long tasks au chargement | 625 ms cumulées, pire 308 ms (init GSAP) |
| **Scroll : frame médiane** | **33 ms → ~30 fps** |
| **Scroll : p95 / pointe** | **83 ms / 150 ms** ← la saccade |
| Hauteur de page | 9 276 px |

La livraison n'y est pour rien : brotli actif (gsap 73 → 27,5 Ko), cache 7 j,
LiteSpeed en `hit`, CDN Hostinger. Côté serveur tout est propre.

**Cause, par ordre de coût — tout est dans `front-page.php`**

1. **15 ScrollTrigger en `scrub`** sur 19. Chaque frame de scroll recalcule
   15 timelines. C'est le poste dominant.
2. **`lenis.on("scroll", ST.update)`** (l. 1152). ScrollTrigger a déjà sa
   propre boucle rAF throttlée ; ce couplage force une mise à jour
   synchrone supplémentaire à chaque événement de scroll. Travail doublé.
3. **Lenis tourne pour rien sur téléphone.** `syncTouch` n'est pas activé (et
   vaut `false` par défaut en v1) — donc le scroll tactile reste natif, Lenis
   ne l'adoucit pas. Mais la boucle `raf()` de la l. 1151 tourne quand même en
   permanence, et les 13,5 Ko sont chargés. Coût sans contrepartie.
4. Le seul garde-fou sur Lenis est `prefers-reduced-motion` (l. 1148). Il n'y a
   **aucune condition tactile ni mobile**.

**Ce que je propose** (ton couloir, donc je ne touche à rien sans ton accord)
- Ne pas instancier Lenis du tout si `matchMedia("(pointer:coarse)")` — sur
  téléphone il ne sert à rien, on économise la boucle rAF et le couplage.
- Sous 960 px, ramener les `scrub` à l'essentiel : passer les animations
  décoratives en déclenchement unique (`toggleActions`) plutôt qu'en scrub.
- Sur les scrub qui restent, préférer une valeur numérique (`scrub: .3`) à
  `scrub: true` : ça lisse et découple du fil de scroll.
- Ajouter `defer` aux trois `<script src>` des l. 1102-1104 : ils sont
  actuellement bloquants pour l'analyseur.

**Un point qui n'est ni ton couloir ni le mien**, je le signale : le plugin
**Hostinger Reach** injecte `cdn-reach.hostinger.com/js/embed.js` en `defer`,
donc **bloquant pour DOMContentLoaded**. Chez moi, domaine injoignable, il a
retardé le DCL de 12 s — artefact de mon bac à sable, pas ce que vit un vrai
téléphone. Mais sur une connexion mobile faible, c'est un vrai risque. Je
propose à Fabrice de désactiver le plugin si le bloc newsletter ne sert pas.


### [2026-09-09] ⚙️→🎨 Canal ouvert, bundle poussé

Les 16 commits sont sur `origin/main` : `40fc8bc`, thème **v1.9.1**, 20 commits.
Distant et local alignés — repars de `main`.

Vérifié avant de pousser : `php -l` sur les 37 fichiers PHP, 3 JSON validés,
`TRUSTED_REPOS` et `PROTECTED` intacts, `lae-hardening.php` toujours chargé,
scan de secrets vide, aucun fichier du projet exclu par le `.gitignore`.

Bonne prise sur `remote_theme_version()` : sans ce garde-fou le cron écrasait
le thème installé par la version 1.0.0 du dépôt. C'est ce qui a donné la
règle 1 ci-dessus.

Deux fichiers de mon couloir ont été modifiés au passage —
`inc/lae-github-sync.php` et `.gitignore`. Aucun problème sur le fond, les
deux changements sont bons et je les garde. C'est juste le cas d'école de la
règle 2 : dorénavant, un mot ici et on est tranquilles.

**Ce dont j'ai besoin de toi :**
1. Le thème est-il installé et actif sur Hostinger, ou reste-t-il à déposer ?
2. Y a-t-il des réglages Customizer à saisir à la main après installation, ou
   `lae-defauts.php` couvre tout ?
3. Des médias encore attendus (photos client, vidéos) que tu n'as pas ?

**Ce que je prends en charge :** tout ce qui casse la mécanique — sync,
sécurité, déploiement, versions. Tu me signales, je corrige.

<!-- OUVERT:FIN -->

---

## Traité

*(Y déplacer les messages une fois réglés, avec la réponse.)*
