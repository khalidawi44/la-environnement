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
		'name'     => lae_nom_site(),
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

	/*
	 * Adresse postale structurée. Elle l'était mal : tout le réglage partait
	 * dans `streetAddress`, si bien que « Vertou (44) » était déclaré à Google
	 * comme un nom de rue. Une fiche d'établissement local se juge sur la
	 * cohérence du triplet nom / adresse / téléphone entre le site, la fiche
	 * Google et les annuaires : une adresse mal découpée l'affaiblit.
	 * Les champs séparés priment ; le réglage libre reste le repli.
	 */
	$rue   = lae_reglage( 'adresse_rue' );
	$cp    = lae_reglage( 'adresse_cp' );
	$ville = lae_reglage( 'adresse_ville' );

	if ( $rue || $cp || $ville ) {
		$postale = array( '@type' => 'PostalAddress', 'addressCountry' => 'FR' );
		if ( $rue )   { $postale['streetAddress']   = $rue; }
		if ( $cp )    { $postale['postalCode']      = $cp; }
		if ( $ville ) { $postale['addressLocality'] = $ville; }
		$donnees['address'] = $postale;
	} else {
		$adresse = lae_reglage( 'adresse' );
		if ( $adresse ) {
			$donnees['address'] = array(
				'@type'         => 'PostalAddress',
				'streetAddress' => str_replace( array( "\r\n", "\n" ), ', ', $adresse ),
			);
		}
	}

	/* Le nom légal n'est pas la marque : l'entreprise est une entreprise
	   individuelle, elle porte le nom de la personne. Google rapproche la
	   fiche des registres publics quand les deux sont déclarés. */
	$legal = trim( (string) lae_reglage( 'editeur_nom' ) );
	if ( $legal && 0 !== strcasecmp( $legal, (string) lae_nom_site() ) ) {
		$donnees['legalName'] = $legal;
	}

	/*
	 * Zone desservie. Le client couvre tout le département (info du 13/09) :
	 * on le déclare en AdministrativeArea, ce qui vaut pour ses 207 communes,
	 * plutôt que d'énumérer une liste forcément incomplète. Les villes
	 * nommées restent déclarées en plus, elles portent le référencement local
	 * là où il y a réellement des chantiers.
	 */
	$zones = array();
	$dep   = lae_reglage( 'zone_departement' );
	if ( $dep ) {
		$zones[] = array( '@type' => 'AdministrativeArea', 'name' => $dep );
	}
	foreach ( lae_communes() as $ville ) {
		$zones[] = array( '@type' => 'City', 'name' => $ville );
	}
	if ( $zones ) {
		$donnees['areaServed'] = $zones;
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
