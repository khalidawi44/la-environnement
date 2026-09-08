<?php
/**
 * Formulaire de recherche.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form class="lae-recherche" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="lae-invisible" for="lae-recherche-champ">Rechercher</label>
	<input type="search" id="lae-recherche-champ" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Rechercher…">
	<button class="lae-btn lae-btn--primaire" type="submit">Rechercher</button>
</form>
