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

// ── Ce qu'il faut sur une prestation : une photo ────────────────────────
/* LA BOÎTE « ICÔNE » A ÉTÉ RETIRÉE LE 14/09. Elle proposait de choisir un
   pictogramme affiché « à défaut d'image mise en avant ». Ce repli n'existe
   plus nulle part dans le thème — demande de Fabrice, capture à l'appui :
   « pas d'icône de ce genre-là dans le site ». Garder le choix aurait été
   pire que de le retirer : le client choisit une icône, enregistre, et rien
   ne change sur le site. Un réglage sans effet est un piège.

   À la place, un rappel là où il sert : c'est l'image à la une qui illustre
   une prestation, et il en faut une. La méta `_lae_icone` déjà enregistrée
   sur d'anciennes prestations n'est pas supprimée — elle ne sert plus à rien
   et ne gêne personne, alors qu'une suppression en masse toucherait des
   données du client sans raison. */

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'lae_prestation_photo',
		'Photo de la prestation',
		'lae_boite_photo',
		'lae_prestation',
		'side',
		'high'
	);
} );

/** Rappelle que l'illustration d'une prestation est son image à la une. */
if ( ! function_exists( 'lae_boite_photo' ) ) {
	function lae_boite_photo( $post ) {
		$a = has_post_thumbnail( $post->ID );
		echo '<p>';
		if ( $a ) {
			echo '<strong style="color:#1b7a3e">Cette prestation a sa photo.</strong> ';
			echo 'Elle s\'affiche sur la carte, en page d\'accueil et dans la liste des prestations.';
		} else {
			echo '<strong style="color:#a33">Cette prestation n\'a pas de photo.</strong> ';
			echo 'Sa carte s\'affichera sans image. Ajoutez-en une dans « Image mise en avant », ';
			echo 'plus bas dans cette colonne.';
		}
		echo '</p>';
		echo '<p class="description">Une vraie photo de chantier, prise sur place — jamais une image ';
		echo 'd\'illustration : les mentions légales du site s\'y engagent.</p>';
	}
}
