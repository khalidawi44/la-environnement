<?php
/**
 * Gabarit d'une page.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<h1 class="lae-titre"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</article>
<?php endwhile;

get_footer();
