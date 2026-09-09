<?php
/**
 * Formulaire de contact — sans plugin.
 *
 * Court-circuit volontaire : pas de base de données, pas de stockage. Le
 * message part par e-mail à l'adresse renseignée dans le personnalisateur,
 * et rien n'est conservé sur le site — c'est la position la plus simple à
 * tenir vis-à-vis du RGPD, et la moins exposée en cas de fuite.
 *
 * Protections : jeton de session (nonce), champ leurre invisible aux humains,
 * et une limite de trois envois par heure et par adresse IP.
 *
 * Usage : le raccourci [lae_contact] dans n'importe quelle page.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Clé du compteur anti-abus pour l'adresse IP courante. */
if ( ! function_exists( 'lae_contact_cle_ip' ) ) {
	function lae_contact_cle_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return 'lae_contact_' . md5( $ip );
	}
}

/**
 * Traite l'envoi. Renvoie un tableau { type, message } ou null si rien à faire.
 *
 * @return array|null
 */
if ( ! function_exists( 'lae_contact_traite' ) ) {
	function lae_contact_traite() {

		if ( ! isset( $_POST['lae_contact_envoi'] ) ) {
			return null;
		}

		if ( ! isset( $_POST['lae_contact_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['lae_contact_nonce'] ) ), 'lae_contact' ) ) {
			return array( 'type' => 'erreur', 'message' => 'Votre session a expiré. Rechargez la page et réessayez.' );
		}

		// Champ leurre : rempli, c'est un robot. On répond comme si tout allait bien.
		if ( ! empty( $_POST['lae_site_web'] ) ) {
			return array( 'type' => 'ok', 'message' => 'Message envoyé.' );
		}

		$cle = lae_contact_cle_ip();
		$n   = (int) get_transient( $cle );
		if ( $n >= 3 ) {
			return array( 'type' => 'erreur', 'message' => 'Trop de messages envoyés depuis cette connexion. Réessayez dans une heure, ou appelez-nous.' );
		}

		$nom     = sanitize_text_field( wp_unslash( $_POST['lae_nom'] ?? '' ) );
		$tel     = sanitize_text_field( wp_unslash( $_POST['lae_tel'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['lae_email'] ?? '' ) );
		$ville   = sanitize_text_field( wp_unslash( $_POST['lae_ville'] ?? '' ) );
		$objet   = sanitize_text_field( wp_unslash( $_POST['lae_objet'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['lae_message'] ?? '' ) );
		$accord  = ! empty( $_POST['lae_accord'] );

		if ( '' === $nom || '' === $message ) {
			return array( 'type' => 'erreur', 'message' => 'Indiquez au moins votre nom et votre demande.' );
		}
		if ( '' === $tel && '' === $email ) {
			return array( 'type' => 'erreur', 'message' => 'Laissez un téléphone ou un e-mail, sans quoi nous ne pourrons pas vous répondre.' );
		}
		if ( ! $accord ) {
			return array( 'type' => 'erreur', 'message' => 'Merci de cocher la case d\'accord avant d\'envoyer.' );
		}

		$dest = lae_reglage( 'email' );
		if ( ! $dest || ! is_email( $dest ) ) {
			return array( 'type' => 'erreur', 'message' => 'Le formulaire n\'est pas encore relié à une adresse. Appelez-nous en attendant.' );
		}

		$corps  = "Nouvelle demande depuis le site.\n\n";
		$corps .= 'Nom : ' . $nom . "\n";
		$corps .= 'Téléphone : ' . ( $tel ? $tel : '—' ) . "\n";
		$corps .= 'E-mail : ' . ( $email ? $email : '—' ) . "\n";
		$corps .= 'Commune : ' . ( $ville ? $ville : '—' ) . "\n";
		$corps .= 'Objet : ' . ( $objet ? $objet : '—' ) . "\n\n";
		$corps .= "Demande :\n" . $message . "\n";

		$entetes = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( $email ) {
			$entetes[] = 'Reply-To: ' . $nom . ' <' . $email . '>';
		}

		$envoye = wp_mail(
			$dest,
			'[' . get_bloginfo( 'name' ) . '] Demande de ' . $nom,
			$corps,
			$entetes
		);

		set_transient( $cle, $n + 1, HOUR_IN_SECONDS );

		return $envoye
			? array( 'type' => 'ok', 'message' => 'Message envoyé. Nous vous rappelons pour convenir d\'une visite sur place.' )
			: array( 'type' => 'erreur', 'message' => 'L\'envoi a échoué. Appelez-nous, c\'est plus sûr.' );
	}
}

/** Le formulaire, via le raccourci [lae_contact]. */
if ( ! function_exists( 'lae_contact_formulaire' ) ) {
	function lae_contact_formulaire() {

		$retour = lae_contact_traite();

		ob_start();
		?>
		<div class="lae-contact">
			<?php if ( $retour ) : ?>
				<p class="lae-contact__retour lae-contact__retour--<?php echo esc_attr( $retour['type'] ); ?>" role="status">
					<?php echo esc_html( $retour['message'] ); ?>
				</p>
			<?php endif; ?>

			<form class="lae-contact__form" method="post" action="">
				<?php wp_nonce_field( 'lae_contact', 'lae_contact_nonce' ); ?>

				<p class="lae-contact__leurre" aria-hidden="true">
					<label for="lae_site_web">Ne remplissez pas ce champ</label>
					<input type="text" id="lae_site_web" name="lae_site_web" tabindex="-1" autocomplete="off">
				</p>

				<div class="lae-contact__duo">
					<p>
						<label for="lae_nom">Votre nom <span aria-hidden="true">*</span></label>
						<input type="text" id="lae_nom" name="lae_nom" required autocomplete="name"
							value="<?php echo esc_attr( wp_unslash( $_POST['lae_nom'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>">
					</p>
					<p>
						<label for="lae_ville">Commune du chantier</label>
						<input type="text" id="lae_ville" name="lae_ville" autocomplete="address-level2"
							value="<?php echo esc_attr( wp_unslash( $_POST['lae_ville'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>">
					</p>
				</div>

				<div class="lae-contact__duo">
					<p>
						<label for="lae_tel">Téléphone</label>
						<input type="tel" id="lae_tel" name="lae_tel" autocomplete="tel"
							value="<?php echo esc_attr( wp_unslash( $_POST['lae_tel'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>">
					</p>
					<p>
						<label for="lae_email">E-mail</label>
						<input type="email" id="lae_email" name="lae_email" autocomplete="email"
							value="<?php echo esc_attr( wp_unslash( $_POST['lae_email'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>">
					</p>
				</div>

				<p>
					<label for="lae_objet">Ce qui vous amène</label>
					<select id="lae_objet" name="lae_objet">
						<option value="">— Choisir —</option>
						<option value="Élagage">Élagage</option>
						<option value="Abattage ou démontage">Abattage ou démontage</option>
						<option value="Haubanage / arbre fragilisé">Haubanage / arbre fragilisé</option>
						<option value="Création de jardin">Création de jardin</option>
						<option value="Entretien">Entretien</option>
						<option value="Urgence après tempête">Urgence après tempête</option>
						<option value="Autre">Autre</option>
					</select>
				</p>

				<p>
					<label for="lae_message">Votre demande <span aria-hidden="true">*</span></label>
					<textarea id="lae_message" name="lae_message" rows="6" required
						placeholder="L'arbre, son emplacement, ce qui vous inquiète. L'accès au terrain, s'il est difficile."><?php echo esc_textarea( wp_unslash( $_POST['lae_message'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></textarea>
				</p>

				<p class="lae-contact__accord">
					<label>
						<input type="checkbox" name="lae_accord" value="1" required>
						J'accepte que ces informations servent à me recontacter au sujet de ma demande. Elles ne sont pas conservées sur le site ni transmises à qui que ce soit.
					</label>
				</p>

				<p>
					<button class="lae-btn lae-btn--primaire" type="submit" name="lae_contact_envoi" value="1">Envoyer ma demande</button>
				</p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}

add_shortcode( 'lae_contact', 'lae_contact_formulaire' );
