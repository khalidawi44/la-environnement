<?php
/**
 * Thème L.A Environnement — chargement.
 *
 * Le socle WordPress, le design et la mécanique de déploiement (sync GitHub
 * automatique). Tout le contenu affiché vient de l'administration : rien
 * n'est écrit en dur dans les gabarits.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LAE_VERSION', '1.8.0' );

/** Supports WordPress de base. */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array(
		'height'      => 96,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	add_image_size( 'lae-carte', 800, 600, true );
	add_image_size( 'lae-large', 1920, 1080, true );

	register_nav_menus( array(
		'principal' => 'Menu principal',
		'pied'      => 'Menu pied de page',
		'legal'     => 'Mentions légales (bas de page)',
	) );
} );

/** Feuille de style et script du thème. */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'lae-style', get_stylesheet_uri(), array(), LAE_VERSION );
	wp_enqueue_script( 'lae-script', get_template_directory_uri() . '/assets/js/lae.js', array(), LAE_VERSION, true );
} );

/** Largeur de contenu utilisée par l'éditeur. */
if ( ! isset( $content_width ) ) {
	$content_width = 760;
}

/** Extrait : longueur et suite. */
add_filter( 'excerpt_length', function () { return 26; }, 20 );
add_filter( 'excerpt_more', function () { return '…'; } );

/** Classe utile sur <body> quand le bandeau principal porte une photo. */
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() && ! lae_reglage( 'hero_image' ) ) {
		$classes[] = 'lae-sans-photo';
	}
	return $classes;
} );

// ── Sécurité ────────────────────────────────────────────────────────
require_once get_template_directory() . '/inc/lae-hardening.php';

// ── Design et contenu ───────────────────────────────────────────────
require_once get_template_directory() . '/inc/lae-template-tags.php';
require_once get_template_directory() . '/inc/lae-customizer.php';
require_once get_template_directory() . '/inc/lae-cpt.php';
require_once get_template_directory() . '/inc/lae-seo.php';
require_once get_template_directory() . '/inc/lae-amorce.php';
require_once get_template_directory() . '/inc/lae-contact.php';

// ── Mécanique de déploiement ────────────────────────────────────────
// L'écran d'admin est chargé en premier : il définit lae_gh_json(),
// utilisé par le moteur de sync pour l'import de contenu.
require_once get_template_directory() . '/inc/lae-sync-admin.php';
require_once get_template_directory() . '/inc/lae-github-sync.php';
