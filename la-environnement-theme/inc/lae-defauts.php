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
			'telephone'            => '07 59 79 03 96',
			'email'                => 'paysagisteenvironnement@gmail.com',
			'adresse'              => 'Vertou (44)',
			'hero_surtitre'        => 'Élagage · Abattage · Création de jardin',
			'hero_titre'           => 'Un arbre trop grand, trop près, *trop vieux ?*',
			'hero_chapo'           => 'On monte, on regarde, et on vous dit ce qu\'il faut faire : tailler, haubaner ou abattre. Élagage en grimpe, démontage par câble, création et entretien de jardin.',
			'hero_points'          => "Diagnostic sur place avant le devis\nDémontage par câble là où l'abattage direct est impossible\nDéchets verts évacués ou broyés sur place",
			'prestations_surtitre' => 'Nos prestations',
			'prestations_titre'    => 'Tout ce qu\'on fait *sur un terrain*',
			'prestations_chapo'    => 'Chaque prestation a sa fiche : ce qu\'elle comprend, comment on procède, et ce qu\'on laisse derrière nous.',
			'etapes_titre'         => 'Comment ça se passe',
			'realisations_titre'   => 'Des chantiers, *pas des images d\'agence*',
			'realisations_chapo'   => 'Ce qu\'il y avait, ce qu\'on a fait, ce qu\'il en reste. Photos prises sur place.',
			'zone_titre'           => 'Zone d\'intervention',
			'zone_texte'           => 'Basé à Vertou, au sud-est de Nantes. On se déplace sur le secteur pour voir l\'arbre ou le terrain avant tout devis — dites-nous où vous êtes.',
			'zone_communes'        => 'Vertou',
			'appel_titre'          => 'On vient voir *votre arbre*',
			'appel_texte'          => 'Un appel, une visite sur place, un devis écrit. La suite vous appartient.',
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
			'cine_arbre_texte'     => 'Une branche au-dessus du toit, un arbre qui penche depuis la tempête, un jardin à reprendre entièrement. On se déplace, on regarde, et vous repartez avec un devis écrit.',
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