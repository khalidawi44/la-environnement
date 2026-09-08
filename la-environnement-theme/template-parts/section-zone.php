<?php
/**
 * Zone d'intervention.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$texte    = lae_reglage( 'zone_texte' );
$communes = lae_communes();

if ( ! $texte && ! $communes ) {
	return;
}
$titre = lae_reglage( 'zone_titre', 'Zone d\'intervention' );
?>
<section class="lae-section">
	<div class="lae-shell">
		<div class="lae-zone">
			<div>
				<p class="lae-surtitre">Où nous intervenons</p>
				<h2><?php echo esc_html( $titre ); ?></h2>
				<?php if ( $texte ) : ?>
					<p class="lae-chapo"><?php echo esc_html( $texte ); ?></p>
				<?php endif; ?>
				<?php if ( $communes ) : ?>
					<ul class="lae-zone__villes">
						<?php foreach ( $communes as $ville ) : ?>
							<li><?php echo esc_html( $ville ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="lae-zone__carte" aria-hidden="true">
				<?php echo lae_icone( 'lieu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>
