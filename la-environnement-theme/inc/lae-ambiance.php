<?php
/**
 * L'ambiance : le site suit la saison et l'heure.
 *
 * Idée de Fabrice (14/09). Elle est juste pour ce métier : un élagueur ne
 * fait pas la même chose en janvier et en juin, et celui qu'on appelle à
 * 23 h n'a pas le même problème que celui qui compare des devis à 14 h.
 * Le site le dit sans l'écrire.
 *
 * DEUX MÉCANIQUES DIFFÉRENTES, ET C'EST LE CŒUR DU SUJET.
 *
 * LA SAISON se calcule en PHP. Elle est la même pour tout le monde et ne
 * change que quatre fois par an : le cache HTTP la digère sans broncher.
 *
 * L'HEURE ne peut PAS se calculer en PHP. D'abord parce que le site est
 * servi par un cache de 5 minutes et par un CDN : une page rendue à 19 h 58
 * resservirait « jour » à 20 h 03. Ensuite et surtout parce que l'heure qui
 * compte est celle du VISITEUR, pas celle du serveur. Elle est donc posée
 * par un script en tête de page, avant le premier rendu — exactement le
 * motif que le thème utilise déjà pour la classe `js-cine`. Aucune
 * variation de cache, aucun scintillement.
 *
 * Sans JavaScript, `data-moment` vaut « jour » : la version la plus neutre
 * et la plus fréquente, jamais une page cassée.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Saison courante, d'après la date du serveur réglée sur le fuseau du site.
 *
 * Bornes astronomiques approchées : elles bougent d'un jour selon les
 * années, et personne ne regarde son écran le 20 mars pour vérifier.
 *
 * @return string hiver|printemps|ete|automne
 */
function lae_saison() {
	$j = (int) wp_date( 'z' );          // jour de l'année, 0 à 365
	if ( $j >= 355 || $j < 78 )  return 'hiver';      // ~21 déc → ~19 mars
	if ( $j < 171 )              return 'printemps';  // ~20 mars → ~20 juin
	if ( $j < 265 )              return 'ete';        // ~21 juin → ~22 sept
	return 'automne';                                 // ~23 sept → ~20 déc
}

/**
 * Moment de la journée à partir d'une heure donnée.
 *
 * Les bornes sont larges à dessein : on cherche une ambiance, pas une
 * éphéméride. Le crépuscule couvre les deux bascules, matin et soir.
 *
 * @param int $h Heure locale, 0 à 23.
 * @return string jour|crepuscule|nuit
 */
function lae_moment( $h ) {
	$h = (int) $h;
	if ( $h >= 8 && $h < 18 )  return 'jour';
	if ( $h >= 22 || $h < 6 )  return 'nuit';
	return 'crepuscule';
}

/* Les attributs voyagent sur <html> : toute la feuille de style peut s'y
   accrocher, et le sélecteur reste lisible — :root[data-saison="automne"]. */
add_filter( 'language_attributes', function ( $sortie ) {
	return $sortie
		. ' data-saison="' . esc_attr( lae_saison() ) . '"'
		. ' data-moment="jour"';
} );

/**
 * Le script qui corrige le moment avec l'heure du visiteur.
 *
 * Posé le plus tôt possible dans <head>, avant la feuille de style, pour
 * que la première peinture soit déjà la bonne. Il ne fait qu'écrire un
 * attribut : s'il échoue, la page reste en « jour ».
 */
add_action( 'wp_head', function () {
	?>
<script>
/* Ambiance : l'heure du visiteur, pas celle du serveur — et donc pas celle
   du cache. Écrit avant le premier rendu, donc sans scintillement. */
(function(){try{
  var h=new Date().getHours(),m='crepuscule';
  if(h>=8&&h<18)m='jour'; else if(h>=22||h<6)m='nuit';
  document.documentElement.setAttribute('data-moment',m);
}catch(e){}}());
</script>
	<?php
}, 1 );


/**
 * Les trois bandeaux d'accueil : le jour, le crépuscule, la nuit.
 *
 * LA MÊME FORÊT À TROIS HEURES. C'est ce qui rend l'idée de Fabrice
 * lisible plutôt que subtile : ce n'est pas une teinte qui change, c'est le
 * moment de la journée. Quelqu'un dont un arbre tombe à 23 h arrive sur un
 * site éclairé à la lune — l'astreinte 24 h/24 n'est plus une phrase, c'est
 * ce qu'il voit avant de lire.
 *
 * UNE SEULE DES TROIS EST TÉLÉCHARGÉE. Elles sont posées en fond CSS et non
 * en balise <img> : le navigateur ne charge que l'image dont la règle
 * s'applique. Trois <img> auraient coûté 800 ko pour n'en montrer qu'une.
 * Le revers — un fond CSS échappe au scanner de préchargement, et le
 * bandeau est l'élément que Google chronomètre — est réglé par le lien de
 * préchargement posé plus bas, dans <head>, donc plus tôt qu'une <img>
 * placée dans le corps de page.
 *
 * LA PHOTO DE JOUR RESTE RÉGLABLE (`hero_image`) : c'est celle que le client
 * voudra changer. Les deux autres ne le sont pas — une photo de jour posée
 * dans le réglage « nuit » casserait tout l'effet sans prévenir, et ces
 * deux-là n'ont de sens que si elles sont réellement crépusculaires et
 * nocturnes.
 *
 * @return array{jour:string,crepuscule:string,nuit:string} URL absolues.
 */
function lae_ambiance_hero() {
	$dir  = get_template_directory_uri();
	$base = '/assets/images/ambiance/';

	$fichier = function ( $nom ) use ( $dir, $base ) {
		$rel = $base . $nom . '.webp';
		return file_exists( get_template_directory() . $rel ) ? $dir . $rel : '';
	};

	/* LA SAISON CHOISIT LA FAMILLE DE PHOTOS, L'HEURE CHOISIT LAQUELLE.
	   Le jeu livré est automnal ; une famille saisonnière se greffe dessus
	   en nommant les fichiers `sous-bois-<famille>-<moment>.webp`. Ce qui
	   manque retombe sur le jeu par défaut, fichier par fichier — on peut
	   donc ajouter UNE photo à la fois sans rien casser.

	   Le printemps partage la famille de l'été : un feuillage vert est un
	   feuillage vert, et c'est la teinte de saison (style.css section 16)
	   qui distingue avril de juillet. L'hiver reste sur le jeu par défaut,
	   faute de photo de saison froide — sa teinte bleue fait le travail.

	   La saison se décide ICI, côté serveur, et c'est possible parce
	   qu'elle ne change que quatre fois par an : le cache de 5 minutes la
	   digère. L'heure, elle, ne peut pas — voir l'en-tête du fichier. */
	$famille = '';
	$saison  = lae_saison();
	if ( 'ete' === $saison || 'printemps' === $saison ) {
		$famille = 'ete-';
	}

	$photo = function ( $moment ) use ( $fichier, $famille ) {
		$saisonnier = $famille ? $fichier( 'sous-bois-' . $famille . $moment ) : '';
		return $saisonnier ? $saisonnier : $fichier( 'sous-bois-' . $moment );
	};

	$jour = lae_reglage( 'hero_image' );
	if ( '' === $jour ) {
		$jour = $photo( 'jour' );
	}
	if ( '' === $jour ) {
		// Dernier repli : une vraie photo de chantier de l'entreprise.
		$jour = $dir . '/assets/images/chantiers/reduction-couronne-grimpeur.webp';
	}

	/* Repli sur le jour, jamais sur rien : un bandeau vide serait pire
	   qu'un bandeau qui ne change pas d'heure. */
	$crep = $photo( 'crepuscule' );
	$nuit = $photo( 'nuit' );

	return array(
		'jour'       => $jour,
		'crepuscule' => $crep ? $crep : $jour,
		'nuit'       => $nuit ? $nuit : $jour,
	);
}

/**
 * Précharger la seule photo de bandeau qui va s'afficher.
 *
 * Écrit juste après le script qui pose `data-moment`, donc l'attribut est
 * déjà là quand celui-ci s'exécute. Sans JavaScript : pas de préchargement,
 * et le fond du jour charge normalement à la lecture de la feuille de
 * style. Rien de cassé, juste non préchargé.
 */
add_action( 'wp_head', function () {
	if ( ! is_front_page() ) {
		return;
	}
	$h = lae_ambiance_hero();
	?>
<script>
(function(){try{
  var m=document.documentElement.getAttribute('data-moment'),
      u={jour:<?php echo wp_json_encode( $h['jour'] ); ?>,
         crepuscule:<?php echo wp_json_encode( $h['crepuscule'] ); ?>,
         nuit:<?php echo wp_json_encode( $h['nuit'] ); ?>}[m||'jour'],
      l=document.createElement('link');
  l.rel='preload';l.as='image';l.href=u;l.setAttribute('fetchpriority','high');
  document.head.appendChild(l);
}catch(e){}}());
</script>
	<?php
}, 2 );
