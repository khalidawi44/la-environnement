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
