<?php
/**
 * Page introuvable.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/entete', 'page', array(
	'titre' => 'Page introuvable',
	'chapo' => 'Cette adresse ne correspond à aucune page du site.',
) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php get_search_form(); ?>
		<p style="margin-top:28px">
			<a class="lae-btn lae-btn--secondaire" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l'accueil</a>
		</p>
	</div>
</div>

<?php
get_footer();
