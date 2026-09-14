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

/**
 * Description courte de la page courante.
 *
 * En accueil, l'accroche du site (« Élagage · Abattage · Création de jardin »)
 * ne fait que 40 caractères : Google la complète alors avec un morceau de page
 * pris au hasard. On préfère le chapô du hero, écrit par le client et long
 * d'environ 170 caractères — la bonne longueur pour un extrait de résultat.
 */
function lae_partage_description() {

	/* D'ABORD LA DESCRIPTION ÉCRITE, s'il y en a une pour cette page.
	   Audit du 14/09 : cinq descriptions sur neuf étaient des troncatures
	   automatiques à 200 caractères, coupées en plein mot — « …d'une
	   mauvaise inter… », « …par un f… », « …Le tarif es… » — et deux
	   paires de pages partageaient la même au caractère près. Une
	   description rédigée bat toujours un extrait découpé à la hache.
	   Voir inc/lae-seo-pages.php. */
	if ( function_exists( 'lae_seo_page' ) ) {
		$ecrite = lae_seo_page();
		if ( ! empty( $ecrite['desc'] ) ) {
			return $ecrite['desc'];
		}
	}

	/* L'accueil, et LUI SEUL. `is_home()` était traité avec
	   `is_front_page()` : avec une page d'accueil statique, la page des
	   articles est `is_home()` — /conseils/ héritait donc mot pour mot de
	   la description de l'accueil, et de son URL de partage. Deux pages
	   indexées avec la même description, et un partage de /conseils/ qui
	   renvoyait vers l'accueil. */
	if ( is_front_page() && function_exists( 'lae_reglage' ) ) {
		$chapo = lae_reglage( 'hero_chapo' );
		if ( is_string( $chapo ) && '' !== trim( $chapo ) ) {
			$chapo = lae_partage_texte( $chapo );
			if ( '' !== $chapo ) return wp_html_excerpt( $chapo, 155, '…' );
		}
	}
	if ( is_singular() ) {
		$p = get_post();
		if ( $p ) {
			$txt = $p->post_excerpt ? $p->post_excerpt : (string) $p->post_content;
			$txt = lae_partage_texte( $txt );
			if ( '' !== $txt ) return wp_html_excerpt( $txt, 155, '…' );
		}
	}
	$d = get_bloginfo( 'description', 'display' );
	return $d ? $d : '';
}

/**
 * Réduit un contenu WordPress à du texte lisible dans un résultat Google.
 *
 * DEUX DÉFAUTS CORRIGÉS ICI, tous deux visibles en ligne le 14/09.
 *
 * 1. LE SHORTCODE PARTAIT BRUT. La page contact servait
 *    `content="… c'est là que le devis se fait. [lae_contact]"` — dans la
 *    balise description ET dans l'Open Graph. `wp_strip_all_tags()`
 *    retire les balises, pas les crochets.
 *
 * 2. LES MOTS SE SOUDAIENT. `wp_strip_all_tags()` supprime `</p>` et
 *    `<br>` sans rien mettre à la place : les mentions légales servaient
 *    « …(EI)Exerçant sous le nom commercial L.A Environnement554 route de
 *    Clisson44120 VertouSIRET… ». On remplace donc les balises de bloc
 *    par une espace AVANT de dépouiller.
 *
 * @param string $html Contenu brut.
 * @return string Texte propre, espaces normalisés.
 */
function lae_partage_texte( $html ) {
	$txt = strip_shortcodes( (string) $html );
	$txt = preg_replace( '#<(?:/p|/h[1-6]|/li|/div|br\s*/?|/tr)\s*>#i', ' ', $txt );
	$txt = wp_strip_all_tags( $txt );
	$txt = html_entity_decode( $txt, ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', $txt ) );
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
	/* L'URL DE PARTAGE, SANS PARAMÈTRE. `home_url( add_query_arg( array() ) )`
	   réinjectait la chaîne de requête entière : un lien partagé depuis
	   Facebook (?fbclid=…) ou une campagne (?utm_source=…) produisait autant
	   d'objets sociaux distincts, et autant de signaux d'URL contradictoires.
	   Constaté en ligne le 14/09 sur les archives, qui n'ont en plus aucune
	   balise canonique pour rattraper le coup. */
	$url = home_url( '/' );
	if ( is_singular() ) {
		$permalien = get_permalink();
		if ( $permalien ) $url = $permalien;
	} elseif ( is_home() && ! is_front_page() ) {
		$page = get_option( 'page_for_posts' );
		$lien = $page ? get_permalink( (int) $page ) : '';
		if ( $lien ) $url = $lien;
	} elseif ( is_post_type_archive() ) {
		$type = get_query_var( 'post_type' );
		$lien = get_post_type_archive_link( is_array( $type ) ? reset( $type ) : $type );
		if ( $lien ) $url = $lien;
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$terme = get_queried_object();
		if ( $terme && ! is_wp_error( $terme ) && isset( $terme->term_id ) ) {
			$lien = get_term_link( $terme );
			if ( ! is_wp_error( $lien ) ) $url = $lien;
		}
	}
	$img = lae_partage_image();

	echo "\n<!-- Partage (LAE) -->\n";
	/* `is_singular()` déclarait « article » les mentions légales, les
	   tarifs, les urgences, le contact et la page à propos. Seul un
	   article de blog en est un. */
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
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
