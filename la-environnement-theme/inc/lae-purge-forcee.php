<?php
/**
 * Purge forcée du cache LiteSpeed — la panne qui rend un déploiement invisible.
 *
 * ═══════════════════════════════════════════════════════════════════
 * LE CONSTAT DU 21/09, ET IL EST SÉVÈRE.
 *
 * Le numéro de téléphone de l'entreprise a changé en v1.31.0. Toutes les
 * vérifications disaient « 20 pages sur 20 à jour ». Fabrice, lui, voyait
 * encore l'ancien numéro. Il avait raison.
 *
 * La vérification était fausse : chaque appel portait `?cb=<aléatoire>` et
 * `Cache-Control: no-cache`, donc chaque appel CONTOURNAIT le cache —
 * c'est-à-dire exactement la couche qui était en panne. Mesuré ensuite sur
 * l'URL nue, telle qu'un visiteur la reçoit :
 *
 *     x-lae-version: 1.30.0   x-litespeed-cache: hit   age: 528898
 *     ancien numéro : 8 occurrences   nouveau : 0
 *
 * Une page de six jours, servie à tout le monde, avec le mauvais numéro.
 *
 * POURQUOI LA PURGE EXISTANTE NE SUFFISAIT PAS. `purge_caches()` dans
 * lae-github-sync.php fait déjà ce qu'il faut, mais il tourne dans
 * wp-cron.php où les en-têtes sont souvent déjà partis. Il arme alors une
 * purge différée (`lae_purge_en_attente`) que `send_headers` enverra « à la
 * prochaine réponse de page ».
 *
 * Sauf que la prochaine réponse de page est servie DEPUIS LE CACHE. PHP ne
 * s'exécute pas, `send_headers` ne se déclenche pas, l'en-tête ne part
 * jamais. Le cache empêche la purge qui l'aurait vidé — et plus il est
 * périmé, plus il se protège. C'est un interblocage, pas un retard.
 *
 * CE QUI LE CASSE : une requête que le cache NE PEUT PAS avoir en réserve.
 * On s'appelle soi-même sur une URL portant un jeton aléatoire à usage
 * unique. LiteSpeed n'a jamais vu cette URL, donc il ne peut que passer la
 * main à PHP — et cette réponse-là, bien vivante, porte l'en-tête de purge
 * que le serveur lit.
 *
 * Le plugin LiteSpeed reste appelé en premier quand il est actif : sur
 * Hostinger le cache est parfois appliqué par le SERVEUR sans que le plugin
 * soit actif, auquel cas `litespeed_purge_all` ne déclenche rien du tout et
 * échoue en silence en se croyant fait. On fait donc les deux.
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Le point d'entrée du rappel : émettre l'en-tête de purge, et rien d'autre.
 *
 * Priorité 1 sur `init`, avant tout le reste : cette réponse ne doit rien
 * rendre, rien lire, rien écrire en base au-delà de la consommation du
 * jeton. Le jeton est aléatoire, à usage unique, et n'autorise QUE la purge
 * d'un cache — il n'ouvre aucun accès, ne révèle rien et ne modifie aucun
 * contenu.
 */
add_action( 'init', function () {

	if ( empty( $_GET['lae_purge'] ) ) {
		return;
	}

	$attendu = (string) get_option( 'lae_purge_jeton', '' );
	$fourni  = sanitize_text_field( wp_unslash( $_GET['lae_purge'] ) );

	// Pas de jeton armé, ou jeton qui ne correspond pas : on ne répond rien
	// de particulier, la page suit son cours normal.
	if ( '' === $attendu || ! hash_equals( $attendu, $fourni ) ) {
		return;
	}

	delete_option( 'lae_purge_jeton' );   // usage unique, consommé tout de suite

	if ( ! headers_sent() ) {
		// L'en-tête que le serveur LiteSpeed lit. `*` = tout le cache du site.
		header( 'X-LiteSpeed-Purge: *' );
		// Et cette réponse-ci ne doit surtout pas être mise en cache à son tour.
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
	}

	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	exit( "purge\n" );
}, 1 );

/**
 * Purge le cache, pour de vrai.
 *
 * @return string[] Ce qui a été tenté, pour le journal de synchronisation.
 */
if ( ! function_exists( 'lae_purge_forcee' ) ) {
	function lae_purge_forcee() {

		$faits = array();

		// 1. Le plugin, uniquement s'il est RÉELLEMENT actif.
		if ( defined( 'LSCWP_V' ) || has_action( 'litespeed_purge_all' ) ) {
			do_action( 'litespeed_purge_all' );
			$faits[] = 'plugin LiteSpeed';
		}

		// 2. Le serveur, via un rappel sur nous-mêmes que le cache ne peut
		//    pas servir de sa réserve.
		$jeton = wp_generate_password( 40, false, false );
		update_option( 'lae_purge_jeton', $jeton, false );

		$url = add_query_arg( 'lae_purge', $jeton, home_url( '/' ) );

		$reponse = wp_remote_get( $url, array(
			'timeout'  => 15,
			'blocking' => true,
			/* Boucle locale vers NOTRE PROPRE domaine, pas vers un service
			   tiers : rien de confidentiel ne transite, et le jeton à usage
			   unique n'autorise qu'une purge de cache. Un certificat interne
			   mal apparié ferait échouer la purge en silence — ce qui est
			   précisément la panne qu'on répare ici. */
			'sslverify' => false,
			'headers'  => array( 'Cache-Control' => 'no-cache' ),
		) );

		if ( is_wp_error( $reponse ) ) {
			$faits[] = 'rappel serveur ÉCHOUÉ (' . $reponse->get_error_code() . ')';
			// Le jeton reste armé : un prochain passage pourra réessayer.
		} else {
			$faits[] = 'serveur LiteSpeed (rappel, HTTP ' . wp_remote_retrieve_response_code( $reponse ) . ')';
			delete_option( 'lae_purge_jeton' );
		}

		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
			$faits[] = 'cache objet';
		}

		return $faits;
	}
}

/**
 * Purger dès que la version du thème change.
 *
 * Le déploiement arrive par WP-Cron, sans qu'aucune page d'administration ne
 * soit chargée : on se branche donc sur `init` ET `admin_init`, comme tous
 * les rattrapages du thème. Le verrou est posé AVANT le travail, pour qu'un
 * échec ne relance pas une purge à chaque requête.
 */
if ( ! function_exists( 'lae_purge_si_version_changee' ) ) {
	function lae_purge_si_version_changee() {

		if ( ! defined( 'LAE_VERSION' ) ) {
			return;
		}

		// La réponse de purge elle-même ne doit rien déclencher de plus.
		if ( ! empty( $_GET['lae_purge'] ) ) {
			return;
		}

		if ( (string) get_option( 'lae_version_purgee', '' ) === (string) LAE_VERSION ) {
			return;
		}

		update_option( 'lae_version_purgee', LAE_VERSION, true );   // verrou AVANT

		$faits = lae_purge_forcee();
		update_option( 'lae_purge_journal', array(
			'version' => LAE_VERSION,
			'date'    => current_time( 'mysql' ),
			'faits'   => $faits,
		), false );
	}
}
add_action( 'init', 'lae_purge_si_version_changee', 5 );
add_action( 'admin_init', 'lae_purge_si_version_changee', 5 );

/**
 * Et après chaque synchronisation qui a modifié des fichiers, sans attendre
 * un changement de version : un correctif peut partir sans bump.
 */
add_filter( 'lae_github_sync_purge', function ( $faits ) {
	$faits = is_array( $faits ) ? $faits : array();
	return array_merge( $faits, lae_purge_forcee() );
}, 10, 1 );
