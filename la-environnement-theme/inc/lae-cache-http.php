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
	/* Reserve aux utilisateurs connectes depuis le 22/09. L'en-tete reste
	   l'outil de diagnostic « quelle version a rendu CETTE page » — il a
	   servi a trouver la panne du 21/09 — mais le publier en clair sur un
	   depot PUBLIC donnait a n'importe qui de quoi lire le diff entre deux
	   versions et cibler une faille. Le durcissement remplace justement le
	   ?ver= des CSS/JS par une empreinte pour cette raison : l'en-tete le
	   contredisait trois fichiers plus loin. Connecte, on garde l'outil. */
	if ( ! is_user_logged_in() ) return;
	header( 'X-LAE-Version: ' . LAE_VERSION );
}, 99 );

add_action( 'send_headers', function () {
	if ( is_admin() || headers_sent() ) return;
	if ( is_user_logged_in() ) return;          // l'admin ne doit jamais être mis en cache
	/* `is_robots()` a été RETIRÉ de cette exclusion le 14/09. Le robots.txt
	   sortait donc avec le TTL hérité du serveur — relevé en ligne :
	   `x-litespeed-cache-control: public,max-age=604665`, soit sept jours.
	   Une correction du robots.txt mettait une semaine à être vue de
	   Googlebot : exactement le problème que ce fichier existe pour régler.
	   Le flux RSS, lui, reste exclu : il porte ses propres en-têtes. */
	if ( is_feed() ) return;

	/* La page CONTACT ne va pas au cache partage. Elle porte un nonce
	   (lae_contact_nonce) qui vit 24 h ; la page vivait aussi 24 h en cache.
	   Les deux durees identiques se croisent forcement : passe l'expiration,
	   le visiteur recevait « votre session a expire », rechargeait, et le
	   cache lui reservait la MEME page avec le MEME nonce mort — formulaire
	   casse en boucle, donc une demande de devis perdue sans trace. Le nonce
	   mis en cache n'offrait de toute facon aucune protection CSRF reelle
	   (identique pour tous, lisible publiquement) : ce qui filtre les robots
	   ici, c'est le champ leurre et la limite par IP, pas lui. Cette page n'a
	   pas d'enjeu de performance comparable a l'accueil : on la sert fraiche. */
	if ( is_page( 'contact' ) ) {
		header( 'Cache-Control: private, no-store, max-age=0' );
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
		return;
	}

	/*
	 * max-age=0            : le navigateur revalide à chaque visite.
	 * s-maxage=300         : les caches partagés (CDN, LiteSpeed) gardent la
	 *                        page 5 minutes — exactement le rythme de la sync.
	 * stale-while-revalidate : le visiteur reçoit la copie tiède pendant que le
	 *                        cache va chercher la nouvelle. Aucune attente.
	 */
	/*
	 * 24 HEURES, ET NON PLUS 5 MINUTES — changé le 22/09, mesures à l'appui.
	 *
	 * Le TTL de 5 minutes datait du 09/09 et visait « le rythme de la sync ».
	 * Mais il ne protégeait personne : sur un site à faible trafic, la
	 * plupart des visiteurs arrivent après l'expiration et encaissent la
	 * génération complète. Mesuré à l'URL nue : l'accueil met 2,183 s sur un
	 * cache MISS contre 0,591 s sur un HIT — elle exécute trois WP_Query et
	 * émet 138 Ko de HTML. Le `stale-while-revalidate` n'amortit que les 60 s
	 * qui suivent l'expiration.
	 *
	 * CE QUI REND LE TTL LONG SÛR, et sans quoi il serait dangereux :
	 *   — `lae-purge-forcee.php` purge tout à chaque changement de version du
	 *     thème, par un appel sur nous-mêmes que le cache ne peut pas servir
	 *     de sa réserve ;
	 *   — et depuis le 22/09, il purge aussi à chaque publication, modification
	 *     ou suppression de contenu, à chaque enregistrement du
	 *     personnalisateur, de menu ou de taxonomie.
	 *
	 * Sans ce second filet, une correction d'Anthony resterait invisible
	 * vingt-quatre heures. Les deux vont ensemble : ne jamais rallonger ce
	 * TTL sans vérifier que les purges suivent.
	 */
	$duree = (int) apply_filters( 'lae_cache_html_secondes', DAY_IN_SECONDS );
	header( 'Cache-Control: public, max-age=0, s-maxage=' . $duree . ', stale-while-revalidate=60', true );
	header_remove( 'Expires' );  // en-tête hérité, il contredirait le précédent

	/*
	 * Et le TTL de LiteSpeed lui-même.
	 *
	 * CONSTAT DU 13/09. Le `Cache-Control` ci-dessus pilote les caches en AVAL
	 * — navigateur, CDN Hostinger. Il ne fixe pas le TTL propre du cache
	 * LiteSpeed, qui tournait à **une heure** : relevé `age: 3611` puis `3623`
	 * sur des réponses où le CDN annonçait pourtant `MISS`. La copie périmée
	 * ne venait donc pas du CDN mais de LiteSpeed, en amont.
	 *
	 * Conséquence : trois versions cohabitaient le même jour — 1.10.1, 1.10.2
	 * et 1.10.3 selon le nœud interrogé — alors que le déploiement était bon.
	 *
	 * La purge ne suffisait pas à rattraper ça, et pour une raison de fond :
	 * quand la sync tourne dans wp-cron.php, la purge est différée à la
	 * prochaine réponse de page — mais cette réponse-là est justement servie
	 * depuis le cache, donc PHP ne s'exécute pas et l'en-tête ne part jamais.
	 * Le cache empêchait la purge qui l'aurait vidé.
	 *
	 * `X-LiteSpeed-Cache-Control` fixe le TTL page par page et prime sur le
	 * `Cache-Control` standard (doc LiteSpeed). On ne dépend donc plus d'une
	 * purge qui réussit : au pire, la page est périmée 5 minutes.
	 */
	header( 'X-LiteSpeed-Cache-Control: public,max-age=' . $duree, true );
}, 100 );
