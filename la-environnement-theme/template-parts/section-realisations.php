<?php
/**
 * Réalisations mises en avant sur l'accueil.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$chantiers = new WP_Query( array(
	'post_type'           => 'lae_realisation',
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
) );

if ( ! $chantiers->have_posts() ) {
	return;
}

$titre = lae_reglage( 'realisations_titre', 'Nos chantiers' );
$chapo = lae_reglage( 'realisations_chapo' );
?>
<section class="lae-section lae-section--vert">
	<div class="lae-shell">
		<div class="lae-section__tete">
			<p class="lae-surtitre">Réalisations</p>
			<h2><?php echo esc_html( $titre ); ?></h2>
			<?php if ( $chapo ) : ?>
				<p class="lae-chapo"><?php echo esc_html( $chapo ); ?></p>
			<?php endif; ?>
		</div>

		<div class="lae-realisations">
			<?php while ( $chantiers->have_posts() ) : $chantiers->the_post();
				get_template_part( 'template-parts/carte', 'realisation' );
			endwhile; ?>
		</div>

		<p style="margin-top:28px">
			<a class="lae-lien-fleche" href="<?php echo esc_url( get_post_type_archive_link( 'lae_realisation' ) ); ?>">
				Voir tous les chantiers <?php echo lae_icone( 'fleche' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</p>
	</div>
</section>
<?php
wp_reset_postdata();
