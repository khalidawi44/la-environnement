<?php
/**
 * Personnalisateur — tout le contenu éditable du thème.
 *
 * Règle de conception : aucun réglage n'a de valeur par défaut inventée.
 * Les seules valeurs pré-remplies sont des libellés de prestation fournis par
 * le client (élagage, abattage, création de jardin) et des intitulés de
 * section neutres. Un réglage laissé vide masque le bloc côté site.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Nettoyage : texte multiligne. */
if ( ! function_exists( 'lae_sanitize_multiligne' ) ) {
	function lae_sanitize_multiligne( $valeur ) {
		return implode( "\n", array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', (string) $valeur ) ) );
	}
}

/** Nettoyage : case à cocher. */
if ( ! function_exists( 'lae_sanitize_bool' ) ) {
	function lae_sanitize_bool( $valeur ) {
		return (bool) $valeur;
	}
}

add_action( 'customize_register', function ( $wp_customize ) {

	$panneau = 'lae_panneau';

	$wp_customize->add_panel( $panneau, array(
		'title'       => 'L.A Environnement',
		'description' => 'Contenu et coordonnées du site. Un champ laissé vide masque le bloc correspondant sur le site.',
		'priority'    => 20,
	) );

	/**
	 * Ajoute un réglage + son contrôle en une passe.
	 */
	$ajoute = function ( $id, $args ) use ( $wp_customize ) {
		$type      = isset( $args['type'] ) ? $args['type'] : 'text';
		$sanitize  = isset( $args['sanitize'] ) ? $args['sanitize'] : 'sanitize_text_field';
		$defaut    = isset( $args['default'] ) ? $args['default'] : '';

		$wp_customize->add_setting( $id, array(
			'default'           => $defaut,
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		) );

		if ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
				'label'       => $args['label'],
				'section'     => $args['section'],
				'description' => isset( $args['description'] ) ? $args['description'] : '',
			) ) );
			return;
		}

		$wp_customize->add_control( $id, array(
			'label'       => $args['label'],
			'section'     => $args['section'],
			'type'        => $type,
			'description' => isset( $args['description'] ) ? $args['description'] : '',
			'input_attrs' => isset( $args['input_attrs'] ) ? $args['input_attrs'] : array(),
		) );
	};

	// ── Identité ────────────────────────────────────────────────────────
	$wp_customize->add_section( 'lae_identite', array(
		'title' => 'Identité',
		'panel' => $panneau,
	) );

	$ajoute( 'lae_baseline', array(
		'label'       => 'Baseline (sous le nom)',
		'section'     => 'lae_identite',
		'description' => 'Exemple : « Élagage · Abattage · Création de jardin ». Laisser vide pour n\'afficher que le nom.',
	) );

	// ── Coordonnées ─────────────────────────────────────────────────────
	$wp_customize->add_section( 'lae_coordonnees', array(
		'title'       => 'Coordonnées',
		'panel'       => $panneau,
		'description' => 'Ces informations alimentent l\'en-tête, le pied de page, la barre mobile et les données structurées Google.',
	) );

	$ajoute( 'lae_telephone', array( 'label' => 'Téléphone', 'section' => 'lae_coordonnees' ) );
	$ajoute( 'lae_email', array( 'label' => 'E-mail', 'section' => 'lae_coordonnees', 'sanitize' => 'sanitize_email' ) );
	$ajoute( 'lae_adresse', array( 'label' => 'Adresse', 'section' => 'lae_coordonnees', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
	$ajoute( 'lae_horaires', array( 'label' => 'Horaires', 'section' => 'lae_coordonnees', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne', 'description' => 'Une ligne par créneau.' ) );
	$ajoute( 'lae_siret', array( 'label' => 'Mention légale de pied de page', 'section' => 'lae_coordonnees', 'description' => 'Exemple : SIRET, numéro d\'assurance décennale. Affiché tel quel.' ) );
	$ajoute( 'lae_url_contact', array( 'label' => 'URL de la page contact', 'section' => 'lae_coordonnees', 'sanitize' => 'esc_url_raw', 'description' => 'Vide = la page dont l\'adresse se termine par /contact est utilisée automatiquement.' ) );

	// ── Accueil : bandeau principal ─────────────────────────────────────
	$wp_customize->add_section( 'lae_hero', array(
		'title' => 'Accueil — bandeau principal',
		'panel' => $panneau,
	) );

	$ajoute( 'lae_hero_surtitre', array( 'label' => 'Surtitre', 'section' => 'lae_hero' ) );
	$ajoute( 'lae_hero_titre', array( 'label' => 'Titre', 'section' => 'lae_hero', 'description' => 'Vide = le nom du site est utilisé.' ) );
	$ajoute( 'lae_hero_chapo', array( 'label' => 'Texte d\'introduction', 'section' => 'lae_hero', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
	$ajoute( 'lae_hero_image', array( 'label' => 'Photo de fond', 'section' => 'lae_hero', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Format paysage, 1920 px de large minimum. Sans photo, un fond vert dégradé est utilisé.' ) );
	$ajoute( 'lae_hero_btn2_texte', array( 'label' => 'Bouton secondaire — libellé', 'section' => 'lae_hero' ) );
	$ajoute( 'lae_hero_btn2_url', array( 'label' => 'Bouton secondaire — lien', 'section' => 'lae_hero', 'sanitize' => 'esc_url_raw' ) );
	$ajoute( 'lae_hero_points', array(
		'label'       => 'Points forts',
		'section'     => 'lae_hero',
		'type'        => 'textarea',
		'sanitize'    => 'lae_sanitize_multiligne',
		'description' => 'Une ligne par point. Rien n\'est affiché si le champ est vide.',
	) );

	// ── Accueil : bandeau de réassurance ────────────────────────────────
	$wp_customize->add_section( 'lae_reassurance', array(
		'title'       => 'Accueil — bandeau de réassurance',
		'panel'       => $panneau,
		'description' => 'Une ligne par bloc, au format : Titre | Description | icone',
	) );

	$ajoute( 'lae_reassurance_items', array(
		'label'       => 'Blocs',
		'section'     => 'lae_reassurance',
		'type'        => 'textarea',
		'sanitize'    => 'lae_sanitize_multiligne',
		'description' => 'Icônes possibles : ' . implode( ', ', array_keys( lae_icones_disponibles() ) ),
	) );

	// ── Accueil : prestations ───────────────────────────────────────────
	$wp_customize->add_section( 'lae_prestations_home', array(
		'title'       => 'Accueil — prestations',
		'panel'       => $panneau,
		'description' => 'Les prestations se saisissent dans le menu « Prestations » de l\'administration.',
	) );

	$ajoute( 'lae_prestations_surtitre', array( 'label' => 'Surtitre', 'section' => 'lae_prestations_home', 'default' => 'Nos prestations' ) );
	$ajoute( 'lae_prestations_titre', array( 'label' => 'Titre', 'section' => 'lae_prestations_home', 'default' => 'Élagage, abattage et création de jardin' ) );
	$ajoute( 'lae_prestations_chapo', array( 'label' => 'Texte d\'introduction', 'section' => 'lae_prestations_home', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );

	// ── Accueil : déroulé d'un chantier ─────────────────────────────────
	$wp_customize->add_section( 'lae_etapes', array(
		'title'       => 'Accueil — déroulé d\'un chantier',
		'panel'       => $panneau,
		'description' => 'Une ligne par étape, au format : Titre | Description',
	) );

	$ajoute( 'lae_etapes_titre', array( 'label' => 'Titre de la section', 'section' => 'lae_etapes', 'default' => 'Comment ça se passe' ) );
	$ajoute( 'lae_etapes_items', array( 'label' => 'Étapes', 'section' => 'lae_etapes', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );

	// ── Accueil : réalisations ──────────────────────────────────────────
	$wp_customize->add_section( 'lae_realisations_home', array(
		'title'       => 'Accueil — réalisations',
		'panel'       => $panneau,
		'description' => 'Les chantiers se saisissent dans le menu « Réalisations ».',
	) );

	$ajoute( 'lae_realisations_titre', array( 'label' => 'Titre', 'section' => 'lae_realisations_home', 'default' => 'Nos chantiers' ) );
	$ajoute( 'lae_realisations_chapo', array( 'label' => 'Texte d\'introduction', 'section' => 'lae_realisations_home', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );

	// ── Zone d'intervention ─────────────────────────────────────────────
	$wp_customize->add_section( 'lae_zone', array(
		'title' => 'Accueil — zone d\'intervention',
		'panel' => $panneau,
	) );

	$ajoute( 'lae_zone_titre', array( 'label' => 'Titre', 'section' => 'lae_zone', 'default' => 'Zone d\'intervention' ) );
	$ajoute( 'lae_zone_texte', array( 'label' => 'Texte', 'section' => 'lae_zone', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
	$ajoute( 'lae_zone_communes', array( 'label' => 'Communes', 'section' => 'lae_zone', 'description' => 'Séparées par des virgules.' ) );

	// ── Bande d'appel ───────────────────────────────────────────────────
	$wp_customize->add_section( 'lae_appel', array(
		'title' => 'Bande « demander un devis »',
		'panel' => $panneau,
	) );

	$ajoute( 'lae_appel_titre', array( 'label' => 'Titre', 'section' => 'lae_appel', 'default' => 'Un projet, un arbre à traiter ?' ) );
	$ajoute( 'lae_appel_texte', array( 'label' => 'Texte', 'section' => 'lae_appel', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
	$ajoute( 'lae_appel_btn_texte', array( 'label' => 'Libellé du bouton', 'section' => 'lae_appel', 'default' => 'Demander un devis' ) );

	$wp_customize->add_setting( 'lae_appel_partout', array(
		'default'           => true,
		'sanitize_callback' => 'lae_sanitize_bool',
	) );
	$wp_customize->add_control( 'lae_appel_partout', array(
		'label'   => 'Afficher cette bande sur toutes les pages',
		'section' => 'lae_appel',
		'type'    => 'checkbox',
	) );

	// ── Accueil cinématique ─────────────────────────────────────────────
	$wp_customize->add_section( 'lae_cine', array(
		'title'       => 'Accueil cinématique',
		'panel'       => $panneau,
		'description' => 'La scène de la page d\'accueil. Chaque média absent est remplacé par un dégradé : rien ne casse, mais la page ne vaudra vraiment que le jour où les vraies photos et la vidéo seront là.',
	) );

	$ajoute( 'lae_cine_video', array(
		'label'       => 'Vidéo du bandeau principal (URL)',
		'section'     => 'lae_cine',
		'sanitize'    => 'esc_url_raw',
		'description' => 'MP4 court, sans son, qui tourne en boucle. Coller l\'URL du fichier depuis la médiathèque.',
	) );
	$ajoute( 'lae_cine_poster', array( 'label' => 'Affiche de la vidéo', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Image montrée le temps que la vidéo démarre.' ) );

	$ajoute( 'lae_cine_marquee', array(
		'label'       => 'Bandeau défilant',
		'section'     => 'lae_cine',
		'type'        => 'textarea',
		'sanitize'    => 'lae_sanitize_multiligne',
		'default'     => "Élagage\nAbattage\nCréation de jardin",
		'description' => 'Un mot ou une expression par ligne. Le bandeau accélère avec le défilement.',
	) );
	$ajoute( 'lae_cine_matiere_image', array( 'label' => 'Matière de fond (bois, écorce, feuillage)', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Utilisée derrière le bandeau et derrière les prestations phares.' ) );

	$ajoute( 'lae_cine_tab_image', array( 'label' => 'Grande image épinglée', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Format paysage. Elle respire lentement pendant qu\'on la traverse.' ) );
	$ajoute( 'lae_cine_tab_surtitre', array( 'label' => 'Image épinglée — surtitre', 'section' => 'lae_cine' ) );
	$ajoute( 'lae_cine_tab_titre', array( 'label' => 'Image épinglée — titre', 'section' => 'lae_cine', 'default' => 'Un jardin, *pas un chantier*', 'description' => 'Le texte entre *astérisques* est mis en italique et en couleur.' ) );
	$ajoute( 'lae_cine_tab_texte', array( 'label' => 'Image épinglée — texte', 'section' => 'lae_cine', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );

	$ajoute( 'lae_cine_chapitres', array(
		'label'       => 'Chapitres',
		'section'     => 'lae_cine',
		'type'        => 'textarea',
		'sanitize'    => 'lae_sanitize_multiligne',
		'description' => 'Une ligne par chapitre : Titre | Texte | mot, mot, mot. Trois chapitres au maximum (une image par chapitre ci-dessous).',
	) );
	$ajoute( 'lae_cine_ch1_image', array( 'label' => 'Chapitre 1 — image', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw' ) );
	$ajoute( 'lae_cine_ch2_image', array( 'label' => 'Chapitre 2 — image', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw' ) );
	$ajoute( 'lae_cine_ch3_image', array( 'label' => 'Chapitre 3 — image', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw' ) );

	$ajoute( 'lae_cine_scene_image', array( 'label' => 'Scène — image qui se dissout', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Elle part en poussière de feuilles au fil du défilement.' ) );
	$ajoute( 'lae_cine_main_image', array( 'label' => 'Scène — la main', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Une main ouverte (terre, feuilles, jeune pousse) : les prestations en sortent.' ) );
	$ajoute( 'lae_cine_scene_titre', array( 'label' => 'Scène — titre', 'section' => 'lae_cine', 'default' => 'Ce qui tient un arbre *ne se voit pas*' ) );
	$ajoute( 'lae_cine_scene_texte', array( 'label' => 'Scène — texte', 'section' => 'lae_cine', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );

	$ajoute( 'lae_cine_phares_surtitre', array( 'label' => 'Prestations phares — surtitre', 'section' => 'lae_cine', 'default' => 'Ce que nous faisons' ) );
	$ajoute( 'lae_cine_phares_titre', array( 'label' => 'Prestations phares — titre', 'section' => 'lae_cine', 'default' => 'Trois métiers, *un seul interlocuteur*' ) );
	$ajoute( 'lae_cine_phares_chapo', array( 'label' => 'Prestations phares — texte', 'section' => 'lae_cine', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
	$ajoute( 'lae_cine_phares_note', array( 'label' => 'Prestations phares — note sous les cartes', 'section' => 'lae_cine' ) );

	$ajoute( 'lae_cine_avis', array(
		'label'       => 'Avis (réels uniquement)',
		'section'     => 'lae_cine',
		'type'        => 'textarea',
		'sanitize'    => 'lae_sanitize_multiligne',
		'description' => 'Une ligne par avis : Nom | Texte de l\'avis | Date. À recopier depuis de VRAIS avis Google. Champ vide = aucune section d\'avis : mieux vaut rien qu\'un témoignage inventé.',
	) );
	$ajoute( 'lae_cine_avis_url', array( 'label' => 'Avis — lien vers la fiche Google', 'section' => 'lae_cine', 'sanitize' => 'esc_url_raw' ) );
	$ajoute( 'lae_cine_avis_note', array( 'label' => 'Avis — note affichée', 'section' => 'lae_cine', 'description' => 'Exemple : 4,8. Laisser vide si la fiche n\'a pas encore de note.' ) );
	$ajoute( 'lae_cine_avis_total', array( 'label' => 'Avis — nombre d\'avis', 'section' => 'lae_cine' ) );

	$ajoute( 'lae_cine_arbre_image', array( 'label' => 'Révélation — image ronde', 'section' => 'lae_cine', 'type' => 'image', 'sanitize' => 'esc_url_raw', 'description' => 'Un arbre, un chantier fini, une équipe : l\'image qui apparaît en gros plan avant l\'appel.' ) );
	$ajoute( 'lae_cine_arbre_surtitre', array( 'label' => 'Révélation — surtitre', 'section' => 'lae_cine' ) );
	$ajoute( 'lae_cine_arbre_titre', array( 'label' => 'Révélation — titre', 'section' => 'lae_cine', 'default' => 'Parlons de *votre terrain*' ) );
	$ajoute( 'lae_cine_arbre_texte', array( 'label' => 'Révélation — texte', 'section' => 'lae_cine', 'type' => 'textarea', 'sanitize' => 'lae_sanitize_multiligne' ) );
}, 20 );
