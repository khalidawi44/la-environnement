<?php
/**
 * L'ambiance : le site suit la saison et l'heure.
 *
 * Idée de Fabrice (14/09). Elle est juste pour ce métier : un élagueur ne
 * fait pas la même chose en janvier et en juin, et celui qu'on appelle à
 * 23 h n'a pas le même problème que celui qui compare des devis à 14 h.
 * Le site le dit sans l'écrire.
 *
 * DEUX MÉCANIQUES DIFFÉRENTES, ET C'EST LE CŒUR DU SUJET.
 *
 * LA SAISON se calcule en PHP. Elle est la même pour tout le monde et ne
 * change que quatre fois par an : le cache HTTP la digère sans broncher.
 *
 * L'HEURE ne peut PAS se calculer en PHP. D'abord parce que le site est
 * servi par un cache de 5 minutes et par un CDN : une page rendue à 19 h 58
 * resservirait « jour » à 20 h 03. Ensuite et surtout parce que l'heure qui
 * compte est celle du VISITEUR, pas celle du serveur. Elle est donc posée
 * par un script en tête de page, avant le premier rendu — exactement le
 * motif que le thème utilise déjà pour la classe `js-cine`. Aucune
 * variation de cache, aucun scintillement.
 *
 * Sans JavaScript, `data-moment` vaut « jour » : la version la plus neutre
 * et la plus fréquente, jamais une page cassée.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Saison courante, d'après la date du serveur réglée sur le fuseau du site.
 *
 * Bornes astronomiques approchées : elles bougent d'un jour selon les
 * années, et personne ne regarde son écran le 20 mars pour vérifier.
 *
 * @return string hiver|printemps|ete|automne
 */
function lae_saison() {
	$j = (int) wp_date( 'z' );          // jour de l'année, 0 à 365
	if ( $j >= 355 || $j < 78 )  return 'hiver';      // ~21 déc → ~19 mars
	if ( $j < 171 )              return 'printemps';  // ~20 mars → ~20 juin
	if ( $j < 265 )              return 'ete';        // ~21 juin → ~22 sept
	return 'automne';                                 // ~23 sept → ~20 déc
}

/**
 * Moment de la journée à partir d'une heure donnée.
 *
 * Les bornes sont larges à dessein : on cherche une ambiance, pas une
 * éphéméride. Le crépuscule couvre les deux bascules, matin et soir.
 *
 * @param int $h Heure locale, 0 à 23.
 * @return string jour|crepuscule|nuit
 */
function lae_moment( $h ) {
	$h = (int) $h;
	if ( $h >= 8 && $h < 18 )  return 'jour';
	if ( $h >= 22 || $h < 6 )  return 'nuit';
	return 'crepuscule';
}

/* Les attributs voyagent sur <html> : toute la feuille de style peut s'y
   accrocher, et le sélecteur reste lisible — :root[data-saison="automne"]. */
add_filter( 'language_attributes', function ( $sortie ) {
	return $sortie
		. ' data-saison="' . esc_attr( lae_saison() ) . '"'
		. ' data-moment="jour"';
} );

/**
 * Le script qui corrige le moment avec l'heure du visiteur.
 *
 * Posé le plus tôt possible dans <head>, avant la feuille de style, pour
 * que la première peinture soit déjà la bonne. Il ne fait qu'écrire un
 * attribut : s'il échoue, la page reste en « jour ».
 */
add_action( 'wp_head', function () {
	?>
<script>
/* Ambiance : l'heure du visiteur, pas celle du serveur — et donc pas celle
   du cache. Écrit avant le premier rendu, donc sans scintillement. */
(function(){try{
  var h=new Date().getHours(),m='crepuscule';
  if(h>=8&&h<18)m='jour'; else if(h>=22||h<6)m='nuit';
  document.documentElement.setAttribute('data-moment',m);
}catch(e){}}());
</script>
	<?php
}, 1 );

