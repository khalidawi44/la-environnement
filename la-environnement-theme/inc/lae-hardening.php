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

/* ---- 2 bis. oEmbed : la porte que les points 2 et 3 avaient laissee ----
 *
 * CONSTAT DU 22/09, mesure a l'URL nue et publique :
 *
 *     GET /wp-json/oembed/1.0/embed?url=https://elagage-vertou.fr/
 *     "author_name":"Petit jardinier"
 *     "author_url":"https://elagage-vertou.fr/author/advise-alliance-groupgmail-com/"
 *
 * Le slug « advise-alliance-groupgmail-com » est le user_nicename passe au
 * tamis de sanitize_title : il se relit a l'endroit en
 * « advise-alliance-group@gmail.com », c'est-a-dire l'identifiant de
 * connexion, donne en clair. Et les deux liens de decouverte oEmbed sont
 * annonces dans le <head> de CHAQUE page : personne n'a besoin de chercher,
 * le site montre lui-meme ou regarder.
 *
 * Le point 2 ferme /wp/v2/users, le point 3 ferme ?author= et /author/ —
 * mais oembed/1.0 construit author_url tout seul, dans le coeur de
 * WordPress, sans passer par ces filtres. Le durcissement etait aux deux
 * tiers fait ; voici le tiers manquant.
 *
 * On retire les deux champs auteur de la reponse oEmbed, et les liens de
 * decouverte du <head> : aucun site tiers ne re-embarque ces pages, ils ne
 * servent donc a rien ici, et c'est une surface de moins. */
add_filter( 'oembed_response_data', function ( $data ) {
	unset( $data['author_url'], $data['author_name'] );
	return $data;
}, 20 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

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
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), interest-cohort=()' );
	/*
	 * CSP COMPLETE — remplacee le 22/09. Avant, ce n'etait qu'un
	 * `upgrade-insecure-requests` : ca force le HTTPS, ca ne restreint AUCUNE
	 * source. Une vraie liste blanche, dans la meme logique que celle
	 * appliquee aux resource_hints.
	 *
	 * POURQUOI `'unsafe-inline'` ET NON DES NONCES. Un nonce CSP doit etre
	 * unique par reponse. Les pages sont servies depuis LiteSpeed avec
	 * s-maxage=86400 : un nonce serait mis en cache et resservi identique a
	 * tous les visiteurs 24 h, donc equivalent a `'unsafe-inline'` mais avec
	 * l'illusion d'une protection. Le cache et les valeurs a usage unique ne
	 * cohabitent pas — c'est le meme piege que le nonce du formulaire de
	 * contact. Les empreintes sha256 survivraient au cache, mais il y en a
	 * onze, dont certaines varient selon le contenu (ld+json,
	 * speculationrules) : ingerable sur 24 pages avec une sync automatique.
	 *
	 * CE QUE CETTE CSP GAGNE, meme avec 'unsafe-inline' : un <script src>
	 * externe injecte (extension compromise, mise a jour veroleee) ne se
	 * charge pas ; base-uri 'self' neutralise le detournement par <base> ;
	 * form-action 'self' empeche de reecrire le formulaire de contact pour
	 * expedier les coordonnees des prospects ailleurs ; object-src 'none'
	 * ferme Flash/applets. Et surtout, elle devient le garde-fou AUTOMATIQUE
	 * de l'engagement « aucune ressource tierce » des mentions legales : ce
	 * que lae-zero-tiers.php retire apres coup, la CSP l'empeche par principe,
	 * y compris ce qui n'existe pas encore.
	 *
	 * A SURVEILLER (dit a Fabrice) : si Site Kit est un jour relie a Analytics
	 * ou Tag Manager, googletagmanager.com sera bloque et les stats cesseront
	 * silencieusement de remonter. Si hostinger-reach est reactive, son CDN
	 * sera bloque. Ce sont des consequences voulues, pas des regressions.
	 */
	$csp = implode( '; ', array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline'",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data:",
		"font-src 'self'",
		"connect-src 'self'",
		"frame-src 'self'",
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self'",
		"frame-ancestors 'self'",
		'upgrade-insecure-requests',
	) );
	/*
	 * ATTENTION — CONSTAT DU 22/09, MESURE EN LIGNE : cet en-tete est
	 * ACTUELLEMENT SANS EFFET. Le CDN d'Hostinger (`server: hcdn`) injecte
	 * son propre `Content-Security-Policy: upgrade-insecure-requests` sur
	 * CHAQUE reponse — verifie jusque sur un fichier .css statique, ou PHP ne
	 * s'execute pas — et il REMPLACE celui-ci a la sortie. Le navigateur ne
	 * voit donc qu'une seule ligne CSP, celle du CDN, pas celle-ci. Meme
	 * classe de probleme que le TTL du cache en septembre : une couche
	 * serveur prime sur PHP.
	 *
	 * On garde quand meme cette ligne : elle est correcte, sans danger, et
	 * s'appliquera le jour ou le CDN cesse de forcer la sienne (reglage
	 * hPanel « Force HTTPS » / en-tetes de securite) ou si le site change
	 * d'hebergement. Le levier est cote Hostinger, pas dans le code —
	 * signale a Fabrice. Ne pas re-tenter par PHP : ca ne peut pas gagner.
	 */
	header( 'Content-Security-Policy: ' . $csp );
	if ( function_exists( 'is_ssl' ) && is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}
	header_remove( 'X-Pingback' );
	header_remove( 'X-Powered-By' );
}, 99 );

/* ---- 5. Masque la version de WordPress ---- */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/* ---- 5 bis. Et la signature des EXTENSIONS, pas seulement celle du cœur ----
 *
 * CONSTAT DU 22/09, en sondant le site servi : la page annonçait
 *
 *     <meta name="generator" content="Site Kit by Google 1.187.0" />
 *
 * Le point 5 ci-dessus ne retire que le générateur de WordPress. Une
 * extension qui pose le sien passe au travers — et celui-ci nomme
 * l'extension ET son numéro de version exact, c'est-à-dire précisément ce
 * qu'un attaquant cherche pour choisir une faille connue à tenter.
 *
 * On ne devine PAS le nom du rappel interne de l'extension : il changerait
 * à la prochaine mise à jour et le correctif tomberait en silence. On
 * filtre la SORTIE de wp_head, ce qui vaut pour toutes les extensions,
 * celles installées demain comprises. Le filtre ne retire que les balises
 * `generator` : tout le reste du <head> est réémis tel quel.
 */
add_action( 'wp_head', static function () {
	ob_start();
}, 0 );

add_action( 'wp_head', static function () {
	$tete = ob_get_clean();
	if ( ! is_string( $tete ) ) {
		return;   // un autre tampon est passé par là : on ne touche à rien
	}
	echo preg_replace( '#<meta[^>]+name=["\']generator["\'][^>]*>\s*#i', '', $tete ); // phpcs:ignore WordPress.Security.EscapeOutput
}, PHP_INT_MAX );

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

	$rules = array(
		'Options -Indexes',
		'<FilesMatch "(?i)^(readme\.html|readme\.txt|license\.txt|licence\.txt|wp-config\.php|wp-config-sample\.php|\.env|\.git.*)$">',
		'<IfModule mod_authz_core.c>',
		'Require all denied',
		'</IfModule>',
		'<IfModule !mod_authz_core.c>',
		'Order allow,deny',
		'Deny from all',
		'</IfModule>',
		'</FilesMatch>',
	);

	/* Verrou VERSIONNE, et non binaire. Ajouter wp-config-sample.php au
	   FilesMatch le 22/09 ne changeait rien sur un site ou le bloc etait
	   deja pose : un verrou « 1 » l'aurait bloque a jamais. On stocke a la
	   place une empreinte du jeu de regles ; si les regles changent, la
	   valeur ne correspond plus et le bloc est reecrit. insert_with_markers
	   remplace le bloc balise « LAE Hardening », l'operation est idempotente. */
	$empreinte = substr( md5( implode( '|', $rules ) ), 0, 12 );
	if ( $empreinte === (string) get_option( 'lae_htaccess_hard', '' ) ) return;

	if ( ! function_exists( 'get_home_path' ) )       require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( ! function_exists( 'insert_with_markers' ) ) require_once ABSPATH . 'wp-admin/includes/misc.php';
	$home = function_exists( 'get_home_path' ) ? get_home_path() : ABSPATH;
	$file = rtrim( $home, '/\\' ) . '/.htaccess';
	if ( ! file_exists( $file ) || ! is_writable( $file ) ) {
		update_option( 'lae_htaccess_hard_note', 'manuel', false ); // à coller à la main
		return;
	}

	if ( function_exists( 'insert_with_markers' ) && insert_with_markers( $file, 'LAE Hardening', $rules ) ) {
		update_option( 'lae_htaccess_hard', $empreinte, false );
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
