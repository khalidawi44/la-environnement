<?php
/**
 * Carte d'une prestation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* PAS DE PICTOGRAMME EN REPLI — demande de Fabrice du 14/09, et elle est
   permanente : « pas d'icône de ce genre-là dans le site ». Une carte sans
   photo n'affiche donc rien à la place ; elle est plus sobre, et surtout
   elle SE VOIT comme incomplète, ce qui pousse à lui donner sa photo. Un
   repli qui a l'air fini est un repli qu'on oublie de remplacer — c'est
   exactement ce qui est arrivé aux six prestations livrées. */
?>
<article <?php post_class( 'lae-carte' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="lae-carte__media">
			<?php the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
		</div>
	<?php endif; ?>

	<div class="lae-carte__corps">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<?php if ( has_excerpt() ) : ?>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>

		<span class="lae-carte__pied lae-lien-fleche">
			En savoir plus <?php echo lae_icone( 'fleche' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</span>
	</div>
</article>
