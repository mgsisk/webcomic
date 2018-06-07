/* global ajaxurl */

/**
 * Quick edit utilities.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	document.addEventListener( 'click', webcomicQuickEditPage );

	/**
	 * Update page quick edit settings.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicQuickEditPage( event ) {
		if ( 'editinline' !== event.target.className ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const element = event.target.parentNode.parentNode.previousElementSibling;
		const postId = element.id.substr( 7 );

		data.append( 'action', 'webcomic_page_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const select = document.querySelector( `#edit-${postId} [name="webcomic_page_collection"]` );
			const options = select.options;
			const collection = JSON.parse( xhr.responseText )[0];

			for ( let i = 0; i < options.length; i++ ) {
				if ( options[i].value !== collection ) {
					continue;
				}

				select.selectedIndex = i;
			}
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}
}() );
