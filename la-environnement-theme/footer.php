<?php
/**
 * Pied de page.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
	</div>
</main>

<footer class="lae-footer">
	<div class="lae-shell">
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
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
