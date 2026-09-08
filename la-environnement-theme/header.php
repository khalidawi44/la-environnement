<?php
/**
 * En-tête du site.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="lae-saut-lien" href="#contenu">Aller au contenu</a>

<header class="lae-header">
	<div class="lae-shell lae-header__inner">
		<?php lae_marque(); ?>

		<button class="lae-burger" type="button" aria-expanded="false" aria-controls="lae-nav">
			<span class="lae-invisible">Ouvrir le menu</span>
			<?php echo lae_icone( 'menu', 'lae-burger__ouvrir' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo lae_icone( 'fermer', 'lae-burger__fermer' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>

		<nav class="lae-nav" id="lae-nav" aria-label="Menu principal">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'principal',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 1,
			) );
			?>
		</nav>

		<div class="lae-header__actions">
			<?php lae_bouton_tel(); ?>
		</div>
	</div>
</header>

<main class="lae-main" id="contenu">
