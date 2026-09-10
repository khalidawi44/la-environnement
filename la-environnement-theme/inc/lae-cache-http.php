<?php
/**
 * Durée de vie du cache HTTP — la cause racine des déploiements invisibles.
 *
 * CONSTAT DU 09/09. Les pages HTML partaient avec :
 *
 *     cache-control: public, max-age=604800     (7 jours)
 *
 * Il y a deux couches de cache devant le site : LiteSpeed côté serveur, et le
 * CDN Hostinger (`x-hcdn-cache-status`) devant lui. Cet en-tête disait aux deux
 * — et au navigateur du visiteur — de garder la page **une semaine**.
 *
 * Conséquences observées, toutes dues à ce seul en-tête :
 *   - un correctif déployé restait invisible jusqu'à 7 jours ;
 *   - chaque nœud du CDN gardait sa propre copie, donc deux visiteurs
 *     pouvaient voir deux versions différentes du site le même jour — et deux
 *     relevés successifs depuis la même machine se contredisaient ;
 *   - purger LiteSpeed ne suffisait pas : le CDN resservait sa copie.
 *
 * Une page HTML n'a rien à faire dans un cache de sept jours : son contenu
 * change à chaque publication. Les fichiers statiques, eux, gardent leur cache
 * long sans risque — le durcissement leur ajoute déjà une empreinte `?ver=`
 * qui change à chaque version du thème.
 *
 * On donne donc au HTML une durée courte, et on laisse le reste tranquille.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Marqueur de version sur la réponse.
 *
 * Toute la soirée du 09/09 a été perdue à confondre deux choses : la version
 * des FICHIERS sur le disque (lisible dans style.css) et la version qui a
 * réellement RENDU la page qu'on regarde. Entre les deux il y a deux caches,
 * et elles peuvent différer de plusieurs versions.
 *
 * Cet en-tête répond à la seule question qui compte : « la page que je tiens
 * dans les mains, quelle version l'a produite ? »
 *
 *     curl -sSI https://elagage-vertou.fr/ | grep -i x-lae-version
 *
 * Si elle est en retard sur style.css, c'est du cache — pas du code.
 */
add_action( 'send_headers', function () {
	if ( headers_sent() || ! defined( 'LAE_VERSION' ) ) return;
	header( 'X-LAE-Version: ' . LAE_VERSION );
}, 99 );

add_action( 'send_headers', function () {
	if ( is_admin() || headers_sent() ) return;
	if ( is_user_logged_in() ) return;          // l'admin ne doit jamais être mis en cache
	if ( is_feed() || is_robots() ) return;

	/*
	 * max-age=0            : le navigateur revalide à chaque visite.
	 * s-maxage=300         : les caches partagés (CDN, LiteSpeed) gardent la
	 *                        page 5 minutes — exactement le rythme de la sync.
	 * stale-while-revalidate : le visiteur reçoit la copie tiède pendant que le
	 *                        cache va chercher la nouvelle. Aucune attente.
	 */
	$duree = (int) apply_filters( 'lae_cache_html_secondes', 300 );
	header( 'Cache-Control: public, max-age=0, s-maxage=' . $duree . ', stale-while-revalidate=60', true );
	header_remove( 'Expires' );  // en-tête hérité, il contredirait le précédent
}, 100 );
