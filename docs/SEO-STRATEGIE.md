# Audit de stratégie SEO — L.A Environnement

> Relevé du 09/09/2026. Concurrents et données vérifiés en ligne ce jour-là.
> **Ce document ne décide rien** : il cartographie le terrain et pose les
> arbitrages qui reviennent à Fabrice et au client.

---

## 1. Ce que je peux mesurer, et ce que je ne peux pas

Règle du projet : ne rien inventer. Donc, d'entrée, la limite.

| | |
|---|---|
| ✅ **Mesuré** | Ciblage réel des concurrents (titres, H1, structure d'URL, communes) |
| ✅ **Mesuré** | Composition de la page de résultats Google sur les requêtes du métier |
| ✅ **Sourcé** | Part du trafic mobile en France |
| ✅ **Mesuré** | Ciblage actuel de `elagage-vertou.fr` |
| ❌ **Indisponible** | **Volumes de recherche réels.** Ils viennent de Google Keyword Planner (compte Google Ads actif) ou de Search Console. Le site est neuf : aucune donnée. **Tout chiffre de volume que j'avancerais serait inventé.** |
| ❌ **Indisponible** | Positions actuelles du site — pas encore indexé assez longtemps |

**Conséquence directe :** la première action n'est pas d'écrire des pages,
c'est de **brancher les instruments**. Search Console, c'est gratuit et
c'est la seule source de vérité sur ce que les gens tapent réellement pour
tomber sur ce site. Sans elle, on optimise à l'aveugle.

---

## 2. Le terrain : qui occupe réellement la première page

Trois familles d'acteurs, et elles ne se combattent pas de la même façon.

### a) Les annuaires et plateformes de mise en relation — **imbattables en frontal**

Sur les requêtes génériques (« élagueur près de moi », « devis élagage »),
la première page est occupée par **PagesJaunes, StarOfService, Travaux.com,
elagage.com, ArbrExpert, France-Élagage, AlloJardin**. Sur une recherche
« élagage abattage Loire-Atlantique », PagesJaunes prend à lui seul 3 résultats
sur 8.

Ce sont des sites à des centaines de milliers de pages, avec une autorité de
domaine hors de portée d'un artisan. **Se battre sur « élagueur Nantes » en
référencement naturel pur est perdu d'avance.** Ce n'est pas du défaitisme,
c'est le constat de la structure de la page de résultats.

### b) Les réseaux multi-communes — **le vrai concurrent direct**

**Élagueur 44** (`elagueur44.fr`) applique une stratégie industrielle :
**un sous-domaine par commune**. `vertou.elagueur44.fr` existe, avec un titre
taillé au cordeau :

> « Élagueur Arboriste à Vertou (44) : Experts en Élagage avec Nacelle | Élagueur 44 »

Commune, département, métier, spécificité technique (nacelle), marque. Tout y
est. L'entreprise est basée à **Mouzeil**, à 40 km de Vertou : elle n'a aucune
présence locale réelle, mais elle occupe le terrain numérique de Vertou.

C'est ça, l'adversaire. Pas l'artisan d'à côté.

### c) Les artisans locaux — **les concurrents légitimes**

| Entreprise | Base | Ciblage | Points forts |
|---|---|---|---|
| **Antoine Élagage** | Vertou | H1 : « Antoine Élagage, entreprise d'élagage à Vertou » | Depuis 2014, pages dédiées `/elagueur` et `/abattage`, couvre Vertou, Nantes, Basse-Goulaine, Saint-Sébastien-sur-Loire |
| **ALO Élagage** | Montbert | Vertou, Rezé, Aigrefeuille | Rayon de 30 km affiché |
| **Hauteur et Cimes** | 44 | Élagage, abattage, mise en sécurité, broyage, rognage | 16 ans d'expérience mis en avant |
| **Chatelain Gary** | Vertou | Élagage, abattage | Concurrent direct sur la commune |

**Antoine Élagage est le concurrent à battre à Vertou.** Son H1 fait ce que le
nôtre ne fait pas : il dit le métier et la commune.

---

## 3. Diagnostic du site actuel — le constat est sévère

Relevé sur `elagage-vertou.fr` le 09/09.

### Le titre gaspille sa place la plus précieuse

```
<title>elagage-vertou.fr – Élagage · Abattage · Création de jardin</title>
```

Il **commence par le nom de domaine**. Ces caractères-là sont les plus lus par
Google et par l'humain qui scanne la page de résultats, et ils ne servent à
rien. Ni « Vertou » en tant que ville, ni « élagueur », ni « arboriste ».

### Le H1 ne contient aucun mot-clé

```
<h1>Un arbre trop grand, trop près, trop vieux ?</h1>
```

C'est une **excellente accroche commerciale** — elle parle au client, elle pose
le problème mieux que n'importe quel concurrent. Mais Google n'a rien à quoi se
raccrocher : ni métier, ni ville, ni service.

Face à « Antoine Élagage, entreprise d'élagage à Vertou », le match est perdu
avant d'avoir commencé.

**Ce n'est pas un défaut de rédaction, c'est un arbitrage manqué :** on peut
garder la force de l'accroche *et* nommer le métier. Les deux ne s'excluent pas.

### Les titres intermédiaires sont tous éditoriaux

« Un arbre, ça se lit », « Ce qui tient un arbre », « Trois métiers »,
« Ce qu'on livre », « Dites-nous ». Aucun ne contient « élagage », « abattage »,
« Vertou », « taille », « haubanage ». La page est belle et muette.

### Les données structurées se limitent à une commune

Le JSON-LD `LocalBusiness` déclare `areaServed` = **Vertou**, une seule ville.
Et le champ `name` vaut **« elagage-vertou.fr »** — le nom de domaine en guise
de raison sociale. Pour Google, l'entreprise s'appelle donc littéralement
« elagage-vertou.fr ».

---

## 4. Mobile ou ordinateur : la donnée, pas l'intuition

**64,71 % du trafic web français vient du mobile** (Similarweb, juin 2026). Au
niveau mondial, le mobile pèse 62-64 %, l'ordinateur environ 35 %.

Nuance française à connaître : les Français passent moins de temps sur mobile
que la moyenne mondiale (2 h 34 par jour), parce que **89 % possèdent un
ordinateur**. La part ordinateur reste donc un peu plus forte qu'ailleurs.

**Ce que ça implique concrètement ici.** Un particulier qui découvre un arbre
menaçant après un coup de vent ne va pas allumer son ordinateur. Il sort son
téléphone, dans son jardin, souvent en 4G. Sur ce métier, la part mobile réelle
est probablement **au-dessus** des 64,71 % moyens — l'intention est urgente,
extérieure et géolocalisée.

C'est ce qui donne sa valeur au travail de performance mobile fait le 09/09
(p95 de frame 67 → 50 ms), et ce qui rend les deux défauts d'affichage restants
plus graves qu'ils n'en ont l'air : **les cartes tronquées sur écran court font
disparaître le bouton d'appel à l'action**, précisément sur l'appareil qui
apporte les deux tiers des visiteurs.

---

## 5. La stratégie : ne pas se battre là où c'est perdu

### Le pack local avant tout le reste

Sur une requête locale, Google affiche d'abord une **carte avec trois fiches**
— le « pack local » — au-dessus des résultats naturels. Ces trois places se
gagnent avec une **fiche Google Business Profile**, pas avec un site.

Une fiche vérifiée, avec de vraies photos de chantier, les bonnes catégories,
des horaires, une zone d'intervention et des **avis authentiques**, vaut plus
que six mois de travail sur les pages. Et c'est là que le téléphone sonne : le
bouton « Appeler » de la fiche est cliqué directement, sans passer par le site.

**C'est l'action numéro un.** Elle ne dépend d'aucun de nous deux : elle
dépend du client.

### La longue traîne locale, pas les requêtes génériques

On abandonne « élagueur Nantes » aux annuaires. On vise ce qu'ils couvrent mal :

- **commune + métier** : élagage Vertou, abattage Saint-Sébastien-sur-Loire,
  élagueur Basse-Goulaine, taille d'arbre Rezé…
- **intention d'urgence** : arbre tombé, arbre dangereux, après tempête,
  mise en sécurité. Segment à forte valeur — les interventions d'urgence se
  facturent 150 à 400 € selon l'ampleur.
- **technique précise** : démontage par câble, haubanage, élagage en grimpe,
  réduction de couronne, rognage de souche. Peu de concurrents les nomment, et
  celui qui cherche « haubanage » sait ce qu'il veut : il achète.
- **question avant achat** : quand élaguer un arbre, prix d'un abattage,
  faut-il une autorisation. Contenu qui capte tôt et installe l'autorité.

### Une page par commune, faite honnêtement

C'est la stratégie d'Élagueur 44, et elle fonctionne. La différence : eux la
font depuis Mouzeil sans présence réelle. **L.A Environnement est à Vertou.**
Une page par commune réellement desservie, avec des chantiers réels et des
photos réelles, bat une page générique dupliquée.

⚠️ **Le piège** : des pages-communes vides et quasi identiques sont considérées
par Google comme du contenu de faible qualité et peuvent pénaliser le site
entier. **Une page par commune uniquement s'il y a de quoi la remplir
honnêtement** — un chantier fait là-bas, une photo, une particularité locale.
Mieux vaut trois pages vraies que quinze pages creuses.

---

## 6. Ce qui doit être tranché — par Fabrice et le client

Aucun de nous deux ne peut décider à leur place.

1. **La raison sociale exacte.** Le JSON-LD dit aujourd'hui
   « elagage-vertou.fr ». Quel est le nom réel ? Il doit être identique
   partout : site, fiche Google, factures. Une incohérence de nom entre ces
   trois endroits abîme le référencement local.
2. **L'adresse et le téléphone exacts**, à l'identique partout, pour la même
   raison.
3. **La liste des communes réellement desservies**, et le rayon assumé.
4. **Le métier prioritaire.** Élagage, abattage et création de jardin ne se
   cherchent pas de la même façon et n'ont pas la même valeur. Lequel porte le
   chiffre d'affaires ?
5. **L'urgence : oui ou non ?** Si l'entreprise intervient après tempête, c'est
   un segment entier à occuper. Sinon il ne faut surtout pas le prétendre.
6. **La fiche Google Business Profile existe-t-elle déjà ?** Est-elle vérifiée,
   et qui la contrôle ?

---

## 7. Plan par lots

### Lot 0 — Instrumenter (à faire en premier, sinon on travaille à l'aveugle)
| Qui | Quoi |
|---|---|
| Fabrice | Google Search Console : vérifier le domaine, soumettre le sitemap |
| Fabrice / client | Fiche Google Business Profile : créer ou reprendre, vérifier, catégories, photos, zone |
| CODE | Sitemap qui répond 200 — **fait en v1.9.6**, à confirmer en ligne |

### Lot 1 — Réparer le ciblage (rapide, fort impact)
| Qui | Quoi |
|---|---|
| CODE | Titre sans le nom de domaine en tête, gabarit par type de page |
| CODE | `areaServed` alimenté par la liste réelle des communes |
| CODE | `name` du JSON-LD = raison sociale réelle, plus le domaine |
| DESIGN | H1 qui garde l'accroche **et** nomme le métier + la commune |
| DESIGN | Titres intermédiaires portant les vrais termes du métier |
| DESIGN | Texte alternatif descriptif sur les photos de chantier |

### Lot 2 — Occuper le terrain local
| Qui | Quoi |
|---|---|
| Fabrice | Arbitrer la liste des communes et le métier prioritaire |
| DESIGN | Une page par commune réellement desservie, avec chantier et photo réels |
| DESIGN | Une page par prestation technique (haubanage, démontage par câble…) |
| CODE | Maillage interne, fil d'Ariane, données structurées par page |

### Lot 3 — Mesurer et corriger
| Qui | Quoi |
|---|---|
| CODE | Relevé mensuel : positions, pages d'entrée, part mobile réelle |
| Tous | Ajuster sur les données de Search Console, plus sur des hypothèses |

---

## 8. Répartition des couloirs pour le SEO

| | |
|---|---|
| ⚙️ **CODE** | Tout ce qui est dans `<head>` : titres, descriptions, robots, canoniques, données structurées, sitemap, performance. Mesurable avec `curl`. |
| 🎨 **DESIGN** | Tout ce qui est dans `<body>` : H1 et titres intermédiaires, texte des pages, textes alternatifs, structure et maillage. |
| 👤 **FABRICE / CLIENT** | Les faits : raison sociale, adresse, téléphone, communes, métiers, urgence. Et la fiche Google Business Profile. |

Cette frontière évite le vrai risque : **deux sessions qui émettent chacune
leurs balises produisent des doublons**, et l'aperçu comme l'extrait Google
deviennent imprévisibles. Un garde-fou existe déjà contre les plugins SEO
(Yoast, Rank Math, SEOPress) ; il n'en existe aucun entre nous deux.

---

## Sources

- Concurrence Loire-Atlantique : PagesJaunes, `antoine-elagage.fr`,
  `alo-elagage-abattage-loire-atlantique.fr`, `vertou.elagueur44.fr`
- Plateformes de mise en relation : StarOfService, Travaux.com, elagage.com,
  ArbrExpert, France-Élagage, AlloJardin
- Part mobile France : Similarweb (juin 2026), Statista, Kinsta
- Relevés directs sur `elagage-vertou.fr` le 09/09/2026
