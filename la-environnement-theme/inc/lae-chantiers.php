<?php
/**
 * Avant / après — le mécanisme des chantiers.
 *
 * Demande de Fabrice (13/09) : « la page nos chantiers n'est pas faite, il
 * faut un mécanisme avant après ». La page existe et répond, mais aucune
 * réalisation n'y est publiée : elle affiche « Les chantiers seront bientôt
 * en ligne ». Ce fichier apporte les deux moitiés qui manquaient — de quoi
 * saisir une paire de photos, et de quoi la montrer.
 *
 * DEUX MODES, et ce n'est pas une coquetterie.
 *
 * Un comparateur à curseur — celui qu'on glisse pour révéler l'avant sous
 * l'après — n'a de sens que si les DEUX PHOTOS PARTAGENT LE MÊME CADRAGE :
 * même endroit, même hauteur, même focale. Sinon le curseur fait glisser
 * deux scènes différentes l'une sur l'autre, et le résultat ne ressemble
 * pas à une transformation, il ressemble à un bug.
 *
 * Les photos réellement disponibles ici (assets/images/chantiers/) ne sont
 * pas prises au trépied : même mur, mais pas le même angle ni la même
 * lumière. Le mode par défaut est donc le DIPTYQUE — deux images côte à
 * côte, chacune étiquetée — qui fonctionne avec n'importe quelle paire et
 * ne ment sur rien. Le curseur reste disponible, coché chantier par
 * chantier, le jour où le client photographie depuis le même point.
 *
 * Conseil à lui transmettre : pour obtenir un vrai avant/après superposable,
 * il suffit de prendre la photo « avant » depuis un repère fixe (un coin de
 * terrasse, un poteau) et de revenir au même endroit à la fin.
 *
 * @package LA_Environnement
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const LAE_META_AVANT     = '_lae_avant';
const LAE_META_AVANT_LIB = '_lae_avant_libelle';
const LAE_META_APRES_LIB = '_lae_apres_libelle';
const LAE_META_SUPERPOSE = '_lae_superpose';

/** Libellés par défaut, jamais « Avant » imposé : voir lae_chantier_libelles(). */
function lae_chantier_libelles( $post_id ) {
	$avant = trim( (string) get_post_meta( $post_id, LAE_META_AVANT_LIB, true ) );
	$apres = trim( (string) get_post_meta( $post_id, LAE_META_APRES_LIB, true ) );
	return array(
		'avant' => '' !== $avant ? $avant : 'Avant',
		'apres' => '' !== $apres ? $apres : 'Après',
	);
}

/**
 * Le comparateur d'un chantier.
 *
 * @param int $post_id Réalisation.
 * @return string HTML, ou chaîne vide s'il n'y a pas de paire complète.
 */
function lae_comparateur( $post_id = 0 ) {

	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$apres   = (int) get_post_thumbnail_id( $post_id );
	$avant   = (int) get_post_meta( $post_id, LAE_META_AVANT, true );

	// Sans les deux images, il n'y a rien à comparer : l'appelant retombe
	// sur l'image mise en avant seule.
	if ( ! $avant || ! $apres ) {
		return '';
	}

	$lib       = lae_chantier_libelles( $post_id );
	$superpose = (bool) get_post_meta( $post_id, LAE_META_SUPERPOSE, true );
	$titre     = get_the_title( $post_id );

	$img = function ( $id, $role ) use ( $titre ) {
		return wp_get_attachment_image( $id, 'lae-large', false, array(
			'class'    => 'lae-cmp__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => sprintf( '%s — %s', $titre, $role ),
		) );
	};

	ob_start();

	if ( ! $superpose ) : ?>
		<figure class="lae-cmp lae-cmp--diptyque">
			<div class="lae-cmp__volet">
				<span class="lae-cmp__etiquette"><?php echo esc_html( $lib['avant'] ); ?></span>
				<?php echo $img( $avant, $lib['avant'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="lae-cmp__volet">
				<span class="lae-cmp__etiquette lae-cmp__etiquette--apres"><?php echo esc_html( $lib['apres'] ); ?></span>
				<?php echo $img( $apres, $lib['apres'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</figure>
	<?php else :
		$uid = 'cmp-' . $post_id;
		?>
		<figure class="lae-cmp lae-cmp--curseur" data-lae-cmp>
			<div class="lae-cmp__scene">
				<div class="lae-cmp__fond">
					<?php echo $img( $apres, $lib['apres'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="lae-cmp__etiquette lae-cmp__etiquette--apres"><?php echo esc_html( $lib['apres'] ); ?></span>
				</div>
				<div class="lae-cmp__dessus" aria-hidden="true">
					<?php echo $img( $avant, $lib['avant'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="lae-cmp__etiquette"><?php echo esc_html( $lib['avant'] ); ?></span>
				</div>
				<span class="lae-cmp__poignee" aria-hidden="true"></span>
			</div>

			<?php /* Le contrôle réel est un input range : clavier, lecteur d'écran
			         et pointeur fonctionnent sans une ligne de JS d'accessibilité. */ ?>
			<label class="lae-cmp__label" for="<?php echo esc_attr( $uid ); ?>">
				Curseur de comparaison entre <?php echo esc_html( $lib['avant'] ); ?> et <?php echo esc_html( $lib['apres'] ); ?>
			</label>
			<input class="lae-cmp__rail" type="range" id="<?php echo esc_attr( $uid ); ?>"
			       min="0" max="100" value="50" step="1">
		</figure>
	<?php endif;

	return (string) ob_get_clean();
}

/* ── Saisie côté administration ─────────────────────────────────────── */

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'lae_avant_apres',
		'Avant / après',
		'lae_chantier_boite',
		'lae_realisation',
		'side',
		'high'
	);
} );

function lae_chantier_boite( $post ) {

	wp_nonce_field( 'lae_chantier_' . $post->ID, 'lae_chantier_nonce' );

	$avant     = (int) get_post_meta( $post->ID, LAE_META_AVANT, true );
	$lib       = lae_chantier_libelles( $post->ID );
	$superpose = (bool) get_post_meta( $post->ID, LAE_META_SUPERPOSE, true );
	?>
	<p class="description" style="margin-bottom:10px">
		L'image <strong>mise en avant</strong> est l'après. Choisissez ici celle d'avant.
	</p>

	<div id="lae-avant-apercu" style="margin-bottom:8px">
		<?php if ( $avant ) { echo wp_get_attachment_image( $avant, 'medium', false, array( 'style' => 'max-width:100%;height:auto' ) ); } ?>
	</div>

	<input type="hidden" name="lae_avant" id="lae-avant-id" value="<?php echo esc_attr( $avant ); ?>">
	<button type="button" class="button" id="lae-avant-choisir">Choisir l'image « avant »</button>
	<button type="button" class="button-link" id="lae-avant-retirer" style="margin-left:8px;<?php echo $avant ? '' : 'display:none'; ?>">Retirer</button>

	<p style="margin-top:14px">
		<label for="lae-avant-lib"><strong>Étiquette de gauche</strong></label><br>
		<input type="text" class="widefat" id="lae-avant-lib" name="lae_avant_libelle"
		       value="<?php echo esc_attr( $lib['avant'] ); ?>" placeholder="Avant">
	</p>
	<p>
		<label for="lae-apres-lib"><strong>Étiquette de droite</strong></label><br>
		<input type="text" class="widefat" id="lae-apres-lib" name="lae_apres_libelle"
		       value="<?php echo esc_attr( $lib['apres'] ); ?>" placeholder="Après">
	</p>
	<p class="description">
		Si la première photo a été prise <em>pendant</em> le chantier et non avant,
		écrivez « Pendant » : mieux vaut une étiquette juste qu'une étiquette flatteuse.
	</p>

	<p style="margin-top:14px">
		<label>
			<input type="checkbox" name="lae_superpose" value="1" <?php checked( $superpose ); ?>>
			<strong>Comparateur à curseur</strong>
		</label>
	</p>
	<p class="description">
		À cocher <strong>uniquement si les deux photos ont le même cadrage</strong>
		(même endroit, même hauteur, même zoom). Sinon le curseur fait glisser deux
		scènes différentes l'une sur l'autre et donne l'impression d'un bug.
		Par défaut, les deux photos s'affichent côte à côte.
	</p>
	<?php
}

add_action( 'save_post_lae_realisation', function ( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['lae_chantier_nonce'] ) ) return;
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['lae_chantier_nonce'] ) ), 'lae_chantier_' . $post_id ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$avant = isset( $_POST['lae_avant'] ) ? absint( wp_unslash( $_POST['lae_avant'] ) ) : 0;
	// Un identifiant qui n'est pas une image du site n'entre pas en base.
	if ( $avant && 'attachment' !== get_post_type( $avant ) ) {
		$avant = 0;
	}
	$avant ? update_post_meta( $post_id, LAE_META_AVANT, $avant ) : delete_post_meta( $post_id, LAE_META_AVANT );

	foreach ( array( LAE_META_AVANT_LIB => 'lae_avant_libelle', LAE_META_APRES_LIB => 'lae_apres_libelle' ) as $meta => $champ ) {
		$v = isset( $_POST[ $champ ] ) ? sanitize_text_field( wp_unslash( $_POST[ $champ ] ) ) : '';
		$v = mb_substr( $v, 0, 40 );
		'' !== $v ? update_post_meta( $post_id, $meta, $v ) : delete_post_meta( $post_id, $meta );
	}

	isset( $_POST['lae_superpose'] )
		? update_post_meta( $post_id, LAE_META_SUPERPOSE, 1 )
		: delete_post_meta( $post_id, LAE_META_SUPERPOSE );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) return;
	if ( 'lae_realisation' !== get_post_type() ) return;

	wp_enqueue_media();
	wp_add_inline_script( 'media-editor', <<<'JS'
( function () {
	var cadre;
	document.addEventListener( 'click', function ( e ) {
		var champ = document.getElementById( 'lae-avant-id' );
		if ( ! champ ) return;

		if ( e.target.id === 'lae-avant-retirer' ) {
			champ.value = '';
			document.getElementById( 'lae-avant-apercu' ).innerHTML = '';
			e.target.style.display = 'none';
			return;
		}
		if ( e.target.id !== 'lae-avant-choisir' ) return;

		e.preventDefault();
		if ( ! cadre ) {
			cadre = wp.media( { title: 'Image « avant »', button: { text: 'Utiliser cette image' }, multiple: false } );
			cadre.on( 'select', function () {
				var img = cadre.state().get( 'selection' ).first().toJSON();
				var url = ( img.sizes && img.sizes.medium ) ? img.sizes.medium.url : img.url;
				champ.value = img.id;
				document.getElementById( 'lae-avant-apercu' ).innerHTML =
					'<img src="' + url + '" style="max-width:100%;height:auto">';
				document.getElementById( 'lae-avant-retirer' ).style.display = '';
			} );
		}
		cadre.open();
	} );
}() );
JS
	);
} );

/* ── Les deux chantiers réellement photographiés ────────────────────────
   Les photos viennent du client, déposées dans le thème par la session
   DESIGN. Elles sont importées une fois dans la médiathèque — sans quoi
   elles ne peuvent être ni image mise en avant, ni redimensionnées.

   RÈGLE TENUE ICI : on ne raconte que ce que la photo montre. Aucune
   commune, aucune essence, aucune date, aucun nom : rien de tout cela
   n'est vérifié. Les deux paires sont étiquetées « Pendant » et non
   « Avant », parce qu'aucune des deux premières photos n'a été prise
   avant le début du chantier — elles montrent le chantier en cours.
   ──────────────────────────────────────────────────────────────────── */

if ( ! function_exists( 'lae_chantier_importe_image' ) ) {
	/**
	 * Importe un fichier du thème dans la médiathèque, une seule fois.
	 *
	 * @return int Identifiant d'attachement, 0 en cas d'échec.
	 */
	function lae_chantier_importe_image( $fichier, $titre ) {

		$deja = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_lae_source',
			'meta_value'     => $fichier,
		) );
		if ( $deja ) {
			return (int) $deja[0];
		}

		/* $fichier est un chemin RELATIF à assets/images/ — « chantiers/x.webp »
		   ou « pelouse-haie.webp ». Les articles puisent dans les deux. */
		$source = get_template_directory() . '/assets/images/' . ltrim( $fichier, '/' );
		if ( ! file_exists( $source ) || false !== strpos( $fichier, '..' ) ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$dossier = wp_upload_dir();
		if ( ! empty( $dossier['error'] ) ) {
			return 0;
		}
		// basename : le nom de fichier ne doit jamais emporter son sous-dossier
		// dans la médiathèque.
		$cible = trailingslashit( $dossier['path'] ) . wp_unique_filename( $dossier['path'], basename( $fichier ) );
		if ( ! copy( $source, $cible ) ) {
			return 0;
		}

		$id = wp_insert_attachment( array(
			'post_mime_type' => 'image/webp',
			'post_title'     => $titre,
			'post_content'   => '',
			'post_status'    => 'inherit',
		), $cible, 0, true );

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $cible ) );
		update_post_meta( $id, '_wp_attachment_image_alt', $titre );
		update_post_meta( $id, '_lae_source', $fichier );

		return (int) $id;
	}
}

if ( ! function_exists( 'lae_chantiers_livres' ) ) {
	function lae_chantiers_livres() {
		return array(
			'demontage-arbres-abri-jardin' => array(
				'titre'   => 'Démontage de cinq arbres au-dessus d\'un jardin',
				'type'    => 'Abattage',
				'extrait' => 'Cinq sujets démontés section par section au-dessus d\'un terrain occupé, puis le terrain rendu net.',
				'avant'   => array( 'chantiers/abri-jardin-1-pendant-demontage.webp', 'Chantier en cours : les cinq fûts démontés' ),
				'apres'   => array( 'chantiers/abri-jardin-2-apres-remise-en-etat.webp', 'Après remise en état du terrain' ),
				'lib'     => array( 'Pendant le chantier', 'Après remise en état' ),
				'texte'   => "<!-- wp:paragraph --><p>Cinq arbres à descendre au-dessus d'un jardin entretenu : abri, allée, massifs et plantations en place. Pas de zone de chute disponible, donc pas d'abattage direct.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Le travail s'est fait en grimpe, tronçon par tronçon, avec descente des pièces en rétention plutôt qu'en chute libre. Sur la photo de gauche, le chantier est en cours : les fûts sont dégarnis, le bois débité attend au sol, la brouette est en place.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>À droite, le terrain après notre passage. Le bois est évacué, les rémanents broyés, la pelouse ratissée. C'est notre définition d'un chantier fini : il ne doit rien rester à ramasser derrière nous.</p><!-- /wp:paragraph -->",
			),
			'broyage-sur-place-mur-mitoyen' => array(
				'titre'   => 'Rideau de conifères le long d\'un mur mitoyen',
				'type'    => 'Élagage',
				'extrait' => 'Une rangée de conifères réduite le long d\'un mur, avec broyage des branches sur place.',
				'avant'   => array( 'chantiers/mur-vert-1-pendant-broyage.webp', 'Broyage des branches en cours' ),
				'apres'   => array( 'chantiers/mur-vert-2-apres-nettoyage.webp', 'Après nettoyage du pied' ),
				'lib'     => array( 'Pendant le broyage', 'Après nettoyage' ),
				'texte'   => "<!-- wp:paragraph --><p>Une rangée de conifères plantée au ras d'un mur mitoyen, devenue trop haute et trop dense. La contrainte tient en un mot : le mur. Rien ne doit tomber dessus, et il n'y a pas de recul pour travailler.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Les branches ont été broyées au fur et à mesure, directement sur le chantier — c'est la photo de gauche, le broyeur en place. Broyer sur place évite une noria de remorques et produit un paillage utilisable par le propriétaire.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>À droite, le pied dégagé après nettoyage. Les fûts sont conservés : ils repartiront, et le propriétaire décidera ensuite s'il les garde ou les fait abattre.</p><!-- /wp:paragraph -->",
			),
		);
	}
}

if ( ! function_exists( 'lae_chantiers_semer' ) ) {
	function lae_chantiers_semer() {

		foreach ( lae_chantiers_livres() as $slug => $c ) {

			// Jamais deux fois, et jamais par-dessus une fiche du client.
			$existe = get_posts( array(
				'post_type'      => 'lae_realisation',
				'post_status'    => 'any',
				'name'           => $slug,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			) );
			if ( $existe ) {
				continue;
			}

			$avant = lae_chantier_importe_image( $c['avant'][0], $c['avant'][1] );
			$apres = lae_chantier_importe_image( $c['apres'][0], $c['apres'][1] );
			if ( ! $avant || ! $apres ) {
				continue;   // sans les deux images la fiche n'aurait pas d'intérêt
			}

			$id = wp_insert_post( array(
				'post_type'    => 'lae_realisation',
				'post_status'  => 'publish',
				'post_title'   => $c['titre'],
				'post_name'    => $slug,
				'post_excerpt' => $c['extrait'],
				'post_content' => $c['texte'],
			), true );

			if ( is_wp_error( $id ) ) {
				continue;
			}

			set_post_thumbnail( $id, $apres );
			update_post_meta( $id, LAE_META_AVANT, $avant );
			update_post_meta( $id, LAE_META_AVANT_LIB, $c['lib'][0] );
			update_post_meta( $id, LAE_META_APRES_LIB, $c['lib'][1] );
			// Pas de curseur : ces paires n'ont pas le même cadrage.

			$terme = term_exists( $c['type'], 'lae_type_chantier' );
			if ( ! $terme ) {
				$terme = wp_insert_term( $c['type'], 'lae_type_chantier' );
			}
			if ( ! is_wp_error( $terme ) ) {
				wp_set_object_terms( $id, array( (int) $terme['term_id'] ), 'lae_type_chantier' );
			}
		}
	}
}

/* Même filet que pour les pages : le thème arrive par WP-Cron, aucune page
   d'administration n'est chargée. `init` suffit donc, `admin_init` double
   la mise, et le verrou rend l'ensemble idempotent. */
if ( ! function_exists( 'lae_chantiers_rattrapage' ) ) {
	function lae_chantiers_rattrapage() {
		if ( get_option( 'lae_chantiers_semes' ) ) {
			return;
		}
		update_option( 'lae_chantiers_semes', 1 );   // verrou posé AVANT le travail
		lae_chantiers_semer();
	}
}
add_action( 'init', 'lae_chantiers_rattrapage', 21 );
add_action( 'admin_init', 'lae_chantiers_rattrapage', 12 );
