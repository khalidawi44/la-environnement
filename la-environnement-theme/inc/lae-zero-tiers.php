<?php
/**
 * Zéro ressource tierce — tenir la promesse écrite dans les mentions légales.
 *
 * ═══════════════════════════════════════════════════════════════════
 * LE PROBLÈME, ET IL EST JURIDIQUE AVANT D'ÊTRE TECHNIQUE.
 *
 * Les mentions légales du site affirment, noir sur blanc :
 *
 *   « Ce site ne dépose AUCUN cookie de mesure d'audience ni de
 *     publicité, et ne charge aucune police de caractères ni ressource
 *     hébergée par un tiers. Aucune bannière de consentement n'est donc
 *     nécessaire : il n'y a rien à consentir. »
 *
 * Or, relevé en ligne le 14/09 sur les neuf pages :
 *
 *   <link rel='dns-prefetch' href='//cdn-reach.hostinger.com' />
 *   <script defer src="https://cdn-reach.hostinger.com/js/embed.js"></script>
 *
 * Le plugin `hostinger-reach`, installé par l'hébergeur, charge un
 * script depuis un domaine tiers sur CHAQUE page — plus sa feuille de
 * style et son script de bloc, soit environ 6 ko servis partout pour un
 * bloc d'abonnement qui n'est présent sur aucune page.
 *
 * Une affirmation fausse dans des mentions légales n'est pas un détail
 * de performance : c'est le seul endroit du site où l'on s'engage
 * juridiquement. Deux issues possibles, et une seule est bonne — soit
 * on corrige la phrase, soit on la rend vraie. On la rend vraie : c'est
 * un argument commercial réel (aucun bandeau de cookies à cliquer), et
 * il ne coûte rien puisque le bloc d'abonnement n'est utilisé nulle part.
 *
 * CE QUE FAIT CE FICHIER : il retire les ressources du plugin, mais
 * SEULEMENT si la page n'utilise pas réellement son bloc. Si quelqu'un
 * pose un formulaire d'abonnement demain, la page qui le porte garde
 * tout ce qu'il lui faut — on ne casse pas une fonctionnalité, on cesse
 * de la charger là où elle ne sert pas.
 *
 * Le plugin peut aussi être désactivé purement et simplement depuis
 * l'admin : c'est plus propre, et ça n'est pas du ressort du thème.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * La page affiche-t-elle réellement un bloc d'abonnement Hostinger Reach ?
 *
 * On regarde le contenu de l'objet demandé, pas une supposition. Si le
 * bloc y est, on ne touche à rien.
 *
 * @return bool
 */
function lae_page_utilise_reach() {
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	if ( ! $post ) {
		return false;
	}
	return function_exists( 'has_block' ) && has_block( 'hostinger-reach/subscription', $post );
}

add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || lae_page_utilise_reach() ) {
		return;
	}
	foreach ( array( 'hostinger-reach-embed', 'hostinger-reach-subscription-block-view' ) as $script ) {
		wp_dequeue_script( $script );
		wp_deregister_script( $script );
	}
	foreach ( array( 'hostinger-reach-subscription-block', 'hostinger-reach-subscription-block-style' ) as $style ) {
		wp_dequeue_style( $style );
		wp_deregister_style( $style );
	}
}, 99 );

/**
 * Et l'indice de résolution DNS qui l'accompagne.
 *
 * WordPress ajoute `dns-prefetch` pour chaque domaine de script
 * enregistré. Le script retiré, l'indice reste — et il suffit à faire
 * partir une requête DNS vers le tiers, donc à révéler l'adresse IP du
 * visiteur à un domaine qu'il n'a pas demandé. Le retirer aussi n'est
 * pas du zèle : c'est la moitié du problème.
 */
/*
 * LISTE BLANCHE, ET NON LISTE NOIRE — corrigé le 22/09.
 *
 * CE QUI S'EST PASSÉ. Ce filtre a été écrit le 14/09 contre un domaine
 * précis, `cdn-reach.hostinger.com`. Une liste noire d'un seul nom ne
 * protège que contre ce nom. Un audit du 22/09 a trouvé, sur les 22 pages
 * du site :
 *
 *     <link rel='dns-prefetch' href='//www.googletagmanager.com' />
 *
 * Aucun script Google ne se charge — seul l'indice DNS restait. Mais il
 * suffit à faire partir une requête vers Google à chaque affichage, donc à
 * révéler l'adresse IP du visiteur à un domaine qu'il n'a pas demandé.
 * C'est exactement le raisonnement écrit six lignes plus haut, et il
 * s'appliquait à un seul domaine.
 *
 * Ce n'est pas qu'un détail technique : les mentions légales du site
 * AFFIRMENT qu'il « ne charge aucune ressource hébergée par un tiers » et
 * qu'« aucune bannière de consentement n'est nécessaire ». Un engagement
 * écrit ne peut pas dépendre d'une liste noire qu'on oublie de tenir.
 *
 * On inverse donc : ne survivent que les indices pointant vers NOTRE
 * domaine. Tout le reste tombe, y compris ce qui n'existe pas encore. Une
 * liste blanche ne se périme pas.
 *
 * ATTENTION, CE FILTRE NE TRAITE QUE LE SYMPTÔME : quelque chose a ajouté
 * ce domaine (extension d'analyse, outil de l'hébergeur, réglage de
 * LiteSpeed). Trouver et désactiver la source reste à faire côté
 * administration — signalé à Fabrice.
 */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'dns-prefetch' !== $relation && 'preconnect' !== $relation ) {
		return $urls;
	}

	$nous = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$nous = is_string( $nous ) ? strtolower( ltrim( $nous, '.' ) ) : '';

	return array_values( array_filter( $urls, static function ( $url ) use ( $nous ) {

		$href = is_array( $url ) && isset( $url['href'] ) ? $url['href'] : $url;
		if ( ! is_string( $href ) || '' === trim( $href ) ) {
			return false;   // dans le doute, on ne laisse pas passer
		}

		/* WordPress émet souvent « //exemple.com » sans protocole :
		   wp_parse_url() le comprend, mais pas un chemin relatif seul. */
		$hote = wp_parse_url( 0 === strpos( $href, '//' ) ? 'https:' . $href : $href, PHP_URL_HOST );
		if ( ! $hote ) {
			return true;    // pas d'hôte du tout = ressource locale
		}

		$hote = strtolower( ltrim( $hote, '.' ) );
		return '' === $nous || $hote === $nous || substr( $hote, - ( strlen( $nous ) + 1 ) ) === '.' . $nous;
	} ) );
}, 20, 2 );
