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
		start: webcomicStartSorting,
		stop: webcomicStopSorting
	});

	/**
	 * Set the placeholder height while sorting.
	 *
	 * @param {object} event The current event object.
	 * @param {object} ui The current ui object.
	 * @return {void}
	 */
	function webcomicStartSorting( event, ui ) {
		ui.placeholder.height( ui.helper.outerHeight() );
	}

	/**
	 * Update term orders and parents after sorting.
	 *
	 * @return {void}
	 */
	function webcomicStopSorting() {
		const elements = document.querySelectorAll( 'ol li' );

		for ( let i = 0; i < elements.length; i++ ) {
			const id = elements[i].parentNode.parentNode.id;

			if ( ! elements[i].querySelector( '[name^="webcomic_term_order"]' ) ) {
				continue;
			}

			elements[i].querySelector( '[name^="webcomic_term_order"]' ).value = i + 1;
			elements[i].querySelector( '[name^="webcomic_term_parent"]' ).value = Number( id.substr( 5 ) );
		}
	}
}() );
