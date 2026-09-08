<?php
/**
 * Détail d'une prestation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
		'fil'   => '<a href="' . esc_url( get_post_type_archive_link( 'lae_prestation' ) ) . '">Prestations</a>',
	) );
	?>
	<article <?php post_class( 'lae-contenu' ); ?>>
		<div class="lae-shell">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'lae-large', array( 'class' => 'alignwide', 'loading' => 'lazy' ) );
			}
			the_content();
			?>
		</div>
	</article>

	<?php
	// Les autres prestations, pour ne pas laisser la page sans suite.
	$autres = new WP_Query( array(
		'post_type'           => 'lae_prestation',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );

	if ( $autres->have_posts() ) : ?>
		<section class="lae-section lae-section--doux">
			<div class="lae-shell">
				<div class="lae-section__tete">
					<h2>Autres prestations</h2>
				</div>
				<div class="lae-grille lae-grille--3">
					<?php while ( $autres->have_posts() ) : $autres->the_post();
						get_template_part( 'template-parts/carte', 'prestation' );
					endwhile; ?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();

endwhile;

get_footer();
