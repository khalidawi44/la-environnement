<?php
/**
 * En-tête de page interne (titre + chapô).
 * Variables attendues via get_template_part( ..., array( 'titre' => ..., 'chapo' => ..., 'fil' => ... ) ).
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$titre = isset( $args['titre'] ) ? $args['titre'] : '';
$chapo = isset( $args['chapo'] ) ? $args['chapo'] : '';
$fil   = isset( $args['fil'] ) ? $args['fil'] : '';
?>
<div class="lae-page-tete">
	<div class="lae-shell">
		<?php if ( $fil ) : ?>
			<p class="lae-fil"><?php echo wp_kses_post( $fil ); ?></p>
		<?php endif; ?>
		<h1><?php echo esc_html( $titre ); ?></h1>
		<?php if ( $chapo ) : ?>
			<p class="lae-chapo"><?php echo esc_html( $chapo ); ?></p>
		<?php endif; ?>
	</div>
</div>
