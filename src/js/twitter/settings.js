/* global ajaxurl */

/**
 * Twitter consumer token update implementation.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	document.addEventListener( 'change', webcomicUpdateTwitterAccount );

	/**
	 * Update Twitter account setting.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicUpdateTwitterAccount( event ) {
		if ( 'input' !== event.target.tagName.toLowerCase() ) {
			return;
		}

		const match = event.target.name.match( /^(webcomic\d+)\[twitter_oauth\]\[oauth_consumer_(?:key|secret)\]$/ );

		if ( ! match ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const container = event.target.parentNode.parentNode;

		data.append( 'action', 'webcomic_twitter_account' );
		data.append( 'collection', match[1]);
		data.append( 'consumer_key', container.querySelector( `.description input[name="${match[0].replace( '_secret', '_key' )}"]` ).value );
		data.append( 'consumer_secret', container.querySelector( `.description input[name="${match[0].replace( '_key', '_secret' )}"]` ).value );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			container.querySelector( '.webcomic-twitter-account-info' ).innerHTML = xhr.responseText;
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}
}() );
