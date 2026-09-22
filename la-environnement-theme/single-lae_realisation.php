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
			/* Une paire complète donne le comparateur ; sinon l'image seule. */
			$lae_cmp = function_exists( 'lae_comparateur' ) ? lae_comparateur( get_the_ID() ) : '';
			if ( $lae_cmp ) {
				echo $lae_cmp; // phpcs:ignore WordPress.Security.EscapeOutput
			} elseif ( has_post_thumbnail() ) {
				the_post_thumbnail( 'lae-large', array( 'class' => 'alignwide', 'loading' => 'lazy' ) );
			}
			the_content();
			?>
		</div>
	</article>

	<?php
	/* LA SUITE — elle n'existait pas, et c'est ce qui coutait le plus cher.
	   Mesure du 22/09 : les seuls liens sortants d'une page chantier etaient
	   le menu et le pied de page. Or quelqu'un qui vient de regarder un
	   avant/apres est exactement la personne qui va appeler : il lui manque
	   juste de savoir comment ca s'appelle et ou demander. */
	$lae_presta   = function_exists( 'lae_maillage_prestations' ) ? lae_maillage_prestations( get_the_ID() ) : array();
	$lae_chantiers = get_posts( array(
		'post_type'      => 'lae_realisation',
		'post_status'    => 'publish',
		'post__not_in'   => array( get_the_ID() ),
		'posts_per_page' => 2,
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	) );

	if ( $lae_presta || $lae_chantiers ) : ?>
		<section class="lae-section lae-suite">
			<div class="lae-shell">

				<?php if ( $lae_presta ) : ?>
					<h2 class="lae-suite__t">Vous avez le même besoin&nbsp;?</h2>
					<p class="lae-suite__p">On vient voir sur place avant de chiffrer quoi que ce soit.</p>
					<ul class="lae-suite__liste">
						<?php foreach ( $lae_presta as $lae_p ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $lae_p ) ); ?>"><?php echo esc_html( get_the_title( $lae_p ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $lae_chantiers ) : ?>
					<h2 class="lae-suite__t">D'autres chantiers</h2>
					<ul class="lae-suite__liste">
						<?php foreach ( $lae_chantiers as $lae_c ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $lae_c ) ); ?>"><?php echo esc_html( get_the_title( $lae_c ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div>
		</section>
	<?php endif;

endwhile;

get_footer();
