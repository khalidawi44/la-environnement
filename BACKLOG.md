# 📋 BACKLOG — L.A Environnement

Chantiers en attente, décisions reportées, idées à reprendre plus tard.

> **Note à Claude** : à chaque réponse importante, glisser un petit bloc
> « 📋 En réserve » rappelant les 2-3 items les plus prioritaires de ce
> backlog, pour éviter qu'ils soient oubliés.

---

## À trancher avec le client

- **Contenu réel du site (pages, textes, photos) — rien ne doit être inventé.**
  Priorité n°1 : sans lui, le site s'affiche avec des sections masquées.
- Photos de chantier (avant / après) pour la section Réalisations.
- Logo : à défaut, le thème affiche le nom du site + une baseline.
- Nom de domaine définitif et bascule DNS.
- Formulaire de contact : destinataire, champs, mentions RGPD.

## Technique

- Brancher un vrai cron Hostinger sur `wp-cron.php` si le trafic est trop
  faible pour déclencher la sync (voir `README.md`).
- Décider si le dépôt reste public ou passe en privé (si privé : créer le
  jeton lecture seule et poser `LAE_GH_TOKEN` dans `wp-config.php`).

---

*(Format : un titre court, le statut, le contexte, et ce qui bloque.)*
