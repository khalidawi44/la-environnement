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
/**
 * Nom du site et slogan.
 *
 * Hostinger installe WordPress avec le nom de domaine comme titre
 * (« elagage-vertou.fr ») : on le remplace, mais uniquement si le client n'a
 * pas déjà choisi le sien.
 */
if ( ! function_exists( 'lae_amorce_identite' ) ) {
	function lae_amorce_identite() {

		$titre = trim( (string) get_option( 'blogname' ) );
		$hote  = preg_replace( '#^www\.#', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );

		$generique = (
			'' === $titre
			|| 0 === strcasecmp( $titre, $hote )
			|| 0 === strcasecmp( $titre, 'Mon site WordPress' )
			|| 0 === strcasecmp( $titre, 'Un site utilisant WordPress' )
			|| 0 === strcasecmp( $titre, 'My WordPress Site' )
			|| 0 === strcasecmp( $titre, 'Just another WordPress site' )
		);

		if ( $generique ) {
			update_option( 'blogname', 'L.A Environnement' );
		}

		$slogan = trim( (string) get_option( 'blogdescription' ) );
		if ( '' === $slogan || 0 === strcasecmp( $slogan, 'Un site utilisant WordPress' ) || 0 === strcasecmp( $slogan, 'Just another WordPress site' ) ) {
			update_option( 'blogdescription', lae_defaut( 'baseline' ) );
		}
	}
}

if ( ! function_exists( 'lae_amorce_structure' ) ) {
	function lae_amorce_structure() {

		lae_amorce_identite();

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
					array( 'page', 'a-propos', 'Notre méthode' ),
					array( 'page', 'contact', 'Contact' ),
				),
			),
			'pied' => array(
				'nom'     => 'Pied de page',
				'entrees' => array(
					array( 'archive', 'lae_prestation', 'Prestations' ),
					array( 'archive', 'lae_realisation', 'Réalisations' ),
					array( 'page', 'a-propos', 'Notre méthode' ),
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
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_option( 'lae_amorce_faite' ) ) {
		lae_amorce_contenu();
	}

	/* Verrou distinct : sur un site déjà amorcé, le titre livré par
	   l'hébergeur (le nom de domaine) n'avait jamais été corrigé.

	   ET IL NE L'A TOUJOURS PAS ÉTÉ, parce que ce verrou-ci était un
	   simple booléen. Il s'est posé pendant que le site vivait encore sur
	   le domaine temporaire de l'hébergeur : à ce moment-là le titre ne
	   ressemblait à aucun nom d'hôte, rien n'a été corrigé, et le verrou
	   a interdit tout nouvel essai. Résultat constaté en ligne le 13/09 :
	   `<title>`, `og:site_name` ET le `name` du LocalBusiness en JSON-LD
	   annonçaient tous « elagage-vertou.fr ». Pour un moteur, l'entreprise
	   n'avait pas de nom — juste une adresse.

	   Le verrou retient donc maintenant le NOM D'HÔTE pour lequel il a
	   été posé. Un changement de domaine — c'est exactement ce qui vient
	   de se produire — redonne sa chance à la correction. */
	lae_identite_rattrapage();
} );

if ( ! function_exists( 'lae_identite_rattrapage' ) ) {
	function lae_identite_rattrapage() {
		$hote_actuel = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		if ( '' === $hote_actuel || get_option( 'lae_identite_faite' ) === $hote_actuel ) {
			return;
		}
		update_option( 'lae_identite_faite', $hote_actuel );
		lae_amorce_identite();
	}
}

/* Sur `init` aussi, et pour la même raison que les pages et les chantiers :
   le thème arrive par WP-Cron, aucune administration n'est chargée. Un titre
   de site faux n'attend pas la prochaine connexion de quelqu'un. */
add_action( 'init', 'lae_identite_rattrapage', 19 );

/* ═══════════════════════════════════════════════════════════════════
   Pages ajoutées après l'amorce
   Les gabarits « Urgences » et « Tarif adapté aux revenus » sont livrés
   depuis la v1.11.0, mais `lae_amorce_faite` était déjà posée sur le
   site : aucune page ne les utilisait, donc le travail était invisible.
   Ce verrou-ci est distinct, comme `lae_identite_faite`, et ne repasse
   jamais par-dessus une page existante ni par-dessus les menus du
   client.

   DEUX VERROUS ET NON UN, et ce n'est pas de la coquetterie.

   Le déploiement passe par WP-Cron : aucune page d'administration n'est
   chargée quand le thème arrive. Un `admin_init` seul ferait attendre
   la première visite de Fabrice dans le tableau de bord pour que les
   pages existent — le code serait déployé et le site inchangé, exactement
   le piège déjà rencontré sur le cache. On écoute donc aussi `init`.

   Mais sur une installation neuve, `init` passe AVANT l'amorce (accrochée
   à `admin_init`) : les menus n'existent pas encore. Un verrou unique
   posé à ce moment-là créerait les pages en laissant les menus vides,
   pour toujours. Les deux étapes ont donc chacune le sien, et celui des
   menus n'est posé que si un menu a réellement été trouvé.
   ═══════════════════════════════════════════════════════════════════ */

if ( ! function_exists( 'lae_pages_tardives' ) ) {
	/**
	 * Pages livrées après la première amorce.
	 *
	 * @return array<string, array{titre:string, extrait:string, gabarit:string, menu:string, contenu:string}>
	 */
	function lae_pages_tardives() {
		return array(
			'urgences' => array(
				'titre'   => 'Urgences',
				'extrait' => 'Arbre tombé, branche menaçante, sécurisation après tempête : joignable 24 h/24 et 7 j/7, déplacement le jour même, sans majoration.',
				'gabarit' => 'page-urgences.php',
				'menu'    => 'Urgences 24 h/24',
				'contenu' => "<!-- wp:heading --><h2>Ce qui compte comme une urgence</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Un arbre déraciné ou couché, une grosse branche fendue qui tient encore, un houppier qui surplombe une toiture après un coup de vent, un tronc qui menace une ligne électrique ou la voie publique. Dans ces cas-là, on ne prend pas rendez-vous pour la semaine suivante.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Comment ça se passe</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Vous appelez et vous décrivez ce que vous voyez — une photo par message aide beaucoup. On vient dans la journée. Le premier geste est de <strong>mettre hors de danger</strong> : hauban, dépose de la partie instable, périmètre dégagé. Le reste du chantier, la finition et l'évacuation se traitent après, quand plus rien ne menace.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Avant d'appeler, quelques réflexes</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Ne passez pas sous la partie qui menace, et éloignez les voitures et les enfants de son aplomb. Si un câble est touché ou arraché, n'y touchez pas et appelez d'abord Enedis. Si la voie publique est coupée, prévenez la mairie ou les pompiers : leur intervention et la nôtre ne font pas le même travail.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Et l'assurance</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Prenez des photos avant toute intervention : votre assureur les demandera. Nous remettons une facture détaillée, et un constat écrit de ce qui a été sécurisé et pourquoi, à joindre à votre déclaration.</p><!-- /wp:paragraph -->",
			),
			'tarifs' => array(
				'titre'   => 'Nos tarifs',
				'extrait' => 'Moins cher que les entreprises du secteur, à garanties identiques — et une réduction selon vos revenus, sans justificatif à fournir.',
				'gabarit' => 'page-tarifs.php',
				'menu'    => 'Tarifs',
				'contenu' => "<!-- wp:paragraph --><p>Un devis d'élagage n'a rien d'évident à lire : d'un professionnel à l'autre, le même arbre peut passer du simple au double sans qu'on comprenne pourquoi. Alors autant dire d'où l'on part.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Le prix d'un chantier dépend de quatre choses, et d'aucune autre : <strong>l'accès</strong> (un fond de jardin sans passage ne se travaille pas comme un bord de route), <strong>la hauteur</strong>, <strong>le risque</strong> (ce qu'il y a sous l'arbre — une toiture, une ligne, une clôture) et <strong>l'évacuation</strong> des déchets verts. C'est pour ça qu'aucun prix ne se donne au téléphone : on vient voir, et le devis écrit qui suit ne bouge plus.</p><!-- /wp:paragraph -->",
			),
		);
	}
}

if ( ! function_exists( 'lae_pages_tardives_creer' ) ) {
	/**
	 * Crée les pages manquantes et leur rattache leur gabarit.
	 *
	 * @return array<int, string> Identifiants des pages créées => libellé de menu.
	 */
	function lae_pages_tardives_creer() {

		$nouvelles = array();

		foreach ( lae_pages_tardives() as $slug => $page ) {

			$existante = get_page_by_path( $slug );
			if ( $existante ) {
				// Jamais par-dessus le contenu du client : on se contente de
				// rattacher le gabarit s'il manque, sinon on laisse tel quel.
				if ( '' === (string) get_post_meta( $existante->ID, '_wp_page_template', true ) ) {
					update_post_meta( $existante->ID, '_wp_page_template', $page['gabarit'] );
				}
				continue;
			}

			$id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['titre'],
				'post_name'    => $slug,
				'post_excerpt' => $page['extrait'],
				'post_content' => $page['contenu'],
			), true );

			if ( is_wp_error( $id ) ) {
				continue;
			}

			update_post_meta( $id, '_wp_page_template', $page['gabarit'] );
			$nouvelles[ (int) $id ] = $page['menu'];
		}

		return $nouvelles;
	}
}

if ( ! function_exists( 'lae_pages_tardives_menus' ) ) {
	/**
	 * Complète les menus en place. On n'en recrée aucun, et on n'ajoute
	 * rien qui pointe déjà vers la même page.
	 *
	 * @return bool Vrai si un menu a été trouvé — c'est ce qui autorise à
	 *              poser le verrou. Faux tant qu'il n'y a rien à compléter :
	 *              on repassera.
	 */
	function lae_pages_tardives_menus() {

		$libelles = array();
		foreach ( lae_pages_tardives() as $slug => $page ) {
			$p = get_page_by_path( $slug );
			if ( $p ) {
				$libelles[ (int) $p->ID ] = $page['menu'];
			}
		}
		if ( ! $libelles ) {
			return false;
		}

		$emplacements = get_theme_mod( 'nav_menu_locations', array() );
		$trouve       = false;

		foreach ( array( 'principal', 'pied' ) as $cle ) {

			if ( empty( $emplacements[ $cle ] ) ) {
				continue;
			}
			$menu = wp_get_nav_menu_object( $emplacements[ $cle ] );
			if ( ! $menu ) {
				continue;
			}
			$trouve = true;

			$deja = array();
			foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
				if ( 'post_type' === $item->type ) {
					$deja[] = (int) $item->object_id;
				}
			}

			foreach ( $libelles as $id => $libelle ) {
				if ( in_array( (int) $id, $deja, true ) ) {
					continue;
				}
				wp_update_nav_menu_item( $menu->term_id, 0, array(
					'menu-item-title'     => $libelle,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => (int) $id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
		}

		return $trouve;
	}
}

if ( ! function_exists( 'lae_pages_tardives_rattrapage' ) ) {
	function lae_pages_tardives_rattrapage() {

		// Les deux drapeaux sont lus à chaque requête : ils sont autochargés,
		// sinon c'est deux requêtes SQL de plus pour toujours.
		if ( ! get_option( 'lae_pages_tardives_faites' ) ) {
			update_option( 'lae_pages_tardives_faites', 1 );   // verrou posé AVANT le travail
			lae_pages_tardives_creer();
		}

		if ( ! get_option( 'lae_pages_tardives_menus' ) ) {
			if ( lae_pages_tardives_menus() ) {
				update_option( 'lae_pages_tardives_menus', 1 );
			}
		}
	}
}

/* `init` parce que le thème arrive par WP-Cron, sans administration ;
   `admin_init` en plus parce que sur une installation neuve c'est là que
   les menus naissent, après `init`. Les verrous rendent l'ensemble
   idempotent quel que soit l'ordre. */
add_action( 'init', 'lae_pages_tardives_rattrapage', 20 );
add_action( 'admin_init', 'lae_pages_tardives_rattrapage', 11 );

/* ═══════════════════════════════════════════════════════════════════
   Mentions légales — le gabarit devient la vraie page
   La page existe depuis l'amorce mais n'a jamais été remplie : elle
   affiche encore « Page à compléter avant la mise en ligne ». Sur un
   site commercial, c'est une obligation légale non tenue (LCEN,
   art. 6-III-1) et la première chose qu'un visiteur méfiant va lire.

   Identité relevée le 13/09 sur le registre officiel des entreprises
   (API recherche-entreprises, data.gouv.fr), confirmée par Fabrice :
   entrepreneur individuel, un seul établissement ouvert, activité
   81.30Z services d'aménagement paysager. Le « L.A » de la marque
   vient de LAMARQUE Anthony.

   DEUX CHOSES NE SONT PAS ÉCRITES ICI, et c'est délibéré :
   — la TVA intracommunautaire, parce qu'on ignore s'il y est assujetti
     ou en franchise en base ; annoncer l'un ou l'autre serait inventer ;
   — l'assurance responsabilité civile professionnelle, parce que
     l'assureur et le numéro de police ne viennent que de lui.
   Elles s'ajouteront quand il les aura données.

   Le remplacement ne se fait QUE si la page porte encore le texte du
   gabarit. Dès que quelqu'un y a écrit quoi que ce soit, on n'y touche
   plus jamais.
   ═══════════════════════════════════════════════════════════════════ */

if ( ! function_exists( 'lae_mentions_texte' ) ) {
	function lae_mentions_texte() {

		$nom   = lae_defaut( 'editeur_nom' );
		$forme = lae_defaut( 'editeur_forme' );
		$rue   = lae_defaut( 'adresse_rue' );
		$cp    = lae_defaut( 'adresse_cp' );
		$ville = lae_defaut( 'adresse_ville' );
		$siret = lae_defaut( 'siret_numero' );
		$tel   = lae_defaut( 'telephone' );
		$mail  = lae_defaut( 'email' );

		/* Les trois manques légaux se lisent dans le PERSONNALISATEUR, pas
		   dans la table des défauts : c'est là qu'Anthony les saisira, et on
		   veut que la saisie suffise, sans déploiement. Vides = sections
		   absentes. Jamais de valeur inventée en repli. */
		$assureur  = lae_reglage( 'assurance_assureur' );
		$police    = lae_reglage( 'assurance_police' );
		$zone      = lae_reglage( 'assurance_zone' );
		$tva_reg   = lae_reglage( 'tva_regime' );
		$tva_num   = lae_reglage( 'tva_numero' );
		$med_nom   = lae_reglage( 'mediateur_nom' );
		$med_adr   = lae_reglage( 'mediateur_adresse' );
		$med_site  = lae_reglage( 'mediateur_site' );

		$blocs = array();

		$blocs[] = '<!-- wp:heading --><h2>Éditeur du site</h2><!-- /wp:heading -->';
		$blocs[] = '<!-- wp:paragraph --><p>'
			. '<strong>' . esc_html( $nom ) . '</strong> — ' . esc_html( $forme ) . '<br>'
			. 'Exerçant sous le nom commercial <strong>L.A Environnement</strong><br>'
			. esc_html( $rue ) . '<br>' . esc_html( $cp . ' ' . $ville ) . '<br>'
			. 'SIRET : ' . esc_html( $siret ) . '<br>'
			/* Une seule des deux lignes, jamais les deux, jamais aucune des
			   deux « au cas où » : en franchise en base il n'existe PAS de
			   numéro intracommunautaire à afficher, et l'absence de mention
			   chez un assujetti est une omission. Tant que le régime n'est
			   pas renseigné, la ligne n'existe simplement pas. */
			. ( 'assujetti' === $tva_reg && $tva_num
				? 'TVA intracommunautaire : ' . esc_html( $tva_num ) . '<br>'
				: '' )
			. ( 'franchise' === $tva_reg
				? 'TVA non applicable, article 293 B du code général des impôts<br>'
				: '' )
			. 'Activité : services d\'aménagement paysager (code APE 81.30Z)<br>'
			. 'Immatriculé au Registre national des entreprises (RNE)<br>'
			. 'Téléphone : ' . esc_html( $tel ) . '<br>'
			. 'Courriel : ' . esc_html( $mail )
			. '</p><!-- /wp:paragraph -->';

		$blocs[] = '<!-- wp:heading --><h2>Responsable de la publication</h2><!-- /wp:heading -->';
		$blocs[] = '<!-- wp:paragraph --><p>' . esc_html( $nom ) . ', joignable aux coordonnées ci-dessus.</p><!-- /wp:paragraph -->';

		$blocs[] = '<!-- wp:heading --><h2>Hébergement</h2><!-- /wp:heading -->';
		$blocs[] = '<!-- wp:paragraph --><p>Hostinger International Ltd<br>'
			. '61 Lordou Vironos Street, 6023 Larnaca, Chypre<br>'
			. '<a href="https://www.hostinger.fr" rel="nofollow noopener">www.hostinger.fr</a></p><!-- /wp:paragraph -->';

		/* ASSURANCE RC PRO. Un élagueur travaille au-dessus des toitures et
		   des voitures des voisins : c'est l'information qu'un client prudent
		   cherche en premier. La section n'apparaît que si l'assureur est
		   renseigné — le numéro de police et la zone restent facultatifs,
		   parce qu'un nom d'assureur seul vaut déjà mieux que rien, alors
		   qu'un numéro sans assureur ne veut rien dire. */
		if ( $assureur ) {
			$blocs[] = '<!-- wp:heading --><h2>Assurance responsabilité civile professionnelle</h2><!-- /wp:heading -->';
			$blocs[] = '<!-- wp:paragraph --><p>' . esc_html( $assureur )
				. ( $police ? '<br>Numéro de police : ' . esc_html( $police ) : '' )
				. ( $zone ? '<br>Couverture géographique : ' . esc_html( $zone ) : '' )
				. '</p><!-- /wp:paragraph -->';
		}

		$blocs[] = '<!-- wp:heading --><h2>Propriété intellectuelle</h2><!-- /wp:heading -->';
		/* CORRIGÉ LE 14/09, ET IL LE FALLAIT. Cette phrase disait, sans
		   nuance, que « les photographies ne proviennent d'aucune banque
		   d'images ». C'était vrai tant que le site ne portait que des
		   photos de chantier. Depuis l'ajout des trois bandeaux d'ambiance
		   — la forêt le jour, au crépuscule et la nuit —, qui sont des
		   images générées, la phrase serait devenue fausse telle quelle.
		   On distingue donc les deux : les photos de chantier restent ce
		   qu'elles sont, et c'est ça qui vaut quelque chose pour un client ;
		   les images d'ambiance sont annoncées pour ce qu'elles sont. */
		$blocs[] = '<!-- wp:paragraph --><p>Les textes de ce site et les photographies de chantier sont la propriété d\''
			. esc_html( $nom ) . '. <strong>Les photographies de chantier — avant/après, réalisations, illustrations de pages — sont prises sur les chantiers réellement réalisés</strong> : elles ne proviennent d\'aucune banque d\'images. Les images d\'ambiance du bandeau d\'accueil sont, elles, des illustrations créées pour ce site et ne représentent aucun chantier. Toute reproduction sans autorisation écrite est interdite.</p><!-- /wp:paragraph -->';

		$blocs[] = '<!-- wp:heading --><h2>Données personnelles</h2><!-- /wp:heading -->';
		$blocs[] = '<!-- wp:paragraph --><p>Le formulaire de contact transmet votre demande par courriel et <strong>n\'enregistre rien sur le site</strong> : ni compte, ni base de données de prospects. Les informations que vous envoyez (nom, coordonnées, description du chantier) ne servent qu\'à vous répondre et à établir un devis, et ne sont transmises à personne.</p><!-- /wp:paragraph -->';
		$blocs[] = '<!-- wp:paragraph --><p>Vous pouvez demander l\'accès, la rectification ou l\'effacement de ces informations en écrivant à ' . esc_html( $mail ) . '. Si la réponse ne vous convient pas, vous pouvez saisir la CNIL (<a href="https://www.cnil.fr" rel="nofollow noopener">www.cnil.fr</a>).</p><!-- /wp:paragraph -->';

		$blocs[] = '<!-- wp:heading --><h2>Cookies et mesure d\'audience</h2><!-- /wp:heading -->';
		$blocs[] = '<!-- wp:paragraph --><p>Ce site ne dépose <strong>aucun cookie</strong> de mesure d\'audience ni de publicité, et ne charge aucune police de caractères ni ressource hébergée par un tiers. Aucune bannière de consentement n\'est donc nécessaire : il n\'y a rien à consentir.</p><!-- /wp:paragraph -->';

		/* MÉDIATION DE LA CONSOMMATION — article L. 616-1 du code de la
		   consommation. Tout professionnel qui vend à des particuliers doit
		   communiquer sur son site le médiateur auquel il adhère. C'est
		   aujourd'hui la seule obligation du site non remplie.

		   Le premier jet écrivait « les coordonnées du médiateur figurent sur
		   les devis et factures ». Je n'en savais rien : c'était inventer un
		   fait sur les documents du client. La section reste donc ABSENTE
		   tant que le médiateur n'est pas désigné — une section absente est
		   un manque connu, une section fausse est un mensonge en ligne. Le
		   jour où il adhère, un champ du personnalisateur suffit. */
		if ( $med_nom ) {
			$blocs[] = '<!-- wp:heading --><h2>Médiation de la consommation</h2><!-- /wp:heading -->';
			$blocs[] = '<!-- wp:paragraph --><p>Conformément à l\'article L. 616-1 du code de la consommation, '
				. 'vous pouvez recourir gratuitement au médiateur de la consommation auquel nous adhérons, '
				. 'après avoir tenté de résoudre le litige directement avec nous par une réclamation écrite.</p><!-- /wp:paragraph -->';
			$blocs[] = '<!-- wp:paragraph --><p><strong>' . esc_html( $med_nom ) . '</strong>'
				. ( $med_adr ? '<br>' . esc_html( $med_adr ) : '' )
				. ( $med_site ? '<br><a href="' . esc_url( $med_site ) . '" rel="nofollow noopener">' . esc_html( $med_site ) . '</a>' : '' )
				. '</p><!-- /wp:paragraph -->';
		}

		return implode( "\n\n", $blocs );
	}
}

if ( ! function_exists( 'lae_mentions_rattrapage' ) ) {
	function lae_mentions_rattrapage() {

		if ( get_option( 'lae_mentions_remplies' ) ) {
			return;
		}

		$page = get_page_by_path( 'mentions-legales' );
		if ( ! $page ) {
			update_option( 'lae_mentions_remplies', 1 );
			return;
		}

		// Le gabarit, et lui seul. Un mot écrit par le client et on s'abstient.
		if ( false === strpos( (string) $page->post_content, 'Page à compléter avant la mise en ligne' ) ) {
			update_option( 'lae_mentions_remplies', 1 );
			return;
		}

		update_option( 'lae_mentions_remplies', 1 );   // verrou posé AVANT l'écriture

		$texte = lae_mentions_texte();
		update_option( 'lae_mentions_signature', md5( $texte ) );

		wp_update_post( array(
			'ID'           => (int) $page->ID,
			'post_content' => $texte,
		) );
	}
}
add_action( 'init', 'lae_mentions_rattrapage', 22 );
add_action( 'admin_init', 'lae_mentions_rattrapage', 13 );

/* ═══════════════════════════════════════════════════════════════════
   METTRE À JOUR LES MENTIONS QUAND LES MANQUES SONT COMBLÉS

   LE PROBLÈME QUE ÇA RÉSOUT. Les mentions sont écrites une fois, puis
   deviennent du contenu WordPress ordinaire. Ajouter les champs assurance,
   TVA et médiateur au personnalisateur ne servirait donc à RIEN : la page
   déjà publiée ne les verrait jamais. Anthony remplirait consciencieusement
   ses champs et la page resterait incomplète, sans le moindre signal.

   LE PRINCIPE, ET C'EST LE SEUL QUI COMPTE : on ne réécrit QUE ce qu'on a
   écrit soi-même. La signature du texte généré est mémorisée ; si la page
   porte encore exactement ce texte, elle est à nous et on la régénère. Dès
   qu'un caractère a changé — Anthony a corrigé une virgule, ajouté un
   paragraphe — la signature ne correspond plus et on ne touche plus jamais
   à cette page. Aucune modification humaine ne peut être écrasée.

   LE CAS DES SITES DÉJÀ EN LIGNE. Sur le site actuel, les mentions ont été
   écrites avant que cette signature existe. On la reconstitue : si la page
   est identique au texte que le générateur produit quand les trois manques
   sont vides — c'est-à-dire exactement ce qui a été publié le 13/09 — alors
   elle est à nous, et on adopte la signature. Sinon on s'abstient.
   ═══════════════════════════════════════════════════════════════════ */

if ( ! function_exists( 'lae_mentions_maj' ) ) {
	function lae_mentions_maj() {

		// Le premier remplissage n'a pas encore eu lieu : ce n'est pas ici.
		if ( ! get_option( 'lae_mentions_remplies' ) ) {
			return;
		}

		$page = get_page_by_path( 'mentions-legales' );
		if ( ! $page ) {
			return;
		}

		$actuel  = (string) $page->post_content;
		$attendu = lae_mentions_texte();

		// Déjà à jour : rien à faire, et surtout pas d'écriture inutile en base.
		if ( md5( $actuel ) === md5( $attendu ) ) {
			update_option( 'lae_mentions_signature', md5( $attendu ) );
			return;
		}

		$signature = get_option( 'lae_mentions_signature' );

		if ( ! $signature ) {
			/* Site antérieur à la signature : on la reconstitue en comparant
			   à ce que le générateur produisait sans les trois manques. */
			$signature = md5( lae_mentions_texte_sans_manques() );
		}

		// La page a été touchée à la main : elle ne nous appartient plus.
		if ( md5( $actuel ) !== $signature ) {
			return;
		}

		update_option( 'lae_mentions_signature', md5( $attendu ) );

		wp_update_post( array(
			'ID'           => (int) $page->ID,
			'post_content' => $attendu,
		) );
	}
}

/**
 * Le texte des mentions tel qu'il était AVANT que les trois manques
 * existent : assurance, TVA et médiateur neutralisés.
 *
 * Sert uniquement à reconnaître une page écrite par une version
 * antérieure du thème. Passe par les mêmes filtres que le générateur, donc
 * il n'y a pas deux textes à maintenir en parallèle.
 *
 * @return string
 */
if ( ! function_exists( 'lae_mentions_texte_sans_manques' ) ) {
	function lae_mentions_texte_sans_manques() {
		$vider = function () { return ''; };
		$cles  = array(
			'assurance_assureur', 'assurance_police', 'assurance_zone',
			'tva_regime', 'tva_numero',
			'mediateur_nom', 'mediateur_adresse', 'mediateur_site',
		);
		foreach ( $cles as $cle ) {
			add_filter( 'theme_mod_lae_' . $cle, $vider, 99 );
		}
		$texte = lae_mentions_texte();
		foreach ( $cles as $cle ) {
			remove_filter( 'theme_mod_lae_' . $cle, $vider, 99 );
		}
		return $texte;
	}
}

add_action( 'init', 'lae_mentions_maj', 23 );
add_action( 'admin_init', 'lae_mentions_maj', 14 );
/* Et dès qu'un réglage change dans le personnalisateur, sans attendre la
   prochaine visite : c'est le moment exact où Anthony s'attend à voir la
   page bouger. */
add_action( 'customize_save_after', 'lae_mentions_maj', 20 );
