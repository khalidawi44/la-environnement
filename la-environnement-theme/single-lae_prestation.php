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

	   ROTATION CIRCULAIRE, ET NON TIRAGE ALEATOIRE.

	   Le tri d'origine, par `menu_order` croissant, affichait TOUJOURS les
	   trois memes fiches sur les sept pages prestation : creation de jardin,
	   entretien de jardin et evacuation n'etaient liees que depuis deux
	   pages du site, contre onze pour les trois premieres (mesure du 22/09).

	   Le reflexe etait de passer en `orderby => rand`. Essaye et MESURE en
	   ligne : sur quinze releves, entretien de jardin n'est jamais sorti.
	   La raison est que le hasard est tire au moment ou la page est MISE EN
	   CACHE, pas a chaque visite — et depuis le 22/09 le cache tient
	   vingt-quatre heures. Un tirage aleatoire derriere un cache long est
	   fige : il ne tourne plus, il choisit une fois pour toutes.

	   On prend donc les trois fiches qui SUIVENT celle-ci dans l'ordre, en
	   bouclant. Avec sept prestations, chacune est alors liee depuis
	   exactement trois autres pages — repartition uniforme, garantie, et
	   stable d'une visite a l'autre. Des liens stables valent mieux que des
	   liens qui changent : Google et le visiteur y gagnent. */
	$lae_toutes = get_posts( array(
		'post_type'      => 'lae_prestation',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'no_found_rows'  => true,
		'fields'         => 'ids',
	) );

	$lae_rang = array_search( get_the_ID(), $lae_toutes, true );
	$lae_ids  = array();
	if ( false !== $lae_rang && count( $lae_toutes ) > 1 ) {
		$lae_n = count( $lae_toutes );
		for ( $i = 1; $i <= min( 3, $lae_n - 1 ); $i++ ) {
			$lae_ids[] = $lae_toutes[ ( $lae_rang + $i ) % $lae_n ];
		}
	}

	$autres = new WP_Query( array(
		'post_type'           => 'lae_prestation',
		'post__in'            => $lae_ids ? $lae_ids : array( 0 ),
		'orderby'             => 'post__in',
		'posts_per_page'      => 3,
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
