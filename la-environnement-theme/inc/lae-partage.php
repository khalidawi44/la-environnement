<?php
/**
 * Balises de partage (Open Graph + Twitter Card).
 *
 * POURQUOI CE FICHIER EXISTE. Le site n'émettait AUCUNE balise `og:`. Quand un
 * lien est partagé — SMS/RCS, iMessage, WhatsApp, Facebook, LinkedIn — le robot
 * qui ne trouve pas d'`og:image` parcourt la page et choisit une image tout
 * seul. Il tombait sur `arbre-colonne.webp`, le rendu de l'arbre en 1200×6000 :
 * l'aperçu du lien affichait une colonne d'arbre sur toute la hauteur. Constaté
 * en vrai le 09/09 sur un envoi RCS.
 *
 * Aucun style, aucune image nouvelle : uniquement des balises `<head>`.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Image de partage, par ordre de préférence.
 *
 * L'image de la colonne est explicitement écartée : son rapport (1 pour 5)
 * ne convient à aucun aperçu de lien, et c'est elle qui était choisie faute
 * de mieux.
 *
 * @return string URL absolue, ou '' si rien d'utilisable.
 */
function lae_partage_image() {
	$pistes = array();

	// 1. Réglage dédié, s'il est un jour ajouté au personnalisateur.
	if ( function_exists( 'lae_reglage' ) ) {
		$pistes[] = lae_reglage( 'partage_image' );
	}

	// 2. Image mise en avant de la page ou de l'article consulté.
	if ( is_singular() && has_post_thumbnail() ) {
		$pistes[] = get_the_post_thumbnail_url( null, 'full' );
	}

	// 3. Réglages paysage déjà renseignés par le client.
	if ( function_exists( 'lae_reglage' ) ) {
		$pistes[] = lae_reglage( 'hero_image' );
		$pistes[] = lae_reglage( 'cine_poster' );
		$pistes[] = lae_reglage( 'cine_tab_image' );
	}

	// 4. Repli livré avec le thème : photo réelle, 1920×1080.
	$pistes[] = get_template_directory_uri() . '/assets/images/jardin-piscine.webp';

	foreach ( $pistes as $url ) {
		if ( ! is_string( $url ) || '' === trim( $url ) ) continue;
		// Jamais la colonne : rapport 1 pour 5, illisible en aperçu.
		if ( false !== strpos( $url, 'arbre-colonne' ) ) continue;
		return esc_url_raw( $url );
	}
	return '';
}

/** Dimensions de l'image si elle est connue de la médiathèque. */
function lae_partage_dimensions( $url ) {
	if ( ! function_exists( 'attachment_url_to_postid' ) ) return array();
	$id = attachment_url_to_postid( $url );
	if ( ! $id ) return array();
	$meta = wp_get_attachment_metadata( $id );
	if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) return array();
	return array( (int) $meta['width'], (int) $meta['height'] );
}

/** Description courte de la page courante. */
function lae_partage_description() {
	if ( is_singular() ) {
		$p = get_post();
		if ( $p ) {
			$txt = $p->post_excerpt ? $p->post_excerpt : wp_strip_all_tags( (string) $p->post_content );
			$txt = trim( preg_replace( '/\s+/', ' ', $txt ) );
			if ( '' !== $txt ) return wp_html_excerpt( $txt, 200, '…' );
		}
	}
	$d = get_bloginfo( 'description', 'display' );
	return $d ? $d : '';
}

add_action( 'wp_head', function () {
	// Un plugin SEO gère déjà l'Open Graph : on ne double pas les balises,
	// deux jeux concurrents donnent un aperçu imprévisible selon le robot.
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	if ( is_404() || is_feed() ) return;

	$titre = wp_get_document_title();
	$desc  = lae_partage_description();
	$url   = home_url( add_query_arg( array() ) );
	if ( is_singular() ) {
		$permalien = get_permalink();
		if ( $permalien ) $url = $permalien;
	} elseif ( is_front_page() || is_home() ) {
		$url = home_url( '/' );
	}
	$img = lae_partage_image();

	echo "\n<!-- Partage (LAE) -->\n";
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() && ! is_front_page() ? 'article' : 'website' );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titre ) );
	if ( $desc ) printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );

	if ( $img ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img ) );
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		$dim = lae_partage_dimensions( $img );
		if ( 2 === count( $dim ) ) {
			printf( '<meta property="og:image:width" content="%d">' . "\n", $dim[0] );
			printf( '<meta property="og:image:height" content="%d">' . "\n", $dim[1] );
		} elseif ( false !== strpos( $img, 'jardin-piscine' ) ) {
			// Repli du thème : dimensions connues, elles évitent un aperçu vide
			// le temps que le robot télécharge le fichier.
			echo '<meta property="og:image:width" content="1920">' . "\n";
			echo '<meta property="og:image:height" content="1080">' . "\n";
		}
	}

	// Twitter/X : « summary_large_image » donne la grande vignette.
	printf( '<meta name="twitter:card" content="%s">' . "\n", $img ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $titre ) );
	if ( $desc ) printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	if ( $img )  printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $img ) );
	echo "<!-- /Partage -->\n\n";
}, 5 );
