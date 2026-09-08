<?php
/**
 * Déroulé d'un chantier. Format d'une ligne : Titre | Description
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$items = lae_lignes( 'etapes_items' );
if ( ! $items ) {
	return;
}
$titre = lae_reglage( 'etapes_titre', 'Comment ça se passe' );
?>
<section class="lae-section lae-section--doux">
	<div class="lae-shell">
		<div class="lae-section__tete lae-section__tete--centre">
			<h2><?php echo esc_html( $titre ); ?></h2>
		</div>
		<ol class="lae-etapes">
			<?php foreach ( $items as $ligne ) :
				$etape = lae_decoupe_ligne( $ligne ); ?>
				<li class="lae-etape">
					<h3><?php echo esc_html( $etape['titre'] ); ?></h3>
					<?php if ( $etape['texte'] ) : ?>
						<p><?php echo esc_html( $etape['texte'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
