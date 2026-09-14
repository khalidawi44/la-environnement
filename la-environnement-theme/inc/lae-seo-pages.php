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
	if ( is_page() ) {
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
