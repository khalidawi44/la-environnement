<?php
/**
 * Liste des prestations.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/entete', 'page', array(
	'titre' => lae_reglage( 'prestations_titre', 'Nos prestations' ),
	'chapo' => lae_reglage( 'prestations_chapo' ),
) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php if ( have_posts() ) : ?>
			<div class="lae-grille lae-grille--3">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/carte', 'prestation' );
				endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'lae-pagination', 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="lae-vide">Les prestations seront bientôt en ligne.</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
