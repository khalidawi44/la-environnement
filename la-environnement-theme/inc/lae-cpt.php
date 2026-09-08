<?php
/**
 * Contenus structurés : Prestations et Réalisations.
 *
 * Le client saisit ses prestations et ses chantiers depuis l'administration
 * WordPress — rien n'est figé dans le thème.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {

	register_post_type( 'lae_prestation', array(
		'labels' => array(
			'name'               => 'Prestations',
			'singular_name'      => 'Prestation',
			'add_new_item'       => 'Ajouter une prestation',
			'edit_item'          => 'Modifier la prestation',
			'search_items'       => 'Rechercher une prestation',
			'not_found'          => 'Aucune prestation',
			'menu_name'          => 'Prestations',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'has_archive'   => 'prestations',
		'rewrite'       => array( 'slug' => 'prestations', 'with_front' => false ),
		'menu_icon'     => 'dashicons-palmtree',
		'menu_position' => 20,
		'taxonomies'    => array( 'lae_famille' ),
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
	) );

	register_taxonomy( 'lae_famille', 'lae_prestation', array(
		'labels' => array(
			'name'          => 'Familles',
			'singular_name' => 'Famille',
			'menu_name'     => 'Familles',
		),
		'public'       => true,
		'show_in_rest' => true,
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'famille', 'with_front' => false ),
	) );

	register_taxonomy( 'lae_type_chantier', 'lae_realisation', array(
		'labels' => array(
			'name'          => 'Types de chantier',
			'singular_name' => 'Type de chantier',
		),
		'public'       => true,
		'show_in_rest' => true,
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'type-de-chantier', 'with_front' => false ),
	) );

	register_post_type( 'lae_realisation', array(
		'labels' => array(
			'name'          => 'Réalisations',
			'singular_name' => 'Réalisation',
			'add_new_item'  => 'Ajouter une réalisation',
			'edit_item'     => 'Modifier la réalisation',
			'not_found'     => 'Aucune réalisation',
			'menu_name'     => 'Réalisations',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'has_archive'   => 'realisations',
		'rewrite'       => array( 'slug' => 'realisations', 'with_front' => false ),
		'menu_icon'     => 'dashicons-format-gallery',
		'menu_position' => 21,
		'taxonomies'    => array( 'lae_type_chantier' ),
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
	) );
}, 5 );

/**
 * Réécriture des permaliens : une seule fois après l'enregistrement des types,
 * repérée par un jeton de version en base (pas de flush à chaque chargement).
 */
add_action( 'init', function () {
	if ( get_option( 'lae_permaliens_version' ) !== LAE_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'lae_permaliens_version', LAE_VERSION, false );
	}
}, 20 );

/** Ordre naturel des prestations : celui défini par le champ « ordre ». */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'lae_prestation' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$query->set( 'posts_per_page', 24 );
	}
	if ( $query->is_post_type_archive( 'lae_realisation' ) || $query->is_tax( 'lae_type_chantier' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
} );

// ── Icône d'une prestation ──────────────────────────────────────────────

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'lae_prestation_icone',
		'Icône',
		'lae_boite_icone',
		'lae_prestation',
		'side',
		'default'
	);
} );

/** Affiche la boîte de choix d'icône. */
if ( ! function_exists( 'lae_boite_icone' ) ) {
	function lae_boite_icone( $post ) {
		wp_nonce_field( 'lae_icone_save', 'lae_icone_nonce' );
		$actuelle = get_post_meta( $post->ID, '_lae_icone', true );
		echo '<p><label for="lae_icone" class="screen-reader-text">Icône</label>';
		echo '<select name="lae_icone" id="lae_icone" style="width:100%">';
		echo '<option value="">— Aucune —</option>';
		foreach ( lae_icones_disponibles() as $cle => $libelle ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $cle ),
				selected( $actuelle, $cle, false ),
				esc_html( $libelle )
			);
		}
		echo '</select></p>';
		echo '<p class="description">Affichée sur la carte de la prestation, à défaut d\'image mise en avant.</p>';
	}
}

add_action( 'save_post_lae_prestation', function ( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['lae_icone_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['lae_icone_nonce'] ) ), 'lae_icone_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$valeur = isset( $_POST['lae_icone'] ) ? sanitize_key( wp_unslash( $_POST['lae_icone'] ) ) : '';
	if ( $valeur && array_key_exists( $valeur, lae_icones_disponibles() ) ) {
		update_post_meta( $post_id, '_lae_icone', $valeur );
	} else {
		delete_post_meta( $post_id, '_lae_icone' );
	}
} );
