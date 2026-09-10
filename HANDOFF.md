# HANDOFF — L.A Environnement

> Dernière mise à jour : 2026-09-10 — branche de travail : `main` (tampon auto à chaque commit).
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

**Site en ligne : `elagage-vertou.fr`, thème v1.9.3.**

✅ **Scroll mobile corrigé et confirmé par Fabrice sur son téléphone (09/09).**
Mesuré avant/après sur iPhone 13 CPU ×4 : p95 de frame 67 → 50 ms, pointe
83 → 61 ms. Trois changements : Lenis n'est plus instancié sur pointeur
tactile, `ST.config({limitCallbacks, ignoreMobileResize})`, et les `scrub:true`
passés en `scrub:.3`.

🧩 **CAUSE RACINE trouvée le 09/09 au soir — deux couches de cache, pas une.**
Le site est derrière **LiteSpeed** (serveur) *et* le **CDN Hostinger**
(`x-hcdn-cache-status`). Les pages HTML partaient avec
`cache-control: public, max-age=604800` — **7 jours**. D'où :
- un correctif déployé restait invisible jusqu'à une semaine ;
- **chaque nœud du CDN gardait sa propre copie** : deux relevés successifs
  depuis la même machine pouvaient se contredire, et deux visiteurs voir deux
  versions différentes le même jour ;
- purger LiteSpeed ne servait à rien, le CDN resservait sa copie.

Corrigé en **v1.9.8** : le HTML passe à
`max-age=0, s-maxage=300, stale-while-revalidate=60`. Un push est désormais
visible en 5 minutes maximum. Les statiques gardent leur cache long, ils
portent une empreinte `?ver=`.

⚠️ **Reste une action manuelle, une seule fois :** les copies stockées AVANT
la v1.9.8 gardent leur TTL de 7 jours. Il faut **purger le CDN Hostinger une
fois depuis hPanel** pour les évacuer. Après ça, plus jamais.

⚠️ **Le cron serveur est la condition d'existence du déploiement.** Une page
servie depuis le cache ne fait pas démarrer WordPress, donc WP-Cron ne tourne
pas, donc la sync ne s'exécute pas. Plus le cache est efficace, moins on
déploie. À créer dans hPanel → Tâches Cron, toutes les 5 min :
```
wget -q -O - https://elagage-vertou.fr/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```
(Note : WordPress pose un verrou de 60 s entre deux exécutions du cron —
inutile de l'appeler plus souvent, les appels rapprochés ne font rien.)

⚠️ **Piège de mesure, deux fois tombé dedans le 09/09.** Ne jamais vérifier un
correctif en cherchant un texte qui est AUSSI affiché dans la page : le chapô
du hero sert de meta description *et* de texte visible, donc un `grep` sur son
contenu matche toujours. Vérifier la **balise**, pas le texte :
```bash
curl -sS https://elagage-vertou.fr/ | grep -oiE "<meta name=.description.[^>]*>"
```
Et comparer systématiquement l'URL normale avec un rendu frais (`?nc=alea`) :
si les deux diffèrent, c'est du cache, pas du code.

⚠️ **Leçon du 09/09 — le cache masquait tous les déploiements.** Le thème était
déployé et le site servait l'ancienne page depuis le cache LiteSpeed (`hit`,
`age: 24180`, `max-age: 604800`). Corrigé en v1.9.3 : `purge_caches()` purge
LiteSpeed, le cache objet et les permaliens à chaque sync qui modifie un
fichier. **Ne jamais conclure qu'un correctif ne marche pas sans avoir lu le
HTML réellement servi :**
```bash
curl -sSI https://elagage-vertou.fr/ | grep -iE "x-litespeed|^age:"
curl -sS  https://elagage-vertou.fr/ | grep -c "UNE_CHAINE_DU_NOUVEAU_CODE"
```

🎨 **Deux points ouverts, couloir DESIGN** (détail et mesures dans
`docs/DESIGN-CODE.md`) :
1. `.ds__stick{height:100svh;overflow:hidden}` coupe les cartes sous ~844 px de
   viewport — texte tranché, bouton perdu. Déclenché par une bannière de
   notification ou les barres de Safari.
2. `.ds{height:560svh}` = 5,6 écrans épinglés sur 14. Proposition chiffrée :
   280svh, plus `.tab` et `.arbre` à 120svh.



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
- **Le point de la fiche Google est faux.** Il est en Vendée (46,754 / -1,470),
  alors que l'enregistrement du domaine donne l'entreprise à **Vertou (44120),
  Loire-Atlantique** — environ 80 km plus au nord. C'est un problème de
  référencement local sérieux : Google place l'entreprise loin de sa zone.
  À corriger sur la fiche, c'est prioritaire.
- 4 photos du propriétaire + 1 vidéo de 29 s. **Deux seulement sont
  exploitables** et sont intégrées au thème (`jardin-piscine.webp`,
  `pelouse-haie.webp`). Les deux autres n'ont rien à faire là :
  une voiture sur un plateau de dépannage (géolocalisée La Varenne / Drain,
  Maine-et-Loire) et un visuel Alliance Groupe (lion, bureau). **La fiche
  Google du client est à nettoyer** : ces deux images desservent un paysagiste.
- Le domaine est chez **Webador** et le client ne peut pas changer les DNS sans
  payer. `paysagiste-environnement.com/contact` répond « Site introuvable » :
  l'ancien site est perdu, son contenu n'est pas récupérable. Il faut donc une
  nouvelle adresse — un sous-domaine d'alliancegroupe-inc.com est gratuit et
  disponible tout de suite.

## Identité de l'entreprise (enregistrement du domaine, Webador/Openprovider)

- Raison sociale : **L A environnement**
- Contact : **Anthony Lamarque**
- Adresse déclarée : 554 route de Clisson, **44120 Vertou**, France.
  **Non publiée sur le site** : la fiche Google du client n'en publie pas non
  plus, c'est visiblement son choix. Seule la commune apparaît, ce qui suffit
  au référencement local. À rediscuter avec lui s'il veut l'afficher.
- Téléphone : +33 7 59 79 03 96 (cohérent avec la fiche Google)
- E-mail : **paysagisteenvironnement@gmail.com** — renseigné dans le thème,
  c'est lui qui reçoit les demandes du formulaire de contact.

## Adresse du site : le sous-domaine est en place

`la-environnement.alliancegroupe-inc.com` — créé le 09/09/2026 dans hPanel,
sous le domaine de l'agence, comme `gwen-services`.

- Répertoire : `/home/u495311222/domains/alliancegroupe-inc.com/public_html/la-environnement`
- Vérifié : le nom résout (145.223.124.15) et répond en **HTTPS 200**.
  L'enregistrement A a été créé automatiquement — il n'y a pas de joker DNS sur
  le domaine (un sous-domaine au hasard ne résout pas), donc c'est bien celui-ci
  qui a été posé.
- Serveur Hostinger : server1746, Europe (France). IP du pack : 147.79.103.74.
  Serveurs de noms Hostinger : `ns1.dns-parking.com`, `ns2.dns-parking.com`.

**Reste à faire, et c'est pour Fabrice** : installer WordPress dans ce
répertoire. L'installation demande de définir un identifiant et un mot de passe
d'administration — Claude ne saisit pas de mots de passe. Une fois l'install
faite, le thème s'y dépose (ZIP ou synchronisation GitHub).

Penser ensuite à **changer l'adresse du site sur la fiche Google** du client :
elle pointe encore sur `paysagiste-environnement.com`, qui est mort.

## Fabriquer le ZIP du thème — piège à éviter

L'archive doit contenir **un seul dossier à la racine** : `la-environnement-theme/`,
avec `style.css` à sa racine à lui.

```
zip -qr la-environnement-theme-X.Y.Z.zip la-environnement-theme
```

Ne JAMAIS y ajouter `outils/` ni quoi que ce soit d'autre au même niveau. Avec
deux dossiers à la racine, WordPress ne sait pas lequel est le thème et affiche
« Aucune feuille de style n'a été trouvée » — un message trompeur qui envoie
chercher le problème dans le thème alors qu'il est dans l'emballage. L'erreur a
été commise de la v1.5.0 à la v1.8.0 ; corrigée le 09/09/2026.

Vérification en une ligne avant d'envoyer :

```
unzip -l le.zip | awk 'NR>3{print $4}' | cut -d/ -f1 | sort -u   # doit afficher UNE seule ligne
```

## Le nom de domaine du client est inutilisable

- `paysagiste-environnement.com` est enregistré du **06/04/2025 au 06/04/2027**.
  Il est payé, il est à jour.
- Serveurs de noms actuels : `ns1.openprovider.nl`, `ns2.openprovider.be`,
  `ns3.openprovider.eu` (registrar d'arrière-plan de Webador).
- Le mail Webador indique le lien pour les modifier :
  `webador.fr/v2/redirect-website/subscription/domains`
- **Mais l'accès à Webador est bloqué** : une facture impayée verrouille le
  compte, et Fabrice a décidé de ne pas la payer. Les serveurs de noms ne
  peuvent donc pas être modifiés. Le domaine est vivant mais inutilisable —
  d'où le sous-domaine ci-dessus, qui est la solution retenue et non un repli.
- Effet de bord à connaître : l'ancien site Webador, déjà hors ligne, ne
  reviendra pas. Aucune adresse e-mail du domaine n'est en jeu — le client
  utilise une adresse Gmail.

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

## v1.9.0 — piège WordPress à ne jamais réintroduire

`get_theme_mod( 'lae_x' )` ne retombe **pas** sur le `'default'` déclaré dans
`add_setting()`. Sur une installation neuve, tout réglage jamais enregistré
renvoie le second argument de `get_theme_mod()` — donc `''`. Résultat : le site
tournait, mais sans téléphone, sans e-mail, sans titre de héros, sans chapitres.

Source unique désormais : `inc/lae-defauts.php` → `lae_defauts()` / `lae_defaut( $cle )`
(clés **sans** le préfixe `lae_`, filtre `lae_defauts`).
- `lae_reglage( $cle )` y prend son repli quand aucun second argument n'est passé.
- Le personnalisateur n'a plus un seul `'default' =>` littéral : `$ajoute()` lit la table.
- Un second argument explicite reste prioritaire (titres propres aux archives).

**Règle** : un nouveau réglage de contenu se déclare dans `inc/lae-defauts.php`,
jamais dans `$ajoute()`.

`lae_amorce_identite()` corrige le titre posé par l'hébergeur (le nom de domaine)
en « L.A Environnement ». Verrou `lae_identite_faite`, séparé de `lae_amorce_faite`,
pour rattraper les sites déjà amorcés.

## v1.9.1 — « on voit rien » : trois causes, pas une

1. **La boucle vidéo était en silhouette.** Dans `outils/canopee-blender.py`, les
   feuilles avaient un Principled BSDF opaque et le soleil venait d'au-dessus :
   vues d'en bas, elles ne pouvaient être que noires (luminance médiane 18/255).
   Corrigé par un matériau **translucide** (mélange Diffuse / Translucent, 55 %),
   un ciel clair qui éclaire le dessous des feuilles, et un soleil ramené à 7,5.
2. **L'arbre en colonne était sombre pour la même raison** (médiane 40/255).
   Remonté en gamma 2.15 + saturation 1.18 → médiane 106.
3. **Les voiles noircissaient tout l'écran** pour protéger le texte. Remplacés
   par une protection locale : ombre portée sur les mots, halo doux derrière les
   blocs de texte, fond de colonne en dégradé de sous-bois plutôt qu'en aplat noir,
   et une colonne de lumière derrière le tronc.

**Règle** : on protège les mots, jamais tout l'écran. Un voile global au-delà de
~.45 d'opacité annule la vidéo et l'arbre — c'est-à-dire tout ce qui fait le site.

## Garde-fou anti-régression de la sync (important)

Le dépôt GitHub est resté en **thème 1.0.0** (les commits ne partent pas, voir
ci-dessous). La sync toutes les 5 minutes aurait donc **écrasé le thème installé
par la version 1.0.0** : `style.css`, `functions.php`, `header/footer/index/page/
single/404.php` remplacés par leurs versions d'origine, les fichiers plus récents
laissés en place → site hybride cassé.

`LAE_GitHub_Sync::remote_theme_version()` lit maintenant le `Version:` du
`style.css` du dépôt, et `sync()` **refuse** d'appliquer une version inférieure à
`LAE_VERSION`, en le disant dans le journal. À vérifier après chaque reprise :
le dépôt doit être à jour AVANT de compter sur la sync.

## Push GitHub : toujours bloqué

`git push` → « not in this session's authorized repository set » (403).
Connecteur GitHub MCP : lecture OK (`get_me`, `list_commits`), écriture refusée
(« Resource not accessible by integration » sur `git/trees`).
Il manque l'autorisation en écriture côté sources de la session / connecteur.
Tant que ce n'est pas fait : livraison par ZIP, et le garde-fou ci-dessus protège
le site.
