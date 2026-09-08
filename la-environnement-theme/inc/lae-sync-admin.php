<?php
/**
 * Écran d'administration « SYNC GitHub » (Outils → SYNC GitHub).
 *
 * La synchronisation tourne toute seule (cron 5 min, cf. lae-github-sync.php).
 * Cet écran sert à : voir l'état, forcer une sync immédiate, lire les logs.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Récupère et décode un JSON distant. */
function lae_gh_json( $url ) {
	$resp = wp_remote_get( $url, array( 'timeout' => 30, 'sslverify' => true ) );
	if ( is_wp_error( $resp ) ) return false;
	if ( 200 !== (int) wp_remote_retrieve_response_code( $resp ) ) return false;
	$data = json_decode( wp_remote_retrieve_body( $resp ), true );
	return ( JSON_ERROR_NONE === json_last_error() ) ? $data : false;
}

/** Récupère un fichier brut distant. */
function lae_gh_raw( $url ) {
	$resp = wp_remote_get( $url, array( 'timeout' => 60, 'sslverify' => true ) );
	if ( is_wp_error( $resp ) ) return false;
	if ( 200 !== (int) wp_remote_retrieve_response_code( $resp ) ) return false;
	return wp_remote_retrieve_body( $resp );
}

add_action( 'admin_menu', function () {
	add_management_page(
		'SYNC GitHub',
		'SYNC GitHub',
		'manage_options',
		'lae-sync',
		'lae_render_sync_page'
	);
} );

function lae_render_sync_page() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'forbidden' );

	if ( isset( $_GET['lae_synced'] ) ) {
		printf(
			'<div class="notice notice-success is-dismissible"><p>Synchronisation terminée : %d fichier(s) mis à jour.</p></div>',
			(int) $_GET['lae_synced']
		);
	}
	if ( isset( $_GET['lae_err'] ) ) {
		printf(
			'<div class="notice notice-error is-dismissible"><p>Erreur : %s</p></div>',
			esc_html( sanitize_text_field( wp_unslash( $_GET['lae_err'] ) ) )
		);
	}

	$repos = LAE_GitHub_Sync::get_repos();
	$next  = LAE_GitHub_Sync::get_next_cron_run();

	echo '<div class="wrap"><h1>SYNC GitHub — L.A Environnement</h1>';
	echo '<p>Le site se met à jour <strong>tout seul toutes les 5 minutes</strong> depuis GitHub. '
		. 'Ce bouton sert uniquement à forcer une mise à jour immédiate.</p>';

	echo '<p><strong>Prochaine vérification automatique :</strong> ';
	echo $next ? esc_html( wp_date( 'd/m/Y H:i:s', $next ) ) : '<span style="color:#b32d2e">cron non planifié</span>';
	echo '</p>';

	foreach ( $repos as $slug => $cfg ) {
		$remote = LAE_GitHub_Sync::get_remote_sha( $slug );
		$local  = LAE_GitHub_Sync::get_local_sha( $slug );
		$time   = LAE_GitHub_Sync::get_last_time( $slug );
		$a_jour = ( $remote && $remote === $local );

		echo '<div class="card" style="max-width:900px;padding:16px;margin:16px 0">';
		echo '<h2 style="margin-top:0">' . esc_html( $cfg['label'] ) . '</h2>';
		echo '<table class="widefat striped" style="margin-bottom:12px"><tbody>';
		printf( '<tr><td style="width:220px"><strong>Dépôt</strong></td><td><code>%s</code> @ <code>%s</code></td></tr>',
			esc_html( $cfg['repo'] ), esc_html( $cfg['branch'] ) );
		printf( '<tr><td><strong>Dossier suivi</strong></td><td><code>%s/</code></td></tr>',
			esc_html( $cfg['subdir'] ) );
		printf( '<tr><td><strong>Commit sur GitHub</strong></td><td><code>%s</code></td></tr>',
			esc_html( $remote ? $remote : 'API injoignable' ) );
		printf( '<tr><td><strong>Commit sur le site</strong></td><td><code>%s</code></td></tr>',
			esc_html( $local ? $local : 'jamais synchronisé' ) );
		printf( '<tr><td><strong>Dernière sync</strong></td><td>%s</td></tr>',
			$time ? esc_html( wp_date( 'd/m/Y H:i:s', $time ) ) : '—' );
		printf( '<tr><td><strong>État</strong></td><td>%s</td></tr>',
			$a_jour
				? '<span style="color:#008a20;font-weight:600">À jour</span>'
				: '<span style="color:#b32d2e;font-weight:600">Mise à jour disponible</span>' );
		echo '</tbody></table>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'lae_github_sync_run' );
		echo '<input type="hidden" name="action" value="lae_github_sync_run">';
		echo '<input type="hidden" name="slug" value="' . esc_attr( $slug ) . '">';
		submit_button( 'Synchroniser maintenant', 'primary', 'submit', false );
		echo '</form>';

		$log = LAE_GitHub_Sync::get_last_log( $slug );
		if ( $log ) {
			echo '<h3>Journal de la dernière sync</h3>';
			echo '<pre style="background:#f6f7f7;padding:12px;max-height:280px;overflow:auto;font-size:12px">';
			echo esc_html( implode( "\n", $log ) );
			echo '</pre>';
		}
		echo '</div>';
	}

	$cron_log = LAE_GitHub_Sync::get_cron_log();
	if ( $cron_log ) {
		echo '<div class="card" style="max-width:900px;padding:16px"><h2 style="margin-top:0">Journal du cron (50 dernières lignes)</h2>';
		echo '<pre style="background:#f6f7f7;padding:12px;max-height:320px;overflow:auto;font-size:12px">';
		echo esc_html( implode( "\n", $cron_log ) );
		echo '</pre></div>';
	}

	echo '</div>';
}
