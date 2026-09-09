# SECURITY-SETUP.md — à faire par le propriétaire (config, hors code)

> Le durcissement **côté code** est en place (`inc/lae-hardening.php`, chargé
> par `functions.php`). Restent les réglages que seul le titulaire des comptes
> peut activer. Cocher au fur et à mesure.

## 1. GitHub — le dépôt fait partie du périmètre de production

Le site se déploie **depuis ce dépôt**. Quiconque peut pousser sur `main` peut
donc modifier le site en moins de 5 minutes. Le dépôt mérite le même niveau de
protection que l'hébergement.

- [ ] **Aucun secret dans le dépôt.** Il est **public**. Jamais de
      `wp-config.php`, de clé API, de mot de passe, de jeton, d'accès
      Hostinger, ni de donnée personnelle du client. Le `.gitignore` couvre
      les cas classiques, mais il ne remplace pas la vigilance.
- [ ] **Activer la 2FA** sur le compte GitHub : Settings → Password and
      authentication → Two-factor authentication.
- [ ] **Protéger la branche `main`** : Settings → Rules → Rulesets :
  - ☑ Block force pushes
  - ☑ Restrict deletions
  - ☐ **NE PAS** cocher « Require a pull request » : ça bloquerait le
    déploiement par push direct, qui est le fonctionnement voulu.
- [ ] **Vérifier les accès en écriture** (Settings → Collaborators) — le moins
      de monde possible.

## 2. Hostinger

- [ ] **2FA** sur le compte hPanel.
- [ ] **HTTPS forcé** + certificat SSL actif sur le domaine.
- [ ] **Sauvegardes automatiques** activées (hPanel → Sauvegardes). La sync
      GitHub sauvegarde les fichiers du thème qu'elle écrase, mais elle **ne
      sauvegarde pas la base de données**.
- [ ] **PHP à jour** (version supportée, pas une version en fin de vie).
- [ ] Coller le bloc `.htaccess` si le bandeau de l'admin le demande — voir
      `SECURITE-HTACCESS.txt`.

## 3. WordPress

- [ ] Compte administrateur avec un **identifiant qui n'est pas `admin`** et un
      mot de passe long et unique.
- [ ] Comptes inutiles supprimés ; le client en **Éditeur**, pas en
      Administrateur, sauf besoin réel.
- [ ] Mises à jour du cœur, des plugins et des thèmes suivies (le MCP
      Hostinger permet de les lancer).
- [ ] Thèmes et plugins inutilisés **supprimés** (pas seulement désactivés :
      un plugin désactivé reste un fichier attaquable).

## 4. Si le dépôt passe en privé

- [ ] Créer un **fine-grained token** GitHub limité à ce dépôt, permission
      **Contents: Read-only** — rien d'autre.
- [ ] Le poser dans `wp-config.php`, **jamais dans le dépôt** :
      ```php
      define( 'LAE_GH_TOKEN', 'github_pat_xxxxxxxx' );
      ```
- [ ] Prévoir sa rotation si quelqu'un quitte le projet.

## 5. Ce que le code fait déjà tout seul

Pour mémoire, `inc/lae-hardening.php` couvre : blocage de `xmlrpc.php`, fin de
l'énumération des comptes (API REST) et des auteurs (`?author=`), en-têtes de
sécurité HTTP (nosniff, X-Frame-Options, Referrer-Policy, Permissions-Policy,
HSTS en HTTPS, CSP `upgrade-insecure-requests`), retrait de `X-Pingback` et
`X-Powered-By`, masquage de la version de WordPress et des versions dans les
URL des CSS/JS — sans casser le cassage de cache — et pose d'un bloc `.htaccess`
balisé quand le fichier est modifiable.

Et `LAE_GitHub_Sync` couvre le risque propre à la sync : whitelist de dépôts de
confiance en dur, whitelist d'extensions, fichiers protégés jamais écrasés,
rejet des chemins `..`, contrôle d'intégrité du tarball, backup avant écrasement.

---

## Vérification en conditions réelles — 09/09/2026

Relevé sur `elagage-vertou.fr` en ligne, pas sur le code. **Tout ce qui suit
est constaté, pas supposé.**

| Contrôle | Attendu | Constaté |
|---|---|---|
| `X-Content-Type-Options` | `nosniff` | ✅ |
| `X-Frame-Options` | `SAMEORIGIN` | ✅ |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | ✅ |
| `Permissions-Policy` | géoloc, micro, caméra coupés | ✅ |
| `Content-Security-Policy` | `upgrade-insecure-requests` | ✅ |
| `Strict-Transport-Security` | 1 an, sous-domaines | ✅ |
| `X-Powered-By` / `X-Pingback` | absents | ✅ |
| `/xmlrpc.php` | refusé | ✅ **403** |
| `/wp-json/wp/v2/users` | pas d'énumération | ✅ **404** |
| `/?author=1` | pas de fuite d'identifiant | ✅ **301** |

`inc/lae-hardening.php` fait donc son travail en production.

### Refaire ce contrôle en une commande

```bash
D=https://elagage-vertou.fr
curl -sSI $D | grep -iE "x-content-type|x-frame|referrer|permissions|content-security|strict-transport"
for u in xmlrpc.php wp-json/wp/v2/users "?author=1"; do
  printf "%-24s %s\n" "$u" "$(curl -sS -o /dev/null -w '%{http_code}' "$D/$u")"
done
```

Attendu : 403, 404, 301. Toute autre valeur signifie que le durcissement ne
tourne plus — thème désactivé, fichier écrasé, ou plugin en conflit.

### Reste hors code, pour mémoire

- [ ] `DISALLOW_FILE_EDIT` dans `wp-config.php` — coupe l'éditeur de fichiers de
      l'admin WordPress. Un compte admin compromis ne peut plus injecter de PHP
      depuis le navigateur.
      ```php
      define( 'DISALLOW_FILE_EDIT', true );
      ```
- [ ] Protection de la branche `main` sur GitHub (cf. §1) — le dépôt déploie en
      production, il mérite le même soin que l'hébergement.
