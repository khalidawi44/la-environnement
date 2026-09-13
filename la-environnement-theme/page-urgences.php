<?php
/**
 * Template Name: Urgences
 *
 * Page dédiée au segment urgence, identifié dans docs/SEO-STRATEGIE.md comme
 * à forte intention et mal couvert par les concurrents locaux. Trois faits
 * client (13/09), et rien d'autre : joignable 24 h/24 et 7 j/7, déplacement
 * le jour même, aucune majoration nuit ni week-end.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$lae_tel      = function_exists( 'lae_reglage' ) ? lae_reglage( 'telephone' ) : '';
$lae_tel_lien = 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $lae_tel );

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
	) );
	?>

	<?php /* Le téléphone d'abord : sur une urgence, on appelle, on ne lit pas. */ ?>
	<?php if ( $lae_tel ) : ?>
		<div class="lae-urgence-appel">
			<div class="lae-shell">
				<p class="lae-urgence-dispo">Joignable 24 h/24, 7 j/7</p>
				<a class="lae-btn lae-btn--primaire lae-btn--tel" href="<?php echo esc_url( $lae_tel_lien ); ?>">
					<?php echo esc_html( $lae_tel ); ?>
				</a>

				<?php /* La consigne de sécurité, reprise des affiches du métier que
				         Fabrice a transmises (14/09) : toutes portent le même
				         avertissement sur les lignes tombées, et pour cause. Elle
				         ne vend rien — elle peut éviter un accident pendant les
				         minutes où la personne attend. C'est aussi ce qui sépare
				         un professionnel d'un numéro de téléphone. */ ?>
				<p class="lae-urgence-consigne">
					<strong>En attendant, ne touchez jamais</strong> une ligne électrique tombée,
					même basse tension, et ne restez pas sous la partie qui menace. Un câble
					arraché : appelez Enedis. La voie publique coupée : les pompiers ou la mairie.
				</p>
			</div>
		</div>
	<?php endif; ?>

	<section class="lae-section">
		<div class="lae-shell">

			<div class="lae-grille lae-grille--3">
				<div class="lae-carte">
					<div class="lae-carte__corps">
						<h2>Jour et nuit</h2>
						<p>Un arbre ne tombe pas aux heures de bureau. Le téléphone est pris 24 h/24, 7 j/7, week-ends et jours fériés compris.</p>
					</div>
				</div>
				<div class="lae-carte">
					<div class="lae-carte__corps">
						<h2>Déplacement le jour même</h2>
						<p>Sur une urgence, on vient voir dans la journée. On sécurise d'abord, on décide ensuite : ce qui menace est traité avant le reste.</p>
					</div>
				</div>
				<div class="lae-carte">
					<div class="lae-carte__corps">
						<h2>Pas de majoration</h2>
						<p>Le tarif est le même la nuit, le dimanche et le 15 août qu'un mardi après-midi. Une urgence n'est pas une occasion de facturer plus.</p>
					</div>
				</div>
			</div>

			<div class="lae-contenu">
				<?php the_content(); ?>
			</div>

		</div>
	</section>

	<?php
endwhile;

get_footer();
