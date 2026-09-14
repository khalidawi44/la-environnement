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
			<?php
			/* LE PICTOGRAMME DE CARTE A ÉTÉ RETIRÉ le 14/09 : « pas d'icône
			   de ce genre-là dans le site » (Fabrice). À sa place, une vraie
			   photo de chantier — c'est ce qu'il demandait, et c'est aussi
			   plus utile : une épingle grise ne dit rien, un jardin entretenu
			   dit ce qu'on vient faire chez vous. Choisie sur le RENDU et non
			   sur le nom — première tentative avec la haie taillée, écartée
			   après rendu : sombre et illisible à cette taille. */
			$lae_zone_illu = get_template_directory() . '/assets/images/pelouse-haie.webp';
			if ( file_exists( $lae_zone_illu ) ) : ?>
				<div class="lae-zone__carte" aria-hidden="true">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/pelouse-haie.webp' ); ?>"
					     alt="" loading="lazy" decoding="async" width="1200" height="900">
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
