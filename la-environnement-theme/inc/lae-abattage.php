<?php
/**
 * Autorisation d'abattage — correction d'un article en ligne, et publication d'un nouveau.
 *
 * ═══════════════════════════════════════════════════════════════════
 * 1. UNE PHRASE FAUSSE EST EN LIGNE, ET ELLE EST SIGNÉE PAR UN PRO.
 *
 * L'article « Quand tailler — et quand surtout ne pas tailler » affirme :
 *
 *     « Personne ne viendra verbaliser une haie taillée en juin. »
 *
 * La préfecture de Loire-Atlantique écrit exactement le contraire : la
 * destruction d'oiseaux, de chauves-souris et d'insectes « est un délit et
 * peut entraîner une verbalisation ». L'art. 3 de l'arrêté du 29 octobre
 * 2009 interdit « en tout temps » la destruction des nids et des œufs des
 * oiseaux protégés, et l'art. L. 415-3 du code de l'environnement punit ces
 * faits commis « intentionnellement OU PAR NÉGLIGENCE GRAVE ». Tailler une
 * haie en pleine nidification sans l'avoir inspectée entre dans cette
 * définition.
 *
 * Une phrase qui invite implicitement à ne pas s'inquiéter, publiée par une
 * entreprise d'élagage, est exactement ce qu'on peut lui opposer. Elle part.
 *
 * ON NE RÉÉCRIT QUE CE QU'ON A ÉCRIT SOI-MÊME : le remplacement n'a lieu
 * que si la phrase exacte est encore présente. Si Anthony a retouché ce
 * paragraphe, la chaîne ne correspond plus et on ne touche à rien.
 *
 * 2. UN ARTICLE MANQUAIT, et c'est le plus commercial des trois sujets
 * repérés : on ne cherche « faut-il une autorisation pour abattre un
 * arbre » que si on a déjà décidé d'abattre.
 *
 * Tout ce qu'il affirme a été vérifié texte par texte le 22/09, sources
 * consultées et datées. Les points restés incertains ne sont pas tranchés
 * dans l'article : ils sont formulés comme des vérifications à faire.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'lae_abattage_rattrapage' ) ) {

	/** Retire la phrase fausse, et complète les sources. */
	function lae_abattage_corrige_taille() {

		$page = get_page_by_path( 'quand-tailler-haie-elaguer-arbre', OBJECT, 'post' );
		if ( ! $page ) {
			return 'article introuvable';
		}

		$avant = (string) $page->post_content;
		$apres = $avant;

		$faux = 'Personne ne viendra verbaliser une haie taillée en juin.';
		$vrai = 'Aucune sanction n\'est attachée à la date elle-même — mais ce que cette recommandation protège, lui, est protégé par la loi. C\'est le paragraphe suivant qui compte.';
		if ( false !== strpos( $apres, $faux ) ) {
			$apres = str_replace( $faux, $vrai, $apres );
		}

		/* La source la plus forte pour cet article n'y était pas : c'est
		   l'État DANS LE DÉPARTEMENT d'Anthony qui emploie lui-même le mot
		   « préconise » pour les particuliers. Plus solide qu'un renvoi
		   générique à l'OFB. */
		$src_avant = 'Sources : conditionnalité des aides PAC (BCAE 8) ; recommandations de l\'Office français de la biodiversité ; protection des espèces au code de l\'environnement.';
		$src_apres = 'Sources : conditionnalité des aides PAC (BCAE 8, fiche technique du ministère de l\'Agriculture) ; préfecture de Loire-Atlantique, « Ne pas tailler les haies ni élaguer les arbres après le 16 mars », qui emploie le verbe « préconise » pour les collectivités, les professionnels et les particuliers ; arrêté du 29 octobre 2009 fixant la liste des oiseaux protégés, article 3, qui interdit « en tout temps » la destruction des nids et des œufs ; article L. 415-3 du code de l\'environnement pour les sanctions. Vérifié le 22 septembre 2026.';
		if ( false !== strpos( $apres, $src_avant ) ) {
			$apres = str_replace( $src_avant, $src_apres, $apres );
		}

		if ( $apres === $avant ) {
			return 'inchangé (texte déjà modifié à la main)';
		}

		wp_update_post( array( 'ID' => (int) $page->ID, 'post_content' => $apres ) );
		return 'corrigé';
	}

	/** Publie l'article sur l'autorisation d'abattage. */
	function lae_abattage_publie() {

		$slug = 'autorisation-abattre-arbre';
		if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
			return 0;
		}

		$p = static function ( $html ) {
			return "<!-- wp:paragraph --><p>{$html}</p><!-- /wp:paragraph -->\n\n";
		};
		$h = static function ( $t ) {
			return "<!-- wp:heading --><h2>{$t}</h2><!-- /wp:heading -->\n\n";
		};

		$texte  = $p( "C'est la première question qu'on nous pose quand un arbre doit tomber, et la réponse courante — « il faut une autorisation » — est fausse la plupart du temps. Il n'existe pas, en droit français, de régime général qui soumettrait l'abattage d'un arbre à autorisation. Ce sont des <strong>exceptions</strong> qui créent l'obligation, et elles ne dépendent pas de l'arbre : elles dépendent de l'endroit où il est planté." );
		$texte .= $p( "Autrement dit : <strong>la question n'est pas de savoir si l'arbre est à vous, mais de savoir sur quelle parcelle il est posé.</strong> Et ça, ça se lit sur une carte, gratuitement, en deux minutes." );

		$texte .= $h( 'La vérification qui répond à tout, en deux minutes' );
		$texte .= $p( "À Vertou et dans les communes de Nantes Métropole, deux dispositifs du plan local d'urbanisme métropolitain (le PLUm) protègent des arbres : l'<strong>Espace Boisé Classé (EBC)</strong> et l'<strong>Espace Paysager à Protéger (EPP)</strong>. Si votre arbre est dans l'un des deux, une <strong>déclaration préalable</strong> est obligatoire avant toute coupe ou tout abattage." );
		$texte .= $p( "Pour le savoir, ouvrez la carte du PLUm de Nantes Métropole, tapez votre adresse dans la barre de recherche, repérez votre parcelle et regardez la légende. C'est la méthode que la Ville de Vertou indique elle-même dans sa fiche officielle. C'est gratuit, c'est public, et ça vous évite de découvrir le problème après la coupe." );
		$texte .= $p( "Si votre parcelle n'est ni en EBC ni en EPP, vous n'avez le plus souvent aucune démarche d'urbanisme à faire. Un doute subsiste pour les grands parcs privés, que le code de l'urbanisme mentionne sans les définir : dans ce cas, un appel au service urbanisme tranche en cinq minutes." );

		$texte .= $h( 'Ce que change une déclaration préalable' );
		$texte .= $p( "Ce n'est pas un refus déguisé, c'est un dossier. Il se dépose en mairie ou sur le guichet en ligne de Nantes Métropole, avec un plan de situation, un plan de masse, la description de l'arbre — essence, taille, âge approximatif, état sanitaire — des photos de près et de loin, et le calcul de la compensation : la commune applique un barème de valeur de l'arbre, et une replantation de valeur équivalente est attendue." );
		$texte .= $p( "En espace protégé, l'abattage reste possible <strong>en cas de mauvais état sanitaire ou de danger avéré</strong>. C'est précisément ce qu'un diagnostic sur place sert à établir, et c'est la partie du dossier que nous montons avec vous." );

		$texte .= $h( 'Les autres cas qui obligent, et qu\'on oublie' );
		$texte .= "<!-- wp:list --><ul>"
			. "<li><strong>Un alignement d'arbres le long d'une route ouverte à la circulation</strong> est protégé par l'article L. 350-3 du code de l'environnement. Le principe y est l'interdiction, la demande se fait auprès du préfet, et une compensation est obligatoire.</li>"
			. "<li><strong>Un site classé</strong> demande une autorisation spéciale ; <strong>un site inscrit</strong> impose d'informer l'administration quatre mois à l'avance.</li>"
			. "<li><strong>Les abords d'un monument historique</strong> — 500 mètres par défaut — font instruire le dossier avec l'avis de l'architecte des Bâtiments de France, et le délai passe à deux mois.</li>"
			. "<li><strong>Un lotissement</strong> peut avoir son propre cahier des charges. Les règles d'urbanisme d'un lotissement deviennent caduques au bout de dix ans, mais les clauses du cahier des charges, elles, restent un contrat entre voisins.</li>"
			. "</ul><!-- /wp:list -->\n\n";

		$texte .= $h( 'Et si l\'arbre menace de tomber ?' );
		$texte .= $p( "Le danger vous dispense de la déclaration, pas de la démonstration." );
		$texte .= $p( "Le code de l'urbanisme prévoit expressément que la déclaration préalable n'est pas requise pour l'enlèvement des <strong>arbres dangereux, des chablis et des bois morts</strong> (article R. 421-23-2). Pour un arbre d'alignement, la dispense existe aussi en cas de danger imminent pour les personnes, à charge d'informer le préfet sans délai." );
		$texte .= $p( "Mais « dangereux » est un constat de fait, et c'est à celui qui abat de pouvoir le prouver ensuite. Trois réflexes, qui ne sont pas des obligations légales mais qui vous protègent si on vous le reproche : <strong>des photos datées avant l'intervention</strong>, sous plusieurs angles ; <strong>un constat écrit du professionnel</strong> décrivant le désordre — fissure, déchaussement, champignon, basculement ; et <strong>un mail à la mairie le jour même</strong>. À Vertou, le service des espaces verts demande à être prévenu sans délai en cas de danger avéré, pour une évaluation rapide." );
		$texte .= $p( "Dernier point, et c'est là que beaucoup se trompent : la dispense ne couvre que ce qui menace. Abattre l'arbre entier quand seule une charpentière est fendue en sort." );

		$texte .= $h( 'Une règle qui ne dépend d\'aucune carte : les oiseaux' );
		$texte .= $p( "Quelle que soit la parcelle, quelle que soit la saison, la destruction des nids et des œufs d'oiseaux protégés est interdite « en tout temps » — c'est le mot du texte. La plupart des oiseaux de nos jardins sont protégés, et la sanction prévue au code de l'environnement vise les faits commis intentionnellement <em>ou par négligence grave</em>." );
		$texte .= $p( "En clair : personne ne vous demandera d'autorisation pour la date, mais intervenir sans avoir regardé ce qu'il y a dans le feuillage est un risque réel. C'est aussi pour ça qu'on monte voir avant de couper." );

		$texte .= $h( 'Et les branches qui débordent sur la rue ?' );
		$texte .= $p( "Beaucoup de riverains pensent que c'est à la mairie de s'en occuper. C'est l'inverse : <strong>l'arbre planté chez vous qui déborde sur la voie publique est à votre charge</strong>, et il doit être coupé à l'aplomb de la limite. La collectivité n'entretient que ses propres arbres. En cas de négligence, elle peut mettre en demeure puis faire exécuter les travaux à vos frais, et une contravention est prévue." );
		$texte .= $p( "La ligne de partage est simple : ce n'est pas le côté où tombe la branche qui compte, c'est à qui appartient l'arbre." );

		$texte .= $h( 'Ce qu\'on fait, nous' );
		$texte .= $p( "On vient voir l'arbre avant de chiffrer quoi que ce soit. Si une déclaration est nécessaire, on vous le dit à ce moment-là — pas après — et on fournit ce qui relève de nous : l'essence, l'état sanitaire, les photos, la mesure du tronc. Le devis est écrit et gratuit, et il comprend tout : la coupe, l'évacuation ou le broyage, le terrain rendu net." );

		$texte .= $p( "<em>Sources consultées le 22 septembre 2026 : code de l'urbanisme, articles R. 421-23 et R. 421-23-2 ; code de l'environnement, articles L. 350-3, L. 341-1, L. 341-10, L. 411-1 et L. 415-3 ; arrêté du 29 octobre 2009 fixant la liste des oiseaux protégés ; code général des collectivités territoriales, article L. 2212-2-2 ; code de la voirie routière, articles L. 131-7 et suivants ; fiche « déclaration préalable pour la coupe et l'abattage d'arbres en EPP ou EBC » de la Ville de Vertou ; plan local d'urbanisme métropolitain de Nantes Métropole. Cet article donne un repère général et ne remplace pas l'avis d'un juriste sur une situation précise, ni la consultation du service urbanisme de votre commune.</em>" );

		$id = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => 'Faut-il une autorisation pour abattre un arbre chez soi ?',
			'post_name'    => $slug,
			'post_excerpt' => 'La réponse courante est fausse la plupart du temps. Ce qui décide, ce n\'est pas l\'arbre, c\'est la parcelle — et ça se vérifie gratuitement en deux minutes.',
			'post_content' => $texte,
		), true );

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		if ( function_exists( 'lae_chantier_importe_image' ) ) {
			$img = lae_chantier_importe_image( 'abattage-troncs.webp', 'Troncs préparés avant abattage' );
			if ( $img ) {
				set_post_thumbnail( $id, $img );
			}
		}

		return (int) $id;
	}

	function lae_abattage_rattrapage() {

		if ( get_option( 'lae_abattage_2026_09' ) ) {
			return;
		}
		update_option( 'lae_abattage_2026_09', 1, false );   // verrou AVANT le travail

		update_option( 'lae_abattage_journal', array(
			'correction' => lae_abattage_corrige_taille(),
			'article'    => lae_abattage_publie(),
			'date'       => current_time( 'mysql' ),
		), false );
	}
}

/* Après les articles livrés (priorité 21 dans lae-articles.php) : le nouvel
   article doit arriver une fois la catégorie et la page Conseils en place. */
add_action( 'init', 'lae_abattage_rattrapage', 26 );
add_action( 'admin_init', 'lae_abattage_rattrapage', 17 );

/* ═══════════════════════════════════════════════════════════════════
   L'IMAGE MANQUAIT — rattrapage du 22/09, quelques minutes après.

   Le premier passage a publié l'article sans vignette : j'avais écrit
   « chantiers/abattage-troncs.webp » alors que ce fichier est à la RACINE
   de assets/images/, pas dans le sous-dossier. `lae_chantier_importe_image()`
   rend 0 pour un fichier absent — silencieusement, par sécurité — donc
   `set_post_thumbnail()` n'a jamais été appelé et rien n'a signalé l'erreur.

   Constaté sur le site servi : l'article sortait sans image, ce qui se voit
   dans la liste de /conseils/ et prive l'aperçu de partage de sa vignette.

   Le verrou du premier rattrapage étant déjà posé en base, corriger le
   chemin ne suffit pas : il faut un second passage, avec son propre verrou.
   Il ne pose la vignette QUE si l'article n'en a pas déjà une — si Anthony
   en a choisi une entre-temps, on n'y touche pas.
   ═══════════════════════════════════════════════════════════════════ */
if ( ! function_exists( 'lae_abattage_vignette' ) ) {
	function lae_abattage_vignette() {

		if ( get_option( 'lae_abattage_vignette_faite' ) ) {
			return;
		}
		update_option( 'lae_abattage_vignette_faite', 1, false );   // verrou AVANT

		$article = get_page_by_path( 'autorisation-abattre-arbre', OBJECT, 'post' );
		if ( ! $article || has_post_thumbnail( $article->ID ) ) {
			return;
		}
		if ( ! function_exists( 'lae_chantier_importe_image' ) ) {
			return;
		}

		$img = lae_chantier_importe_image( 'abattage-troncs.webp', 'Troncs préparés avant abattage' );
		if ( $img ) {
			set_post_thumbnail( $article->ID, $img );
		}
	}
}
add_action( 'init', 'lae_abattage_vignette', 27 );
add_action( 'admin_init', 'lae_abattage_vignette', 18 );
