<?php
/**
 * Page 404.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<h1 class="lae-titre">Page introuvable</h1>
<p>La page demandée n'existe pas ou a été déplacée.</p>
<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l'accueil</a></p>
<?php
get_footer();
