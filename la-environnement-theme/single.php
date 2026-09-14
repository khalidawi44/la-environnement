<?php
/**
 * Gabarit d'un article.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	/* LE FIL D'ARIANE RAMÈNE AUX CONSEILS. Il ne portait que la date :
	   un visiteur arrivé de Google sur « distance de plantation » n'avait
	   aucun chemin de retour vers les autres articles, et Google aucun
	   signal de hiérarchie. La date reste, après le lien. */
	$lae_blog    = get_option( 'page_for_posts' );
	$lae_lien_bl = $lae_blog ? get_permalink( (int) $lae_blog ) : '';
	$lae_fil     = $lae_lien_bl
		? '<a href="' . esc_url( $lae_lien_bl ) . '">' . esc_html( get_the_title( (int) $lae_blog ) ) . '</a> &rsaquo; ' . esc_html( get_the_date( 'j F Y' ) )
		: esc_html( get_the_date( 'j F Y' ) );
	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
		'fil'   => $lae_fil,
	) );
	?>
	<article <?php post_class( 'lae-contenu' ); ?>>
		<div class="lae-shell">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'lae-large', array( 'class' => 'alignwide', 'loading' => 'lazy' ) );
			}
			the_content();
			wp_link_pages( array( 'before' => '<p class="lae-pagination">', 'after' => '</p>' ) );
			?>
		</div>
	</article>

	<?php
	/* ═══════════════════════════════════════════════════════════════
	   LES ARTICLES ÉTAIENT DES CULS-DE-SAC

	   Audit du 14/09 : les trois articles ne contenaient AUCUN lien.
	   Ni vers une prestation, ni vers les urgences, ni entre eux. Un
	   visiteur arrivé de Google sur « distance de plantation arbre
	   voisin » lisait une réponse juste, puis n'avait nulle part où
	   aller — alors que c'est exactement quelqu'un qui a un arbre qui
	   pose problème.

	   On ne touche PAS au texte des articles. Ils appartiennent au
	   client, ils sont publiés, et y injecter des liens par programme
	   reviendrait à réécrire son contenu dans son dos. Les chemins se
	   posent AUTOUR : le fil d'ariane en haut, ces deux blocs en bas.
	   ═══════════════════════════════════════════════════════════════ */
	$lae_autres = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'rand',
	) );

	$lae_presta = get_posts( array(
		'post_type'      => 'lae_prestation',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );

	if ( $lae_autres || $lae_presta ) : ?>
	<section class="lae-section lae-suite">
		<div class="lae-shell">

			<?php if ( $lae_presta ) : ?>
				<h2 class="lae-suite__t">Un arbre qui pose problème&nbsp;?</h2>
				<p class="lae-suite__p">On vient le voir avant de chiffrer quoi que ce soit.</p>
				<ul class="lae-suite__liste">
					<?php foreach ( $lae_presta as $lae_p ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $lae_p ) ); ?>"><?php echo esc_html( get_the_title( $lae_p ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $lae_autres ) : ?>
				<h2 class="lae-suite__t">À lire aussi</h2>
				<ul class="lae-suite__liste">
					<?php foreach ( $lae_autres as $lae_a ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $lae_a ) ); ?>"><?php echo esc_html( get_the_title( $lae_a ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
