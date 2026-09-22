<?php
/**
 * Détail d'une prestation.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
		'fil'   => '<a href="' . esc_url( get_post_type_archive_link( 'lae_prestation' ) ) . '">Prestations</a>',
	) );
	?>
	<article <?php post_class( 'lae-contenu' ); ?>>
		<div class="lae-shell">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'lae-large', array( 'class' => 'alignwide', 'loading' => 'lazy' ) );
			}
			the_content();
			?>
		</div>
	</article>

	<?php
	/* LE CHANTIER QUI ILLUSTRE CETTE PRESTATION — l'autre sens du lien.
	   Une fiche prestation decrit ; un chantier prouve. Les deux ne se
	   liaient pas, alors meme qu'il n'y a qu'un chantier gazon et qu'une
	   prestation gazon. */
	$lae_preuves = function_exists( 'lae_maillage_chantiers' ) ? lae_maillage_chantiers( get_the_ID(), 2 ) : array();
	if ( $lae_preuves ) : ?>
		<section class="lae-section lae-suite">
			<div class="lae-shell">
				<h2 class="lae-suite__t">Vu sur un chantier</h2>
				<p class="lae-suite__p">Des photos prises sur place, avant et après.</p>
				<ul class="lae-suite__liste">
					<?php foreach ( $lae_preuves as $lae_pr ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $lae_pr ) ); ?>"><?php echo esc_html( get_the_title( $lae_pr ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif;

	/* Les autres prestations, pour ne pas laisser la page sans suite.

	   TRI ALEATOIRE, et ce n'est pas un detail. Le tri par `menu_order`
	   affichait TOUJOURS les trois memes fiches, sur les sept pages
	   prestation comme sur les trois articles : creation de jardin,
	   entretien de jardin et evacuation n'etaient liees que depuis deux
	   pages du site, contre onze pour les trois premieres. Mesure du 22/09.
	   `single.php` fait deja tourner ses articles de cette facon. */
	$autres = new WP_Query( array(
		'post_type'           => 'lae_prestation',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'orderby'             => 'rand',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );

	if ( $autres->have_posts() ) : ?>
		<section class="lae-section lae-section--doux">
			<div class="lae-shell">
				<div class="lae-section__tete">
					<h2>Autres prestations</h2>
				</div>
				<div class="lae-grille lae-grille--3">
					<?php while ( $autres->have_posts() ) : $autres->the_post();
						get_template_part( 'template-parts/carte', 'prestation' );
					endwhile; ?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();

endwhile;

get_footer();
