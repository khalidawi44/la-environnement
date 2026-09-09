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

		// Pages, accueil statique et menus : indépendants des prestations.
		lae_amorce_structure();

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


/** Les pages livrées avec le site. */
if ( ! function_exists( 'lae_amorce_pages' ) ) {
	function lae_amorce_pages() {
		return array(
			'accueil' => array(
				'titre'   => 'Accueil',
				'contenu' => '',
			),
			'a-propos' => array(
				'titre'   => 'Notre façon de travailler',
				'contenu' => "<!-- wp:paragraph --><p>Un arbre n'est pas un poteau de bois. C'est un organisme vivant qui répond à chaque coupe, qui cicatrise mal une plaie mal placée, et qui met des années à montrer les conséquences d'une mauvaise intervention. C'est ce qui décide de notre manière de travailler.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>On regarde avant de couper</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Aucun devis ne se chiffre au téléphone. On vient voir l'arbre : son état sanitaire, l'équilibre de son houppier, la présence de bois mort, l'état du collet et ce qu'on devine du système racinaire. On regarde aussi ce qu'il y a autour — une toiture, une ligne, une limite de propriété, un accès étroit. C'est cet ensemble qui dit s'il faut tailler, haubaner, ou abattre.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>On coupe le moins possible</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Une taille douce enlève ce qui gêne et ce qui menace, pas un tiers de l'arbre. L'étêtage, lui, ne règle rien : l'arbre repart en gourmands mal accrochés, plus dangereux que les branches qu'on vient de lui prendre. Quand un arbre peut être conservé, on le conserve — et on vous dit ce qu'il faudra surveiller.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>On travaille à la corde</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>La nacelle ne va pas partout : fond de jardin, terrain en pente, arbre cerné de constructions. La grimpe permet d'atteindre ce qu'aucune machine n'atteint, et de descendre les pièces en rétention plutôt que de les laisser tomber. C'est plus long, et c'est souvent la seule manière de faire proprement.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Le chantier est fini quand le terrain est net</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Les branches partent au broyage — le broyat peut rester chez vous en paillage — ou sont évacuées. Le bois de chauffage est débité et laissé sur place si vous le souhaitez. Allées dégagées, sciure ramassée : il ne doit rien rester à ramasser après nous.</p><!-- /wp:paragraph -->",
			),
			'contact' => array(
				'titre'   => 'Contact',
				'contenu' => "<!-- wp:paragraph --><p>Dites-nous ce qui vous amène : l'arbre, son emplacement, ce qui vous inquiète. Nous vous rappelons pour convenir d'une visite sur place — c'est là que le devis se fait.</p><!-- /wp:paragraph -->\n\n<!-- wp:shortcode -->[lae_contact]<!-- /wp:shortcode -->",
			),
			'mentions-legales' => array(
				'titre'   => 'Mentions légales',
				'contenu' => "<!-- wp:paragraph --><p><strong>Page à compléter avant la mise en ligne.</strong> Les mentions ci-dessous sont obligatoires : elles doivent porter les informations réelles de l'entreprise.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Éditeur du site</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Dénomination, forme juridique, adresse du siège, téléphone, e-mail, numéro SIRET, numéro de TVA intracommunautaire le cas échéant, nom du responsable de la publication.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Hébergement</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Nom, adresse et téléphone de l'hébergeur.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Assurance professionnelle</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Assureur, numéro de police, couverture géographique.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Données personnelles</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Le formulaire de contact envoie votre demande par e-mail et n'enregistre rien sur le site. Vous pouvez demander l'accès, la rectification ou l'effacement des informations que vous nous avez transmises en écrivant à l'adresse indiquée ci-dessus.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Cookies</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Ce site ne dépose aucun cookie de mesure d'audience ni de publicité, et ne charge aucune police ni ressource hébergée par un tiers.</p><!-- /wp:paragraph -->",
			),
		);
	}
}

/**
 * Crée les pages, l'accueil statique et les trois menus.
 * Comme les prestations : une seule fois, et jamais par-dessus l'existant.
 */
if ( ! function_exists( 'lae_amorce_structure' ) ) {
	function lae_amorce_structure() {

		$ids = array();
		foreach ( lae_amorce_pages() as $slug => $page ) {
			$existante = get_page_by_path( $slug );
			if ( $existante ) {
				$ids[ $slug ] = (int) $existante->ID;
				continue;
			}
			$id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['titre'],
				'post_name'    => $slug,
				'post_content' => $page['contenu'],
			), true );
			if ( ! is_wp_error( $id ) ) {
				$ids[ $slug ] = (int) $id;
			}
		}

		// Accueil statique : sans cela WordPress affiche la liste des articles.
		if ( isset( $ids['accueil'] ) && 'page' !== get_option( 'show_on_front' ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $ids['accueil'] );
		}

		// Menus.
		$menus = array(
			'principal' => array(
				'nom'     => 'Menu principal',
				'entrees' => array(
					array( 'page', 'accueil', 'Accueil' ),
					array( 'archive', 'lae_prestation', 'Prestations' ),
					array( 'archive', 'lae_realisation', 'Réalisations' ),
					array( 'page', 'a-propos', 'Notre façon de travailler' ),
					array( 'page', 'contact', 'Contact' ),
				),
			),
			'pied' => array(
				'nom'     => 'Pied de page',
				'entrees' => array(
					array( 'archive', 'lae_prestation', 'Prestations' ),
					array( 'archive', 'lae_realisation', 'Réalisations' ),
					array( 'page', 'a-propos', 'Notre façon de travailler' ),
					array( 'page', 'contact', 'Contact' ),
				),
			),
			'legal' => array(
				'nom'     => 'Mentions légales',
				'entrees' => array(
					array( 'page', 'mentions-legales', 'Mentions légales' ),
				),
			),
		);

		$emplacements = get_theme_mod( 'nav_menu_locations', array() );

		foreach ( $menus as $cle => $menu ) {
			if ( ! empty( $emplacements[ $cle ] ) && wp_get_nav_menu_object( $emplacements[ $cle ] ) ) {
				continue; // un menu est déjà en place à cet emplacement
			}

			$objet = wp_get_nav_menu_object( $menu['nom'] );
			$menu_id = $objet ? (int) $objet->term_id : (int) wp_create_nav_menu( $menu['nom'] );
			if ( ! $menu_id || is_wp_error( $menu_id ) ) {
				continue;
			}

			if ( ! $objet ) {
				foreach ( $menu['entrees'] as $entree ) {
					list( $genre, $cible, $libelle ) = $entree;

					if ( 'page' === $genre ) {
						if ( ! isset( $ids[ $cible ] ) ) {
							continue;
						}
						wp_update_nav_menu_item( $menu_id, 0, array(
							'menu-item-title'     => $libelle,
							'menu-item-object'    => 'page',
							'menu-item-object-id' => $ids[ $cible ],
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						) );
					} else {
						wp_update_nav_menu_item( $menu_id, 0, array(
							'menu-item-title'  => $libelle,
							'menu-item-object' => $cible,
							'menu-item-type'   => 'post_type_archive',
							'menu-item-status' => 'publish',
						) );
					}
				}
			}

			$emplacements[ $cle ] = $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $emplacements );
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
