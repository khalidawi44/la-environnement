<?php
/**
 * Gabarit par défaut (liste d'articles).
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/entete', 'page', array(
	'titre' => is_home() ? ( get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : 'Actualités' ) : get_the_archive_title(),
) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php if ( have_posts() ) : ?>
			<ul class="lae-articles">
				<?php while ( have_posts() ) : the_post(); ?>
					<li>
						<article <?php post_class( 'lae-carte' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="lae-carte__media">
									<?php the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
								</div>
							<?php endif; ?>
							<div class="lae-carte__corps">
								<p class="lae-article-meta"><?php echo esc_html( get_the_date() ); ?></p>
								<h2 class="lae-carte__titre"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php the_posts_pagination( array( 'class' => 'lae-pagination', 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="lae-vide">Aucun contenu pour le moment.</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
