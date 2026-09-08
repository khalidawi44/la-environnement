<?php
/**
 * Carte d'une prestation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$icone = get_post_meta( get_the_ID(), '_lae_icone', true );
?>
<article <?php post_class( 'lae-carte' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="lae-carte__media">
			<?php the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
		</div>
	<?php endif; ?>

	<div class="lae-carte__corps">
		<?php if ( ! has_post_thumbnail() && $icone ) : ?>
			<span class="lae-carte__icone"><?php echo lae_icone( $icone ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php endif; ?>

		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<?php if ( has_excerpt() ) : ?>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>

		<span class="lae-carte__pied lae-lien-fleche">
			En savoir plus <?php echo lae_icone( 'fleche' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</span>
	</div>
</article>
