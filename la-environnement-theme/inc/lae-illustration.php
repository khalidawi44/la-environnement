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
		'urgences'         => array( 'chantiers/demontage-bouleau.webp',           'Démontage d\'un bouleau au-dessus d\'un jardin' ),
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
