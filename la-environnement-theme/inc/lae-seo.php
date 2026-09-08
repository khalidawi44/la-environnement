<?php
/**
 * Données structurées (schema.org).
 *
 * Uniquement construites à partir des réglages réellement remplis :
 * aucun champ n'est deviné ni complété d'office.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_head', function () {

	$donnees = array(
		'@context' => 'https://schema.org',
		'@type'    => 'LocalBusiness',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	$telephone = lae_reglage( 'telephone' );
	if ( $telephone ) {
		$donnees['telephone'] = $telephone;
	}

	$email = lae_reglage( 'email' );
	if ( $email ) {
		$donnees['email'] = $email;
	}

	$adresse = lae_reglage( 'adresse' );
	if ( $adresse ) {
		$donnees['address'] = array(
			'@type'         => 'PostalAddress',
			'streetAddress' => str_replace( array( "\r\n", "\n" ), ', ', $adresse ),
		);
	}

	$communes = lae_communes();
	if ( $communes ) {
		$donnees['areaServed'] = array_map( static function ( $ville ) {
			return array( '@type' => 'City', 'name' => $ville );
		}, $communes );
	}

	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			$donnees['image'] = $src[0];
		}
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $donnees, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 20 );
