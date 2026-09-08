<?php
/**
 * Détail d'une réalisation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	$types = get_the_terms( get_the_ID(), 'lae_type_chantier' );

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
		'fil'   => '<a href="' . esc_url( get_post_type_archive_link( 'lae_realisation' ) ) . '">Réalisations</a>'
				   . ( ( $types && ! is_wp_error( $types ) ) ? ' · ' . esc_html( $types[0]->name ) : '' ),
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
endwhile;

get_footer();
