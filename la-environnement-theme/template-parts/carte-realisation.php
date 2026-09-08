<?php
/**
 * Vignette d'une réalisation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$types = get_the_terms( get_the_ID(), 'lae_type_chantier' );
?>
<a class="lae-realisation" href="<?php the_permalink(); ?>">
	<?php
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( 'lae-carte', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) );
	}
	?>
	<span class="lae-realisation__voile">
		<?php if ( $types && ! is_wp_error( $types ) ) : ?>
			<span class="lae-realisation__meta"><?php echo esc_html( $types[0]->name ); ?></span>
		<?php endif; ?>
		<h3><?php the_title(); ?></h3>
	</span>
</a>
