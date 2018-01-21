/* global ajaxurl */

/**
 * Manage the collection theme settings dynamic customization.
 *
 * Sends the required XHR for handling non-active theme customization.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const elements = document.querySelectorAll( '.theme-actions .no-save' );

	for ( let i = 0; i < elements.length; i++ ) {
		elements[ i ].addEventListener( 'click', ( event )=> {
			const data = new FormData,
						xhr	 = new XMLHttpRequest;

			data.append( 'action', 'webcomic_customize_theme' );
			data.append( 'theme', event.target.getAttribute( 'data-stylesheet' ) );

			xhr.open( 'POST', ajaxurl );
			xhr.send( data );
		});
	}
}() );
