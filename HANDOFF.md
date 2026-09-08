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

- Socle du thème posé (gabarits de base, styles minimaux, menus).
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

## Reste à faire côté design

Le thème est volontairement nu. Le design est réalisé séparément (Cowork),
puis intégré ici. La skill `ui-ux-pro-max` (dans `.claude/skills/`) est
disponible pour les décisions de style, typographie, palette et accessibilité.

## Historique

*(À compléter à chaque session : ce qui a été fait, ce qui a été décidé, ce
qui a été mis de côté et pourquoi.)*
