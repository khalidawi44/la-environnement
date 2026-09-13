<?php
/**
 * En-tête de page interne (photo + titre + hachure + chapô).
 * Variables via get_template_part( ..., array( 'titre' => ..., 'chapo' => ..., 'fil' => ... ) ).
 *
 * La bande hachurée sous le titre est demandée par Fabrice (13/09). Elle est
 * purement décorative : aria-hidden, et elle ne porte aucune information que
 * le texte ne donnerait pas.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$titre = isset( $args['titre'] ) ? $args['titre'] : '';
$chapo = isset( $args['chapo'] ) ? $args['chapo'] : '';
$fil   = isset( $args['fil'] ) ? $args['fil'] : '';
$illu  = function_exists( 'lae_illustration' ) ? lae_illustration() : null;
$surt  = function_exists( 'lae_entete_surtitre' ) ? lae_entete_surtitre() : '';
?>
<div class="lae-page-tete<?php echo $illu ? ' lae-page-tete--photo' : ''; ?>">

	<?php if ( $illu ) : ?>
		<?php /* La photo est décorative ici : le titre porte le sens, et
		         répéter « élagage » en texte alternatif n'apprendrait rien
		         à quelqu'un qui écoute la page. D'où alt vide. */ ?>
		<div class="lae-page-tete__fond" aria-hidden="true">
			<img src="<?php echo esc_url( $illu['url'] ); ?>" alt=""
			     loading="eager" fetchpriority="high" decoding="async">
		</div>
	<?php endif; ?>

	<div class="lae-shell">
		<?php if ( $fil ) : ?>
			<p class="lae-fil"><?php echo wp_kses_post( $fil ); ?></p>
		<?php endif; ?>
		<?php if ( $surt && 0 !== strcasecmp( $surt, $titre ) ) : ?>
			<p class="lae-pilule"><?php echo esc_html( $surt ); ?></p>
		<?php endif; ?>
		<h1><?php echo esc_html( $titre ); ?></h1>
		<span class="lae-hachure" aria-hidden="true"></span>
		<?php if ( $chapo ) : ?>
			<p class="lae-chapo"><?php echo esc_html( $chapo ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $illu && function_exists( 'lae_tampon' ) ) {
		echo lae_tampon(); // phpcs:ignore WordPress.Security.EscapeOutput
	} ?>

	<?php if ( $illu && function_exists( 'lae_vague' ) ) : ?>
		<?php echo lae_vague(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
</div>
