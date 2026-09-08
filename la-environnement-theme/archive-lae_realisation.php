<?php
/**
 * Liste des réalisations (et filtres par type de chantier).
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$terme = is_tax( 'lae_type_chantier' ) ? get_queried_object() : null;

get_template_part( 'template-parts/entete', 'page', array(
	'titre' => $terme ? $terme->name : lae_reglage( 'realisations_titre', 'Nos chantiers' ),
	'chapo' => $terme ? wp_strip_all_tags( term_description( $terme ) ) : lae_reglage( 'realisations_chapo' ),
	'fil'   => $terme ? '<a href="' . esc_url( get_post_type_archive_link( 'lae_realisation' ) ) . '">Réalisations</a>' : '',
) );

$types = get_terms( array( 'taxonomy' => 'lae_type_chantier', 'hide_empty' => true ) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php if ( $types && ! is_wp_error( $types ) ) : ?>
			<ul class="lae-filtres">
				<li class="<?php echo $terme ? '' : 'est-actif'; ?>">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'lae_realisation' ) ); ?>">Tous</a>
				</li>
				<?php foreach ( $types as $type ) : ?>
					<li class="<?php echo ( $terme && $terme->term_id === $type->term_id ) ? 'est-actif' : ''; ?>">
						<a href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo esc_html( $type->name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="lae-realisations">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/carte', 'realisation' );
				endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'lae-pagination', 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="lae-vide">Les chantiers seront bientôt en ligne.</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
