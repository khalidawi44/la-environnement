<?php
/**
 * Gabarit par défaut (liste d'articles).
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

if ( have_posts() ) : ?>
	<h1 class="lae-titre"><?php echo is_home() ? esc_html( get_bloginfo( 'name' ) ) : ''; ?></h1>
	<ul class="lae-liste">
		<?php while ( have_posts() ) : the_post(); ?>
			<li>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="lae-meta"><?php echo esc_html( get_the_date() ); ?></p>
				<?php the_excerpt(); ?>
			</li>
		<?php endwhile; ?>
	</ul>
	<?php
	the_posts_pagination();
else :
	echo '<p>Aucun contenu pour le moment.</p>';
endif;

get_footer();
