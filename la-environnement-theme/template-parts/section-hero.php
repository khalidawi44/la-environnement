<?php
/**
 * Bandeau principal de l'accueil.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$surtitre = lae_reglage( 'hero_surtitre' );
$titre    = lae_reglage( 'hero_titre' );
$chapo    = lae_reglage( 'hero_chapo' );
$image    = lae_reglage( 'hero_image' );
$points   = lae_lignes( 'hero_points' );
$btn2_txt = lae_reglage( 'hero_btn2_texte' );
$btn2_url = lae_reglage( 'hero_btn2_url' );
$contact  = lae_url_contact();

if ( '' === $titre ) {
	$titre = get_bloginfo( 'name' );
}
?>
<section class="lae-hero<?php echo $image ? '' : ' lae-hero--sans-image'; ?>">
	<?php if ( $image ) : ?>
		<div class="lae-hero__media">
			<img src="<?php echo esc_url( $image ); ?>" alt="" fetchpriority="high" decoding="async">
		</div>
	<?php endif; ?>

	<div class="lae-shell">
		<div class="lae-hero__inner">
			<?php if ( $surtitre ) : ?>
				<p class="lae-surtitre"><?php echo esc_html( $surtitre ); ?></p>
			<?php endif; ?>

			<h1><?php echo esc_html( $titre ); ?></h1>

			<?php if ( $chapo ) : ?>
				<p class="lae-hero__chapo"><?php echo esc_html( $chapo ); ?></p>
			<?php endif; ?>

			<div class="lae-hero__actions">
				<?php lae_bouton_tel( 'lae-btn lae-btn--primaire lae-btn--tel' ); ?>

				<?php if ( $btn2_txt && $btn2_url ) : ?>
					<a class="lae-btn lae-btn--clair" href="<?php echo esc_url( $btn2_url ); ?>"><?php echo esc_html( $btn2_txt ); ?></a>
				<?php elseif ( $contact ) : ?>
					<a class="lae-btn lae-btn--clair" href="<?php echo esc_url( $contact ); ?>">Demander un devis</a>
				<?php endif; ?>
			</div>

			<?php if ( $points ) : ?>
				<ul class="lae-hero__points">
					<?php foreach ( $points as $point ) : ?>
						<li><?php echo lae_icone( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $point ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
