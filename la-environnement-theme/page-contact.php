<?php
/**
 * Gabarit de la page Contact (slug `contact`).
 *
 * WordPress l'applique tout seul à la page dont le slug est `contact` ;
 * si elle est renommée, le site retombe sur page.php sans rien casser.
 *
 * POURQUOI CE FICHIER. Audit SEO du 14/09 : la page Contact comptait
 * 135 mots, dont environ 70 de libellés de formulaire. Elle ne portait
 * ni adresse, ni disponibilité, ni zone d'intervention — l'adresse
 * n'apparaissait que dans le pied de page commun aux neuf pages. Or
 * c'est la page où quelqu'un décide de décrocher : il doit y lire qu'on
 * vient chez lui, et quand.
 *
 * La zone d'intervention est la même section que sur l'accueil, avec les
 * mêmes treize communes — une seule source (`zone_communes`), qui
 * alimente aussi le `areaServed` des données structurées. Le site, la
 * page contact et Google disent donc la même chose sans qu'on ait à
 * l'entretenir à trois endroits.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/entete', 'page', array(
		'titre' => get_the_title(),
		'chapo' => has_excerpt() ? get_the_excerpt() : '',
	) );
	?>
	<article <?php post_class( 'lae-contenu' ); ?>>
		<div class="lae-shell">
			<?php
			the_content();
			wp_link_pages( array( 'before' => '<p class="lae-pagination">', 'after' => '</p>' ) );
			?>
		</div>
	</article>
	<?php
	/* Après le formulaire : on ne fait pas patienter quelqu'un qui veut
	   écrire. Il envoie son message, puis il lit où on intervient. */
	get_template_part( 'template-parts/section', 'zone' );
endwhile;

get_footer();
