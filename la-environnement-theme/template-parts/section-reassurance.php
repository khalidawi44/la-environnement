<?php
/**
 * Bandeau de réassurance (sous le bandeau principal).
 * Format d'une ligne de réglage : Titre | Description | icone
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$items = lae_lignes( 'reassurance_items' );
if ( ! $items ) {
	return;
}
?>
<section class="lae-reassurance">
	<div class="lae-shell">
		<ul>
			<?php foreach ( $items as $ligne ) :
				$bloc  = lae_decoupe_ligne( $ligne );
				$icone = $bloc['icone'] ? $bloc['icone'] : 'check';
				?>
				<li>
					<?php echo lae_icone( $icone ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div>
						<strong><?php echo esc_html( $bloc['titre'] ); ?></strong>
						<?php if ( $bloc['texte'] ) : ?>
							<span><?php echo esc_html( $bloc['texte'] ); ?></span>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
