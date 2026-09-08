<?php
/**
 * Thème L.A Environnement — chargement.
 *
 * Le design est fait dans un second temps ; ce fichier ne pose que le socle
 * WordPress et la mécanique de déploiement (sync GitHub automatique).
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LAE_VERSION', '1.0.0' );

/** Supports WordPress de base. */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'principal' => 'Menu principal',
		'pied'      => 'Menu pied de page',
	) );
} );

/** Feuille de style du thème. */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'lae-style', get_stylesheet_uri(), array(), LAE_VERSION );
} );

// ── Sécurité ────────────────────────────────────────────────────────
require_once get_template_directory() . '/inc/lae-hardening.php';

// ── Mécanique de déploiement ────────────────────────────────────────
// L'écran d'admin est chargé en premier : il définit lae_gh_json(),
// utilisé par le moteur de sync pour l'import de contenu.
require_once get_template_directory() . '/inc/lae-sync-admin.php';
require_once get_template_directory() . '/inc/lae-github-sync.php';
