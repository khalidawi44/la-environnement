# Images à fournir — L.A Environnement

Liste établie le 14/09/2026 à la demande de Fabrice, **à partir de mesures**
et non d'impressions : chaque image du thème a été ouverte, sa taille relevée,
et le facteur d'agrandissement calculé pour un écran de bureau de 1440 px.
Au-delà de ×1,15 l'image commence à se voir floue ; à ×1,5 ça ne pardonne plus.

---

## La règle qui décide de tout : réelle ou générée ?

Depuis le 14/09, les mentions légales du site disent, noir sur blanc :

> Les photographies de chantier — avant/après, réalisations, illustrations de
> pages — sont prises sur les chantiers réellement réalisés : elles ne
> proviennent d'aucune banque d'images. Les images d'ambiance du bandeau
> d'accueil sont, elles, des illustrations créées pour ce site et ne
> représentent aucun chantier.

**Conséquence pratique : une photo de chantier ne peut pas être générée.**
Si on remplace une photo de chantier par une image d'IA, la phrase ci-dessus
devient fausse et le site ment à ses visiteurs sur le seul point où sa
crédibilité se joue. Les deux listes ci-dessous ne sont donc pas
interchangeables.

C'est aussi un avantage commercial, pas une contrainte : **le concurrent qui
met des photos de banque d'images ne peut pas écrire cette phrase.**

---

## A. Photos réelles — Anthony, avec son téléphone

### A1. Les originaux non redimensionnés — PRIORITÉ 1, et c'est gratuit

Ces photos existent déjà sur le site, mais en version réduite. Un téléphone
récent photographie en 3000 à 4000 px de large : les originaux sont donc
largement suffisants, il suffit de les **renvoyer sans les redimensionner**
(sur WhatsApp : « document » et non « photo », sinon c'est recompressé).

| Photo | Taille actuelle | Agrandissement à 1440 | Où elle sert |
|---|---:|---:|---|
| Intervention de nuit | 768 × 511 | **×1,88** | en-tête de la page Urgences |
| Abri de jardin, pendant | 900 × 1200 | ×1,60 | avant/après |
| Abri de jardin, après | 900 × 1200 | ×1,60 | avant/après |
| Démontage de bouleau | 960 × 1190 | ×1,50 | réalisations |
| Élagage en grimpe, cordes | 980 × 1225 | ×1,47 | en-tête À propos |
| Élagueur en grimpe | 1200 × 1500 | ×1,20 | en-tête Prestations |
| Pelouse et haie | 1200 × 900 | ×1,20 | en-tête Conseils |

La photo de nuit est la plus urgente : elle est agrandie de 88 % sur la page
qui doit convaincre quelqu'un d'appeler à 3 h du matin.

### A2. Anthony au travail — la photo qui manque vraiment

**Une photo d'Anthony, en action, visage visible.** C'est l'image qui manque
le plus au site et la seule qu'aucune génération ne remplacera : sur le site
d'un artisan, voir la personne qu'on va faire venir chez soi vaut plus que
n'importe quel bandeau. En grimpe ou au sol, peu importe — en train de
travailler, pas en train de poser.

Destination : page « Notre façon de travailler ». Cadrage vertical ou carré.

### A3. Des avant/après supplémentaires

Le mécanisme avant/après du site ne tourne aujourd'hui qu'avec **deux**
chantiers. Pour les prochains, une seule règle et elle change tout :

> **Photographier l'« après » depuis EXACTEMENT le même endroit que l'« avant ».**
> Repérer un point au sol avant de commencer (une dalle, un piquet, l'angle
> d'un mur), s'y remettre à la fin, même hauteur, même orientation.

Sans ça, les deux photos se posent côte à côte. Avec ça, elles se superposent
avec un curseur qu'on fait glisser — et c'est cet effet-là qui fait rester les
visiteurs sur la page.

---

## B. Images d'ambiance — générées, aucun problème

### B1. Le sous-bois en version ÉTÉ — ✅ FAIT, les trois photos sont en ligne

Reçues et installées le 14/09 : **jour, crépuscule et nuit**, toutes en
1672 x 940, aucune marque sur les outils, aucun agrandissement nécessaire
(×0,86 à 1440 px). Le site sert désormais un jeu cohérent à toute heure en
été ET au printemps — un feuillage vert est un feuillage vert, c'est la
teinte de saison qui distingue avril de juillet.

Contraste des douze états re-mesuré après coup : plancher 7,05:1
(été/jour, grand écran) pour un seuil AA de 4,5:1. Les deux nouvelles
photos ne l'ont pas fait bouger.

### B2. Le sous-bois en version HIVER — si tu veux aller au bout

Même prompt, en remplaçant le feuillage : « arbres nus, givre sur le sol,
lumière froide et basse, ciel gris-bleu ». Trois moments comme ci-dessus.
Le printemps peut réutiliser la version été sans que ça choque.

---

## Les contraintes qui valent pour TOUTES les images

1. **Aucune marque visible.** Sur les trois dernières, le nom du constructeur
   était écrit sur le guide-chaîne et il a fallu le retirer au pixel. Le
   demander dans le prompt fait gagner une heure à chaque fois.
2. **Taille minimale : 1920 px de large pour un bandeau, 1600 px pour un
   en-tête de page.** En dessous, l'image n'est pas installée — elle est
   signalée.
3. **Jamais de planche de plusieurs vignettes.** Une planche de six cases en
   1536 × 1024 donne six images de 512 × 512, inutilisables. Une image = une
   génération.
4. **Aucun visage de client, aucune plaque d'immatriculation, aucun numéro de
   rue lisible** sur les photos de chantier : on photographie chez des gens.

---

## Et ce qui n'est pas une image mais bloque toujours

- **L'assurance RC pro** : assureur, numéro de police, couverture. Elle a sa
  place réservée sur l'affiche et dans les mentions légales, et c'est
  l'argument le plus fort d'un prospectus d'élagueur.
- **Le régime de TVA** : franchise en base ou assujetti. Les deux mentions
  s'excluent, aucune ne peut être devinée.
- **Le médiateur de la consommation** : seule obligation légale que le site
  ne remplit toujours pas.

Les trois se saisissent dans le personnalisateur WordPress, section
« Mentions légales — à compléter » : la page publique se met à jour seule.
