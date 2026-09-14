<?php
/**
 * Gabarit de la page « Notre façon de travailler » (slug a-propos).
 *
 * WordPress applique ce fichier tout seul à la page dont le slug est
 * `a-propos` : rien à régler dans l'admin, et si la page est renommée le
 * site retombe sur page.php sans rien casser.
 *
 * Identique à page.php, plus le bloc des trois métiers après le contenu.
 * Il est APRÈS et non avant : la page explique d'abord comment on travaille
 * (on regarde avant de couper, on coupe le moins possible…), le récit
 * cime → tronc → racines vient ensuite illustrer sur quoi ça s'applique.
 * L'inverse ferait commencer la page par un catalogue.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
	) );
	?>
	<article <?php post_class( 'lae-contenu' ); ?>>
		<div class="lae-shell">
			<?php
			the_content();
			wp_link_pages( array( 'before' => '<p class="lae-pagination">', 'after' => '</p>' ) );
			?>
		</div>
	</article>
	<?php
	get_template_part( 'template-parts/section', 'chapitres' );
endwhile;

get_footer();
