<?php
/**
 * Prestations mises en avant sur l'accueil.
 * Les contenus proviennent du type « Prestations » de l'administration.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$prestations = new WP_Query( array(
	'post_type'           => 'lae_prestation',
	'posts_per_page'      => 6,
	'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
) );

if ( ! $prestations->have_posts() ) {
	return;
}

$surtitre = lae_reglage( 'prestations_surtitre', 'Nos prestations' );
$titre    = lae_reglage( 'prestations_titre', 'Élagage, abattage et création de jardin' );
$chapo    = lae_reglage( 'prestations_chapo' );
?>
<section class="lae-section">
	<div class="lae-shell">
		<div class="lae-section__tete">
			<?php if ( $surtitre ) : ?>
				<p class="lae-surtitre"><?php echo esc_html( $surtitre ); ?></p>
			<?php endif; ?>
			<h2><?php echo esc_html( $titre ); ?></h2>
			<?php if ( $chapo ) : ?>
				<p class="lae-chapo"><?php echo esc_html( $chapo ); ?></p>
			<?php endif; ?>
		</div>

		<div class="lae-grille lae-grille--3">
			<?php while ( $prestations->have_posts() ) : $prestations->the_post();
				get_template_part( 'template-parts/carte', 'prestation' );
			endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
