<?php
/**
 * Page d'accueil.
 *
 * Chaque section se masque d'elle-même si son contenu n'est pas renseigné :
 * le site ne montre jamais de texte de remplissage.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/section', 'hero' );
get_template_part( 'template-parts/section', 'reassurance' );
get_template_part( 'template-parts/section', 'prestations' );

// Contenu libre saisi dans la page d'accueil (éditeur WordPress).
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$lae_contenu = get_the_content();
		if ( '' !== trim( (string) $lae_contenu ) ) : ?>
			<section class="lae-section">
				<div class="lae-shell lae-shell--etroit lae-contenu">
					<?php the_content(); ?>
				</div>
			</section>
		<?php endif;
	endwhile;
endif;

get_template_part( 'template-parts/section', 'etapes' );
get_template_part( 'template-parts/section', 'realisations' );
get_template_part( 'template-parts/section', 'zone' );
get_template_part( 'template-parts/section', 'appel' );

get_footer();
