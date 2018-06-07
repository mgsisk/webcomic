/* global ajaxurl */

/**
 * Quick edit utilities.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	document.addEventListener( 'click', webcomicQuickEditPrints );

	/**
	 * Update prints quick edit settings.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicQuickEditPrints( event ) {
		if ( 'editinline' !== event.target.className ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const element = event.target.parentNode.parentNode.previousElementSibling;
		const postId = element.id.substr( 7 );

		data.append( 'action', 'webcomic_commerce_prints_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const response = JSON.parse( xhr.responseText );
			const elements = document.querySelectorAll( `#edit-${postId} .webcomic-commerce-prints-checklist input` );

			for ( let i = 0; i < elements.length; i++ ) {
				if ( 0 > response.indexOf( elements[i].value ) ) {
					continue;
				}

				elements[i].checked = true;
			}
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}
}() );
