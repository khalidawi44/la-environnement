<?php
/**
 * Valeurs par défaut du contenu — source unique de vérité.
 *
 * WordPress n'utilise PAS le 'default' déclaré dans add_setting() comme
 * repli de get_theme_mod() : tant que le personnalisateur n'a pas été
 * enregistré une fois, get_theme_mod() renvoie le second argument, pas le
 * défaut déclaré. Le site sortait donc vide de son installation.
 *
 * Ce tableau est lu à la fois par le personnalisateur (pour pré-remplir les
 * champs) et par lae_reglage() (pour l'affichage). Une seule valeur à changer.
 *
 * Clés SANS le préfixe « lae_ ».
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'lae_defauts' ) ) {
	function lae_defauts() {
		static $table = null;
		if ( null !== $table ) {
			return $table;
		}
		$table = array(
			'baseline'             => 'Élagage · Abattage · Création de jardin',
			// Barème construit le 13/09 à la demande de Fabrice, et VALIDÉ par
			// le client le 14/09 : les taux de 30 % et 15 % sont les siens.
			// Ce sont les deux seules valeurs du thème qui l'engagent
			// commercialement — elles restent modifiables dans le
			// personnalisateur, un barème évolue.
			//
			// Mécanisme DÉCLARATIF et non quotient familial : réclamer un avis
			// d'imposition pour faire couper un arbre est intrusif, et ça fait
			// renoncer exactement les gens que la réduction vise. C'est le
			// barème affiché qui fait le travail, pas le justificatif.
			// Conséquence heureuse : aucun seuil en euros à inventer.
			'atouts_urgence_titre' => 'On décroche la nuit',
			'atouts_urgence_texte' => 'Branche sur le toit, arbre qui penche après la tempête : 24 h/24, 7 j/7, week-ends et jours fériés compris.',
			'atouts_urgence_badge' => '24 h/24 · 7 j/7',
			'atouts_tarif_titre'   => 'Le tarif suit vos revenus',
			// « sans justificatif à fournir » et NON « sans avoir à la demander » :
			// la réduction est déclarative, personne ne peut deviner la
			// situation de quelqu'un. La page /tarifs dit « dites-nous votre
			// situation quand vous appelez » — une accroche qui promet
			// l'inverse fabrique une déception le jour du devis.
			'atouts_tarif_texte'   => 'Étudiant, sans emploi, minima sociaux, retraite modeste : dites-le en appelant, la réduction est appliquée au devis. Aucun justificatif à fournir.',
			'tarif_tranches'       => "Étudiant, apprenti, sans emploi, minima sociaux | 30\nRetraité modeste, temps partiel, famille monoparentale | 15",
			/* Numero de l'entreprise change le 19/09 sur indication de Fabrice.
			   L'ancien (07 59 79 03 96) n'est plus le numero de L.A
			   Environnement. Tout le site le lit ici : boutons d'appel,
			   pied de page, liens tel:, donnees structurees, mentions
			   legales. Une valeur deja enregistree dans le personnalisateur
			   primerait sur cette table — c'est ce que reprend le rattrapage
			   `lae_telephone_rattrapage()` dans lae-amorce.php. */
			'telephone'            => '06 04 40 83 00',
			'email'                => 'paysagisteenvironnement@gmail.com',
			/* Identité légale — relevée le 13/09 sur le registre officiel
			   (API recherche-entreprises, data.gouv.fr) et confirmée par
			   Fabrice. Entrepreneur individuel, un seul établissement.
			   Le « L.A » de la marque vient de LAMARQUE Anthony. */
			'adresse'              => "554 route de Clisson\n44120 Vertou",
			'adresse_rue'          => '554 route de Clisson',
			'adresse_cp'           => '44120',
			'adresse_ville'        => 'Vertou',
			'editeur_nom'          => 'Anthony Lamarque',
			'editeur_forme'        => 'Entrepreneur individuel (EI)',
			'siret_numero'         => '839 920 147 00023',
			'siret'                => 'Anthony Lamarque · EI · SIRET 839 920 147 00023',

			/* ── LES TROIS MANQUES LÉGAUX, EN ATTENTE DU CLIENT ─────────────
			   Ces cinq champs sont VIDES à dessein, et ils doivent le rester
			   tant qu'Anthony n'a pas donné les valeurs réelles. Chacun
			   commande une section des mentions légales qui n'apparaît que
			   s'il est rempli : rien d'inventé ne peut donc partir en ligne,
			   et il n'y a plus de code à écrire le jour où il répond — juste
			   un champ à saisir dans le personnalisateur.

			   1. ASSURANCE RC PRO. Un élagueur travaille au-dessus des
			      toitures et des voitures des voisins : c'est l'information
			      qu'un client prudent cherche en premier, et c'est aussi ce
			      qui le rassure sur la différence avec le travail au noir.
			   2. RÉGIME DE TVA. Deux régimes possibles, et on ne peut pas
			      deviner : en franchise en base (art. 293 B du CGI), les
			      devis et factures doivent porter « TVA non applicable » et
			      il n'y a PAS de numéro intracommunautaire ; assujetti, le
			      numéro doit figurer dans les mentions légales. Annoncer
			      l'un pour l'autre serait une fausse mention fiscale.
			   3. MÉDIATEUR DE LA CONSOMMATION. Obligation de l'article
			      L. 616-1 du code de la consommation pour tout
			      professionnel qui vend à des particuliers : le médiateur
			      auquel il adhère doit être communiqué sur le site. C'est
			      aujourd'hui la seule obligation du site non remplie.
			   ──────────────────────────────────────────────────────────── */
			'assurance_assureur'   => '',
			'assurance_police'     => '',
			'assurance_zone'       => '',
			'tva_regime'           => '',   // '' | 'franchise' | 'assujetti'
			'tva_numero'           => '',
			'mediateur_nom'        => '',
			'mediateur_adresse'    => '',
			'mediateur_site'       => '',
			'hero_surtitre'        => 'Élagage · Abattage · Création de jardin',
			'hero_titre'           => 'Un arbre trop grand, trop près, *trop vieux ?*',
			'hero_chapo'           => 'On monte, on regarde, et on vous dit ce qu\'il faut faire : tailler, haubaner ou abattre. Élagage en grimpe, démontage par câble, création et entretien de jardin.',
						/* « GRATUIT » — confirme par Fabrice le 22/09, et absent du site
			   jusque-la : le mot n'apparaissait pas une seule fois sur les 22
			   pages. C'est une expression tres recherchee, et ce n'est PAS un
			   montant : elle ne tombe donc pas sous la regle « aucun prix
			   affiche pour L.A Environnement ». On ne la repete pas partout —
			   trois endroits ou quelqu'un hesite encore a decrocher. */
			'hero_points'          => "Devis gratuit après visite sur place\nDémontage par câble là où l'abattage direct est impossible\nDéchets verts évacués ou broyés sur place",
			'prestations_surtitre' => 'Nos prestations',
			'prestations_titre'    => 'Tout ce qu\'on fait *sur un terrain*',
			'prestations_chapo'    => 'Chaque prestation a sa fiche : ce qu\'elle comprend, comment on procède, et ce qu\'on laisse derrière nous.',
			'etapes_titre'         => 'Comment ça se passe',
			'realisations_titre'   => 'Des chantiers, *pas des images d\'agence*',
			'realisations_chapo'   => 'Ce qu\'il y avait, ce qu\'on a fait, ce qu\'il en reste. Photos prises sur place.',
			/* LES HORAIRES DU PIED DE PAGE. Le bloc existe dans footer.php
			   et ne sortait JAMAIS : aucune valeur par défaut n'était
			   fournie, donc `lae_lignes('horaires')` renvoyait un tableau
			   vide sur les neuf pages. Le site déclarait l'astreinte 24 h/24
			   à Google (openingHoursSpecification) et à personne d'autre.
			   C'est pourtant l'argument qui distingue un artisan joignable
			   la nuit d'une entreprise en horaires de bureau. */
			'horaires'             => "Lundi au samedi : 8 h – 19 h\nUrgences : 24 h/24, 7 j/7, dimanches et jours fériés compris",
			/* Jeton de vérification Google Search Console. Vide tant que
			   Fabrice n'a pas créé la propriété — et une balise vide ne
			   s'émet pas. */
			'google_verification'  => '',
			'zone_titre'           => 'Zone d\'intervention',
			'zone_texte'           => 'Basé à Vertou, au sud-est de Nantes, et on se déplace dans toute la Loire-Atlantique. On vient voir l\'arbre ou le terrain avant tout devis — dites-nous où vous êtes.',
			/* LES COMMUNES NOMMÉES — ajoutées le 14/09 après audit SEO.
			   Le site ne disait NULLE PART où il travaille : « Vertou »
			   n'apparaissait 0 fois dans le corps des neuf pages, y compris
			   dans les 2 747 mots de l'accueil, et « Loire-Atlantique »
			   n'existait que dans les données structurées. Mesuré, pas
			   supposé. Sur un domaine qui s'appelle elagage-vertou.fr, c'est
			   le signal le plus facile à donner et il n'était donné nulle
			   part.

			   Ce sont les communes LIMITROPHES de Vertou, plus le Vignoble
			   proche — un fait de géographie, vérifiable, et strictement
			   plus étroit que ce que le site annonce déjà par ailleurs
			   (« toute la Loire-Atlantique », zone_texte ci-dessus). On
			   n'élargit donc aucune promesse : on nomme une partie de ce qui
			   était déjà promis en bloc.

			   VALIDÉE PAR FABRICE LE 14/09, la liste telle qu'elle est écrite
			   ci-dessous. C'est SA validation, pas celle d'Anthony : la
			   nuance compte, et on ne la maquillera pas en accord du client
			   final. Si Anthony corrige un jour son périmètre, chaque commune
			   se retire en une saisie dans le personnalisateur.

			   « Nantes Sud » est volontairement un quartier et non la commune
			   entière. C'est plus honnête — un artisan seul basé à Vertou ne
			   couvre pas Nantes nord — et c'est plus gagnable : personne ne
			   se bat sur « élagueur Nantes Sud », tout le monde se bat sur
			   « élagueur Nantes ». */
			'zone_communes'        => 'Vertou, Saint-Sébastien-sur-Loire, Rezé, Basse-Goulaine, Haute-Goulaine, Les Sorinières, Nantes Sud, Saint-Julien-de-Concelles, Le Loroux-Bottereau, Château-Thébaud, La Haye-Fouassière, Pont-Saint-Martin, Clisson',
			// Département couvert (info client du 13/09). Déclaré en zone
			// administrative dans le JSON-LD : couvrir un département ne se dit
			// pas en énumérant ses 207 communes, ça se dit en nommant le
			// département. Les communes restent listées pour la page zone.
			'zone_departement'     => 'Loire-Atlantique',
			'appel_titre'          => 'On vient voir *votre arbre*',
			'appel_texte'          => 'Un appel, une visite sur place, un devis écrit et gratuit. La suite vous appartient.',
			'appel_btn_texte'      => 'Demander mon devis',
			'cine_marquee'         => "Élagage\nAbattage\nCréation de jardin",
			'cine_tab_surtitre'    => 'De la cime aux racines',
			'cine_tab_titre'       => 'Un arbre, ça se lit *avant de se couper*',
			'cine_tab_texte'       => 'Un houppier déséquilibré, une fourche à écorce incluse, un collet enterré : ce qui décide d\'une taille ou d\'un abattage se voit d\'en haut et se vérifie en bas. Le premier travail, c\'est de regarder.',
			'cine_chapitres'       => "La cime — *l'élagage en grimpe* | Taille douce, éclaircie, réduction de couronne, haubanage. On travaille à la corde là où la nacelle ne passe pas, et on respecte la physiologie de l'arbre : ni étêtage, ni plaie ouverte laissée telle quelle. | Taille douce, Éclaircie, Haubanage\nLe tronc — *l'abattage maîtrisé* | Abattage direct quand la place le permet, démontage par câble quand elle ne le permet pas : arbre en limite de propriété, au-dessus d'une toiture, coincé entre deux murs. On sécurise, puis on descend pièce par pièce. | Démontage, Rétention, Sécurisation\nLes racines — *le jardin qui tient* | Création, plantation, engazonnement, massifs, et l'entretien qui va avec. Un jardin se dessine pour durer : les essences sont choisies pour le sol et l'exposition, pas pour la photo du premier printemps. | Création, Plantation, Entretien",
			'cine_scene_titre'     => 'Ce qui tient un arbre *ne se voit pas d\'en bas*',
			'cine_scene_texte'     => 'Le système racinaire fait la moitié de l\'arbre. C\'est lui qui décide si on taille, si on haubane, ou s\'il faut abattre.',
			'cine_phares_surtitre' => 'Nos trois métiers',
			'cine_phares_titre'    => 'Un grimpeur, un abatteur, *un jardinier*',
			'cine_phares_chapo'    => 'La même personne du premier appel au dernier passage. Vous ne racontez pas deux fois votre chantier.',
			'cine_phares_note'     => 'Arbre remarquable, accès impossible, urgence après tempête : ça se regarde sur place',
			'cine_avis'            => 'Djessy Azs | Je recommande travail efficace et de qualité. À l\'écoute de nôtres demande de bon conseil personnalisé. Nous sommes très satisfaits de son travail et nous recommandions vivement son service ! | Mai 2025',
			'cine_avis_url'        => 'https://www.google.com/maps/place/?q=place_id:ChIJd15eFR_nBUgRqwOuCR9Hlrs',
			'cine_avis_note'       => '5,0',
			'cine_avis_total'      => '1',
			'cine_arbre_surtitre'  => 'Devis',
			'cine_arbre_titre'     => 'Dites-nous *ce qui vous inquiète*',
			'cine_arbre_texte'     => 'Une branche au-dessus du toit, un arbre qui penche depuis la tempête, un jardin à reprendre entièrement. On se déplace, on regarde, et vous repartez avec un devis écrit et gratuit.',
		);
		/**
		 * Permet à un site enfant de changer le contenu livré sans toucher au thème.
		 */
		$table = apply_filters( 'lae_defauts', $table );
		return $table;
	}
}

/**
 * Valeur par défaut d'un réglage.
 *
 * @param string $cle    Clé sans le préfixe « lae_ » (le préfixe est toléré).
 * @param mixed  $sinon  Repli si la clé n'a pas de défaut livré.
 * @return mixed
 */
if ( ! function_exists( 'lae_defaut' ) ) {
	function lae_defaut( $cle, $sinon = '' ) {
		$cle   = ( 0 === strpos( $cle, 'lae_' ) ) ? substr( $cle, 4 ) : $cle;
		$table = lae_defauts();
		return array_key_exists( $cle, $table ) ? $table[ $cle ] : $sinon;
	}
}