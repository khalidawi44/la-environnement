<?php
/**
 * Deux prestations qui manquaient : dessouchage et taille de haie.
 *
 * ═══════════════════════════════════════════════════════════════════
 * POURQUOI CES DEUX-LÀ, ET PAS D'AUTRES.
 *
 * L'audit du 22/09 a compté les mots du site entier, page par page.
 * Résultat :
 *
 *   — « dessouchage » : ZÉRO occurrence sur les 22 pages. « Rognage de
 *     souche » n'apparaît que dans UNE phrase, noyée en bas de la fiche
 *     abattage. C'est une prestation qu'Anthony pratique, que les gens
 *     cherchent SEULE — on ne tape pas « abattage » quand on a déjà une
 *     souche — et le mot qu'ils tapent n'était nulle part.
 *
 *   — « taille de haie » : une ligne dans la fiche entretien de jardin.
 *     Or la page /tarifs/ dit noir sur blanc qu'« une haie se chiffre au
 *     mètre » : c'est donc une prestation chiffrée à part, sans page à
 *     elle. Et c'est le pic saisonnier du métier.
 *
 * CE QUE CES TEXTES N'INVENTENT PAS. Aucun prix, aucun délai, aucune
 * profondeur de rognage chiffrée, aucun matériel nommé. Tout ce qui est
 * écrit ici découle de ce que le site affirme déjà ailleurs (forfait après
 * visite, broyage sur place possible, terrain rendu net) ou de faits
 * botaniques généraux. La règle des dates renvoie à l'article existant
 * plutôt que de la réécrire — une règle recopiée à deux endroits finit
 * par diverger.
 *
 * L'amorce étant verrouillée en ligne, ces fiches sont créées ici, sous
 * leur propre verrou, et ne sont jamais réécrites si elles existent déjà.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'lae_prestations2_table' ) ) {
	function lae_prestations2_table() {
		return array(

			'dessouchage-et-rognage-de-souche' => array(
				'titre'   => 'Dessouchage et rognage de souche',
				'famille' => 'chantier',
				'ordre'   => 7,
				'extrait' => 'La souche qui reste après l\'abattage : rognée sous le niveau du sol, ou extraite quand le terrain doit être repris.',
				'texte'   => "Un arbre abattu laisse une souche, et elle pose trois problèmes : on s'y prend les pieds, elle empêche de replanter ou de poser quoi que ce soit à cet endroit, et elle attire les champignons lignivores qui s'installent ensuite dans le bois mort.\n\nDeux façons de s'en débarrasser, et elles ne servent pas la même chose.\n\nLe <strong>rognage</strong> réduit la souche en copeaux sous le niveau du sol. C'est rapide, ça n'ouvre pas le terrain, et c'est ce qu'il faut dans la plupart des cas : une pelouse à rétablir, une allée, un massif à replanter à côté. Les copeaux peuvent rester sur place et servir de paillage, ou partir avec le reste.\n\nLe <strong>dessouchage</strong> arrache la souche et ses grosses racines. C'est plus lourd, ça ouvre une fosse qu'il faut combler, et ça ne se justifie que lorsque le terrain doit être repris en profondeur : terrassement, dalle, piscine, plantation d'un nouvel arbre au même endroit.\n\nCe qui décide, c'est ce que le terrain doit devenir — et l'accès. Une souche au fond d'un jardin clos ne se traite pas comme une souche en bordure d'allée. On regarde sur place avant de chiffrer, et le devis est gratuit.\n\nUn point à savoir : replanter un arbre exactement là où le précédent a été rogné n'est pas une bonne idée. Le sol y est saturé de bois en décomposition, et les champignons qui le digèrent s'attaquent volontiers aux jeunes racines. Décaler la nouvelle plantation d'un mètre ou deux coûte moins cher qu'un arbre qui dépérit trois ans plus tard.",
			),

			'taille-de-haie' => array(
				'titre'   => 'Taille de haie',
				'famille' => 'jardin',
				'ordre'   => 8,
				'extrait' => 'Taille d\'entretien, remise en forme ou rabattage d\'une haie devenue trop haute, au bon moment de l\'année.',
				'texte'   => "Une haie taillée régulièrement demande moins de travail qu'une haie reprise une fois tous les cinq ans — et elle reste dense. Une haie laissée filer se dégarnit par le bas, et le bas ne repart pas toujours.\n\nTrois interventions différentes, qu'on confond souvent.\n\nLa <strong>taille d'entretien</strong> maintient la forme et la densité. Sur la plupart des haies de jardin, un à deux passages par an suffisent, selon l'essence et la vigueur.\n\nLa <strong>remise en forme</strong> reprend une haie qui a débordé : on redonne une ligne, on rattrape les trous, on dégage ce qui empiète sur une allée, un portail ou la limite du voisin.\n\nLe <strong>rabattage</strong> réduit franchement une haie devenue trop haute ou trop large. C'est la plus délicate des trois : certaines essences repartent sur du vieux bois, d'autres non — un thuya rabattu trop court ne reverdit jamais. On regarde l'essence et l'état avant de décider jusqu'où descendre, et on le dit franchement si ce n'est pas rattrapable.\n\nLes déchets sont broyés sur place et laissés en paillage au pied de la haie, ou évacués. Dans les deux cas le pied est ratissé : le chantier est fini quand il ne reste rien à ramasser.\n\n<strong>Le moment compte autant que le geste.</strong> Une haie se taille de préférence hors période de nidification, et une coupe faite en pleine montée de sève fatigue la plante. Notre article sur les périodes de taille explique ce qui est une obligation, ce qui est une recommandation, et ce qui relève du bon sens pour l'arbre.\n\nLa haie se chiffre au mètre linéaire, après visite. Le devis est écrit et gratuit.",
			),
		);
	}
}

if ( ! function_exists( 'lae_prestations2_rattrapage' ) ) {
	function lae_prestations2_rattrapage() {

		if ( get_option( 'lae_prestations2_2026_09' ) ) {
			return;
		}
		update_option( 'lae_prestations2_2026_09', 1, false );   // verrou AVANT le travail

		$journal = array();

		foreach ( lae_prestations2_table() as $slug => $f ) {

			if ( get_page_by_path( $slug, OBJECT, 'lae_prestation' ) ) {
				$journal[ $slug ] = 'existait deja';
				continue;
			}

			$id = wp_insert_post( array(
				'post_type'    => 'lae_prestation',
				'post_status'  => 'publish',
				'post_title'   => $f['titre'],
				'post_name'    => $slug,
				'post_excerpt' => $f['extrait'],
				'post_content' => $f['texte'],
				'menu_order'   => (int) $f['ordre'],
			), true );

			if ( is_wp_error( $id ) ) {
				$journal[ $slug ] = 'echec';
				continue;
			}

			$terme = term_exists( $f['famille'], 'lae_famille' );
			if ( $terme && ! is_wp_error( $terme ) ) {
				wp_set_object_terms( (int) $id, array( (int) $terme['term_id'] ), 'lae_famille' );
			}

			$journal[ $slug ] = (int) $id;
		}

		update_option( 'lae_prestations2_journal', $journal, false );
	}
}

add_action( 'init', 'lae_prestations2_rattrapage', 28 );
add_action( 'admin_init', 'lae_prestations2_rattrapage', 19 );
