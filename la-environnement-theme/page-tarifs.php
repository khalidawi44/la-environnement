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
$lae_niveaux  = function_exists( 'lae_tarif_niveaux' ) ? lae_tarif_niveaux() : array();
$lae_marche   = function_exists( 'lae_tarif_marche' ) ? lae_tarif_marche() : array();
$lae_ecart    = function_exists( 'lae_tarif_ecart' ) ? lae_tarif_ecart() : array( 0, 0 );
$lae_releve   = function_exists( 'lae_tarif_marche_releve' ) ? lae_tarif_marche_releve() : '';
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


			<?php if ( $lae_niveaux ) : ?>
				<h2 class="lae-tarif-h2">Le marché se joue à trois niveaux</h2>
				<div class="lae-niveaux">
					<?php foreach ( $lae_niveaux as $n ) : ?>
						<div class="lae-niveau lae-niveau--<?php echo esc_attr( $n['cle'] ); ?>">
							<h3 class="lae-niveau__titre"><?php echo esc_html( $n['titre'] ); ?></h3>
							<p class="lae-niveau__texte"><?php echo esc_html( $n['texte'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
				<p class="lae-tarif-cle">On ne vend pas moins cher en rognant sur le travail. On vend moins cher parce qu&rsquo;il y a moins de structure à payer.</p>
			<?php endif; ?>

			<?php if ( $lae_marche ) : ?>
				<h2 class="lae-tarif-h2">Ce que coûte un chantier ailleurs</h2>
				<p class="lae-tarif-intro">Des fourchettes constatées sur le marché, pas les tarifs d&rsquo;une entreprise en particulier. Elles servent de repère : c&rsquo;est en dessous que nous nous plaçons.</p>

				<div class="lae-marche-cadre">
					<table class="lae-marche">
						<thead>
							<tr><th scope="col">Prestation</th><th scope="col">Prix constatés</th><th scope="col">Zone</th></tr>
						</thead>
						<tbody>
							<?php foreach ( $lae_marche as $m ) : ?>
								<tr>
									<td><?php echo esc_html( $m['quoi'] ); ?></td>
									<td class="lae-marche__prix"><?php echo esc_html( $m['fourchette'] ); ?></td>
									<td class="lae-marche__zone"><?php echo esc_html( $m['zone'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<?php if ( $lae_ecart[0] && $lae_ecart[1] ) : ?>
					<p class="lae-tarif-position">
						<strong>Notre devis se situe <?php echo (int) $lae_ecart[0]; ?> à <?php echo (int) $lae_ecart[1]; ?>&nbsp;% sous le milieu de ces fourchettes</strong>,
						à prestation équivalente et garanties identiques. Le chiffre exact dépend du chantier :
						l&rsquo;accès, la hauteur, le risque et l&rsquo;évacuation ne se devinent pas depuis un écran.
						C&rsquo;est pour ça qu&rsquo;on vient voir avant de chiffrer.
					</p>
				<?php endif; ?>

				<?php if ( $lae_releve ) : ?>
					<p class="lae-marche__source">Fourchettes relevées le <?php echo esc_html( $lae_releve ); ?> sur des barèmes publics du secteur (Loire-Atlantique et moyennes nationales). Elles bougent : si vous avez un devis en main qui dit autre chose, montrez-le-nous.</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $lae_tranches ) : ?>
				<h2 class="lae-tarif-h2">Et une réduction selon ce que vous gagnez</h2>
				<p class="lae-tarif-intro">Le même travail, le même soin, le même délai. Ce qui change, c&rsquo;est ce que vous payez — parce qu&rsquo;un arbre dangereux ne devient pas moins dangereux quand on n&rsquo;a pas les moyens de le faire tomber.</p>

				<div class="lae-tarif-grille">
					<?php foreach ( $lae_tranches as $t ) : ?>
						<div class="lae-tarif-ligne">
							<span class="lae-tarif-qui"><?php echo esc_html( $t['libelle'] ); ?></span>
							<span class="lae-tarif-taux">&minus;&nbsp;<?php echo (int) $t['taux']; ?>&nbsp;%</span>
						</div>
					<?php endforeach; ?>
					<div class="lae-tarif-ligne lae-tarif-ligne--plein">
						<span class="lae-tarif-qui">Toute autre situation</span>
						<span class="lae-tarif-taux lae-tarif-taux--plein">tarif de référence</span>
					</div>
				</div>

				<p class="lae-tarif-note">
					La réduction s'applique sur le devis, après la visite. Elle est
					écrite noir sur blanc dessus : vous voyez le tarif de référence,
					la réduction, et ce que vous payez. Nous ne demandons aucun
					justificatif — vous nous dites votre situation, on vous croit.
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
