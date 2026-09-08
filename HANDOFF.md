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

- **Design du thème intégré (v1.1.0)** : accueil composée de sections
  (bandeau, réassurance, prestations, déroulé de chantier, réalisations,
  zone d'intervention, bande devis), gabarits internes, pied de page
  complet, barre d'appel mobile.
- Deux types de contenu : **Prestations** et **Réalisations**
  (+ taxonomie « type de chantier »), saisis depuis l'administration.
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

## Reste à faire côté contenu (bloquant)

Le design est en place, **le contenu réel manque** :

1. Textes et prestations de l'ancien site (domaine cassé — à récupérer).
2. Coordonnées : téléphone, e-mail, adresse, horaires, mention légale.
3. Communes de la zone d'intervention.
4. **Photos de chantier** — la section Réalisations ne vaut que par elles.

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
