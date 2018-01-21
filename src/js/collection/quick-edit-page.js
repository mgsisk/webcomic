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

		data.append( 'action', 'webcomic_page_quick_edit' );
		data.append( 'post', postId );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const element    = document.querySelector( `#edit-${postId} [name="webcomic_page_collection"]` ),
						options    = element.options,
						collection = JSON.parse( xhr.responseText )[0];

			for ( let i = 0; i < options.length; i++ ) {
				if ( options[ i ].value !== collection ) {
					continue;
				}

				element.selectedIndex = i;
			}
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	});
}() );
