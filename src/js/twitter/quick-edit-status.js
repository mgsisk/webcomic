/* global ajaxurl */

/**
 * Quick edit utilities.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	document.addEventListener( 'click', webcomicQuickEditTwitter );

	/**
	 * Update Twitter quick edit settings.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicQuickEditTwitter( event ) {
		if ( 'editinline' !== event.target.className ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const element = event.target.parentNode.parentNode.previousElementSibling;
		const postId = element.id.substr( 7 );

		data.append( 'action', 'webcomic_twitter_status_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const response = JSON.parse( xhr.responseText );

			document.querySelector( `#edit-${postId} [type="checkbox"][name="webcomic_twitter_update"]` ).checked = response[0];
			document.querySelector( `#edit-${postId} [type="checkbox"][name="webcomic_twitter_update_media"]` ).checked = response[1];
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}
}() );
