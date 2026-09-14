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

				<?php
				/* DEUX COLONNES, PLUS TROIS. Relevé par Fabrice le 14/09 : sur
				   un téléphone, le tableau dépassait de l'écran. Mesuré — il
				   était forcé à 480 px de large (`min-width:30rem`) dans un
				   cadre de 346 px, et c'est la COLONNE DES PRIX qui sortait,
				   c'est-à-dire la seule qu'on vient lire. Le lecteur voyait
				   « 30 à 70 € H… » et « à partir de 4… ».
				   La zone ne mérite pas une colonne à elle : c'est une
				   précision sur la ligne, pas une donnée qu'on compare. Elle
				   passe sous le libellé, le tableau tombe à deux colonnes et
				   tient dans n'importe quel écran sans défilement latéral. */
				?>
				<div class="lae-marche-cadre">
					<table class="lae-marche">
						<?php /* Légende lue par les lecteurs d'écran, pas affichée : le
						     paragraphe juste au-dessus dit déjà la même chose à
						     l'œil, et la répéter ferait doublon. */ ?>
						<caption class="lae-invisible">Fourchettes de prix constatées sur le marché, par prestation et par zone.</caption>
						<thead>
							<tr><th scope="col">Prestation</th><th scope="col">Prix constatés</th></tr>
						</thead>
						<tbody>
							<?php foreach ( $lae_marche as $m ) : ?>
								<tr>
									<td>
										<span class="lae-marche__quoi"><?php echo esc_html( $m['quoi'] ); ?></span>
										<span class="lae-marche__zone"><?php echo esc_html( $m['zone'] ); ?></span>
									</td>
									<td class="lae-marche__prix"><?php echo esc_html( $m['fourchette'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<?php if ( $lae_ecart[0] && $lae_ecart[1] ) : ?>
					<?php /* La deuxième moitié de ce paragraphe — « l'accès, la hauteur
					         et l'évacuation ne se devinent pas depuis un écran » — a été
					         retirée le 14/09 : la section « Comment nous chiffrons » qui
					         suit dit exactement la même chose, deux blocs plus bas. La
					         même phrase deux fois affaiblit les deux. */ ?>
					<p class="lae-tarif-position">
						<strong>Notre devis se situe <?php echo (int) $lae_ecart[0]; ?> à <?php echo (int) $lae_ecart[1]; ?>&nbsp;% sous le milieu de ces fourchettes</strong>,
						à prestation équivalente et garanties identiques.
					</p>
				<?php endif; ?>

				<?php if ( $lae_releve ) : ?>
					<p class="lae-marche__source">Fourchettes relevées le <?php echo esc_html( $lae_releve ); ?> sur des barèmes publics du secteur (Loire-Atlantique et moyennes nationales). Elles bougent : si vous avez un devis en main qui dit autre chose, montrez-le-nous.</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			/* LE FORFAIT — information donnée par Anthony et rapportée par
			   Fabrice le 14/09. Elle est ici, juste après le comparatif,
			   parce que c'est elle qui explique pourquoi les prix du marché
			   sont affichés et les nôtres non : les autres chiffrent à
			   l'heure ou à l'unité, lui chiffre un chantier entier.
			   Rien n'est écrit ici qui n'ait été dit : il travaille au
			   forfait, une haie se chiffre au mètre linéaire, et le forfait
			   comprend tout, broyage compris. Le reste — délais, garanties —
			   n'a pas été dit, donc n'est pas écrit. */
			?>
			<h2 class="lae-tarif-h2">Comment nous chiffrons, nous</h2>
			<div class="lae-forfait">
				<p class="lae-forfait__phrase"><strong>Nous travaillons au forfait, et le forfait comprend tout.</strong></p>
				<ul class="lae-forfait__liste">
					<li><strong>Un chantier, un prix.</strong> Pas un taux horaire qui court, pas une ligne qui s'ajoute à la fin. Ce qui est annoncé est ce qui est payé.</li>
					<li><strong>Le broyage est dedans.</strong> C'est souvent ce qui fait la différence entre deux devis : couper un arbre coûte une chose, faire disparaître ce qui reste au sol en coûte une autre. Chez nous, les deux sont dans le même chiffre.</li>
					<li><strong>Une haie se chiffre au mètre linéaire.</strong> Mais le prix au mètre n'est pas le même partout : la hauteur, la largeur, ce qu'il y a derrière et la place pour travailler le font bouger du simple au double.</li>
				</ul>
				<p class="lae-forfait__pourquoi">
					<strong>C'est pour ça qu'aucun prix n'est affiché sur cette page pour nos chantiers, et c'est volontaire.</strong>
					Un chiffre mis en ligne sans avoir vu le terrain est soit trop haut — et vous payez la
					prudence de celui qui l'a écrit — soit trop bas, et il faudra bien le rattraper quelque
					part. L'accès, la hauteur, ce qu'il y a en dessous et ce qu'il faut évacuer ne se devinent
					pas depuis un écran. On vient voir, on annonce un forfait, et il ne bouge plus.
				</p>
			</div>

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
