# Règles du projet — Site L.A Environnement

Site **client** (prestation Advise Alliance Group), hébergé sur **Hostinger**.
Dépôt **public**.

## Vérifier avant de déployer (règle absolue)
Un push sur `main` est **en ligne sous 5 minutes**, automatiquement. Il n'y a
pas de filet entre le commit et le site du client.

- `php -l` sur **chaque** fichier PHP touché, avant chaque commit. Un PHP cassé
  poussé sur `main` casse le site en production.
- Ne JAMAIS inventer un nom, un chiffre, une prestation, une référence client.
  Si une info n'est pas vérifiée : rester générique OU demander.
- Relire chaque ligne d'un contenu client avant de le committer.

## Déploiement
- Branche de travail → merge dans `main` → push. **`main` = production.**
- La mise en ligne est automatique (cron 5 min, `LAE_GitHub_Sync`). Aucun FTP.
- Pour forcer : WordPress → *Outils → SYNC GitHub* → « Synchroniser maintenant ».
- Après un déploiement qui compte : **vérifier le site en live**, pas seulement
  supposer que le code suffit.
- Bumper `Version:` dans `style.css` **et** `LAE_VERSION` dans `functions.php`
  à chaque déploiement notable.

## Dépôt public — rien de sensible
Le dépôt est lisible par n'importe qui. N'y entrent JAMAIS :
identifiants, tokens, clés API, mots de passe, accès Hostinger, données
personnelles du client ou de ses contacts.
Les secrets vont dans `wp-config.php` (hors dépôt), ex. `LAE_GH_TOKEN`.

## MCP Hostinger
`.mcp.json` déclare les serveurs `hostinger-wordpress` (38 outils) et
`hostinger-dns` (8), binaires scopés de `hostinger-api-mcp`. Ne PAS basculer
sur le binaire unifié `hostinger-api-mcp` : 382 outils, il sature le contexte.

Auth : OAuth par défaut (identifiants hors dépôt, `~/.config/hostinger-mcp/`),
ou variable `HOSTINGER_API_TOKEN` dans l'environnement. Le jeton ne va JAMAIS
dans `.mcp.json` ni nulle part dans le dépôt.

Le MCP pilote l'hébergement (cache LiteSpeed, maintenance, plugins, thèmes,
mises à jour, DNS) — il ne dépose PAS de fichiers. Le déploiement du code
reste le push sur `main`.

## Ne pas casser la mécanique de sync
`inc/lae-github-sync.php` est le cœur du déploiement. Avant d'y toucher :

- `TRUSTED_REPOS` est une whitelist **en dur**, volontairement. Ne jamais la
  rendre pilotable par de la donnée (option, filtre, réglage admin) : la sync
  écrit du PHP qui sera exécuté → ce serait une RCE.
- Ne jamais retirer un fichier de `PROTECTED` (`wp-config.php`, `.htaccess`…).
- Ne jamais élargir `ALLOWED_EXT` sans raison précise.
- Le SHA local ne doit **jamais** avancer sur un échec partiel : c'est ce qui
  garantit que la sync suivante réessaie.

## Contenu
- Pages et articles versionnés dans `content/` + `content/manifest.json`.
- L'import est **idempotent** : un slug déjà présent dans WordPress est ignoré.
  On n'écrase jamais un contenu édité depuis l'admin par le client.

## Design
Le design est réalisé séparément (Cowork). Le thème livré ici est un socle nu :
structure, gabarits, styles de base. Ne pas y empiler de design non validé.

## Style de code
- Indentation : **tabulations**.
- Préfixe : `lae_` (fonctions, options) / `LAE_` (classes, constantes).
- Chaque brique indépendante (`if ( ! defined( 'ABSPATH' ) ) exit;`, gardes
  `function_exists`), pour rester copiable seule.
- Capacité admin requise partout : `manage_options`.
