<?php
/**
 * Données structurées par page : services, fil d'ariane, articles, site.
 *
 * ═══════════════════════════════════════════════════════════════════
 * CE QUI MANQUAIT. Le site ne déclarait qu'UN seul bloc — la fiche
 * d'entreprise — répété à l'identique sur les neuf pages. Aucun
 * `Service`, aucun `BreadcrumbList`, aucun `BlogPosting`, aucun
 * `WebSite`. Or le contenu existe réellement : six fiches de prestation
 * à URL propre, trois articles rédigés avec image et date, deux
 * réalisations, une hiérarchie d'URL vraie.
 *
 * LA RÈGLE QUI DÉCIDE DE CE QUI EST ÉCRIT ICI : on ne balise que ce que
 * la page AFFICHE réellement. Un balisage qui annonce ce qui n'est pas
 * sur la page est une faute, pas une optimisation — et Google le
 * sanctionne. Chaque `description` ci-dessous est l'extrait réellement
 * publié, pas un texte réécrit pour les moteurs.
 *
 * CE QU'ON NE BALISE PAS, ET POURQUOI :
 * — `aggregateRating` / `review` : INTERDIT. Le site affiche bien un
 *   avis Google réel, cité et vérifiable — l'afficher est irréprochable,
 *   le baliser ne l'est pas. Google exclut depuis 2019 les avis
 *   « auto-servis » (hébergés par l'entreprise sur son propre site) et
 *   sanctionne le contournement par action manuelle.
 * — `priceRange` : les seuls montants du site sont des fourchettes
 *   RELEVÉES CHEZ LES CONCURRENTS, présentées comme telles. Les
 *   recopier reviendrait à déclarer comme prix de L.A Environnement des
 *   prix qui ne sont pas les siens. L'entreprise travaille au forfait
 *   après visite : aucune fourchette propre n'existe.
 * — `FAQPage` : aucune section question/réponse sur aucune page. Et
 *   depuis 2023 Google ne montre plus ce résultat enrichi hors sites
 *   gouvernementaux et de santé.
 * — `geo` : des coordonnées ne s'estiment pas. À géocoder depuis la Base
 *   Adresse Nationale puis à contrôler, le jour où on le fera.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** L'identifiant de l'entreprise, sur lequel tout le reste s'accroche. */
function lae_schema_id_entreprise() {
	return home_url( '/' ) . '#entreprise';
}

/** Écrit un bloc JSON-LD, ou rien si le tableau est vide. */
/**
 * Texte d'extrait pret a partir dans du JSON-LD.
 *
 * `wp_strip_all_tags()` retire les balises mais LAISSE les entites HTML.
 * Or le JSON-LD n'est pas decode comme du HTML : Google lisait litteralement
 * les huit caracteres `&rsquo;` la ou WordPress avait pose une apostrophe
 * typographique. Constate le 14/09 sur /urgences/ (« une branche fendue
 * au-dessus d&rsquo;une toiture »). Les autres pages n'etaient epargnees que
 * parce que leur extrait n'avait pas d'apostrophe courbe — un coup de chance,
 * pas une protection. On decode donc, comme le fait deja lae_partage_texte()
 * pour la meta description.
 */
function lae_schema_texte( $html ) {
	if ( function_exists( 'lae_partage_texte' ) ) {
		return lae_partage_texte( $html );
	}
	$txt = wp_strip_all_tags( (string) $html );
	$txt = html_entity_decode( $txt, ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', $txt ) );
}

function lae_schema_ecrire( $donnees ) {
	if ( ! $donnees ) {
		return;
	}
	$donnees = array_merge( array( '@context' => 'https://schema.org' ), $donnees );
	echo "\n" . '<script type="application/ld+json">'
		. wp_json_encode( $donnees, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n";
}

add_action( 'wp_head', function () {

	if ( is_404() || is_feed() ) {
		return;
	}
	$entreprise = array( '@id' => lae_schema_id_entreprise() );

	/* ── Le site lui-même, sur l'accueil ───────────────────────────
	   SANS `SearchAction`, délibérément : aucune page du site n'affiche
	   de champ de recherche, et déclarer une recherche que le visiteur
	   ne peut pas atteindre serait du balisage sans substance. Google a
	   d'ailleurs retiré ce résultat enrichi fin 2024. */
	if ( is_front_page() ) {
		lae_schema_ecrire( array(
			'@type'      => 'WebSite',
			'@id'        => home_url( '/' ) . '#site',
			'url'        => home_url( '/' ),
			'name'       => lae_nom_site(),
			'inLanguage' => 'fr-FR',
			'publisher'  => $entreprise,
		) );
	}

	/* ── Une prestation = un Service ───────────────────────────────
	   Sans `offers` ni `price` : le prix s'établit après visite, et un
	   `Offer` sans montant est licite — il rattache le service à son
	   prestataire, rien de plus. */
	if ( is_singular( 'lae_prestation' ) ) {
		$p = get_post();
		lae_schema_ecrire( array(
			'@type'       => 'Service',
			'@id'         => get_permalink( $p ) . '#service',
			'name'        => lae_schema_texte( get_the_title( $p ) ),
			'description' => lae_schema_texte( get_the_excerpt( $p ) ),
			'url'         => get_permalink( $p ),
			'provider'    => $entreprise,
			'areaServed'  => array(
				'@type' => 'AdministrativeArea',
				'name'  => lae_reglage( 'zone_departement', 'Loire-Atlantique' ),
			),
		) );
	}

	/* ── Le service d'urgence ──────────────────────────────────────
	   C'est ICI que `availableChannel` est valide : son domaine est
	   `Service`, pas `Organization`. Sur la fiche d'entreprise, il était
	   ignoré en silence. */
	$page = get_post();
	if ( is_page() && $page && 'urgences' === $page->post_name ) {
		$tel = lae_reglage( 'telephone' );
		$urg = array(
			'@type'       => 'Service',
			'@id'         => get_permalink( $page ) . '#service',
			'name'        => 'Intervention d\'urgence sur arbre',
			'description' => lae_schema_texte( get_the_excerpt( $page ) ),
			'serviceType' => 'Intervention d\'urgence',
			'url'         => get_permalink( $page ),
			'provider'    => $entreprise,
			'hoursAvailable' => array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
				'opens'     => '00:00',
				'closes'    => '23:59',
			),
		);
		if ( $tel ) {
			$urg['availableChannel'] = array(
				'@type'             => 'ServiceChannel',
				'name'              => 'Ligne d\'urgence',
				'availableLanguage' => 'fr',
				'servicePhone'      => array(
					'@type'             => 'ContactPoint',
					'telephone'         => lae_tel_e164( $tel ),
					'contactType'       => 'emergency',
					'areaServed'        => 'FR',
					'availableLanguage' => 'fr',
				),
			);
		}
		lae_schema_ecrire( $urg );
	}

	/* ── Un article ────────────────────────────────────────────────
	   `datePublished` et `image` sont LUS sur l'article, jamais écrits
	   en dur : l'heure exacte et l'URL pleine taille ne se devinent pas.
	   `author` = l'organisation : les mentions légales établissent une
	   responsabilité de publication, ce qui n'est pas la même chose
	   qu'un auteur nommé. Le jour où Anthony signera ses articles, ce
	   sera une `Person`. */
	if ( is_singular( 'post' ) ) {
		$a   = get_post();
		$art = array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink( $a ) . '#article',
			'headline'         => lae_schema_texte( get_the_title( $a ) ),
			'url'              => get_permalink( $a ),
			'datePublished'    => get_the_date( DATE_W3C, $a ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $a ),
			'inLanguage'       => 'fr-FR',
			'author'           => $entreprise,
			'publisher'        => $entreprise,
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink( $a ) ),
		);
		$extrait = lae_schema_texte( get_the_excerpt( $a ) );
		if ( $extrait ) {
			$art['description'] = $extrait;
		}
		$img = get_the_post_thumbnail_url( $a, 'full' );
		if ( $img ) {
			$art['image'] = $img;
		}
		lae_schema_ecrire( $art );
	}

	/* ── Le fil d'ariane ───────────────────────────────────────────
	   Il reflète le CHEMIN D'URL RÉEL et rien d'autre. Un fil inventé
	   pour y placer des mots-clés est une faute. Le dernier élément n'a
	   volontairement pas d'`item` : c'est la page courante. */
	$fil = array();
	if ( is_singular( 'lae_prestation' ) || is_singular( 'lae_realisation' ) ) {
		$type    = get_post_type();
		$archive = get_post_type_archive_link( $type );
		$objet   = get_post_type_object( $type );
		if ( $archive && $objet ) {
			$fil[] = array( 'name' => $objet->labels->name, 'item' => $archive );
		}
	} elseif ( is_singular( 'post' ) ) {
		$blog = get_option( 'page_for_posts' );
		if ( $blog ) {
			$fil[] = array( 'name' => get_the_title( (int) $blog ), 'item' => get_permalink( (int) $blog ) );
		}
	}
	if ( $fil && is_singular() ) {
		$elements = array( array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Accueil',
			'item'     => home_url( '/' ),
		) );
		$rang = 2;
		foreach ( $fil as $etape ) {
			$elements[] = array(
				'@type'    => 'ListItem',
				'position' => $rang++,
				'name'     => $etape['name'],
				'item'     => $etape['item'],
			);
		}
		$elements[] = array(
			'@type'    => 'ListItem',
			'position' => $rang,
			'name'     => lae_schema_texte( get_the_title() ),
		);
		lae_schema_ecrire( array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $elements,
		) );
	}
}, 21 );
