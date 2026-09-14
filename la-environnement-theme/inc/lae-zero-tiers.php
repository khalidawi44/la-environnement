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
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'dns-prefetch' !== $relation && 'preconnect' !== $relation ) {
		return $urls;
	}
	return array_values( array_filter( $urls, static function ( $url ) {
		$href = is_array( $url ) && isset( $url['href'] ) ? $url['href'] : $url;
		return ! is_string( $href ) || false === strpos( $href, 'cdn-reach.hostinger.com' );
	} ) );
}, 20, 2 );
