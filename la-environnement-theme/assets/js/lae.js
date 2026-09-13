/**
 * L.A Environnement — interactions minimales.
 * Un seul fichier, aucune dépendance, chargé en pied de page.
 */
( function () {
	'use strict';

	var burger = document.querySelector( '.lae-burger' );
	var nav    = document.getElementById( 'lae-nav' );

	if ( burger && nav ) {
		burger.addEventListener( 'click', function () {
			var ouvert = burger.getAttribute( 'aria-expanded' ) === 'true';
			burger.setAttribute( 'aria-expanded', ouvert ? 'false' : 'true' );
			nav.classList.toggle( 'est-ouvert', ! ouvert );
		} );

		// Échap referme le menu, et rend le focus au bouton.
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && burger.getAttribute( 'aria-expanded' ) === 'true' ) {
				burger.setAttribute( 'aria-expanded', 'false' );
				nav.classList.remove( 'est-ouvert' );
				burger.focus();
			}
		} );

		// Un clic sur un lien du menu mobile referme le panneau.
		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) {
				burger.setAttribute( 'aria-expanded', 'false' );
				nav.classList.remove( 'est-ouvert' );
			}
		} );
	}
}() );

/**
 * Comparateur avant / après à curseur.
 *
 * Le contrôle réel est un <input type="range"> : clavier, lecteur d'écran et
 * pointeur fonctionnent sans une ligne de code d'accessibilité. Le JS ne fait
 * que reporter sa valeur dans une variable CSS — s'il ne s'exécute pas, on
 * voit l'image « après » en entier plus un curseur, jamais une page cassée.
 */
( function () {
	'use strict';

	var cmps = document.querySelectorAll( '[data-lae-cmp]' );
	if ( ! cmps.length ) {
		return;
	}

	Array.prototype.forEach.call( cmps, function ( cmp ) {

		var rail = cmp.querySelector( '.lae-cmp__rail' );
		var scene = cmp.querySelector( '.lae-cmp__scene' );
		if ( ! rail || ! scene ) {
			return;
		}

		function place( v ) {
			cmp.style.setProperty( '--lae-cmp-pos', v + '%' );
		}
		place( rail.value );
		rail.addEventListener( 'input', function () { place( rail.value ); } );

		// Glisser directement sur l'image, sans viser le rail. On écrit dans
		// l'input plutôt qu'à côté : il reste la source de vérité.
		function suivre( e ) {
			var r = scene.getBoundingClientRect();
			if ( ! r.width ) {
				return;
			}
			var v = Math.min( 100, Math.max( 0, ( ( e.clientX - r.left ) / r.width ) * 100 ) );
			rail.value = v;
			place( v );
		}

		scene.addEventListener( 'pointerdown', function ( e ) {
			scene.setPointerCapture( e.pointerId );
			suivre( e );
		} );
		scene.addEventListener( 'pointermove', function ( e ) {
			if ( scene.hasPointerCapture( e.pointerId ) ) {
				e.preventDefault();   // sinon le glissement fait défiler la page
				suivre( e );
			}
		} );
	} );
}() );
