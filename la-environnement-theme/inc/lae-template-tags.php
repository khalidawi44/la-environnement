<?php
/**
 * Briques d'affichage réutilisables : icônes SVG, coordonnées, listes.
 *
 * Aucune donnée n'est écrite en dur ici : tout ce qui est propre au client
 * (téléphone, e-mail, villes, textes) vient du personnalisateur WordPress.
 * Un réglage vide = le bloc correspondant ne s'affiche pas.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Jeu d'icônes inline (aucune requête externe, aucune police d'icônes).
 *
 * @param string $nom   Clé de l'icône.
 * @param string $extra Classe CSS additionnelle.
 * @return string SVG prêt à être affiché.
 */
if ( ! function_exists( 'lae_icone' ) ) {
	function lae_icone( $nom, $extra = '' ) {
		$chemins = array(
			'arbre'     => '<path d="M12 3 6.5 11h3L5 18h6v3h2v-3h6l-4.5-7h3z"/>',
			'feuille'   => '<path d="M4 20c0-8 6-14 16-15 1 10-4 16-12 16H4zm2-1c3-5 7-8 11-10"/>',
			'tronconneuse' => '<path d="M3 9h9l3-3h6v5h-6l-3 3H6a3 3 0 0 1-3-3z"/><path d="M6 14v3m4-3v3m4-3v3"/>',
			'pelle'     => '<path d="M14 3l7 7-3 3-7-7z"/><path d="M11 6 4 13v4l3 3h4l1-8"/>',
			'telephone' => '<path d="M6 3h4l2 5-2.5 1.5a12 12 0 0 0 5 5L16 12l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 4 5a2 2 0 0 1 2-2z"/>',
			'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
			'lieu'      => '<path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
			'horloge'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
			'check'     => '<path d="m4 12.5 5 5L20 6.5"/>',
			'bouclier'  => '<path d="M12 3 5 6v6c0 4.4 3 8.2 7 9 4-.8 7-4.6 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
			'devis'     => '<path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v5h5M9 13h7M9 17h5"/>',
			'fleche'    => '<path d="M5 12h13m-5-6 6 6-6 6"/>',
			'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
			'fermer'    => '<path d="M6 6l12 12M18 6 6 18"/>',
			'camion'    => '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/>',
			'recyclage' => '<path d="m7 6 2.5-3L12 6"/><path d="M9.5 3 5 11l2 4"/><path d="m19 15-2 4h-5"/><path d="M14.5 6 19 14"/>',
		);

		if ( ! isset( $chemins[ $nom ] ) ) {
			return '';
		}

		$classe = trim( 'lae-icone ' . $extra );

		return '<svg class="' . esc_attr( $classe ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $chemins[ $nom ] . '</svg>';
	}
}

/** Liste des icônes disponibles, pour les écrans d'administration. */
if ( ! function_exists( 'lae_icones_disponibles' ) ) {
	function lae_icones_disponibles() {
		return array(
			'arbre'        => 'Arbre',
			'feuille'      => 'Feuille',
			'tronconneuse' => 'Tronçonneuse',
			'pelle'        => 'Pelle / plantation',
			'camion'       => 'Évacuation',
			'recyclage'    => 'Recyclage / broyage',
			'bouclier'     => 'Sécurité / assurance',
			'devis'        => 'Devis',
			'lieu'         => 'Zone d\'intervention',
			'horloge'      => 'Disponibilité',
		);
	}
}

/**
 * Récupère un réglage du personnalisateur, nettoyé.
 *
 * @param string $cle    Nom du theme_mod (sans préfixe).
 * @param string $defaut Valeur par défaut.
 */
if ( ! function_exists( 'lae_reglage' ) ) {
	function lae_reglage( $cle, $defaut = null ) {
		// WordPress n'utilise pas le 'default' d'add_setting() comme repli de
		// get_theme_mod() : on passe explicitement la valeur livrée.
		if ( null === $defaut ) {
			$defaut = lae_defaut( $cle );
		}
		$valeur = get_theme_mod( 'lae_' . $cle, $defaut );
		return is_string( $valeur ) ? trim( $valeur ) : $valeur;
	}
}

/** Numéro de téléphone au format lien tel: (chiffres et + uniquement). */
if ( ! function_exists( 'lae_tel_lien' ) ) {
	function lae_tel_lien() {
		$tel = lae_reglage( 'telephone' );
		return $tel ? 'tel:' . preg_replace( '/[^0-9+]/', '', $tel ) : '';
	}
}

/**
 * Transforme un réglage multiligne en tableau de lignes non vides.
 *
 * @param string $cle Nom du theme_mod.
 * @return string[]
 */
if ( ! function_exists( 'lae_lignes' ) ) {
	function lae_lignes( $cle ) {
		$brut = lae_reglage( $cle );
		if ( '' === $brut ) {
			return array();
		}
		$lignes = preg_split( '/\r\n|\r|\n/', $brut );
		$lignes = array_map( 'trim', (array) $lignes );
		return array_values( array_filter( $lignes, static function ( $l ) { return '' !== $l; } ) );
	}
}

/**
 * Découpe une ligne « Titre | Description » (ou « Titre | Description | icone »).
 *
 * @param string $ligne Ligne brute.
 * @return array{titre:string,texte:string,icone:string}
 */
if ( ! function_exists( 'lae_decoupe_ligne' ) ) {
	function lae_decoupe_ligne( $ligne ) {
		$morceaux = array_map( 'trim', explode( '|', (string) $ligne ) );
		return array(
			'titre' => isset( $morceaux[0] ) ? $morceaux[0] : '',
			'texte' => isset( $morceaux[1] ) ? $morceaux[1] : '',
			'icone' => isset( $morceaux[2] ) ? $morceaux[2] : '',
		);
	}
}

/** Liste des communes de la zone d'intervention (séparées par des virgules). */
if ( ! function_exists( 'lae_communes' ) ) {
	function lae_communes() {
		$brut = lae_reglage( 'zone_communes' );
		if ( '' === $brut ) {
			return array();
		}
		$villes = array_map( 'trim', explode( ',', $brut ) );
		return array_values( array_filter( $villes, static function ( $v ) { return '' !== $v; } ) );
	}
}

/** Marque du site : logo si défini, sinon nom + baseline. */
if ( ! function_exists( 'lae_marque' ) ) {
	function lae_marque( $contexte = 'header' ) {
		if ( has_custom_logo() ) {
			the_custom_logo();
			return;
		}
		$baseline = lae_reglage( 'baseline' );
		?>
		<a class="lae-marque" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo lae_icone( 'arbre', 'lae-marque__glyphe' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="lae-marque__mot">
				<span><?php bloginfo( 'name' ); ?></span>
				<?php if ( $baseline ) : ?>
					<span class="lae-marque__baseline"><?php echo esc_html( $baseline ); ?></span>
				<?php endif; ?>
			</span>
		</a>
		<?php
	}
}

/** Bouton d'appel, affiché seulement si un numéro est renseigné. */
if ( ! function_exists( 'lae_bouton_tel' ) ) {
	function lae_bouton_tel( $classe = 'lae-btn lae-btn--primaire lae-btn--tel' ) {
		$lien = lae_tel_lien();
		if ( ! $lien ) {
			return;
		}
		?>
		<a class="<?php echo esc_attr( $classe ); ?>" href="<?php echo esc_url( $lien ); ?>">
			<?php echo lae_icone( 'telephone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="lae-header__tel-texte"><?php echo esc_html( lae_reglage( 'telephone' ) ); ?></span>
		</a>
		<?php
	}
}

/** URL de la page de contact (réglage, sinon /contact si la page existe). */
if ( ! function_exists( 'lae_url_contact' ) ) {
	function lae_url_contact() {
		$url = lae_reglage( 'url_contact' );
		if ( $url ) {
			return $url;
		}
		$page = get_page_by_path( 'contact' );
		return $page ? get_permalink( $page ) : '';
	}
}

/**
 * Titre avec emphase : « Un jardin *tenu toute l'année* » devient
 * « Un jardin <em>tenu toute l'année</em> ». Le texte est échappé AVANT
 * l'insertion de la balise : rien de ce que saisit le client n'est du HTML.
 *
 * @param string $texte Titre brut.
 * @return string Titre échappé, avec <em> autour des parties encadrées d'astérisques.
 */
if ( ! function_exists( 'lae_titre_em' ) ) {
	function lae_titre_em( $texte ) {
		$texte = esc_html( (string) $texte );
		return preg_replace( '/\*(.+?)\*/u', '<em>$1</em>', $texte );
	}
}

/**
 * Balise <img> pour un média du personnalisateur.
 * Renvoie une chaîne vide si aucun média n'est défini : le dégradé CSS
 * prend alors le relais, aucune image cassée ne s'affiche.
 *
 * @param string $url    URL du média.
 * @param string $alt    Texte alternatif.
 * @param array  $attrs  Attributs supplémentaires (loading, class, decoding…).
 * @return string
 */
if ( ! function_exists( 'lae_img' ) ) {
	function lae_img( $url, $alt = '', $attrs = array() ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		$defaut = array( 'loading' => 'lazy', 'decoding' => 'async' );
		$attrs  = array_merge( $defaut, (array) $attrs );
		$html   = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"';
		foreach ( $attrs as $cle => $val ) {
			if ( '' === $val || null === $val ) {
				continue;
			}
			$html .= ' ' . esc_attr( $cle ) . '="' . esc_attr( $val ) . '"';
		}
		return $html . '>';
	}
}

/**
 * Découpe une ligne en morceaux séparés par « | », déjà nettoyés.
 *
 * @param string $ligne Ligne brute.
 * @param int    $n     Nombre de morceaux attendus (complétés par des chaînes vides).
 * @return string[]
 */
if ( ! function_exists( 'lae_morceaux' ) ) {
	function lae_morceaux( $ligne, $n = 3 ) {
		$m = array_map( 'trim', explode( '|', (string) $ligne ) );
		return array_pad( array_slice( $m, 0, $n ), $n, '' );
	}
}
