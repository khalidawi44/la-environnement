<?php
/**
 * Les conseils — la partie du site qui fait venir les gens.
 *
 * Fabrice (13/09) : « il faudrait des articles aussi, parce que les articles
 * ça fait venir les gens. Une section article qui explique le métier
 * d'élaguer les arbres, donner des astuces pour bien s'occuper des arbres. »
 *
 * POURQUOI ÇA MARCHE, ET À QUELLE CONDITION. Une page de prestation capte
 * quelqu'un qui cherche déjà un élagueur — c'est-à-dire peu de monde, et
 * seulement au moment où il en a besoin. Un article capte quelqu'un qui se
 * pose une question : « à quelle distance planter d'un voisin », « quand
 * tailler une haie », « mon voisin refuse d'élaguer ». Ces gens-là sont
 * cent fois plus nombreux, ils cherchent toute l'année, et le jour où le
 * problème devient un chantier, ils reviennent sur le site qui leur avait
 * répondu gratuitement.
 *
 * La condition, c'est l'exactitude. Un article qui se trompe sur le code
 * civil fait perdre un procès à quelqu'un, et coule la crédibilité de
 * l'entreprise qui l'a publié. Chaque affirmation juridique ci-dessous a
 * été vérifiée le 13/09 aux sources, et les NUANCES sont écrites, pas
 * gommées — ce sont elles qui distinguent ce site des dizaines de pages
 * qui recopient « 2 mètres » sans dire le reste :
 *
 *   — art. 671 : moins de 0,50 m, plantation interdite ; de 0,50 m à 2 m,
 *     hauteur plafonnée à 2 m ; au-delà de 2 m, libre ;
 *   — art. 672 : l'arrachage ou la réduction peut être exigé, SAUF titre,
 *     destination du père de famille ou PRESCRIPTION TRENTENAIRE ;
 *   — art. 673 : le droit de faire couper les branches est IMPRESCRIPTIBLE,
 *     lui — mais on ne coupe pas soi-même les branches. On peut en revanche
 *     couper soi-même racines, ronces et brindilles, à la limite exacte ;
 *   — taille des haies du 16 mars au 15 août : INTERDICTION pour les
 *     agriculteurs soumis à la PAC (BCAE 8), simple RECOMMANDATION de
 *     l'Office français de la biodiversité pour les particuliers. Beaucoup
 *     de sites annoncent une interdiction générale : c'est faux.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'lae_articles_livres' ) ) {
	function lae_articles_livres() {
		return array(

			'distance-plantation-arbre-limite-propriete' => array(
				'titre'   => 'À quelle distance de la limite peut-on planter un arbre ?',
				'extrait' => 'Deux mètres, cinquante centimètres, et la règle des trente ans que presque personne ne mentionne. Ce que dit vraiment le code civil.',
				'image'   => 'pelouse-haie.webp',
				'texte'   => "<!-- wp:paragraph --><p>C'est la question qui revient le plus souvent sur nos chantiers, et c'est aussi celle sur laquelle on lit le plus de bêtises. La règle tient en trois articles du code civil, et surtout en une exception que la plupart des sites oublient.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Trois zones, pas une</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>L'article 671 du code civil ne fixe pas une distance mais un barème. À partir de la limite séparative :</p><!-- /wp:paragraph -->\n\n<!-- wp:list --><ul><li><strong>Moins de 50 cm</strong> : aucune plantation n'est admise.</li><li><strong>Entre 50 cm et 2 m</strong> : on peut planter, à condition que la plante ne dépasse jamais <strong>2 m de haut</strong>. C'est une obligation permanente, pas seulement au moment de la plantation : un arbuste qui grandit vous oblige à le maintenir sous les 2 m.</li><li><strong>Au-delà de 2 m</strong> : la hauteur est libre.</li></ul><!-- /wp:list -->\n\n<!-- wp:paragraph --><p>La distance se mesure depuis la limite jusqu'au <strong>milieu du tronc</strong>, et la hauteur depuis le sol. Attention : un document d'urbanisme local ou un usage constant de la commune peut prévoir autre chose, et il prime alors sur le code civil.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Ce que le voisin peut exiger</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>L'article 672 donne au voisin le droit de demander l'arrachage de ce qui est planté trop près, ou sa réduction à 2 m selon la zone. Mais il ajoute une exception décisive : ce droit disparaît en cas de titre, de destination du père de famille, ou de <strong>prescription trentenaire</strong>.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Autrement dit : <strong>un arbre planté trop près depuis plus de trente ans ne peut plus être arraché</strong>. Il est devenu légitime là où il est, aussi mal placé soit-il. C'est le point qui change tout dans un conflit de voisinage, et c'est celui qu'on lit le moins.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Les branches qui dépassent : jamais soi-même</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>L'article 673 traite d'autre chose, et il ne se prescrit pas. Si les branches du voisin avancent au-dessus de chez vous, <strong>vous pouvez le contraindre à les couper</strong> — et ce droit reste ouvert indéfiniment, même après trente ans, même si l'arbre est parfaitement en règle.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>En revanche vous <strong>n'avez pas le droit de les couper vous-même</strong>. C'est au propriétaire de l'arbre de le faire ou de le faire faire. Une branche coupée de votre propre initiative peut se retourner contre vous.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>La règle s'inverse pour ce qui vient d'en bas : <strong>racines, ronces et brindilles, vous pouvez les couper vous-même</strong>, à la limite exacte de votre terrain. Les fruits tombés naturellement des branches qui surplombent chez vous, eux, vous appartiennent.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Notre conseil, avant d'en arriver au droit</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Un élagage coûte toujours moins cher qu'un conflit. La plupart des situations se règlent quand quelqu'un vient voir l'arbre et explique ce qui est faisable : souvent, une réduction bien menée résout la gêne sans amputer l'arbre, et les deux voisins y gagnent. C'est ce qu'on fait en premier.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><em>Sources : articles 671, 672 et 673 du code civil. Cet article donne un repère général et ne remplace pas l'avis d'un juriste sur une situation précise.</em></p><!-- /wp:paragraph -->",
			),

			'quand-tailler-haie-elaguer-arbre' => array(
				'titre'   => 'Quand tailler — et quand surtout ne pas tailler',
				'extrait' => 'Du 16 mars au 15 août, c\'est une interdiction pour les agriculteurs et une recommandation pour vous. La nuance est réelle, et la raison est meilleure que la règle.',
				'image'   => 'chantiers/haie-taillee-broyat.webp',
				'texte'   => "<!-- wp:paragraph --><p>On lit partout qu'il est « interdit de tailler les haies du 16 mars au 15 août ». C'est vrai pour certains, faux pour la plupart des gens qui le lisent — et la vraie raison de s'abstenir est plus convaincante que l'interdiction elle-même.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Ce qui est interdit, et à qui</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>L'interdiction du <strong>16 mars au 15 août</strong> s'applique aux <strong>agriculteurs percevant les aides de la politique agricole commune</strong>, au titre de la conditionnalité des aides. Pour eux, c'est une obligation, avec des sanctions à la clé.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Pour un particulier dans son jardin, ce n'est <strong>pas une interdiction</strong> : c'est une <strong>recommandation de l'Office français de la biodiversité</strong>, adressée aux collectivités, aux professionnels et aux particuliers. Personne ne viendra verbaliser une haie taillée en juin.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Mais attention : détruire le nid d'une espèce protégée — et la plupart des oiseaux de nos jardins le sont — reste une infraction, quelle que soit la date. La recommandation n'est pas contraignante ; ce qu'elle protège l'est.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>La vraie raison d'attendre</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Au printemps, une haie n'est pas seulement en feuilles : elle est habitée. Les nichées se tiennent au cœur du feuillage, invisibles depuis l'extérieur, et un passage de taille-haie détruit ce qu'on n'a pas vu. Une fois la nichée envolée, la même taille ne coûte rien à personne.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>L'arbre, lui, a ses propres raisons. Une coupe faite en pleine montée de sève cicatrise moins bien et fatigue le sujet au pire moment de son année.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Le calendrier, simplement</h2><!-- /wp:heading -->\n\n<!-- wp:list --><ul><li><strong>Septembre à février</strong> : la bonne période pour la taille des haies. Rien ne niche, la plante est au repos.</li><li><strong>Repos végétatif, hors gel</strong> : la fenêtre classique pour l'élagage des arbres à feuilles caduques, quand la charpente est visible et que l'arbre ne dépense rien.</li><li><strong>16 mars au 15 août</strong> : on s'abstient si on peut.</li></ul><!-- /wp:list -->\n\n<!-- wp:heading --><h2>Et si c'est dangereux ?</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Une branche fendue au-dessus d'une toiture, un arbre déraciné après un coup de vent : <strong>la sécurité passe avant le calendrier</strong>, et il n'a jamais été question d'attendre septembre pour écarter un danger. On intervient, en limitant la coupe à ce qui menace. C'est exactement ce que couvrent nos interventions d'urgence, toute l'année.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><em>Sources : conditionnalité des aides PAC (BCAE 8) ; recommandations de l'Office français de la biodiversité ; protection des espèces au code de l'environnement.</em></p><!-- /wp:paragraph -->",
			),

			'pourquoi-ne-pas-eteter-un-arbre' => array(
				'titre'   => 'Pourquoi l\'étêtage abîme un arbre — et ce qu\'on fait à la place',
				'extrait' => 'Couper un arbre à mi-hauteur paraît régler le problème. En réalité ça le déplace de quelques années, et ça l\'aggrave.',
				'image'   => 'chantiers/reduction-couronne-grimpeur.webp',
				'texte'   => "<!-- wp:paragraph --><p>Un arbre devenu trop grand, et la tentation est simple : on coupe en haut. C'est rapide, c'est visible, c'est moins cher sur le moment. C'est aussi la pire chose qu'on puisse faire à un arbre, et voici pourquoi — sans jargon.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Ce qui se passe après la coupe</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Un arbre étêté ne renonce pas : il réagit. Il repart en <strong>gourmands</strong>, des rejets nombreux et vigoureux qui poussent vite. Le problème, c'est qu'ils ne s'ancrent pas comme une branche normale : ils partent d'une couche superficielle, en amas, autour d'une plaie. Ils sont donc <strong>plus fragiles que les branches qu'on vient d'enlever</strong>.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Trois ou quatre ans plus tard, l'arbre a retrouvé sa hauteur, en plus dense, en plus lourd — et avec des attaches qui lâchent au vent. Le danger qu'on croyait supprimer est revenu, en pire.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>La plaie qui ne se referme pas</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Un arbre ne cicatrise pas comme nous : il ne répare pas le bois blessé, il le <strong>cloisonne</strong> en isolant la zone atteinte. Une petite coupe, faite au bon endroit, se referme en quelques saisons. Une section de tronc laissée à nu est trop large pour être recouverte : elle reste une porte ouverte aux champignons, qui s'installent et creusent le tronc de l'intérieur, pendant des années, sans que rien ne se voie d'en bas.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Ce qu'on fait à la place</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Réduire un arbre est possible — c'est la manière qui change. On coupe <strong>au-dessus d'un tirage</strong>, c'est-à-dire d'une branche assez forte pour prendre la relève et continuer à alimenter la partie conservée. La coupe est petite, l'arbre garde une structure, et il ne part pas en gourmands.</p><!-- /wp:paragraph -->\n\n<!-- wp:list --><ul><li><strong>Éclaircie</strong> : on retire du bois mort et on allège le houppier pour laisser passer le vent, plutôt que de le raccourcir.</li><li><strong>Réduction douce</strong> : on diminue l'encombrement par étapes, en respectant la silhouette.</li><li><strong>Haubanage</strong> : quand une fourche est fragile, on la soulage au lieu de couper.</li><li><strong>Abattage</strong> : quand l'arbre est condamné ou trop dangereux, on le dit franchement. Un arbre qu'on ne peut pas sauver vaut mieux qu'un arbre massacré qu'on gardera dix ans en le redoutant.</li></ul><!-- /wp:list -->\n\n<!-- wp:heading --><h2>Le vrai calcul</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Un étêtage coûte moins cher qu'une réduction bien menée. Mais il faut y revenir tous les trois ou quatre ans, sur un arbre de plus en plus dangereux, jusqu'à l'abattage qu'on aurait pu éviter. Sur dix ans, c'est l'intervention la plus chère des deux — et l'arbre est perdu.</p><!-- /wp:paragraph -->",
			),
		);
	}
}

if ( ! function_exists( 'lae_articles_semer' ) ) {
	function lae_articles_semer() {

		$ids = array();

		foreach ( lae_articles_livres() as $slug => $a ) {

			$existe = get_posts( array(
				'post_type'      => 'post',
				'post_status'    => 'any',
				'name'           => $slug,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			) );
			if ( $existe ) {
				continue;   // jamais par-dessus un article du client
			}

			$id = wp_insert_post( array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $a['titre'],
				'post_name'    => $slug,
				'post_excerpt' => $a['extrait'],
				'post_content' => $a['texte'],
			), true );

			if ( is_wp_error( $id ) ) {
				continue;
			}
			$ids[] = (int) $id;

			if ( function_exists( 'lae_chantier_importe_image' ) ) {
				$att = lae_chantier_importe_image( $a['image'], $a['titre'] );
				if ( $att ) {
					set_post_thumbnail( $id, $att );
				}
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'lae_articles_rattrapage' ) ) {
	function lae_articles_rattrapage() {

		if ( get_option( 'lae_articles_semes' ) ) {
			return;
		}
		update_option( 'lae_articles_semes', 1 );   // verrou posé AVANT le travail

		lae_articles_semer();

		/* Une page « Conseils » porte la liste des articles, et entre dans les
		   menus déjà en place. Sans elle, le blog n'existe qu'à une URL que
		   personne ne devine. */
		$page = get_page_by_path( 'conseils' );
		if ( ! $page ) {
			$pid = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Conseils',
				'post_name'    => 'conseils',
				'post_excerpt' => 'Ce qu\'il faut savoir avant de tailler, d\'abattre ou de planter : le droit, les périodes, et les gestes qui abîment un arbre.',
				'post_content' => '',
			), true );
			if ( is_wp_error( $pid ) ) {
				return;
			}
			$page = get_post( $pid );
		}

		// La page devient la liste des articles de WordPress.
		if ( ! get_option( 'page_for_posts' ) ) {
			update_option( 'page_for_posts', (int) $page->ID );
		}

		$emplacements = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( array( 'principal', 'pied' ) as $cle ) {
			if ( empty( $emplacements[ $cle ] ) ) {
				continue;
			}
			$menu = wp_get_nav_menu_object( $emplacements[ $cle ] );
			if ( ! $menu ) {
				continue;
			}
			foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
				if ( 'post_type' === $item->type && (int) $item->object_id === (int) $page->ID ) {
					continue 2;   // déjà au menu
				}
			}
			wp_update_nav_menu_item( $menu->term_id, 0, array(
				'menu-item-title'     => 'Conseils',
				'menu-item-object'    => 'page',
				'menu-item-object-id' => (int) $page->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
		}
	}
}
add_action( 'init', 'lae_articles_rattrapage', 23 );
add_action( 'admin_init', 'lae_articles_rattrapage', 14 );
