# HANDOFF — L.A Environnement

> Dernière mise à jour : 2026-09-08 — branche de travail : `main` (tampon auto à chaque commit).
> Document de reprise pour toute nouvelle session Claude (PC, web ou mobile).
> Lire ce fichier en premier, puis `BACKLOG.md` pour ce qui est en réserve.

## Le projet en une phrase

Site WordPress du client **L.A Environnement**, hébergé sur Hostinger,
déployé automatiquement depuis ce dépôt (push sur `main` → en ligne sous
5 minutes).

## Règle qui prime sur tout

`main` = **production**. Un commit resté sur une branche n'est **pas** en
ligne. Un PHP cassé poussé sur `main` casse le site du client dans les
5 minutes. Toujours `php -l` avant de committer (le pre-commit le fait, mais
ne pas s'y reposer aveuglément : il ne tourne que si `php` est dans le PATH).

## État actuel

- **La vidéo de fond est livrée** : `assets/video/canopee.mp4` (536 Ko, 5 s,
  bouclable) — une canopée vue d'en bas, agitée par le vent, rendue avec
  Blender (`outils/canopee-blender.py`). Trois houppiers à des profondeurs
  différentes pour la parallaxe, mouvement en sinusoïdes qui bouclent, étalonnage
  vert sombre à l'encodage. Le réglage « Vidéo de fond » du personnalisateur la
  remplace ; laissé vide, c'est elle qui tourne.
- **La colonne (v1.3.0)** — le décor unique de l'accueil : une vidéo qui
  occupe tout l'écran derrière TOUTE la page, et un arbre haut de plusieurs
  écrans que l'on descend en défilant (cime en haut de page, racines en bas).
  L'arbre livré est un **rendu 3D** (Blender / Cycles, 1200 x 6000 px, fond
  transparent) : `assets/images/arbre-colonne.webp`, 463 Ko. Le script qui le
  produit est versionné dans `outils/arbre-blender.py` — il se rejoue avec
  `blender -b -noaudio --python outils/arbre-blender.py`, ce qui permet de
  regénérer l'arbre (autre essence, autre saison) sans repartir de zéro.
  Une photo verticale le remplace depuis le personnalisateur (« Arbre en
  colonne »).
- **Le site est livré rempli** : `inc/lae-amorce.php` crée à la première
  activation les 3 familles, les 6 prestations du métier avec leurs textes,
  les pages (Accueil, Notre façon de travailler, Contact, Mentions légales),
  l'accueil statique et les 3 menus. Idempotent : ne tourne qu'une fois, ne
  recrée pas une page existante, ne remplace pas un menu déjà en place.
- **Formulaire de contact** (`inc/lae-contact.php`, raccourci `[lae_contact]`) :
  sans plugin, sans stockage. Le message part par e-mail à l'adresse du
  personnalisateur ; rien n'est enregistré sur le site. Jeton de session,
  champ leurre et limite de 3 envois par heure et par IP.
- **Tous les textes sont réels** (plus aucun « à écrire avec le client ») et
  modifiables dans le personnalisateur.
- **Accueil cinématique (v1.2.0)** — même mécanique que l'accueil d'Alliance
  Groupe, en vert : bandeau plein écran (vidéo), bandeau défilant accéléré par
  la vitesse de scroll, grande image épinglée en Ken Burns, chapitres en
  parallaxe, **scène unique** (l'image se dissout en poussière de feuilles, une
  main paraît, les trois prestations phares en sortent, s'évaporent, la grille
  filtrable prend leur place), réalisations, révélation, appel.
  Fichier : `front-page.php`. Libs : `assets/js/lib/{gsap,ScrollTrigger,lenis}`.
- Sans JavaScript, sans GSAP, ou sous « réduire les animations » : la page
  s'affiche dans son **état final**, tout visible et cliquable.
- L'accueil sectionné (calme) reste disponible : gabarit de page
  « Accueil sobre (sections) » (`page-accueil-sobre.php`).
- Design du thème intégré (v1.1.0) : gabarits internes, pied de page complet,
  barre d'appel mobile.
- Deux types de contenu : **Prestations** (+ taxonomie « Familles », qui
  alimente les filtres de l'accueil) et **Réalisations** (+ taxonomie
  « type de chantier »), saisis depuis l'administration.
- Tout le contenu éditable est dans **Apparence → Personnaliser →
  L.A Environnement** : coordonnées, bandeau, réassurance, étapes, zone
  d'intervention, bande devis. **Un champ vide masque son bloc** — le site
  ne montre jamais de texte de remplissage.
- Aucune police ni ressource externe (RGPD) : pile système, icônes SVG inline.
- Socle du thème posé (gabarits de base, styles, menus).
- Moteur de sync GitHub → WordPress opérationnel (`LAE_GitHub_Sync`).
- Écran admin *Outils → SYNC GitHub* en place.
- MCP Hostinger déclaré (`.mcp.json`) : WordPress + DNS.
- Config de travail en place : hook SessionStart, pre-commit `php -l`,
  skill design `ui-ux-pro-max`.

## Reste à faire côté Fabrice

1. Déposer `la-environnement-theme/` dans `wp-content/themes/` sur Hostinger
   et activer le thème (voir `README.md`, section Installation).
2. Lancer la première sync (*Outils → SYNC GitHub* → « Synchroniser maintenant »).
3. Vérifier que l'état passe à **À jour**.

## Fiche Google de l'entreprise — source de vérité

Relevé le 09/09/2026 sur la fiche Google (place_id `ChIJd15eFR_nBUgRqwOuCR9Hlrs`) :

- Nom exact affiché : **L A environnement** (sans point après le L)
- Catégorie Google : **Paysagiste**
- Téléphone : **07 59 79 03 96** — renseigné dans le thème
- Site déclaré : **paysagiste-environnement.com** — c'est le domaine « cassé ».
  Diagnostic : le certificat HTTPS est **auto-signé**, donc tout navigateur
  affiche un avertissement de sécurité avant d'ouvrir la page. Ce n'est pas le
  domaine qui est mort, c'est son certificat.
- Note : **5,0 sur 1 avis** (un seul, réel, de Djessy Azs, mai 2025, avec
  réponse du propriétaire). Recopié dans le thème.
- Pas d'adresse publiée sur la fiche (établissement sans vitrine).
- Le point de la fiche est situé en **Vendée** (46,754 / -1,470), alors que le
  site Alliance Groupe présente L.A Environnement comme « Paysagiste ·
  Loire-Atlantique ». **À trancher avec le client** : c'est la zone
  d'intervention et tout le référencement local qui en dépendent.
- 4 photos du propriétaire + 1 vidéo de 29 s sur la fiche : à récupérer.

## Reste à faire côté contenu (bloquant)

Le design est en place, **le contenu réel manque** :

1. Textes et prestations de l'ancien site (domaine cassé — à récupérer).
2. Coordonnées : téléphone, e-mail, adresse, horaires, mention légale.
3. Communes de la zone d'intervention.
4. **Photos de chantier** — la section Réalisations ne vaut que par elles.
4 bis. **Mentions légales** : la page est créée mais VIDE de contenu réel
   (SIRET, hébergeur, assurance). Obligatoire avant mise en ligne.
5. **Médias de l'accueil cinématique** : la grande image
   épinglée, les trois images de chapitre, l'image qui se dissout, la main
   ouverte, l'image ronde de la révélation. Sans elles la scène tourne, mais
   sur des dégradés.
6. **Promesses à confirmer** dans les textes livrés (aucun chiffre, aucun nom,
   aucune certification n'a été écrit — mais ces phrases engagent) :
   « Diagnostic sur place avant le devis », « On se déplace, on regarde, et
   vous repartez avec un devis écrit », « Déchets verts évacués ou broyés sur
   place », « On travaille à la corde là où la nacelle ne passe pas ».
   À valider ou réécrire dans Apparence → Personnaliser → L.A Environnement.

Rien de tout cela n'est inventé ni pré-rempli : les réglages sont vides et
les blocs correspondants restent masqués tant qu'ils ne sont pas remplis.

## Repères pour la suite

- Menus à créer : `principal`, `pied`, `legal`.
- Trois emplacements de menu, deux types de contenu, un panneau de
  personnalisation — tout est décrit dans `inc/lae-customizer.php`.
- La skill `ui-ux-pro-max` (dans `.claude/skills/`) reste disponible pour les
  décisions de style, typographie, palette et accessibilité.

## Historique

*(À compléter à chaque session : ce qui a été fait, ce qui a été décidé, ce
qui a été mis de côté et pourquoi.)*
