<?php
/**
 * SEO technique — ce que les robots lisent, pas ce que le visiteur voit.
 *
 * Complète `lae-seo.php` (données structurées JSON-LD) sans le modifier :
 * ici on ne traite que les manques constatés sur le site en ligne le 09/09.
 *
 *   - Aucune balise `<meta name="description">` : Google fabriquait lui-même
 *     l'extrait affiché dans ses résultats, à partir d'un bout de page au
 *     hasard. On reprend la main.
 *   - Aucune balise `<meta name="robots">` : sans `max-image-preview:large`,
 *     les photos de chantier ne peuvent pas s'afficher en grand dans Google
 *     Images ni dans Discover. Pour un métier qui se vend par la photo, c'est
 *     une perte sèche.
 *   - `robots.txt` annonçait `Sitemap: /wp-sitemap.xml`, qui répondait **404**.
 *     Annoncer un sitemap inexistant est pire que ne rien annoncer : le robot
 *     se heurte à une erreur à chaque passage. Le sitemap natif de WordPress
 *     était désactivé ; on le réactive.
 *
 * Aucun style, aucune image : uniquement des en-têtes et des balises.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ---- 1. Sitemap natif de WordPress : remis en service ---- */
add_filter( 'wp_sitemaps_enabled', '__return_true', 99 );

/* ---- 2. Directives d'indexation ----
 * `max-image-preview:large` autorise la grande vignette dans Google Images et
 * Discover. `max-snippet:-1` et `max-video-preview:-1` lèvent les limites de
 * longueur d'extrait. Les pages de recherche et les archives paginées passent
 * en `noindex` : elles diluent le référencement sans jamais rien apporter. */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		return $robots;
	}
	if ( is_paged() ) {
		$robots['noindex'] = true;
	}
	$robots['index']              = true;
	$robots['follow']             = true;
	$robots['max-image-preview']  = 'large';
	$robots['max-snippet']        = -1;
	$robots['max-video-preview']  = -1;
	return $robots;
}, 20 );

/* ---- 3. Meta description ----
 * On réutilise la description déjà calculée pour le partage (lae-partage.php)
 * afin que l'extrait Google et l'aperçu d'un lien racontent la même chose.
 * On s'efface si un plugin SEO gère déjà la balise. */
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	if ( is_404() || is_feed() || ! function_exists( 'lae_partage_description' ) ) {
		return;
	}
	$desc = lae_partage_description();
	if ( '' === $desc ) return;
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
}, 4 );

/* ---- 4. Le sitemap n'annonce que ce qui a un sens ----
 * Les types techniques et les taxonomies vides encombrent l'index sans
 * apporter de page utile. */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['attachment'] );
	return $types;
} );

/* ---- 5. Nettoyage du <head> ----
 * Balises héritées, sans usage aujourd'hui, qui alourdissent chaque page. */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
