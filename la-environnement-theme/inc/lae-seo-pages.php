<?php
/**
 * Titres et descriptions écrits, page par page.
 *
 * ═══════════════════════════════════════════════════════════════════
 * POURQUOI CE FICHIER EXISTE — audit SEO du 14/09, constats mesurés
 * sur le HTML réellement servi, pas sur le code.
 *
 * 1. LE SITE NE DISAIT NULLE PART OÙ IL TRAVAILLE. Le mot « Vertou »
 *    apparaissait ZÉRO fois dans le corps des neuf pages — y compris
 *    dans les 2 747 mots de l'accueil — et « Loire-Atlantique »
 *    n'existait que dans les données structurées. Aucun des neuf
 *    `<title>` ne contenait de commune. Sur un domaine qui s'appelle
 *    elagage-vertou.fr, c'est le signal le plus facile à donner et il
 *    n'était donné nulle part.
 *
 * 2. LE TITRE DE L'ONGLET ÉTAIT LE TITRE DE LA PAGE. Aucun filtre ne
 *    séparait les deux : impossible d'écrire un titre de résultat
 *    Google riche en mots-clés sans dénaturer le grand titre visible.
 *    D'où ce filtre `document_title_parts`, qui les découple.
 *
 * 3. CINQ DESCRIPTIONS SUR NEUF ÉTAIENT DES TRONCATURES AUTOMATIQUES
 *    à 200 caractères, coupées en plein mot : « …d'une mauvaise
 *    inter… », « …par un f… », « …Le tarif es… ». Deux paires de pages
 *    partageaient en plus la même description au caractère près
 *    (accueil/conseils, prestations/réalisations).
 *
 * CE QUE CE FICHIER NE FAIT PAS : inventer. Chaque titre et chaque
 * description ci-dessous ne dit que ce que la page dit déjà — le
 * métier, la ville, la façon de travailler. Aucun chiffre, aucune
 * ancienneté, aucun superlatif, aucun montant. L'entreprise travaille
 * au forfait après visite : aucun prix ne peut être annoncé, et aucune
 * de ces descriptions n'en annonce.
 *
 * LONGUEURS VISÉES : ~60 caractères pour le titre (seuil de coupe de
 * Google ≈ 600 px), 120 à 155 pour la description (≈ 920 px). Chaque
 * valeur ci-dessous a été comptée.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * La table des titres et descriptions, par clé de contexte.
 *
 * La clé est le slug de la page, ou un mot réservé pour les contextes
 * qui n'en ont pas (`front`, `blog`, `archive_prestation`…). Filtrable,
 * comme le reste du thème, pour qu'une reprise n'oblige pas à toucher
 * au code.
 *
 * @return array<string, array{titre:string, desc:string}>
 */
function lae_seo_pages() {
	return apply_filters( 'lae_seo_pages', array(

		'front' => array(
			'titre' => 'Élagueur à Vertou (44) : élagage, abattage, jardin',
			'desc'  => "Élagage en grimpe, abattage, démontage par câble, création et entretien de jardin. Vertou, Nantes Sud, 44. Urgences 24 h/24.",
		),

		'urgences' => array(
			/* La page la plus rentable du site : quelqu'un dont un arbre
			   vient de tomber ne compare pas, il appelle le premier qui
			   a l'air joignable. Le titre doit donc dire l'urgence, la
			   disponibilité et la ville en moins de 60 caractères. */
			'titre' => 'Urgence arbre tombé à Vertou — élagueur 24 h/24',
			'desc'  => "Arbre tombé, branche fendue sur une toiture, accès bloqué : on décroche 24 h/24 et on vient le jour même à Vertou et autour.",
		),

		'tarifs' => array(
			/* C'est la seule page du site qui contienne déjà « prix » et
			   « Nantes », et la mieux écrite des neuf (811 mots, un
			   comparatif sourcé et daté). Elle était invisible sur ces
			   requêtes faute d'un titre qui les porte. */
			'titre' => 'Prix d\'un élagage ou d\'un abattage à Vertou et Nantes',
			'desc'  => "Comment se chiffre un élagage ou un abattage : forfait après visite, repères du marché, réduction selon vos revenus.",
		),

		'a-propos' => array(
			'titre' => 'Élagueur grimpeur à Vertou : notre façon de travailler',
			'desc'  => "On regarde avant de couper, on coupe le moins possible, on travaille à la corde, le terrain est rendu net. Vertou (44).",
		),

		'contact' => array(
			'titre' => 'Contact élagueur Vertou (44) — devis après visite',
			'desc'  => "Dites-nous l'arbre, la commune et ce qui vous inquiète. On rappelle pour convenir d'une visite : c'est là que le devis se fait.",
		),

		'mentions-legales' => array(
			'titre' => 'Mentions légales — L.A Environnement, Vertou (44)',
			'desc'  => "Éditeur, hébergeur, propriété intellectuelle et données personnelles du site de L.A Environnement, Vertou (44120).",
		),

		/* La page des articles. Elle héritait mot pour mot de la
		   description de l'accueil, parce que `is_home()` et
		   `is_front_page()` étaient traités ensemble. */
		'blog' => array(
			'titre' => 'Conseils d\'élagueur : tailler, planter, abattre (44)',
			'desc'  => "Distances de plantation, périodes de taille, gestes qui abîment un arbre : les conseils d'un élagueur installé à Vertou (44).",
		),

		/* Les deux archives partageaient la même description de 40
		   caractères — la simple accroche du site, sans verbe ni ville. */
		'archive_prestation' => array(
			'titre' => 'Élagage, abattage, jardin : nos prestations à Vertou',
			'desc'  => "Élagage en grimpe, abattage, haubanage, création et entretien de jardin, broyage : six prestations. Vertou et Nantes Sud.",
		),

		'archive_realisation' => array(
			'titre' => 'Chantiers d\'élagage et d\'abattage à Vertou et en 44',
			'desc'  => "Avant et après de chantiers réels d'élagage et d'abattage, à Vertou et autour de Nantes. Photos prises sur place.",
		),

		/* ─────────────────────────────────────────────────────────────
		   LES 11 PAGES DE DÉTAIL — ajoutées le 14/09 après ré-audit.

		   Elles n'avaient ni titre ni description écrits : le filtre ne
		   couvrait que les 9 pages ci-dessus et laissait passer le reste
		   tel quel. Mesuré en ligne : « Élagage en grimpe – L.A
		   Environnement » dépensait 22 de ses 37 caractères en suffixe de
		   marque et ne nommait aucune commune ; deux titres d'article
		   dépassaient 60 caractères et étaient coupés dans Google ; les
		   six descriptions de prestation faisaient 66 à 90 caractères là
		   où la zone utile en accepte 155 — entre un tiers et la moitié
		   de l'extrait laissé vide, sur les six pages qui portent
		   l'intention d'achat.

		   Même discipline que ci-dessus : rien d'inventé. Chaque phrase
		   reprend ce que la page dit déjà (le texte de la prestation, le
		   récit du chantier, le fond de l'article). Aucun prix, aucune
		   ancienneté, aucun superlatif. ───────────────────────────── */

		/* Les six prestations. */
		'elagage-en-grimpe' => array(
			'titre' => 'Élagage en grimpe à Vertou (44) : taille douce d\'arbre',
			'desc'  => "Éclaircie, réduction de couronne, bois mort : on monte à la corde, on coupe au bourrelet, jamais en étêtage. Vertou et Nantes Sud.",
		),
		'abattage-et-demontage' => array(
			'titre' => 'Abattage et démontage d\'arbre par câble — Vertou (44)',
			'desc'  => "Abattage direct quand la place le permet, démontage par câble quand elle ne le permet pas : chaque pièce descend en rétention. Vertou (44).",
		),
		'haubanage-et-securisation' => array(
			'titre' => 'Haubanage d\'arbre à Vertou (44) : tenir sans abattre',
			'desc'  => "Fourche à écorce incluse, charpentière fissurée : un arbre fragilisé se haubane et se surveille plutôt qu'il ne s'abat. Vertou (44).",
		),
		'creation-de-jardin' => array(
			'titre' => 'Création de jardin à Vertou (44) : plantation, gazon',
			'desc'  => "Le sol, l'exposition, l'eau : un jardin se dessine avant de se planter. Massifs, haies, engazonnement, arbres plantés pour durer.",
		),
		'entretien-de-jardin' => array(
			'titre' => 'Entretien de jardin à Vertou (44) : taille, tonte',
			'desc'  => "Taille des haies au bon moment, tonte, débroussaillage, désherbage manuel, feuilles. Au coup par coup ou par passages convenus (44).",
		),
		'evacuation-et-broyage' => array(
			'titre' => 'Broyage et évacuation de branches à Vertou (44)',
			'desc'  => "Branches broyées sur place en paillage, ou rémanents chargés et évacués. Allées dégagées, sciure ramassée, terrain rendu net.",
		),

		'pose-de-gazon-synthetique' => array(
			'titre' => 'Pose de gazon synthétique à Vertou (44)',
			'desc'  => "Là où l'herbe ne tient pas — ombre, sol tassé, passage — une surface verte toute l'année, sans tonte. Sol préparé avant la pose.",
		),

		/* Les deux chantiers. Ce sont des photos réelles prises sur
		   place : la description le dit, sans en rajouter. */
		'demontage-arbres-abri-jardin' => array(
			'titre' => 'Démontage de cinq arbres au-dessus d\'un jardin (44)',
			'desc'  => "Cinq sujets démontés section par section au-dessus d'un terrain occupé, sans zone de chute possible, puis le terrain rendu net.",
		),
		'broyage-sur-place-mur-mitoyen' => array(
			'titre' => 'Conifères réduits le long d\'un mur mitoyen (44)',
			'desc'  => "Une rangée de conifères réduite au ras d'un mur mitoyen, sans recul pour travailler, avec broyage des branches directement sur place.",
		),

		'gazon-synthetique-autour-olivier' => array(
			'titre' => 'Gazon synthétique posé autour d\'un olivier (44)',
			'desc'  => "Un jardin en terre battue où l'herbe ne tenait plus, refait en gazon synthétique — sauf au pied de l'olivier, laissé en terre.",
		),

		/* Les trois articles. Les deux premiers titres dépassaient 60
		   caractères une fois le suffixe de marque ajouté, et étaient
		   coupés dans les résultats. */
		'distance-plantation-arbre-limite-propriete' => array(
			'titre' => 'Distance de plantation d\'un arbre : ce que dit la loi',
			'desc'  => "50 cm, 2 m, et la prescription trentenaire que presque personne ne mentionne : ce que disent les articles 671, 672 et 673 du code civil.",
		),
		'quand-tailler-haie-elaguer-arbre' => array(
			'titre' => 'Quand tailler une haie — et quand surtout s\'abstenir',
			'desc'  => "Du 16 mars au 15 août : une interdiction pour les agriculteurs aidés, une recommandation pour vous. La nuance est réelle, la raison meilleure.",
		),
		'pourquoi-ne-pas-eteter-un-arbre' => array(
			'titre' => 'Pourquoi l\'étêtage abîme un arbre, et quoi faire sinon',
			'desc'  => "Couper à mi-hauteur déplace le problème de trois ans et l'aggrave : gourmands mal accrochés, plaie qui ne se referme jamais.",
		),
	) );
}

/**
 * La clé de contexte de la page en cours, ou '' si aucune ne s'applique.
 *
 * L'ordre des tests compte : `is_home()` doit être écarté de
 * `is_front_page()`, sinon la page des articles se prend pour l'accueil
 * — c'est exactement le défaut qui donnait à /conseils/ la description
 * ET l'URL de partage de la page d'accueil.
 *
 * @return string
 */
function lae_seo_page_cle() {
	if ( is_front_page() ) {
		return 'front';
	}
	if ( is_home() ) {
		return 'blog';
	}
	if ( is_post_type_archive( 'lae_prestation' ) ) {
		return 'archive_prestation';
	}
	if ( is_post_type_archive( 'lae_realisation' ) ) {
		return 'archive_realisation';
	}
	/* Toute page singuliere, pas seulement les `page` : les six
	   prestations, les deux chantiers et les trois articles sont des
	   contenus singuliers eux aussi, et c'est eux qui portent les
	   requetes commerciales. La cle est le slug ; la table ne repond
	   que pour les slugs qu'elle connait, le reste garde le
	   comportement d'origine, suffixe de marque compris. */
	if ( is_singular() ) {
		$p = get_post();
		return $p ? $p->post_name : '';
	}
	return '';
}

/** Le couple titre/description écrit pour la page en cours, ou array(). */
function lae_seo_page() {
	$cle   = lae_seo_page_cle();
	$table = lae_seo_pages();
	return ( $cle && isset( $table[ $cle ] ) ) ? $table[ $cle ] : array();
}

/**
 * Le titre de l'onglet et du résultat Google — découplé du <h1>.
 *
 * On remplace la partie « titre » et on RETIRE le suffixe du site :
 * « – L.A Environnement » mangeait jusqu'à 22 des 60 caractères
 * disponibles, sur des titres qui n'en utilisaient parfois que 27
 * (« Contact – L.A Environnement »). La marque est déjà dans le nom de
 * domaine affiché sous le titre ; la répéter coûte plus qu'elle ne
 * rapporte. Les pages sans entrée dans la table gardent le
 * comportement d'origine, suffixe compris.
 */
add_filter( 'document_title_parts', function ( $parts ) {
	$page = lae_seo_page();
	if ( ! $page ) {
		return $parts;
	}
	$parts['title'] = $page['titre'];
	unset( $parts['site'], $parts['tagline'] );
	return $parts;
}, 20 );

/**
 * Une balise canonique sur les archives.
 *
 * WordPress n'émet `rel="canonical"` que sur les pages singulières :
 * vérifié sur le site servi, /conseils/, /prestations/ et
 * /realisations/ n'en avaient aucune. Ce sont précisément les trois
 * pages qui souffraient aussi d'une description dupliquée — donc les
 * trois où un doublon d'URL avec paramètre (?fbclid=, ?utm_source=)
 * n'avait aucun garde-fou.
 */
add_action( 'wp_head', function () {
	if ( is_singular() || is_404() || is_feed() ) {
		return;   // WordPress s'en charge déjà
	}
	$url = '';
	if ( is_home() && ! is_front_page() ) {
		$page = get_option( 'page_for_posts' );
		$url  = $page ? get_permalink( $page ) : '';
	} elseif ( is_post_type_archive() ) {
		$type = get_query_var( 'post_type' );
		$url  = get_post_type_archive_link( is_array( $type ) ? reset( $type ) : $type );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$terme = get_queried_object();
		if ( $terme && ! is_wp_error( $terme ) && isset( $terme->term_id ) ) {
			$lien = get_term_link( $terme );
			$url  = is_wp_error( $lien ) ? '' : $lien;
		}
	}
	if ( $url ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
}, 3 );
