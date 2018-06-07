/* global jQuery */

/**
 * Media manager helper.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	jQuery( document ).on( 'ajaxComplete', webcomicResetMediaManager );

	/**
	 * Reset the media manager selection when creating new terms.
	 *
	 * @return {void}
	 */
	function webcomicResetMediaManager() {
		if ( document.querySelector( '#ajax-response' ).children.length ) {
			return;
		}

		const elements = document.querySelectorAll( '[data-webcomic-media-manager] a' );

		for ( let i = 0; i < elements.length; i++ ) {
			elements[i].dispatchEvent( new MouseEvent( 'click' ) );
		}
	}
}() );
