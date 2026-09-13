<?php
/**
 * Template Name: Tarif adapté aux revenus
 *
 * Rend visible une réduction que le client applique déjà. Aucun montant n'est
 * annoncé : le prix d'un chantier sort d'une visite, pas d'un formulaire.
 * Voir inc/lae-tarif.php pour les deux précautions structurelles.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$lae_tranches = function_exists( 'lae_tarif_tranches' ) ? lae_tarif_tranches() : array();
$lae_max      = function_exists( 'lae_tarif_taux_max' ) ? lae_tarif_taux_max() : 0;
$lae_tel      = function_exists( 'lae_reglage' ) ? lae_reglage( 'telephone' ) : '';
$lae_tel_lien = 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $lae_tel );

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
	) );
	?>

	<section class="lae-section">
		<div class="lae-shell">

			<div class="lae-contenu">
				<?php the_content(); ?>
			</div>

			<?php if ( $lae_tranches ) : ?>
				<div class="lae-tarif-grille">
					<?php foreach ( $lae_tranches as $t ) : ?>
						<div class="lae-tarif-ligne">
							<span class="lae-tarif-qui"><?php echo esc_html( $t['libelle'] ); ?></span>
							<span class="lae-tarif-taux">&minus;&nbsp;<?php echo (int) $t['taux']; ?>&nbsp;%</span>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="lae-tarif-note">
					La réduction s'applique sur le devis, après la visite. Elle est
					écrite noir sur blanc dessus : vous voyez le tarif de référence,
					la réduction, et ce que vous payez.
				</p>
			<?php endif; ?>

			<div class="lae-appel">
				<div class="lae-appel__inner">
					<p>Dites-nous votre situation quand vous appelez. Ça ne change rien au travail, ça change ce que vous payez.</p>
					<?php if ( $lae_tel ) : ?>
						<a class="lae-btn lae-btn--primaire" href="<?php echo esc_url( $lae_tel_lien ); ?>"><?php echo esc_html( $lae_tel ); ?></a>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</section>

	<?php
endwhile;

get_footer();
