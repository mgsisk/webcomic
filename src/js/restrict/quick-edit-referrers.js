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

	document.addEventListener( 'click', ( event )=> {
		if ( 'editinline' !== event.target.className ) {
			return;
		}

		const data    = new FormData,
					xhr     = new XMLHttpRequest,
					element = event.target.parentNode.parentNode.previousElementSibling,
					postId  = element.id.substr( 7 );

		data.append( 'action', 'webcomic_restrict_referrers_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			document.querySelector( `#edit-${postId} [name="webcomic_restrict_referrers"]` ).value = JSON.parse( xhr.responseText );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	});
}() );
