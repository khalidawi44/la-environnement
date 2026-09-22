<?php
/**
 * Liste des prestations.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

/* Le <h1> portait « Nos prestations » : pas un mot-clé, pas une commune, sur
   la page qui liste justement ce qu'on vend. Le <title> est bien écrit depuis
   le 14/09, mais le grand titre visible ne disait rien à Google ni au
   visiteur arrive d'une recherche. Il nomme desormais les trois metiers et la
   ville. Ce n'est qu'une valeur de REPLI : une saisie du personnalisateur la
   remplace toujours, rien n'est ecrase. */
/* CLÉ DISTINCTE DE CELLE DE L'ACCUEIL — et c'est le fond du correctif.

   `lae_prestations_titre` et `lae_realisations_titre` alimentent le <h2> des
   sections de l'ACCUEIL, où ils sont rendus par lae_titre_em() qui convertit
   la convention *mot* en <em>. Les réutiliser ici créait deux pièges :

   1. le jour où Anthony saisit un titre de section d'accueil dans le
      personnalisateur, le <h1> de cette archive changerait en silence pour
      la même chaîne, sans qu'aucune interface ne le lui dise ;
   2. entete-page.php fait esc_html() sans conversion : une saisie avec
      astérisques afficherait des « * » littéraux dans le <h1>.

   Une clé propre à l'archive supprime les deux. Elle n'est pas exposée dans
   le personnalisateur : c'est un défaut éditorial, pas un réglage client. */
get_template_part( 'template-parts/entete', 'page', array(
	'titre' => lae_reglage( 'archive_prestations_titre', 'Élagage, abattage et création de jardin à Vertou' ),
	'chapo' => lae_reglage( 'prestations_chapo' ),
) );
?>

<div class="lae-section">
	<div class="lae-shell">
		<?php if ( have_posts() ) : ?>
			<div class="lae-grille lae-grille--3">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/carte', 'prestation' );
				endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'lae-pagination', 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="lae-vide">Les prestations seront bientôt en ligne.</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
