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
	// Des CHAÎNES, pas des entiers : wp_robots() n'écrit « clé:valeur » que
	// pour une chaîne. Avec l'entier -1 il produisait « max-snippet » tout
	// court, directive invalide que Google ignore. Constaté en ligne le 09/09.
	$robots['max-snippet']        = '-1';
	$robots['max-video-preview']  = '-1';
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

/* ---- 3 bis. Le sitemap doit répondre 200, pas 404 ----
 * Constaté en ligne le 09/09 : /wp-sitemap.xml renvoyait un contenu XML
 * parfaitement valide… avec un statut HTTP 404. Un robot n'en lit pas une
 * ligne : pour lui la page n'existe pas. Le sitemap paraissait réparé et ne
 * l'était pas.
 *
 * Cause : les règles de réécriture avaient été calculées alors que le sitemap
 * était désactivé. On les régénère une fois à chaque changement de version, et
 * on force le statut quand une requête de sitemap est effectivement servie —
 * ceinture et bretelles, parce qu'un sitemap en 404 est invisible et
 * silencieux. */
add_action( 'template_redirect', function () {
	if ( '' === (string) get_query_var( 'sitemap' ) ) return;
	global $wp_query;
	if ( $wp_query ) {
		$wp_query->is_404 = false;
	}
	status_header( 200 );
}, 0 );

add_action( 'admin_init', function () {
	if ( ! defined( 'LAE_VERSION' ) ) return;
	if ( get_option( 'lae_rewrite_version' ) === LAE_VERSION ) return;
	flush_rewrite_rules( true );
	update_option( 'lae_rewrite_version', LAE_VERSION, false );
} );

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
