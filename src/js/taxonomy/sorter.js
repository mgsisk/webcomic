/* global jQuery */

/**
 * Hierarchical sorting implementation.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	jQuery( '#col-right ol' ).nestedSortable({
		items: 'li',
		handle: 'label',
		tabSize: 32,
		protectRoot: 0 === document.querySelectorAll( '[data-webcomic-terms-hierarchical]' ).length,
		toleranceElement: '> label',
		start( event, ui ) {
			ui.placeholder.height( ui.helper.outerHeight() );
		},
		stop() {
			const elements = document.querySelectorAll( 'ol li' );

			for ( let i = 0; i < elements.length; i++ ) {
				elements[ i ].querySelector( '[name^="webcomic_term_order"]' ).value  = i + 1;
				elements[ i ].querySelector( '[name^="webcomic_term_parent"]' ).value = Number( elements[ i ].parentNode.parentNode.id.substr( 5 ) );
			}
		}
	});
}() );
