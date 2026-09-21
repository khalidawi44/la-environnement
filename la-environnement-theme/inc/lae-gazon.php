<?php
/**
 * Pose de gazon synthétique — prestation et chantier, créés sur un site DÉJÀ amorcé.
 *
 * ═══════════════════════════════════════════════════════════════════
 * POURQUOI CE FICHIER EXISTE.
 *
 * `lae_amorce_prestations()` et `lae_chantiers_livres()` ne sèment qu'UNE
 * fois, derrière les verrous `lae_amorce_faite` et `lae_chantiers_semes`.
 * C'est voulu : sans ces verrous, chaque déploiement réécrirait le contenu
 * qu'Anthony aurait modifié entre-temps.
 *
 * Conséquence directe : ajouter une entrée à ces tables ne change RIEN sur
 * elagage-vertou.fr, dont les verrous sont posés depuis le 09/09. La table
 * ne sert plus qu'aux installations neuves. La septième prestation livrée
 * par la session design serait donc restée invisible en ligne.
 *
 * Ce fichier fait le rattrapage, sous son propre verrou, avec la même
 * discipline que le reste du thème : on ne crée QUE ce qui n'existe pas,
 * on ne réécrit jamais une fiche déjà là, et le verrou est posé AVANT le
 * travail pour qu'un échec ne relance pas le traitement à chaque requête.
 *
 * POURQUOI PAS DE CURSEUR SUR CE CHANTIER. La paire avant/après est
 * authentique — terre battue, puis gazon posé — mais le photographe s'est
 * déplacé entre les deux prises : un fondu 50/50 montre un double olivier
 * et un double pilier de portail. Un comparateur à curseur superposerait
 * deux points de vue et donnerait un effet fantôme. On laisse donc
 * `LAE_META_SUPERPOSE` vide, ce qui rend le diptyque côte à côte — c'est
 * le comportement par défaut de `lae_comparateur()`, et le bon ici.
 * Mesure et décision : session design, 21/09, consignées dans
 * CHANTIERS-PHOTOS.md.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'lae_gazon_rattrapage' ) ) {

	/** La prestation, si elle n'est pas déjà publiée. */
	function lae_gazon_prestation() {

		if ( get_page_by_path( 'pose-de-gazon-synthetique', OBJECT, 'lae_prestation' ) ) {
			return 0;   // déjà là : on n'y touche pas
		}

		// Le texte vient de la table de l'amorce : une seule source, pour ne
		// pas entretenir deux versions du même paragraphe.
		$source = function_exists( 'lae_amorce_prestations' ) ? lae_amorce_prestations() : array();
		$fiche  = array();
		foreach ( $source as $p ) {
			if ( isset( $p['titre'] ) && 'Pose de gazon synthétique' === $p['titre'] ) {
				$fiche = $p;
				break;
			}
		}
		if ( ! $fiche ) {
			return 0;   // la table ne la porte pas : on n'invente pas de texte
		}

		$id = wp_insert_post( array(
			'post_type'    => 'lae_prestation',
			'post_status'  => 'publish',
			'post_title'   => $fiche['titre'],
			'post_name'    => 'pose-de-gazon-synthetique',
			'post_excerpt' => isset( $fiche['extrait'] ) ? $fiche['extrait'] : '',
			'post_content' => isset( $fiche['texte'] ) ? $fiche['texte'] : '',
			'menu_order'   => 5,
		), true );

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		$terme = term_exists( 'jardin', 'lae_famille' );
		if ( $terme && ! is_wp_error( $terme ) ) {
			wp_set_object_terms( (int) $id, array( (int) $terme['term_id'] ), 'lae_famille' );
		}

		return (int) $id;
	}

	/** Le chantier avant/après, si le slug n'est pas déjà pris. */
	function lae_gazon_chantier() {

		$slug = 'gazon-synthetique-autour-olivier';

		$existe = get_posts( array(
			'post_type'      => 'lae_realisation',
			'post_status'    => 'any',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );
		if ( $existe ) {
			return 0;
		}

		if ( ! function_exists( 'lae_chantier_importe_image' ) ) {
			return 0;
		}

		$avant = lae_chantier_importe_image( 'chantiers/gazon-1-avant.webp', 'Avant : le jardin en terre battue' );
		$apres = lae_chantier_importe_image( 'chantiers/gazon-2-apres.webp', 'Après : le gazon synthétique posé' );
		if ( ! $avant || ! $apres ) {
			return 0;   // sans les deux images, la fiche n'aurait aucun intérêt
		}

		$texte = "<!-- wp:paragraph --><p>Un jardin de ville en terre battue, où l'herbe ne tenait plus : l'olivier fait de l'ombre une bonne partie de la journée, le sol est tassé par le passage, et ce qui repoussait au printemps avait disparu avant l'été.</p><!-- /wp:paragraph -->\n\n"
			. "<!-- wp:paragraph --><p>Le sol a été préparé et nivelé avant la pose — c'est ce qui se voit le moins et qui décide du résultat : un gazon synthétique posé sur un sol irrégulier fait apparaître chaque creux. Les lés ont ensuite été ajustés et fixés, les raccords travaillés pour ne pas se lire.</p><!-- /wp:paragraph -->\n\n"
			. "<!-- wp:paragraph --><p>Un détail visible sur la photo de droite : <strong>un cercle de terre est laissé au pied de l'olivier</strong>. On ne recouvre pas la base d'un arbre en place. Ses racines ont besoin d'air et d'eau, et les en priver l'affaiblit à bas bruit, sur plusieurs années. C'est ce qu'un élagueur regarde avant un poseur.</p><!-- /wp:paragraph -->";

		$id = wp_insert_post( array(
			'post_type'    => 'lae_realisation',
			'post_status'  => 'publish',
			'post_title'   => 'Pose de gazon synthétique autour d\'un olivier',
			'post_name'    => $slug,
			'post_excerpt' => 'Un jardin en terre battue où l\'herbe ne tenait plus, remplacé par du gazon synthétique — sauf au pied de l\'olivier, laissé en terre.',
			'post_content' => $texte,
		), true );

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		set_post_thumbnail( $id, $apres );
		update_post_meta( $id, LAE_META_AVANT, $avant );
		update_post_meta( $id, LAE_META_AVANT_LIB, 'Avant' );
		update_post_meta( $id, LAE_META_APRES_LIB, 'Après' );
		/* LAE_META_SUPERPOSE reste VIDE : diptyque, pas curseur. Voir l'en-tête
		   de ce fichier — les deux prises n'ont pas le même point de vue. */

		$terme = term_exists( 'Jardin', 'lae_type_chantier' );
		if ( ! $terme ) {
			$terme = wp_insert_term( 'Jardin', 'lae_type_chantier' );
		}
		if ( ! is_wp_error( $terme ) ) {
			wp_set_object_terms( (int) $id, array( (int) $terme['term_id'] ), 'lae_type_chantier' );
		}

		return (int) $id;
	}

	function lae_gazon_rattrapage() {

		if ( get_option( 'lae_gazon_2026_09' ) ) {
			return;
		}
		update_option( 'lae_gazon_2026_09', 1, false );   // verrou posé AVANT le travail

		$journal = array(
			'prestation' => lae_gazon_prestation(),
			'chantier'   => lae_gazon_chantier(),
			'date'       => current_time( 'mysql' ),
		);
		update_option( 'lae_gazon_journal', $journal, false );
	}
}

/* Après l'amorce et après les chantiers : la prestation lit la table de
   l'amorce, et le chantier a besoin que la taxonomie existe. Le déploiement
   arrive par WP-Cron, sans page d'administration — d'où les deux crochets. */
add_action( 'init', 'lae_gazon_rattrapage', 25 );
add_action( 'admin_init', 'lae_gazon_rattrapage', 16 );
