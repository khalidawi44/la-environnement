<?php
/**
 * Gabarit d'un article.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<h1 class="lae-titre"><?php the_title(); ?></h1>
		<p class="lae-meta"><?php echo esc_html( get_the_date() ); ?></p>
		<?php the_post_thumbnail( 'large' ); ?>
		<?php the_content(); ?>
	</article>
<?php endwhile;

get_footer();
