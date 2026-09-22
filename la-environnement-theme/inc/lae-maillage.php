<?php
/**
 * Maillage interne — relier les prestations, les chantiers et les articles.
 *
 * ═══════════════════════════════════════════════════════════════════
 * CE QUE L'AUDIT DU 22/09 A MESURÉ, et c'est net.
 *
 * 1. LES PAGES CHANTIER SONT DES CULS-DE-SAC. Leurs seuls liens sortants
 *    sont le menu et le pied de page — rien de contextuel. Or c'est LA
 *    page de conversion : quelqu'un qui regarde un avant/après veut savoir
 *    comment et combien. Aujourd'hui on ne lui propose rien.
 *
 * 2. TROIS PRESTATIONS SUR SEPT SONT ENTERRÉES. Le bloc « Autres
 *    prestations » triait par `menu_order` croissant : il affichait donc
 *    TOUJOURS les trois mêmes, sur les sept pages prestation comme sur les
 *    trois articles. Création de jardin, entretien de jardin et évacuation
 *    n'étaient liées que depuis deux pages du site ; les trois premières,
 *    depuis onze.
 *
 * 3. LA PRESTATION GAZON ET SON CHANTIER NE SE LIENT PAS, alors qu'il n'y
 *    a qu'un chantier gazon et qu'une prestation gazon. L'appariement est
 *    évident et manquait dans les deux sens.
 *
 * POURQUOI UNE TABLE EXPLICITE plutôt qu'un rapprochement automatique par
 * mots communs : un rapprochement deviné se trompe en silence et met en
 * avant un chantier qui n'a rien à voir. Trois chantiers, sept
 * prestations : la table tient en six lignes, elle est vérifiable à l'œil,
 * et elle ne rapproche que ce qui est vrai. Elle est filtrable pour que la
 * faire évoluer n'oblige pas à toucher au code.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Quelles prestations correspondent à quel type de chantier.
 *
 * Clé = nom du terme `lae_type_chantier`, valeur = slugs de prestation.
 * Vérifié le 22/09 contre les trois chantiers réellement publiés.
 *
 * @return array<string, string[]>
 */
if ( ! function_exists( 'lae_maillage_table' ) ) {
	function lae_maillage_table() {
		return apply_filters( 'lae_maillage_table', array(
			'abattage' => array( 'abattage-et-demontage', 'evacuation-et-broyage' ),
			'élagage'  => array( 'elagage-en-grimpe', 'evacuation-et-broyage' ),
			'elagage'  => array( 'elagage-en-grimpe', 'evacuation-et-broyage' ),
			'jardin'   => array( 'pose-de-gazon-synthetique', 'creation-de-jardin', 'entretien-de-jardin' ),
		) );
	}
}

/** Les prestations à proposer depuis une page chantier. */
if ( ! function_exists( 'lae_maillage_prestations' ) ) {
	function lae_maillage_prestations( $chantier_id ) {

		$types = get_the_terms( $chantier_id, 'lae_type_chantier' );
		if ( ! $types || is_wp_error( $types ) ) {
			return array();
		}

		$table = lae_maillage_table();
		$slugs = array();
		foreach ( $types as $t ) {
			$cle = mb_strtolower( $t->name, 'UTF-8' );
			if ( isset( $table[ $cle ] ) ) {
				$slugs = array_merge( $slugs, $table[ $cle ] );
			}
		}
		$slugs = array_values( array_unique( $slugs ) );
		if ( ! $slugs ) {
			return array();
		}

		/* `post_name__in` ne garantit pas l'ordre demandé : on le rétablit,
		   pour que la prestation la plus proche du chantier arrive en tête. */
		$trouves = get_posts( array(
			'post_type'        => 'lae_prestation',
			'post_status'      => 'publish',
			'post_name__in'    => $slugs,
			'posts_per_page'   => count( $slugs ),
			'no_found_rows'    => true,
			'suppress_filters' => false,
		) );

		$par_slug = array();
		foreach ( $trouves as $p ) {
			$par_slug[ $p->post_name ] = $p;
		}
		$ordonnes = array();
		foreach ( $slugs as $s ) {
			if ( isset( $par_slug[ $s ] ) ) {
				$ordonnes[] = $par_slug[ $s ];
			}
		}
		return $ordonnes;
	}
}

/** Les chantiers à montrer depuis une page prestation — l'autre sens. */
if ( ! function_exists( 'lae_maillage_chantiers' ) ) {
	function lae_maillage_chantiers( $prestation_id, $max = 2 ) {

		$slug  = get_post_field( 'post_name', $prestation_id );
		$table = lae_maillage_table();

		// Quels types de chantier mènent à CETTE prestation ?
		$types = array();
		foreach ( $table as $type => $slugs ) {
			if ( in_array( $slug, $slugs, true ) ) {
				$types[] = $type;
			}
		}
		if ( ! $types ) {
			return array();
		}

		/* La table contient des variantes d'écriture d'un même terme
		   (« élagage » et « elagage ») pour absorber l'accent. On interroge
		   donc la taxonomie par NOM, en laissant WordPress ne retenir que
		   les termes qui existent vraiment. */
		$termes = get_terms( array(
			'taxonomy'   => 'lae_type_chantier',
			'hide_empty' => true,
			'fields'     => 'ids',
			'name'       => array_map( 'ucfirst', $types ),
		) );
		if ( ! $termes || is_wp_error( $termes ) ) {
			return array();
		}

		return get_posts( array(
			'post_type'      => 'lae_realisation',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $max,
			'no_found_rows'  => true,
			'tax_query'      => array(  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'lae_type_chantier',
					'field'    => 'term_id',
					'terms'    => $termes,
				),
			),
		) );
	}
}
