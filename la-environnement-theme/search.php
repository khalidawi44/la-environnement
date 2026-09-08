<?php
/**
 * Résultats de recherche.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/entete', 'page', array(
	'titre' => 'Recherche',
	'chapo' => sprintf( '%d résultat(s) pour « %s »', (int) $wp_query->found_posts, get_search_query() ),
) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php get_search_form(); ?>

		<?php if ( have_posts() ) : ?>
			<ul class="lae-articles" style="margin-top:32px">
				<?php while ( have_posts() ) : the_post(); ?>
					<li>
						<article <?php post_class( 'lae-carte' ); ?>>
							<div class="lae-carte__corps">
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination( array( 'class' => 'lae-pagination', 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="lae-vide">Aucun résultat. Essayez avec un autre mot.</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
