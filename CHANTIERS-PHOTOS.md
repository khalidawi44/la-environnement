# Photos de chantier L.A Environnement

Dix photos fournies par le client. Neuf retenues, retraitées et rangées dans
`la-environnement-theme/assets/images/chantiers/`.

Toutes ont été corrigées pour la **lisibilité** : remontée des ombres par gamma
local (le point noir est préservé, donc pas de voile gris), récupération des
hautes lumières brûlées, contraste et saturation légers, netteté légère.
Format WebP, qualité 85, 1800 px sur le grand côté.

## Ce qui fait vraiment un avant/après

**Une seule séquence exploitable**, identifiée par des repères matériels
communs — mêmes bidons verts, mêmes caisses blanches, même mur :

| Fichier | Moment | Ce qu'on voit |
|---|---|---|
| `mur-vert-1-pendant-broyage.webp` | pendant | Arbres démontés, branchages au sol, broyeur en action |
| `mur-vert-2-apres-nettoyage.webp` | après | Terrain dégagé, troncs nets, bois rangé |

C'est un **pendant → après**, pas un avant → après : il n'existe aucune photo
de ce terrain avant intervention. Ne pas l'étiqueter « avant ».

Les deux prises de vue n'ont pas le même axe. Un comparateur à curseur
(glissière superposant les deux images) serait donc **bancal** : il faut les
poser côte à côte, légendées.

### Le cas de l'abri de jardin

`abri-jardin-1-pendant-demontage.webp` et `abri-jardin-2-apres-remise-en-etat.webp`
viennent du **même jardin** — le même abri gris à toit de tuiles apparaît au
fond des deux. Mais les cadrages regardent dans des directions opposées et
l'« après » ne montre pas la zone des troncs : mises côte à côte, elles ne
racontent rien. **À traiter comme deux photos isolées**, sauf si le client
fournit une vue de la même zone après coup.

Aucune autre paire n'existe dans le lot. Associer deux chantiers différents
pour fabriquer un avant/après serait un faux : les saisons, les ciels et les
lieux ne correspondent pas.

## Inventaire

| Fichier | Moment | Sujet | Usage suggéré |
|---|---|---|---|
| `elagage-grimpe-cordes.webp` | pendant | Grimpeur encordé sur le tronc | Chapitre « La cime », photo métier |
| `reduction-couronne-grimpeur.webp` | pendant | Grand arbre en réduction de couronne, grimpeur dans le houppier, ciel bleu | La plus spectaculaire du lot — bandeau ou réalisation en tête |
| `demontage-bouleau.webp` | pendant | Bouleau démonté par sections le long d'une maison | Démontage en espace contraint |
| `mur-vert-1-pendant-broyage.webp` | pendant | Broyage sur place, branchages | Séquence ci-dessus / évacuation |
| `mur-vert-2-apres-nettoyage.webp` | après | Troncs nets, terrain dégagé | Séquence ci-dessus |
| `abri-jardin-1-pendant-demontage.webp` | pendant | Troncs étêtés, branchages au sol | Abattage |
| `abri-jardin-2-apres-remise-en-etat.webp` | après | Pelouse nette, palissade neuve | Remise en état / création de jardin |
| `haie-taillee-broyat.webp` | après | Haie réduite, sol couvert de broyat | Taille de haie, entretien |
| `dechets-verts-tas.webp` | pendant | Tas de branchages de conifères | Matière de fond, évacuation |
| `intervention-nuit.webp` | pendant | Élagueur à la tronçonneuse de nuit, arbre tombé contre une maison, camion-nacelle éclairé | En-tête de `/urgences` — la seule photo de nuit du fonds |

**Écartée** : la vue du portail prise depuis la camionnette (vitre sale, rien
qui montre le métier).

## Deux retouches à connaître

1. **`mur-vert-1-pendant-broyage.webp`** : le numéro de téléphone et la marque
   du loueur du broyeur (« LA LOCATION », 02 40 52 69 15) sont **floutés**. Ce
   n'est pas le numéro de l'entreprise ; affiché sur le site, un visiteur
   pouvait l'appeler par erreur. Refaire ce floutage sur toute autre photo de
   ce chantier.
2. **`demontage-bouleau.webp`** : l'incrustation de story « 16:41 / LA MONTAGNE »
   a été supprimée par recadrage. **L'information mérite d'être gardée ailleurs** :
   La Montagne est une commune de Loire-Atlantique, c'est une preuve de zone
   d'intervention réelle, utile au référencement local (réglage
   « Zone d'intervention → Communes », qui ne contient aujourd'hui que Vertou).

## Ce qui est déjà branché sur l'accueil

Quatre images vivent encore dans `assets/images/` et servent de repli à la page
d'accueil (`front-page.php`) : `elagage-grimpe.webp`, `broyage-chantier.webp`,
`abattage-troncs.webp`, `dechets-verts.webp`. Ce sont les mêmes prises de vue,
recadrées pour ces emplacements précis. Le dossier `chantiers/` est la
bibliothèque complète, destinée aux réalisations.


## Ajout du 13/09 — la photo de nuit

`intervention-nuit.webp` vient d'un lot transmis par Fabrice, qui la déclare
**prise chez son client**. Les quatre autres visuels du même envoi étaient des
affiches publicitaires d'entreprises concurrentes identifiables (MT Forest,
BR Espace Services, Vert Évasion, Team Paysage) : elles ont été **écartées**,
en changer le texte aurait été de la contrefaçon.

Traitement : bandes noires de letterbox retirées (18 px en haut, 47 en bas),
gamma local pour ouvrir les ombres sans lever le point noir — la scène reste
nocturne, c'est tout son intérêt — et hautes lumières du projecteur retenues.
Médiane 31 → 58 sur 255.

**Limite à connaître :** 768 × 511 px seulement. Suffisant pour un en-tête sur
téléphone, un peu doux en pleine largeur sur grand écran. Si le client a
l'original en meilleure définition, il vaut le coup de le redemander.
