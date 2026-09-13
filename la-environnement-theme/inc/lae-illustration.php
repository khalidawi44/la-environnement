<?php
/**
 * Illustration des pages intérieures.
 *
 * Demande de Fabrice (13/09) : « illustre les autres pages, une bande verte
 * hachurée sous le titre, pas d'icônes, de vraies images ».
 *
 * PAS D'ICÔNES, DE VRAIES PHOTOS. Une icône est un pictogramme générique :
 * elle dit « jardin » sans rien montrer. Une photo de chantier dit ce que
 * l'entreprise fait réellement, et c'est la seule chose qu'un visiteur ne
 * peut pas trouver ailleurs. Toutes les images utilisées ici viennent du
 * client, prises sur ses chantiers — aucune banque d'images.
 *
 * L'image mise en avant de la page, si le client en pose une dans
 * l'administration, PRIME toujours sur la table ci-dessous : le thème
 * propose, il n'impose pas.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Image d'en-tête pour le contexte courant.
 *
 * @return array{url:string, alt:string}|null
 */
function lae_illustration() {

	/* 1. L'image mise en avant gagne toujours. */
	if ( is_singular() && has_post_thumbnail() ) {
		$id  = get_post_thumbnail_id();
		$src = wp_get_attachment_image_src( $id, 'lae-large' );
		if ( $src ) {
			$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
			return array( 'url' => $src[0], 'alt' => $alt );
		}
	}

	/* 2. Sinon, la table livrée. Clés : slug de page, ou contexte d'archive. */
	$table = apply_filters( 'lae_illustrations', array(
		/* Photo de nuit — provenance confirmée par Fabrice le 14/09 : prise sur
		   un chantier du client, l'homme à la tronçonneuse est un de ses ouvriers
		   et non Anthony. C'est donc bien une image maison, cohérente avec ce que
		   nos mentions légales affirment.

		   Le texte alternatif ne nomme personne, et c'est voulu : il décrit la
		   scène. Nommer quelqu'un sur une photo de chantier, c'est publier une
		   donnée personnelle sans nécessité.

		   Reste à sécuriser côté client : l'ouvrier est reconnaissable, son
		   accord écrit vaut mieux qu'un accord tacite — voir CHANTIERS-PHOTOS.md. */
		'urgences'         => array( 'chantiers/intervention-nuit.webp',           'Élagueur à la tronçonneuse de nuit, arbre tombé contre une maison, camion-nacelle éclairé en arrière-plan' ),
		'tarifs'           => array( 'chantiers/haie-taillee-broyat.webp',         'Haie taillée, broyat laissé en paillage au pied' ),
		'a-propos'         => array( 'chantiers/elagage-grimpe-cordes.webp',       'Élagage en grimpe, travail à la corde' ),
		'contact'          => array( 'jardin-piscine.webp',                        'Jardin entretenu au bord d\'une piscine' ),
		'archive_prestation' => array( 'elagage-grimpe.webp',                      'Élagueur en grimpe dans un houppier' ),
		'archive_realisation' => array( 'chantiers/reduction-couronne-grimpeur.webp', 'Réduction de couronne, grimpeur en place' ),
		'blog'             => array( 'pelouse-haie.webp',                          'Pelouse tondue le long d\'une haie taillée' ),
	) );

	$cle = '';

	if ( is_page() ) {
		$post = get_post();
		$cle  = $post ? $post->post_name : '';
	} elseif ( is_post_type_archive( 'lae_prestation' ) || is_singular( 'lae_prestation' ) ) {
		$cle = 'archive_prestation';
	} elseif ( is_post_type_archive( 'lae_realisation' ) || is_tax( 'lae_type_chantier' ) ) {
		$cle = 'archive_realisation';
	} elseif ( is_home() || is_category() || is_singular( 'post' ) || is_archive() ) {
		$cle = 'blog';
	}

	if ( ! $cle || ! isset( $table[ $cle ] ) ) {
		return null;
	}

	$fichier = get_template_directory() . '/assets/images/' . $table[ $cle ][0];
	if ( ! file_exists( $fichier ) ) {
		return null;   // une image annoncée mais absente ne doit pas casser l'en-tête
	}

	return array(
		'url' => get_template_directory_uri() . '/assets/images/' . $table[ $cle ][0],
		'alt' => $table[ $cle ][1],
	);
}

/**
 * Surtitre en pilule au-dessus du titre de page.
 *
 * Inspiration validée par Fabrice (13/09) : la pilule dorée de Gwen Services.
 * Ici en ton bois (--lae-bois), pas en or : les deux sites appartiennent à la
 * même famille d'écriture visuelle, ils ne doivent pas se ressembler.
 *
 * Elle dit en deux mots ce qu'est la page, avant que le titre ne le dise en
 * une phrase. Si elle n'apporte rien de plus que le titre, on ne l'affiche
 * pas : un surtitre qui répète le titre est du bruit.
 *
 * @return string Vide si aucun surtitre n'est prévu pour ce contexte.
 */
function lae_entete_surtitre() {

	$table = apply_filters( 'lae_entete_surtitres', array(
		'urgences'            => 'Intervention d\'urgence',
		'tarifs'              => 'Ce que ça coûte',
		'conseils'            => 'Conseils d\'élagueur',
		'a-propos'            => 'Notre façon de travailler',
		'contact'             => 'Parlons de votre arbre',
		'archive_prestation'  => 'Ce que nous faisons',
		'archive_realisation' => 'Chantiers réalisés',
		'blog'                => 'Conseils d\'élagueur',
	) );

	$cle = '';
	if ( is_page() ) {
		$post = get_post();
		$cle  = $post ? $post->post_name : '';
	} elseif ( is_post_type_archive( 'lae_prestation' ) || is_singular( 'lae_prestation' ) ) {
		$cle = 'archive_prestation';
	} elseif ( is_post_type_archive( 'lae_realisation' ) || is_tax( 'lae_type_chantier' ) || is_singular( 'lae_realisation' ) ) {
		$cle = 'archive_realisation';
	} elseif ( is_home() || is_singular( 'post' ) || is_category() ) {
		$cle = 'blog';
	}

	return ( $cle && isset( $table[ $cle ] ) ) ? $table[ $cle ] : '';
}

/**
 * La vague qui ferme un en-tête illustré.
 *
 * Une coupure droite entre une photo et le corps de page fait « bloc collé ».
 * La courbe fait passer de l'un à l'autre. Le tracé vient de la même famille
 * que celui de Gwen Services, redessiné plus calme : ce site parle d'arbres,
 * pas de soin à domicile, et une ondulation trop marquée ferait décoratif.
 *
 * aria-hidden et focusable="false" : c'est une bordure, pas une image.
 */
function lae_vague() {
	return '<div class="lae-vague" aria-hidden="true">'
		. '<svg viewBox="0 0 1440 70" preserveAspectRatio="none" focusable="false">'
		. '<path d="M0,38 C260,68 520,10 760,30 C990,49 1220,20 1440,44 L1440,70 L0,70 Z"/>'
		. '</svg></div>';
}

/**
 * Le tampon circulaire à texte tournant.
 *
 * Inspiration validée par Fabrice (13/09). Deux règles absolues, parce qu'un
 * sceau a l'air d'un label officiel et qu'on le croit sur parole :
 *
 * 1. RIEN QUE DES FAITS DU CLIENT. Le modèle de Gwen Services affiche
 *    « 50 % crédit d'impôt » — c'est vrai chez elle, et c'est faux ici :
 *    élagage, abattage, démontage et dessouchage sont exclus du dispositif.
 *    Recopier le sceau aurait recopié une contrevérité.
 * 2. AUCUN CHIFFRE QUI ENGAGE. Pas de prix, pas de délai chiffré, pas de
 *    nombre de chantiers : rien qu'on ne puisse tenir chaque jour.
 *
 * Reste ce qui est vérifié et confirmé par le client le 13/09 : joignable
 * 24 h/24 et 7 j/7, week-ends et jours fériés compris.
 *
 * @return string Vide si la page n'a pas de tampon.
 */
function lae_tampon() {

	if ( ! is_page( 'urgences' ) ) {
		return '';
	}

	$tour   = 'Jour et nuit · Week-ends et jours fériés · ';
	$centre = '24/7';
	$pied   = 'Urgences';

	// Le texte fait deux fois le tour : une seule occurrence laisserait une
	// moitié de cercle vide selon la longueur de la phrase.
	$piste = 'M74,74 m-56,0 a56,56 0 1,1 112,0 a56,56 0 1,1 -112,0';

	return '<div class="lae-tampon" aria-hidden="true">'
		. '<svg class="lae-tampon__tour" viewBox="0 0 148 148" focusable="false">'
		. '<defs><path id="lae-tampon-piste" d="' . $piste . '"/></defs>'
		. '<circle class="lae-tampon__cercle" cx="74" cy="74" r="70"/>'
		. '<circle class="lae-tampon__cercle" cx="74" cy="74" r="46"/>'
		. '<text><textPath href="#lae-tampon-piste" startOffset="0">'
		. esc_html( $tour . $tour )
		. '</textPath></text>'
		. '</svg>'
		. '<span class="lae-tampon__coeur"><b>' . esc_html( $centre ) . '</b>'
		. '<span>' . esc_html( $pied ) . '</span></span>'
		. '</div>';
}
