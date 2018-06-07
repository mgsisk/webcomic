/* global ajaxurl  */

/**
 * Comic term search implementation.
 *
 * Enables the search and comic term select features of the Webcomic Term Link
 * widget.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	jQuery( document ).on( 'widget-added widget-updated', webcomicUpdateTermSearchWidget );

	webcomicPrepareTermSearch( document.querySelectorAll( '[data-webcomic-term-search]' ) );

	/**
	 * Prepare comic term search elements for searching.
	 *
	 * @param {array} elements The current list of comic term search elements.
	 * @return {void}
	 */
	function webcomicPrepareTermSearch( elements ) {
		for ( let i = 0; i < elements.length; i++ ) {
			webcomicUpdateTermSeach(
				elements[i],
				Number( document.querySelector( `[name="${elements[i].getAttribute( 'data-input' )}"]` ).value )
			);
		}
	}

	/**
	 * Update the comic term search during widget events.
	 *
	 * Widgets have their own, jQuery-enabled events, so we have to use jQuery
	 * to listen for these events and update the comic term search as necessary.
	 *
	 * @param {object} event The current event object.
	 * @param {object} widget The current widget object.
	 * @return {void}
	 */
	function webcomicUpdateTermSearchWidget( event, widget ) {
		if ( ! widget[0].id.match( /_mgsisk_webcomic_/ ) ) {
			return;
		}

		const elements = widget[0].querySelectorAll( '[data-webcomic-term-search]' );

		for ( let i = 0; i < elements.length; i++ ) {
			webcomicUpdateTermSeach(
				elements[i],
				Number( document.querySelector( `[name="${elements[i].getAttribute( 'data-input' )}"]` ).value )
			);
		}
	}

	/**
	 * Update the comic term search.
	 *
	 * @param {object} element The comic term search element.
	 * @param {int}    term The selected term.
	 * @return {void}
	 */
	function webcomicUpdateTermSeach( element, term ) {
		const input = document.querySelector( `[name="${element.getAttribute( 'data-input' )}"]` );
		const data = new FormData;
		const xhr = new XMLHttpRequest;

		input.value = term;

		if ( input.value !== term ) {
			input.dispatchEvent( new Event( 'change', {
				bubbles: true
			}) );
		}

		data.append( 'action', 'webcomic_update_comic_term_search' );
		data.append( 'term', term );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( ! term ) {
				webcomicGetSearchFields( element );

				element.querySelector( 'input' ).dispatchEvent( new Event( 'input' ) );

				return;
			}

			webcomicGetSelectedTerm( element, JSON.parse( xhr.responseText ) );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get search elements.
	 *
	 * @param {object} element The comic term search element.
	 * @return {void}
	 */
	function webcomicGetSearchFields( element ) {
		if ( ! element.querySelector( 'input' ) ) {
			element.innerHTML = `<input type="search" id="${element.getAttribute( 'data-webcomic-term-search' )}" class="widefat" placeholder="${element.getAttribute( 'data-webcomic-term-search-search' )}">`;
		}

		if ( ! element.querySelector( 'div' ) ) {
			element.innerHTML += '<div></div>';
		}

		element.querySelector( 'input' ).addEventListener( 'input', ( event )=> {
			webcomicSearchTerms( element, event.target.value );
		});
	}

	/**
	 * Search for comics.
	 *
	 * @param {object} element The comic term search element.
	 * @param {string} query The query to search for.
	 * @return {void}
	 */
	function webcomicSearchTerms( element, query ) {
		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_update_comic_term_search' );
		data.append( 'query', query );
		data.append( 'taxonomy', element.getAttribute( 'data-webcomic-term-taxonomy' ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			webcomicGetSearchResults( element, query, JSON.parse( xhr.responseText ) );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get comic term search results.
	 *
	 * @param {object} element The comic term search element.
	 * @param {string} query The query to search for.
	 * @param {array}  terms The array of elements returned by the XHR handler.
	 * @return {void}
	 */
	function webcomicGetSearchResults( element, query, terms ) {
		let results = '<table class="widefat fixed striped media">';

		for ( let i = 0; i < terms.length; i++ ) {
			results += `
			<tr data-id="${terms[i].term_id}">
				<td class="title column-title column-primary">
					<strong class="has-media-icon">
						<span class="media-icon image-icon">${terms[i].webcomic_term_media}</span>
						${terms[i].name}
					</strong>
				</td>
			</tr>`;
		}

		if ( query && ! terms.length ) {
			results += `<tr><td>${element.getAttribute( 'data-webcomic-term-search-null' )}</td></tr>`;
		}

		results += '</table>';

		element.querySelector( 'div' ).innerHTML = results.replace(
			'<table class="widefat fixed striped media"></table>',
			`<small>${element.getAttribute( 'data-webcomic-term-search-info' )}</small>`
		);

		if ( ! terms ) {
			return;
		}

		const elements = element.querySelectorAll( '[data-id]' );

		for ( let i = 0; i < elements.length; i++ ) {
			elements[i].addEventListener( 'click', ()=> {
				webcomicUpdateTermSeach( element, Number( elements[i].getAttribute( 'data-id' ) ) );
			});
		}
	}

	/**
	 * Get the selected comic term.
	 *
	 * @param {object} element The comic term search element.
	 * @param {object} term The term object returned by the XHR handler.
	 * @return {void}
	 */
	function webcomicGetSelectedTerm( element, term ) {
		element.innerHTML = `
			<a class="dashicons dashicons-no">
				<span class="screen-reader-text">${element.getAttribute( 'data-webcomic-term-search-remove' )}</span>
			</a>
			<strong>${term.title}</strong><br>
			${term.media}`;

		element.querySelector( 'a' ).addEventListener( 'click', ()=> {
			webcomicUpdateTermSeach( element, 0 );
		});
	}
}() );
