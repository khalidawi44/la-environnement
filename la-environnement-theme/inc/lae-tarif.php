<?php
/**
 * Tarif adapté aux revenus — la mécanique, pas les chiffres.
 *
 * Le client applique une réduction selon les revenus de la personne. Sa
 * demande (13/09) n'est pas « faire une remise » — il la fait déjà — mais
 * **qu'elle se voie** : qu'en lisant le site, quelqu'un comprenne ce qu'il
 * paie en moins par rapport au tarif de référence.
 *
 * DEUX PRÉCAUTIONS, et elles sont structurelles.
 *
 * 1. On n'annonce JAMAIS un montant. Le prix d'un chantier dépend de l'accès,
 *    de la hauteur, du risque et de l'évacuation : il sort d'une visite, pas
 *    d'un formulaire. Un montant affiché ici deviendrait un engagement, et la
 *    première visite qui le dépasse crée un litige. On affiche donc une
 *    **réduction en pourcentage appliquée au devis**, ce qui rend le geste
 *    visible sans rien promettre de chiffré.
 *
 * 2. Aucune tranche n'est écrite en dur. Elles viennent du personnalisateur,
 *    saisies par le client lui-même. Tant qu'il n'a rien saisi, la page
 *    explique le principe et n'affiche aucun tableau — plutôt que d'inventer
 *    un barème qui ne serait pas le sien.
 *
 * Format de saisie, une tranche par ligne : `Libellé | pourcentage`
 *   Étudiant, apprenti, sans emploi | 30
 *   Retraité au minimum vieillesse | 25
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Tranches de réduction saisies par le client.
 *
 * @return array<int, array{libelle:string, taux:int}> Vide si rien n'est saisi.
 */
function lae_tarif_tranches() {
	if ( ! function_exists( 'lae_reglage' ) ) return array();
	$brut = (string) lae_reglage( 'tarif_tranches' );
	if ( '' === trim( $brut ) ) return array();

	$tranches = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $brut ) as $ligne ) {
		$ligne = trim( $ligne );
		if ( '' === $ligne ) continue;
		$bouts = array_map( 'trim', explode( '|', $ligne ) );
		if ( count( $bouts ) < 2 || '' === $bouts[0] ) continue;
		$taux = (int) preg_replace( '/[^0-9]/', '', $bouts[1] );
		if ( $taux < 1 || $taux > 100 ) continue;   // une ligne mal saisie est ignorée, pas affichée de travers
		$tranches[] = array( 'libelle' => $bouts[0], 'taux' => $taux );
	}
	return $tranches;
}

/** Le taux le plus élevé, pour l'annoncer en tête de page. */
function lae_tarif_taux_max() {
	$t = lae_tarif_tranches();
	if ( ! $t ) return 0;
	return max( wp_list_pluck( $t, 'taux' ) );
}
