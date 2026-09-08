<?php
/**
 * Contenu de départ — prestations et familles.
 *
 * Le site est livré prêt : à la première activation du thème, les six
 * prestations du métier et leurs trois familles sont créées, avec leurs
 * textes. Rien n'est écrasé ensuite : si une prestation existe déjà, ou si
 * l'amorce a déjà tourné, la fonction ne fait rien.
 *
 * Aucun chiffre, aucun nom, aucune certification, aucun prix : ces textes
 * décrivent le métier, pas des engagements que le thème n'a pas à prendre à
 * la place du client. Tout est modifiable depuis l'administration.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Familles de prestations, dans l'ordre d'affichage des filtres. */
if ( ! function_exists( 'lae_amorce_familles' ) ) {
	function lae_amorce_familles() {
		return array(
			'arbre'    => 'Arbre',
			'jardin'   => 'Jardin',
			'chantier' => 'Chantier',
		);
	}
}

/** Les six prestations livrées avec le thème. */
if ( ! function_exists( 'lae_amorce_prestations' ) ) {
	function lae_amorce_prestations() {
		return array(
			array(
				'titre'   => 'Élagage en grimpe',
				'famille' => 'arbre',
				'icone'   => 'tronconneuse',
				'extrait' => 'Taille douce, éclaircie, réduction de couronne : on monte à la corde et on coupe le moins possible.',
				'texte'   => "Un arbre ne se taille pas pour le raccourcir, mais pour corriger ce qui le met en danger : une branche morte au-dessus d'un passage, une couronne devenue trop lourde d'un côté, un frottement contre une toiture.\n\nNous travaillons en grimpe, à la corde, là où aucune nacelle ne passe : fond de jardin, terrain en pente, arbre entouré de constructions. La coupe se fait au bon endroit, au bourrelet, pour que l'arbre puisse refermer sa plaie lui-même. Pas d'étêtage : un arbre étêté repart en gourmands mal accrochés, et le problème revient plus gros trois ans plus tard.\n\nÉclaircie du houppier, réduction de couronne, suppression du bois mort, dégagement de toiture ou de ligne : chaque intervention se décide sur place, l'arbre sous les yeux.",
			),
			array(
				'titre'   => 'Abattage et démontage',
				'famille' => 'arbre',
				'icone'   => 'arbre',
				'extrait' => 'Abattage direct quand la place le permet, démontage par câble quand elle ne le permet pas.',
				'texte'   => "Abattre est la dernière option. Quand elle s'impose — arbre mort, tronc fendu, système racinaire compromis, sujet devenu dangereux pour une construction — elle se prépare.\n\nS'il y a la place pour coucher l'arbre au sol, l'abattage est direct : entaille de direction, trait d'abattage, chute maîtrisée. S'il n'y a pas la place — limite de propriété, toiture en dessous, mur de chaque côté — on démonte. Le grimpeur monte, coupe par tronçons, et chaque pièce descend en rétention sur cordage plutôt que de tomber.\n\nLe tronc et les branches sont ensuite débités, puis évacués ou laissés sur place en bûches, selon ce qui a été convenu. Le rognage de souche se décide avec vous : tout dépend de ce que le terrain doit devenir.",
			),
			array(
				'titre'   => 'Haubanage et sécurisation',
				'famille' => 'arbre',
				'icone'   => 'bouclier',
				'extrait' => 'Un arbre fragilisé ne se coupe pas toujours : parfois il se tient.',
				'texte'   => "Une fourche à écorce incluse, une charpentière fissurée, un sujet ébranlé par un coup de vent : l'arbre est fragilisé, mais il est vivant, et rien n'oblige à l'abattre.\n\nLe haubanage relie deux parties de la couronne par un cordage souple, calculé pour reprendre l'effort au moment où il devient dangereux, sans empêcher l'arbre de bouger — un arbre qui ne bouge plus s'affaiblit. Une réduction de prise au vent accompagne souvent la pose.\n\nCe type d'intervention se contrôle dans le temps. Nous vous disons quand il faudra repasser voir.",
			),
			array(
				'titre'   => 'Création de jardin',
				'famille' => 'jardin',
				'icone'   => 'pelle',
				'extrait' => 'Conception, plantation, engazonnement, massifs : un jardin dessiné pour durer.',
				'texte'   => "Un jardin se dessine avant de se planter. On regarde le sol, l'exposition, l'eau, ce que vous voulez en faire — un coin d'ombre, un potager, un écran qui vous rende votre intimité.\n\nLes essences sont choisies pour tenir à cet endroit-là : une haie de persistants au nord ne vaut pas une haie champêtre au sud, et un arbre planté trop près d'une façade sera un problème dans dix ans. Nous plantons ce qui aura la place de grandir.\n\nTerrassement léger, engazonnement ou placage de gazon, massifs, paillage, haies, plantation d'arbres et d'arbustes : le jardin est livré planté et paillé, avec ce qu'il faut savoir pour l'arroser la première année.",
			),
			array(
				'titre'   => 'Entretien de jardin',
				'famille' => 'jardin',
				'icone'   => 'feuille',
				'extrait' => 'Tailles, tontes, débroussaillage, désherbage : le jardin suivi au fil des saisons.',
				'texte'   => "Un jardin entretenu par passages réguliers demande moins de travail — et coûte moins cher — qu'un jardin repris une fois par an quand tout est parti.\n\nTaille des haies et des arbustes au bon moment de l'année, tonte, débroussaillage des parties enfrichées, désherbage manuel, ramassage des feuilles, remise en état d'un terrain laissé de côté. Chaque passage se cale sur la saison : on ne taille pas une haie en pleine nidification, et on ne rabat pas un arbuste à fleurs juste avant qu'il fleurisse.\n\nL'entretien peut se faire au coup par coup ou par passages convenus à l'avance dans l'année.",
			),
			array(
				'titre'   => 'Évacuation et broyage',
				'famille' => 'chantier',
				'icone'   => 'camion',
				'extrait' => 'Le chantier est fini quand le terrain est net : rien à ramasser après notre passage.',
				'texte'   => "Un chantier d'élagage ou d'abattage produit du volume. Ce volume repart, ou il se transforme.\n\nLes branches peuvent être broyées sur place : le broyat est alors étalé en paillage au pied des massifs et des haies, ce qui garde l'humidité, limite les herbes indésirables et vous évite d'acheter du paillis. C'est la solution la plus simple et la plus utile quand le terrain s'y prête.\n\nSinon, les rémanents sont chargés et évacués. Le bois de chauffage est débité et laissé sur place si vous le souhaitez. Dans tous les cas, le terrain est rendu propre : allées dégagées, sciure ramassée, accès libérés.",
			),
		);
	}
}

/**
 * Crée le contenu de départ. Idempotent à deux verrous : l'amorce ne tourne
 * qu'une fois, et jamais si des prestations existent déjà — on n'écrase
 * jamais ce que le client a saisi.
 */
if ( ! function_exists( 'lae_amorce_contenu' ) ) {
	function lae_amorce_contenu() {

		if ( get_option( 'lae_amorce_faite' ) ) {
			return;
		}
		update_option( 'lae_amorce_faite', 1, false );

		$deja = get_posts( array(
			'post_type'      => 'lae_prestation',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );
		if ( $deja ) {
			return;
		}

		// Familles.
		$ids = array();
		foreach ( lae_amorce_familles() as $slug => $nom ) {
			$terme = term_exists( $slug, 'lae_famille' );
			if ( ! $terme ) {
				$terme = wp_insert_term( $nom, 'lae_famille', array( 'slug' => $slug ) );
			}
			if ( ! is_wp_error( $terme ) ) {
				$ids[ $slug ] = (int) $terme['term_id'];
			}
		}

		// Prestations.
		$ordre = 0;
		foreach ( lae_amorce_prestations() as $p ) {
			$ordre += 10;

			$post_id = wp_insert_post( array(
				'post_type'    => 'lae_prestation',
				'post_status'  => 'publish',
				'post_title'   => $p['titre'],
				'post_excerpt' => $p['extrait'],
				'post_content' => $p['texte'],
				'menu_order'   => $ordre,
			), true );

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			if ( isset( $ids[ $p['famille'] ] ) ) {
				wp_set_object_terms( $post_id, array( $ids[ $p['famille'] ] ), 'lae_famille' );
			}
			update_post_meta( $post_id, '_lae_icone', $p['icone'] );
		}
	}
}

/* À la première activation du thème. */
add_action( 'after_switch_theme', 'lae_amorce_contenu' );

/* Filet : si le thème a été déposé par la synchronisation plutôt qu'activé
   depuis l'administration, `after_switch_theme` n'a jamais été déclenché.
   Le premier chargement d'une page d'administration rattrape l'amorce — une
   seule fois, l'option posée juste avant fait office de verrou. */
add_action( 'admin_init', function () {
	if ( ! get_option( 'lae_amorce_faite' ) && current_user_can( 'manage_options' ) ) {
		lae_amorce_contenu();
	}
} );
