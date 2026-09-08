<?php
/**
 * Pied de page.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$lae_tel      = lae_reglage( 'telephone' );
$lae_email    = lae_reglage( 'email' );
$lae_adresse  = lae_reglage( 'adresse' );
$lae_horaires = lae_lignes( 'horaires' );
$lae_mention  = lae_reglage( 'siret' );

if ( get_theme_mod( 'lae_appel_partout', true ) && ! is_front_page() ) {
	get_template_part( 'template-parts/section', 'appel' );
}
?>
</main>

<footer class="lae-footer">
	<div class="lae-shell">
		<div class="lae-footer__grille">

			<div>
				<?php lae_marque( 'footer' ); ?>
				<?php if ( $lae_mention ) : ?>
					<p style="margin-top:16px"><?php echo esc_html( $lae_mention ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $lae_tel || $lae_email || $lae_adresse ) : ?>
				<div>
					<h2>Contact</h2>
					<ul class="lae-footer__contact">
						<?php if ( $lae_tel ) : ?>
							<li>
								<?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<a href="<?php echo esc_url( lae_tel_lien() ); ?>"><?php echo esc_html( $lae_tel ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( $lae_email ) : ?>
							<li>
								<?php echo lae_icone( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<a href="mailto:<?php echo esc_attr( $lae_email ); ?>"><?php echo esc_html( $lae_email ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( $lae_adresse ) : ?>
							<li>
								<?php echo lae_icone( 'lieu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span><?php echo nl2br( esc_html( $lae_adresse ) ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $lae_horaires ) : ?>
				<div>
					<h2>Horaires</h2>
					<ul>
						<?php foreach ( $lae_horaires as $lae_h ) : ?>
							<li><?php echo esc_html( $lae_h ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'pied' ) ) : ?>
				<div>
					<h2>Le site</h2>
					<nav aria-label="Menu pied de page">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'pied',
							'container'      => false,
							'fallback_cb'    => false,
							'depth'          => 1,
						) );
						?>
					</nav>
				</div>
			<?php endif; ?>

		</div>

		<div class="lae-footer__bas">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
			<?php if ( has_nav_menu( 'legal' ) ) : ?>
				<nav aria-label="Mentions légales">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'legal',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					) );
					?>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php
$lae_contact = lae_url_contact();
if ( $lae_tel || $lae_contact ) : ?>
	<div class="lae-barre-mobile">
		<?php if ( $lae_tel ) : ?>
			<a class="est-primaire" href="<?php echo esc_url( lae_tel_lien() ); ?>">
				<?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				Appeler
			</a>
		<?php endif; ?>
		<?php if ( $lae_contact ) : ?>
			<a href="<?php echo esc_url( $lae_contact ); ?>">
				<?php echo lae_icone( 'devis' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				Devis
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
