<?php
/**
 * Les trois métiers, racontés — « La cime, le tronc, les racines ».
 *
 * POURQUOI CE BLOC EXISTE ICI ET PLUS SUR L'ACCUEIL. Il y vivait jusqu'au
 * 14/09, où il a été retiré pour raccourcir la page : il répétait, en trois
 * fois plus long, ce que la scène des prestations disait juste au-dessus.
 * Mais le supprimer aurait coûté trois blocs de texte réel sur l'élagage,
 * l'abattage et le jardin — sur un site de cinq pages, c'est cher payé pour
 * un écran et demi. Fabrice a tranché le déplacement : il atterrit sur
 * « Notre façon de travailler », où le récit cime → tronc → racines
 * prolonge la page au lieu de doubler l'accueil.
 *
 * CE QUI CHANGE PAR RAPPORT À LA VERSION ACCUEIL, et c'est volontaire :
 *
 * 1. Aucun attribut d'animation (`data-rv`, `data-mots`, `data-txt`,
 *    `data-media`). GSAP ne tourne que sur l'accueil ; les porter ici
 *    n'animerait rien et laisserait des crochets morts dans le balisage —
 *    exactement le défaut qui avait rendu `hero_image` invisible pendant
 *    des semaines.
 * 2. Des classes `lae-` et une feuille claire. L'ancien bloc tirait ses
 *    couleurs de jetons qui n'existent que dans le <style> de l'accueil
 *    (--serif, --feuille, --muted) et supposait un fond sombre. Recopié
 *    tel quel sur une page blanche, il aurait été illisible.
 * 3. `<h2>` et non `<h3>` : sur l'accueil le bloc suivait un titre de
 *    section, ici il est de premier niveau sous le <h1> de la page.
 *
 * La source de contenu ne change pas : réglage « Accueil cinématique →
 * Chapitres » du personnalisateur, une ligne par métier,
 * « Titre | Texte | mot, mot, mot », plus une image par chapitre
 * (`cine_ch1_image`…). Le client modifie au même endroit qu'avant.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$lae_chs = lae_lignes( 'cine_chapitres' );
if ( ! $lae_chs ) {
	return; // Réglage vidé : le bloc disparaît, il ne laisse pas de coquille.
}

$lae_dir = get_template_directory_uri();
?>
<section class="lae-chapitres" aria-labelledby="lae-chapitres-t">
	<div class="lae-shell">
		<h2 id="lae-chapitres-t" class="lae-chapitres__t">Nos trois métiers</h2>
		<span class="lae-hachure" aria-hidden="true"></span>

		<?php foreach ( $lae_chs as $lae_i => $lae_ligne ) :
			list( $lae_t, $lae_p, $lae_meta ) = lae_morceaux( $lae_ligne, 3 );

			$lae_ch_img = lae_reglage( 'cine_ch' . ( $lae_i + 1 ) . '_image' );
			if ( '' === $lae_ch_img ) {
				/* Photos de chantier fournies par l'entreprise, une par métier :
				   la cime (grimpe), le tronc (abattage), les racines (jardin). */
				$lae_ch_defauts = array(
					0 => 'elagage-grimpe.webp',
					1 => 'abattage-troncs.webp',
					2 => 'pelouse-haie.webp',
				);
				if ( isset( $lae_ch_defauts[ $lae_i ] ) ) {
					$lae_ch_img = $lae_dir . '/assets/images/' . $lae_ch_defauts[ $lae_i ];
				}
			}
			?>
			<article class="lae-chapitre<?php echo $lae_ch_img ? '' : ' lae-chapitre--nu'; ?>">
				<div class="lae-chapitre__txt">
					<p class="lae-chapitre__n" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $lae_i + 1 ) ); ?></p>
					<h3 class="lae-chapitre__t"><?php echo lae_titre_em( $lae_t ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
					<?php if ( $lae_p ) : ?>
						<p class="lae-chapitre__p"><?php echo esc_html( $lae_p ); ?></p>
					<?php endif; ?>
					<?php if ( $lae_meta ) : ?>
						<ul class="lae-chapitre__meta">
							<?php foreach ( array_filter( array_map( 'trim', explode( ',', $lae_meta ) ) ) as $lae_mot ) : ?>
								<li><?php echo esc_html( $lae_mot ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php if ( $lae_ch_img ) : ?>
					<?php /* Décorative : le titre et le texte à côté portent le sens. */ ?>
					<div class="lae-chapitre__media" aria-hidden="true">
						<?php echo lae_img( $lae_ch_img, '' ); ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
