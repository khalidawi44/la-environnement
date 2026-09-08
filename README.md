# L.A Environnement — site WordPress

Dépôt du site **L.A Environnement**, hébergé sur **Hostinger**.

Le dépôt contient **le thème** (`la-environnement-theme/`) et **le contenu
versionné** (`content/`). Le cœur WordPress, la base de données et les médias
(`wp-content/uploads/`) restent sur l'hébergement, hors dépôt.

---

## Le principe : je push, le site se met à jour

Il n'y a **pas de FTP, pas de déploiement manuel**.

```
   commit + push sur main
            ↓
   API GitHub (SHA du dernier commit)
            ↓
   WP-Cron du site, toutes les 5 min
            ↓
   fichiers du thème mis à jour en place
```

Concrètement : un push sur `main` est en ligne **en moins de 5 minutes**, sans
que personne ait à toucher au serveur.

Le moteur est dans `la-environnement-theme/inc/lae-github-sync.php`
(classe `LAE_GitHub_Sync`).

### Ce que fait le moteur

- **Cron toutes les 5 min** : compare le SHA du dernier commit distant au SHA
  de la dernière sync réussie. Identiques → il ne fait rien.
- **Sync incrémentale** : ne télécharge que les fichiers réellement modifiés
  entre les deux commits (API `compare` + `raw`). Gère créations, mises à jour,
  suppressions et renommages.
- **Repli tarball** : si l'incrémental n'est pas applicable (plus de 300
  fichiers modifiés, API `compare` en échec, première sync), il bascule sur
  l'archive complète de la branche.
- **Backup systématique** : tout fichier écrasé est d'abord copié dans
  `wp-content/uploads/lae-backups/`.
- **Import de contenu** : les pages et articles décrits dans
  `content/manifest.json` sont créés s'ils n'existent pas encore. Un contenu
  déjà présent dans WordPress n'est **jamais** écrasé.
- **OPcache vidé** après chaque sync qui modifie des fichiers.

### Les garde-fous

La sync écrit du PHP qui sera ensuite exécuté par le site. Elle est donc
volontairement verrouillée :

| Garde-fou | Détail |
|---|---|
| Dépôts de confiance | `LAE_GitHub_Sync::TRUSTED_REPOS`, en dur dans le code. Toute autre source est refusée, même injectée par un filtre. |
| Droits | `manage_options` uniquement, nonce sur chaque action. |
| Extensions | Whitelist (`php`, `css`, `js`, `json`, images, polices, médias…). Le reste est ignoré. |
| Fichiers protégés | `wp-config.php`, `.env`, `.htaccess`, `.user.ini`, `php.ini`, `.htpasswd`, `web.config`, `.git` — jamais écrasés. |
| Traversée de chemin | Tout chemin contenant `..` est rejeté. |
| Intégrité | Le dossier racine du tarball doit porter le SHA annoncé, sinon abandon. |
| Échec partiel | Le SHA local n'avance pas → la sync suivante réessaie automatiquement. |

---

## Installation sur Hostinger (une seule fois)

1. **Déposer le thème.** hPanel → *Gestionnaire de fichiers*, ou SFTP.
   Copier le dossier `la-environnement-theme/` dans
   `public_html/wp-content/themes/`.
   Nom du dossier sur le serveur : **`la-environnement-theme`** (exactement —
   c'est ce nom que la sync utilise comme cible).

2. **Activer le thème.** WordPress → *Apparence → Thèmes* →
   « L.A Environnement » → *Activer*.

3. **Vérifier la mécanique.** WordPress → *Outils → SYNC GitHub*.
   L'écran doit afficher le dépôt, le commit distant, le commit local et la
   date de la prochaine vérification automatique.

4. **Première synchronisation.** Cliquer sur « Synchroniser maintenant ».
   L'état doit passer à **À jour**.

À partir de là, plus rien à faire : chaque push sur `main` descend tout seul.

### Si le site a peu de trafic

WP-Cron ne se déclenche que lorsqu'une page est visitée. Sur un site neuf,
la sync peut donc attendre. Deux options :

- cliquer « Synchroniser maintenant » sur l'écran *Outils → SYNC GitHub* ;
- ou brancher un vrai cron côté Hostinger (hPanel → *Tâches Cron*, toutes les
  5 minutes) :
  ```
  wget -q -O - https://LE-DOMAINE/wp-cron.php?doing_wp_cron >/dev/null 2>&1
  ```

### Si le dépôt passe en privé

Le moteur gère les dépôts privés. Créer un *fine-grained token* GitHub en
**lecture seule** sur ce dépôt (permission « Contents: Read-only »), puis
l'ajouter dans `wp-config.php` — **jamais dans le dépôt** :

```php
define( 'LAE_GH_TOKEN', 'github_pat_xxxxxxxx' );
```

---

## MCP Hostinger (piloter l'hébergement depuis Claude)

Le dépôt déclare deux serveurs MCP Hostinger dans `.mcp.json`, chargés
automatiquement à l'ouverture du projet dans Claude Code :

| Serveur | Binaire | Outils |
|---|---|---|
| `hostinger-wordpress` | `hostinger-wordpress-mcp` | 38 |
| `hostinger-dns` | `hostinger-dns-mcp` | 8 |

Ce sont des **binaires scopés** du paquet `hostinger-api-mcp` (v1.57.2). Le
binaire unifié `hostinger-api-mcp` existe aussi mais expose **382 outils** :
inutile ici, et ça sature le contexte pour rien. Autres binaires disponibles si
besoin : `hostinger-hosting-mcp` (64), `hostinger-vps-mcp` (64),
`hostinger-domains-mcp` (40), `hostinger-mail-mcp` (38),
`hostinger-billing-mcp` (9), `hostinger-horizons-mcp` (2).

### Ce que ça permet

Gestion **de l'hébergement**, pas des fichiers : lister et créer des
installations WordPress, installer et activer plugins et thèmes, lancer les
mises à jour, **purger le cache LiteSpeed**, basculer le mode maintenance,
gérer Memcached, générer un lien de connexion admin, et gérer les
enregistrements DNS.

C'est **complémentaire** de la sync GitHub, pas un remplacement : l'API
Hostinger ne dépose pas de fichiers arbitraires sur le site. Le déploiement du
code reste le push sur `main` ; le MCP sert à piloter le reste (cache,
maintenance, plugins, DNS).

### Authentification

Aucun secret n'est stocké dans ce dépôt (il est **public**). Deux méthodes,
dans cet ordre de priorité :

1. **OAuth** (par défaut, recommandé sur un poste de travail) — au premier
   appel d'outil, une page Hostinger s'ouvre dans le navigateur. Les
   identifiants sont ensuite stockés hors dépôt, dans
   `~/.config/hostinger-mcp/credentials.json` (mode 0600), et partagés entre
   tous les binaires Hostinger. Connexion immédiate :
   `npx -y hostinger-api-mcp --login` — déconnexion : `--logout`.
2. **Jeton d'API** (pour un environnement sans navigateur : CI, session
   distante) — exporter `HOSTINGER_API_TOKEN` dans l'environnement. Quand elle
   est présente, cette variable court-circuite entièrement OAuth. Ne **jamais**
   l'écrire dans `.mcp.json` : le dépôt est public.

Prérequis : Node.js. Le paquet déclare `engines: >=20`.

### Ajouter les serveurs hors de ce dépôt

```bash
claude mcp add hostinger-wordpress -- npx -y -p hostinger-api-mcp hostinger-wordpress-mcp
claude mcp add hostinger-dns       -- npx -y -p hostinger-api-mcp hostinger-dns-mcp
```

Il existe aussi un serveur hébergé par Hostinger, sans rien installer :

```bash
claude mcp add --transport http hostinger https://mcp.hostinger.com
```

Il expose l'API complète (382 outils) et s'authentifie en OAuth — donc il lui
faut un navigateur : à réserver au poste de travail.

---

## Sécurité

Trois niveaux, repris de la config Alliance Groupe.

### 1. Le dépôt

Le dépôt est **public** et il déploie en production : quiconque peut pousser
sur `main` modifie le site en moins de 5 minutes. Il est donc dans le
périmètre de production, et protégé comme tel — voir `SECURITY-SETUP.md`
(2FA, protection de la branche `main`, accès en écriture limités).

Le `.gitignore` n'exclut **aucun fichier du projet**. Il ne bloque que trois
choses : les **secrets** (`wp-config.php`, `.env`, `.htaccess`, `*.key`,
`*.pem`, `credentials.json`…), ce qui **n'est pas à nous** (cœur WordPress,
plugins tiers, `vendor/`, `node_modules/`) et ce qui est **régénérable**
(caches, builds, dumps SQL, sauvegardes de la sync).

### 2. Le site

`la-environnement-theme/inc/lae-hardening.php`, chargé par `functions.php`,
applique un durcissement défensif :

| | |
|---|---|
| `xmlrpc.php` | bloqué (403) — force brute et DDoS par pingback |
| API REST | plus d'énumération des comptes pour les visiteurs non connectés |
| `?author=N` et `/author/` | redirigés, l'identifiant de connexion ne fuit plus |
| En-têtes HTTP | `nosniff`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, CSP `upgrade-insecure-requests`, HSTS en HTTPS |
| Divulgations | `X-Pingback` et `X-Powered-By` retirés, version de WordPress masquée, liens RSD/WLW retirés |
| Versions dans les URL CSS/JS | remplacées par une empreinte — la version disparaît **sans casser le cassage de cache** |
| `.htaccess` | bloc balisé `LAE Hardening` posé automatiquement si le fichier est modifiable, sinon un bandeau admin renvoie vers `SECURITE-HTACCESS.txt` |

### 3. La sync

Détaillé plus haut (« Les garde-fous ») : whitelist de dépôts en dur,
whitelist d'extensions, fichiers protégés jamais écrasés, rejet des chemins
`..`, contrôle d'intégrité du tarball, backup avant tout écrasement.

---

## Config de travail (reprise entre sessions)

Reprise de la config du dépôt Alliance Groupe, allégée de ce qui lui est
propre (agents de prospection, cadenas des templates vendus, workflow Gwen).

| Fichier | Rôle |
|---|---|
| `.claude/settings.json` | Déclare le hook `SessionStart`. |
| `.claude/hooks/session-start.sh` | Injecté au démarrage de **chaque** session Claude : rappel que `main` = production, branche courante, 8 derniers commits, travail non commité, chantier en cours, en-tête de `HANDOFF.md`. Réinstalle aussi les hooks git (ils ne survivent pas à un conteneur éphémère). |
| `.claude/skills/ui-ux-pro-max/` | Skill design : 84 styles, 192 palettes, 74 associations de polices, 98 règles UX, 22 stacks. Pour l'étape design. |
| `scripts/install-git-hooks.sh` | Installe le `pre-commit`. |
| `scripts/stamp-handoff.sh` | Tamponne date + branche dans `HANDOFF.md` à chaque commit. |
| `HANDOFF.md` | État du projet, à lire en premier en début de session. |
| `BACKLOG.md` | Chantiers en attente et décisions reportées. |
| `.WORKING_ON.md` | Chantier en cours et ce qu'on ne touche pas. |
| `.gitattributes` | Fins de ligne. Les `.sh` restent en LF, sinon les hooks cassent (« bad interpreter »). |

### Le pre-commit

Différence assumée avec AG : là-bas le hook vérifie les cadenas des templates
vendus (`check-all-locks.sh`), ce qui n'a pas d'objet ici. Il fait donc ce qui
compte pour ce dépôt, et c'est **bloquant** :

1. `php -l` sur chaque fichier `.php` indexé ;
2. validation de chaque fichier `.json` indexé ;
3. tampon de `HANDOFF.md`.

Testé : PHP cassé → refusé, JSON invalide → refusé, PHP valide → accepté.

Si `php` n'est pas dans le `PATH`, le hook le signale et laisse passer plutôt
que de bloquer tout commit. Sur cette machine-là, vérifier à la main : `main`
est déployé en production sous 5 minutes.

Pour (ré)installer : `bash scripts/install-git-hooks.sh`.

---

## Structure

```
la-environnement/
├── content/
│   └── manifest.json              pages + articles versionnés
└── la-environnement-theme/
    ├── style.css                  identité du thème + styles de base
    ├── functions.php              supports WP + chargement de la mécanique
    ├── header.php / footer.php
    ├── index.php / page.php / single.php / 404.php
    ├── template-parts/            blocs réutilisables
    └── inc/
        ├── lae-github-sync.php    moteur de sync (cron 5 min)
        └── lae-sync-admin.php     écran Outils → SYNC GitHub
```

## Règles de travail

- Toujours `php -l` avant de committer : un PHP cassé poussé sur `main` casse
  le site en ligne dans les 5 minutes.
- Indentation : **tabulations**.
- Le dépôt est **public** : aucun identifiant, token, mot de passe ou donnée
  client dedans.
