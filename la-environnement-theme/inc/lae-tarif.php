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
 * 2. Le barème est PROPOSÉ, jamais figé. Fabrice a demandé le 13/09 qu'on le
 *    construise (« c'est toi qui construis la grille tarifaire ») : deux taux
 *    et deux libellés partent donc livrés dans lae-defauts.php. Ce sont les
 *    seules valeurs de tout le thème qui engagent commercialement le client,
 *    et elles se remplacent dans le personnalisateur en une saisie. Champ
 *    vidé = aucun tableau affiché, la page explique le principe sans barème.
 *
 *    Le mécanisme retenu est DÉCLARATIF, pas le quotient familial : réclamer
 *    un avis d'imposition pour faire couper un arbre est intrusif et fait
 *    renoncer exactement les gens visés. Aucun seuil en euros n'est donc
 *    inventé — les libellés décrivent des situations, pas des revenus.
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

/* ═══════════════════════════════════════════════════════════════════
   Le comparatif de marché
   Demande de Fabrice (13/09) : « analyse le marché en Loire-Atlantique
   et positionne-toi moins cher, montre des exemples de tarifs pratiqués
   par les autres ».

   TROIS RÈGLES, et elles sont juridiques autant qu'éditoriales.

   1. AUCUN CONCURRENT NOMMÉ. La publicité comparative est licite en
      France mais encadrée : comparer nommément ses prix à ceux d'une
      entreprise identifiable suppose une comparaison objective,
      vérifiable, portant sur des prestations équivalentes et tenue à
      jour. Un prix relevé qui bouge, et c'est nous qui sommes en tort.
      On publie donc des FOURCHETTES DE MARCHÉ sourcées et datées :
      le lecteur situe le prix tout autant, et le risque disparaît.

   2. AUCUN MONTANT POUR NOUS. Même précaution que pour le barème : un
      montant affiché deviendrait un engagement. On publie un ÉCART EN
      POURCENTAGE par rapport au milieu de fourchette du marché.

   3. LE TRAVAIL NON DÉCLARÉ NE S'ATTAQUE PAS. On décrit ce que le
      client achète en plus — assurance, facture, recours — plutôt que
      d'accuser qui que ce soit. C'est plus fort, et ça n'expose à rien.

   Le crédit d'impôt « services à la personne » de 50 % est
   VOLONTAIREMENT ABSENT : élagage en hauteur, abattage, démontage et
   dessouchage en sont exclus (art. D. 7231-1 du Code du travail), et
   il faut de toute façon détenir la déclaration SAP. À ajouter
   seulement si le client confirme qu'il l'a, et pour la partie jardin
   uniquement.
   ═══════════════════════════════════════════════════════════════════ */

/**
 * Date du relevé de prix. Affichée sur la page : une comparaison tenue à
 * jour est la condition de sa licéité, donc la date se voit.
 */
function lae_tarif_marche_releve() {
	return '13 septembre 2026';
}

/**
 * Fourchettes de prix constatées sur le marché. Sourcées, datées, jamais
 * rattachées à une entreprise nommée.
 *
 * @return array<int, array{quoi:string, fourchette:string, zone:string}>
 */
function lae_tarif_marche() {
	return apply_filters( 'lae_tarif_marche', array(
		array( 'quoi' => 'Taux horaire d\'un élagueur',        'fourchette' => '30 à 70 € HT',   'zone' => 'France' ),
		array( 'quoi' => 'Forfait à la journée',               'fourchette' => '300 à 600 €',    'zone' => 'France' ),
		array( 'quoi' => 'Élagage d\'un arbre de taille moyenne', 'fourchette' => '200 à 600 €', 'zone' => 'Nantes' ),
		array( 'quoi' => 'Abattage, arbre de 10 à 15 m',       'fourchette' => 'à partir de 425 €', 'zone' => 'Nantes' ),
		array( 'quoi' => 'Abattage, arbre de plus de 15 m',    'fourchette' => 'à partir de 550 €', 'zone' => 'Nantes' ),
	) );
}

/**
 * Écart annoncé par rapport au milieu de fourchette du marché.
 *
 * En dessous de 15 %, l'écart ne se remarque pas et on brade. Au-delà de
 * 25 %, le client se demande ce qui cloche — et on se rapproche du terrain
 * du travail non déclaré, qui ne se gagne pas sur le prix.
 *
 * @return array{0:int, 1:int}
 */
function lae_tarif_ecart() {
	$e = apply_filters( 'lae_tarif_ecart', array( 15, 25 ) );
	return array( (int) $e[0], (int) $e[1] );
}

/**
 * Les trois niveaux du marché. L'entreprise n'est en concurrence frontale
 * avec aucun des deux autres : c'est ce qui rend sa position tenable.
 *
 * @return array<int, array{cle:string, titre:string, texte:string}>
 */
function lae_tarif_niveaux() {
	return array(
		array(
			'cle'   => 'noir',
			'titre' => 'Le travail non déclaré',
			'texte' => 'Imbattable sur le prix affiché. Mais sans assurance, il n\'y a ni facture, ni garantie, ni recours : une branche qui part sur la toiture du voisin, ou quelqu\'un qui se blesse chez vous, et c\'est vous qui répondez.',
		),
		array(
			'cle'   => 'nous',
			'titre' => 'Nous',
			'texte' => 'Déclaré, assuré, avec facture — mais sans l\'appareil d\'une entreprise à salariés et à commerciaux. C\'est cet écart de structure qui finance l\'écart de prix : on ne coupe pas dans le travail, on coupe dans ce qui n\'a rien à voir avec l\'arbre.',
		),
		array(
			'cle'   => 'entreprises',
			'titre' => 'Les entreprises du secteur',
			'texte' => 'Les mêmes garanties que nous, et des coûts de structure supérieurs. Ce sont elles qui fixent la référence de prix du marché — celle des fourchettes ci-dessous.',
		),
	);
}
