<?php
/**
 * Durcissement sécurité du SITE lui-même.
 *
 * Corrige ce qu'un audit WordPress standard remonte : xmlrpc exposé,
 * énumération des comptes (wp-json) et d'auteur (?author=), pingback,
 * en-têtes de sécurité manquants, divulgation de version.
 *
 * 100 % défensif, sur notre propre site. Aucune incidence sur les visiteurs.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined( 'LAE_HARDENING_LOADED' ) ) {
	return;
}
define( 'LAE_HARDENING_LOADED', '1.0.0' );

/* ---- 1. xmlrpc.php : bloqué entièrement (force brute + DDoS pingback) ---- */
add_action( 'init', function () {
	$sf  = isset( $_SERVER['SCRIPT_FILENAME'] ) ? basename( (string) $_SERVER['SCRIPT_FILENAME'] ) : '';
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	if ( 'xmlrpc.php' === $sf || false !== strpos( $uri, '/xmlrpc.php' ) ) {
		status_header( 403 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		exit( 'Forbidden.' );
	}
}, 0 );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pings_open', '__return_false' );
add_filter( 'xmlrpc_methods', function ( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
} );

/* ---- 2. Énumération des comptes via l'API REST (visiteurs non connectés) ---- */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		foreach ( array( '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' ) as $route ) {
			if ( isset( $endpoints[ $route ] ) ) unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
} );

/* ---- 3. Énumération d'auteur ?author=N et archives /author/ ----
 * On intercepte ?author= dès 'init' (priorité 1), AVANT le redirect_canonical
 * de WordPress (template_redirect, priorité 10) qui, lui, révèle l'identifiant
 * de connexion en redirigeant vers /author/{login}/. */
add_action( 'init', function () {
	if ( ! is_admin() && isset( $_GET['author'] ) && '' !== $_GET['author'] ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );
add_action( 'template_redirect', function () {
	if ( ! is_admin() && is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 0 );

/* ---- 4. En-têtes de sécurité HTTP + retrait des divulgations ---- */
add_action( 'send_headers', function () {
	if ( headers_sent() ) return;
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
	// CSP « douce » : force les ressources en HTTPS sans rien bloquer.
	header( 'Content-Security-Policy: upgrade-insecure-requests' );
	if ( function_exists( 'is_ssl' ) && is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}
	header_remove( 'X-Pingback' );
	header_remove( 'X-Powered-By' );
}, 99 );

/* ---- 5. Masque la version de WordPress ---- */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/* ---- 6. Retire les liens de découverte inutiles (RSD/xmlrpc, manifest) ---- */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/* ---- 7. Durcissement .htaccess (niveau serveur, ce que PHP ne peut pas faire) :
 *        désactive le LISTING de répertoires et bloque readme / license /
 *        wp-config / .env / .git (fuite de version).
 *        Écrit UNE fois, dans un bloc balisé (donc réversible), et seulement si
 *        le .htaccess est accessible en écriture. Gardes IfModule → pas de 500
 *        selon la version d'Apache / LiteSpeed. */
add_action( 'admin_init', function () {
	if ( '1' === (string) get_option( 'lae_htaccess_hard', '' ) ) return; // déjà appliqué
	if ( ! function_exists( 'get_home_path' ) )       require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( ! function_exists( 'insert_with_markers' ) ) require_once ABSPATH . 'wp-admin/includes/misc.php';
	$home = function_exists( 'get_home_path' ) ? get_home_path() : ABSPATH;
	$file = rtrim( $home, '/\\' ) . '/.htaccess';
	if ( ! file_exists( $file ) || ! is_writable( $file ) ) {
		update_option( 'lae_htaccess_hard_note', 'manuel', false ); // à coller à la main
		return;
	}
	$rules = array(
		'Options -Indexes',
		'<FilesMatch "(?i)^(readme\.html|readme\.txt|license\.txt|licence\.txt|wp-config\.php|\.env|\.git.*)$">',
		'<IfModule mod_authz_core.c>',
		'Require all denied',
		'</IfModule>',
		'<IfModule !mod_authz_core.c>',
		'Order allow,deny',
		'Deny from all',
		'</IfModule>',
		'</FilesMatch>',
	);
	if ( function_exists( 'insert_with_markers' ) && insert_with_markers( $file, 'LAE Hardening', $rules ) ) {
		update_option( 'lae_htaccess_hard', '1', false );
		delete_option( 'lae_htaccess_hard_note' );
	}
} );

/* Rappel admin si le .htaccess n'était pas modifiable (à coller à la main). */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) return;
	if ( 'manuel' !== (string) get_option( 'lae_htaccess_hard_note', '' ) ) return;
	echo '<div class="notice notice-warning"><p><strong>Sécurité :</strong> le fichier <code>.htaccess</code> n\'a pas pu être durci automatiquement (droits en écriture). Ajoute ces lignes en haut de ton <code>.htaccess</code> pour bloquer le listing de répertoires et la fuite de version :</p>'
		. '<pre style="background:#fff;padding:10px;border:1px solid #ccd0d4;">Options -Indexes' . "\n" . '&lt;FilesMatch "(?i)^(readme\.html|readme\.txt|license\.txt|wp-config\.php|\.env)$"&gt;' . "\n" . '  Require all denied' . "\n" . '&lt;/FilesMatch&gt;</pre>'
		. '<p>Détail complet : <code>SECURITE-HTACCESS.txt</code> à la racine du dépôt.</p></div>';
} );

/*
 * 8. Masque les versions dans les URL des CSS/JS — SANS casser le cassage de cache.
 *
 * Retirer purement le ?ver= serait une erreur : ce paramètre est aussi ce qui
 * force le navigateur à recharger une feuille modifiée. Le supprimer figerait
 * le CSS des visiteurs à la version qu'ils ont déjà, indéfiniment — une mise à
 * jour du thème ne leur parviendrait jamais.
 *
 * On ne le retire donc pas : on le REMPLACE par une empreinte courte de sa
 * valeur. Le numéro de version disparaît (un attaquant ne sait plus quelle
 * faille connue tenter), mais l'empreinte change dès que la version change,
 * donc le navigateur recharge. Les deux objectifs sont tenus.
 */
if ( ! function_exists( 'lae_hard_strip_ver' ) ) {
	function lae_hard_strip_ver( $src ) {
		if ( is_user_logged_in() || ! $src ) {
			return $src;
		}
		$ver = '';
		$q   = wp_parse_url( $src, PHP_URL_QUERY );
		if ( $q ) {
			parse_str( $q, $args );
			$ver = isset( $args['ver'] ) ? (string) $args['ver'] : '';
		}
		if ( '' === $ver ) {
			return $src;
		}
		return add_query_arg( 'ver', substr( md5( $ver ), 0, 8 ), $src );
	}
}
add_filter( 'style_loader_src', 'lae_hard_strip_ver', 9999 );
add_filter( 'script_loader_src', 'lae_hard_strip_ver', 9999 );
