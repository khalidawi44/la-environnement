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

	/* LE TYPE : `HomeAndConstructionBusiness` plutôt que `LocalBusiness`
	   nu. schema.org n'a pas de type « élagueur » ni « paysagiste » — ses
	   enfants sont Electrician, Plumber, RoofingContractor… dont aucun ne
	   convient. Le parent « corps de métier qui intervient sur la
	   propriété » est la description la plus précise qui reste VRAIE,
	   donc meilleure pour la désambiguïsation, sans rien affirmer de
	   faux. `ProfessionalService` est écarté : schema.org le documente
	   lui-même comme transitoire et d'usage déconseillé. */
	$donnees = array(
		'@context' => 'https://schema.org',
		'@type'    => 'HomeAndConstructionBusiness',
		/* UN IDENTIFIANT STABLE. Sans lui, chaque page déclarait une
		   entreprise ANONYME : neuf entités distinctes au lieu d'une, que
		   rien ne reliait entre elles. C'est aussi le point d'accroche
		   des autres blocs — services, fil d'ariane, articles — qui
		   pointent désormais vers cette même entité. */
		'@id'      => home_url( '/' ) . '#entreprise',
		'name'     => lae_nom_site(),
		'url'      => home_url( '/' ),
	);

	$telephone = lae_reglage( 'telephone' );
	if ( $telephone ) {
		/* FORMAT INTERNATIONAL pour le balisage. « 06 04 40 83 00 » est
		   la bonne écriture pour un lecteur français, mais Google et les
		   assistants vocaux réconcilient une fiche d'établissement sur un
		   numéro E.164. L'affichage, lui, ne change pas : c'est seulement
		   ici, dans les données structurées, que le format bascule. */
		$donnees['telephone'] = lae_tel_e164( $telephone );
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
	/* `City` attend une COMMUNE. « Nantes Sud » n'en est pas une : c'est un
	   secteur de la commune de Nantes. Le déclarer en City est faux, et le
	   coût est double — un validateur ne le reconnaît pas, et surtout
	   « Nantes », le terme au plus fort volume de toute la zone, n'apparaît
	   alors dans AUCUNE des treize entrées. Or travailler à Nantes Sud,
	   c'est travailler à Nantes : rien n'est exagéré en le disant.

	   On ne touche PAS à la liste affichée : « Nantes Sud » est ce que
	   Fabrice a validé le 14/09, c'est ce que les gens du coin disent, et
	   c'est plus honnête sur une page que « Nantes » tout court, qui
	   laisserait croire qu'on couvre le nord de l'agglomération. Seule la
	   traduction vers le balisage change. */
	$commune = static function ( $ville ) {
		$secteurs = apply_filters( 'lae_seo_secteurs_communes', array(
			'Nantes Sud' => 'Nantes',
		) );
		return isset( $secteurs[ $ville ] ) ? $secteurs[ $ville ] : $ville;
	};
	$vues = array();
	foreach ( lae_communes() as $ville ) {
		$nom = $commune( $ville );
		if ( isset( $vues[ $nom ] ) ) {
			continue;   // deux libellés pour une même commune : une seule entrée
		}
		$vues[ $nom ] = true;
		$zones[]      = array( '@type' => 'City', 'name' => $nom );
	}
	if ( $zones ) {
		$donnees['areaServed'] = $zones;
	}

	/*
	 * Les heures d'ouverture de l'entreprise — corrigées le 21/09.
	 *
	 * CE QUI ÉTAIT FAUX. Le balisage déclarait l'entreprise ouverte
	 * 00:00–23:59 les sept jours, au motif que les urgences sont assurées
	 * 24 h/24. Mais `openingHoursSpecification` porte les heures de
	 * l'ÉTABLISSEMENT, pas la disponibilité d'un service particulier. Le
	 * pied de page du site annonce « Lundi au samedi : 8 h – 19 h », et la
	 * fiche Google Business de l'entreprise affiche « ferme à 19 h ». Le
	 * balisage était donc le seul des trois à dire autre chose.
	 *
	 * POURQUOI ÇA COMPTE. Google demande que les données structurées
	 * correspondent au contenu visible de la page, et il juge la cohérence
	 * entre le site et la fiche d'établissement — c'est le socle du
	 * référencement local. Une contradiction entre les deux coûte plus
	 * qu'un « Ouvert 24 h/24 » affiché à tort ne rapporte.
	 *
	 * LE 24 H/24 N'EST PAS PERDU POUR AUTANT, et c'est le point : il est
	 * porté là où il est vrai et où schema.org l'attend — le `contactPoint`
	 * d'urgence ci-dessous, et le `hoursAvailable` du `Service` d'urgence
	 * dans lae-schema-pages.php. L'entreprise ferme à 19 h ; la ligne
	 * d'urgence, elle, répond la nuit et le dimanche.
	 *
	 * Horaires confirmés par Fabrice le 21/09, concordants avec le pied de
	 * page du site et la fiche Google Business.
	 */
	$donnees['openingHoursSpecification'] = array(
		array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
			'opens'     => '08:00',
			'closes'    => '19:00',
		),
	);

	/* LE SERVICE D'URGENCE — corrigé le 14/09, il ne parvenait à personne.
	   Il était déclaré en `availableChannel`, dont le domaine schema.org
	   est `Service` et NON `Organization` : un validateur signale la
	   propriété comme non reconnue, et Google l'ignore en silence. Le
	   signal « joignable 24 h/24 », qui est l'argument le plus rentable de
	   ce métier, était donc écrit et jamais lu. `contactPoint`, lui, est
	   valide sur une entreprise. Le vrai `availableChannel` est posé sur
	   le nœud `Service` de la page urgences, où il est légitime. */
	if ( $telephone ) {
		$donnees['contactPoint'] = array(
			array(
				'@type'             => 'ContactPoint',
				'name'              => 'Intervention d\'urgence',
				'contactType'       => 'emergency',
				'telephone'         => lae_tel_e164( $telephone ),
				'areaServed'        => 'FR',
				'availableLanguage' => 'fr',
				/* Le 24 h/24 se déclare ICI, et plus sur l'entreprise.
				   Depuis le 21/09 `openingHoursSpecification` porte les
				   vraies heures de bureau (lundi-samedi, 8 h – 19 h) ;
				   sans cette précision, le signal « joignable la nuit et
				   le dimanche » disparaîtrait du balisage alors qu'il
				   reste vrai — et c'est l'argument le plus rentable du
				   métier. `hoursAvailable` est valide sur un
				   ContactPoint : c'est la disponibilité de CE canal, pas
				   celle de l'établissement. */
				'hoursAvailable'    => array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
					'opens'     => '00:00',
					'closes'    => '23:59',
				),
			),
		);
	}

	/* L'IMAGE — et c'était bloquant. `image` est une propriété REQUISE par
	   Google pour qu'une fiche d'établissement soit éligible aux résultats
	   enrichis. Elle était conditionnée au logo du personnalisateur, qui
	   n'a jamais été défini : la clé était donc absente des neuf pages, et
	   tout le bloc inéligible. Le logo reste prioritaire s'il existe un
	   jour ; à défaut, une vraie photo de chantier du thème — mieux qu'un
	   logo, d'ailleurs, pour un métier qui se vend par la photo. */
	$image = '';
	$logo  = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			$image = $src[0];
		}
	}
	if ( '' === $image ) {
		$repli = '/assets/images/jardin-piscine.webp';
		if ( file_exists( get_template_directory() . $repli ) ) {
			$image = get_template_directory_uri() . $repli;
		}
	}
	if ( $image ) {
		$donnees['image'] = $image;
	}

	/* L'entrepreneur individuel derrière le nom commercial. Vérifié au
	   registre, déjà porté par `legalName` — `founder` le dit dans la
	   forme que Google attend d'une personne. */
	$editeur = lae_reglage( 'editeur_nom' );
	if ( $editeur ) {
		$donnees['founder'] = array( '@type' => 'Person', 'name' => $editeur );
	}

	/* SIRET et code d'activité. En `identifier`/`PropertyValue` et NON en
	   `taxID`/`vatID` : le SIRET identifie un établissement, pas une
	   situation fiscale. Le seul identifiant fiscal serait le numéro de
	   TVA intracommunautaire, que le thème laisse vide à dessein tant
	   qu'Anthony n'a pas dit son régime. L'écrire serait une fausse
	   mention fiscale. */
	$siret = preg_replace( '/[^0-9]/', '', (string) lae_reglage( 'siret_numero' ) );
	if ( 14 === strlen( $siret ) ) {
		$donnees['identifier'] = array(
			array( '@type' => 'PropertyValue', 'name' => 'SIRET', 'value' => $siret ),
			array( '@type' => 'PropertyValue', 'name' => 'APE',   'value' => '81.30Z' ),
		);
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $donnees, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 20 );
