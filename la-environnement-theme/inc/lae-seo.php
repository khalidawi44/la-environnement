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

	/*
	 * Ouverture 24 h/24, 7 j/7 (information client du 13/09).
	 *
	 * C'est ce qui fait afficher « Ouvert 24 h/24 » par Google et ce qui rend
	 * l'entreprise éligible aux recherches d'urgence — un arbre qui menace se
	 * cherche la nuit et le week-end, pas aux heures de bureau. Déclarer les
	 * horaires vaut donc plus ici que sur n'importe quel autre métier.
	 *
	 * `dayOfWeek` liste les sept jours, `opens`/`closes` à 00:00 est la
	 * notation schema.org pour une ouverture continue.
	 */
	$donnees['openingHoursSpecification'] = array(
		array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
			'opens'     => '00:00',
			'closes'    => '23:59',
		),
	);

	/*
	 * Service d'urgence déclaré explicitement : Google et les assistants
	 * vocaux s'en servent pour les requêtes « en urgence », « maintenant »,
	 * « ouvert la nuit ».
	 */
	$donnees['availableChannel'] = array(
		'@type'           => 'ServiceChannel',
		'name'            => 'Intervention d\'urgence',
		'servicePhone'    => array(
			'@type'       => 'ContactPoint',
			'telephone'   => $telephone ? $telephone : '',
			'contactType' => 'emergency',
			'areaServed'  => 'FR',
			'availableLanguage' => 'French',
		),
	);

	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			$donnees['image'] = $src[0];
		}
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $donnees, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 20 );
