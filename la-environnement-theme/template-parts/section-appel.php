<?php
/**
 * Bande « demander un devis ».
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$titre   = lae_reglage( 'appel_titre', 'Un projet, un arbre à traiter ?' );
$texte   = lae_reglage( 'appel_texte' );
$btn     = lae_reglage( 'appel_btn_texte', 'Demander un devis' );
$contact = lae_url_contact();
$tel     = lae_tel_lien();

if ( ! $tel && ! $contact ) {
	return;
}
?>
<section class="lae-appel">
	<div class="lae-shell lae-appel__inner">
		<div>
			<h2><?php echo esc_html( $titre ); ?></h2>
			<?php if ( $texte ) : ?>
				<p><?php echo esc_html( $texte ); ?></p>
			<?php endif; ?>
		</div>
		<div class="lae-appel__actions">
			<?php if ( $tel ) : ?>
				<a class="lae-btn lae-btn--blanc lae-btn--tel" href="<?php echo esc_url( $tel ); ?>">
					<?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( lae_reglage( 'telephone' ) ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $contact ) : ?>
				<a class="lae-btn lae-btn--clair" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html( $btn ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
