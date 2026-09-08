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
