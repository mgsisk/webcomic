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

	document.addEventListener( 'click', webcomicQuickEditAge );

	/**
	 * Update age quick edit settings.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicQuickEditAge( event ) {
		if ( 'editinline' !== event.target.className ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const element = event.target.parentNode.parentNode.previousElementSibling;
		const postId = element.id.substr( 7 );

		data.append( 'action', 'webcomic_restrict_age_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			document.querySelector( `#edit-${postId} [name="webcomic_restrict_age"]` ).value = JSON.parse( xhr.responseText )[0];
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}
}() );
