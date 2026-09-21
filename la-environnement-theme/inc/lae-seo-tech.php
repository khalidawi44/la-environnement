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
	// Ne JAMAIS reposer `index` par-dessus un `noindex` deja pose. Ce filtre
	// passe en priorite 20, apres celui des taxonomies fermees (priorite 11) :
	// sans ce garde, /famille/arbre/ sortait « noindex, follow, index » dans la
	// meme balise. Google retient la directive la plus restrictive, donc le
	// noindex gagnait quand meme — mais une balise qui se contredit est
	// illisible pour qui la relit. Constate en ligne le 14/09.
	if ( empty( $robots['noindex'] ) ) {
		$robots['index'] = true;
	}
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

/* ═══════════════════════════════════════════════════════════════════
   LE SITEMAP PUBLIAIT L'ADRESSE E-MAIL DE L'ÉDITEUR

   Constaté en ligne le 14/09. `wp-sitemap-users-1.xml` contenait une
   seule URL :

       https://elagage-vertou.fr/author/advise-alliance-groupgmail-com/

   Le slug est dérivé mot pour mot de l'adresse du compte éditeur. Une
   adresse personnelle publiée en clair dans un XML public, offerte aux
   moissonneurs de spam — et par un fichier dont le rôle est justement
   d'être lu par des robots.

   Deuxième défaut, dans le même fichier : cette URL répond 301 vers
   l'accueil (durcissement anti-énumération d'auteur, lae-hardening.php).
   Un sitemap ne doit contenir que des URL canoniques répondant 200 ;
   Search Console remonte « URL soumise avec redirection » à chaque
   passage. Le durcissement était donc contourné par le sitemap, qui
   publiait l'URL que le durcissement s'employait à fermer.

   Ce site n'a qu'un auteur et n'affiche aucune page d'auteur : le
   fournisseur entier est retiré.

   ATTENTION, CE CORRECTIF NE SUFFIT PAS SEUL : l'adresse reste
   dérivable du `user_nicename` en base. Il faut aussi renommer ce
   champ côté WordPress — signalé à Fabrice, ça ne se fait pas depuis
   le thème.
   ═══════════════════════════════════════════════════════════════════ */
add_filter( 'wp_sitemaps_add_provider', function ( $fournisseur, $nom ) {
	return ( 'users' === $nom ) ? false : $fournisseur;
}, 10, 2 );

/* La catégorie « Uncategorized » duplique /conseils/ à l'identique —
   mêmes trois articles, aucun lien entrant. Elle n'a rien à indexer de
   propre, et elle concurrence la page qu'on veut positionner. */
add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
	// Regle simple : ce qu'on ferme a l'indexation ne va pas au sitemap.
	// Soumettre une URL en `noindex` fait remonter « URL soumise avec balise
	// noindex » dans Search Console — le motif d'erreur qu'on vient justement
	// de supprimer cote `users`. Les trois familles et les deux types de
	// chantier sont dans ce cas.
	unset( $taxonomies['category'], $taxonomies['lae_famille'], $taxonomies['lae_type_chantier'] );
	return $taxonomies;
} );

/* ═══════════════════════════════════════════════════════════════════
   LES ARCHIVES /prestations/ ET /realisations/ AU SITEMAP — RÉSOLU

   HISTORIQUE, PARCE QU'IL EXPLIQUE LE CHOIX. Deux tentatives (v1.28.0
   puis v1.28.1) sont passées par le filtre `wp_sitemaps_posts_url_list`
   et n'ont produit AUCUN effet en ligne. Avaient été écartés, mesures à
   l'appui : le déploiement (deux filtres voisins du même commit
   agissaient bien), le cache (vérifié sur `x-litespeed-cache: miss` ET
   `x-hcdn-cache-status: MISS`) et le nombre d'arguments. Le code avait
   été retiré plutôt que laissé en place, pour ne pas laisser un filtre
   inerte qui ressemble à un filtre actif.

   POURQUOI ÇA NE POUVAIT PAS MARCHER. `wp_sitemaps_posts_url_list`
   filtre la liste d'un fournisseur EXISTANT. Or le fournisseur `posts`
   n'énumère que des publications ; l'archive d'un type de contenu n'est
   pas une publication, elle n'a pas d'identifiant, et rien dans ce
   fournisseur ne la représente. On greffait une URL sur un objet qui ne
   l'attendait pas.

   LA VOIE CORRECTE est l'autre API, celle des FOURNISSEURS :
   `wp_register_sitemap_provider()` déclare une nouvelle source d'URL,
   qui obtient son propre fichier `wp-sitemap-archives-1.xml` référencé
   dans l'index. C'est exactement ce à quoi elle sert.

   Classe anonyme, et pour une raison précise : `WP_Sitemaps_Provider`
   n'existe qu'une fois le sous-système des sitemaps chargé. Une classe
   déclarée au niveau du fichier serait évaluée trop tôt et provoquerait
   une erreur fatale ; déclarée à l'intérieur du rappel, elle n'est
   construite qu'au moment où sa classe parente est disponible.
   ═══════════════════════════════════════════════════════════════════ */
add_action( 'init', function () {

	if ( ! function_exists( 'wp_register_sitemap_provider' ) || ! class_exists( 'WP_Sitemaps_Provider' ) ) {
		return;   // sitemaps natifs indisponibles : on ne casse rien
	}

	$fournisseur = new class extends WP_Sitemaps_Provider {

		public function __construct() {
			$this->name        = 'archives';
			$this->object_type = 'archive';
		}

		/**
		 * Les deux archives, et seulement si elles existent vraiment.
		 *
		 * `get_post_type_archive_link()` rend `false` quand le type n'est
		 * pas enregistré ou n'a pas d'archive : on n'inscrit jamais une
		 * URL qu'on n'a pas vérifiée.
		 */
		public function get_url_list( $page_num, $object_subtype = '' ) {
			$urls = array();
			foreach ( array( 'lae_prestation', 'lae_realisation' ) as $type ) {
				$lien = get_post_type_archive_link( $type );
				if ( $lien ) {
					$urls[] = array( 'loc' => $lien );
				}
			}
			return apply_filters( 'lae_sitemap_archives', $urls );
		}

		public function get_max_num_pages( $object_subtype = '' ) {
			return 1;
		}
	};

	wp_register_sitemap_provider( 'archives', $fournisseur );
}, 20 );

/* Les archives de familles (/famille/arbre/…) et la catégorie par
   défaut ne reçoivent AUCUN lien interne — vérifié sur les dix-huit
   pages du site — et leur contenu est celui de /prestations/ et de
   /conseils/. Laissées indexables, elles consomment du budget de crawl
   et concurrencent les pages qu'on veut positionner. On les ferme à
   l'indexation tout en laissant suivre les liens. */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_tax( 'lae_famille' ) || is_tax( 'lae_type_chantier' ) || is_category() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}, 11 );

/* ═══════════════════════════════════════════════════════════════════
   VÉRIFICATION GOOGLE SEARCH CONSOLE

   Pour prouver à Google qu'on est propriétaire du site, la méthode la
   plus simple est une balise `<meta>` dans le <head>. Le champ se
   remplit dans le personnalisateur (« Référencement »), sans toucher au
   code ni téléverser de fichier à la racine — un fichier serait écrasé
   à la prochaine synchro du thème.

   On accepte la balise ENTIÈRE collée depuis Google autant que le seul
   jeton : quelqu'un qui copie l'écran de Google colle
   `<meta name="google-site-verification" content="abc..." />`, et ça
   doit marcher aussi. Champ vide = aucune balise émise.
   ═══════════════════════════════════════════════════════════════════ */
add_action( 'wp_head', function () {
	$brut = function_exists( 'lae_reglage' ) ? (string) lae_reglage( 'google_verification' ) : '';
	if ( '' === trim( $brut ) ) {
		return;
	}
	/* Si c'est une balise complète, on n'en garde que le jeton. */
	if ( preg_match( '/content=["\']([^"\']+)["\']/', $brut, $m ) ) {
		$brut = $m[1];
	}
	$jeton = preg_replace( '/[^A-Za-z0-9_\-]/', '', $brut );
	if ( '' === $jeton ) {
		return;
	}
	printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $jeton ) );
}, 2 );

/* ---- 5. Nettoyage du <head> ----
 * Balises héritées, sans usage aujourd'hui, qui alourdissent chaque page. */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
