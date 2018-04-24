/* global ajaxurl, webcomicSearchL10n  */

/**
 * Comic search implementation.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	prepareComicSearch( document.querySelectorAll( '[data-webcomic-search]' ) );

	/**
	 * Update the comic search during widget events.
	 *
	 * Widgets have their own, jQuery-enabled events, so we have to use jQuery
	 * to listen for these events and update the comic search as necessary.
	 */
	jQuery( document ).on( 'widget-added widget-updated', ( event, widget )=> {
		if ( ! widget[0].id.match( /_mgsisk_webcomic_/ ) ) {
			return;
		}

		const elements = widget[0].querySelectorAll( '[data-webcomic-search]' );

		for ( let i = 0; i < elements.length; i++ ) {
			updateComicSearch(
				elements[i],
				Number( document.querySelector( `[name="${elements[i].getAttribute( 'data-input' )}"]` ).value )
			);
		}
	});

	/**
	 * Preapre comic search elements for searching.
	 *
	 * @param {array} elements The current list of comic search elements.
	 * @return {void}
	 */
	function prepareComicSearch( elements ) {
		for ( let i = 0; i < elements.length; i++ ) {
			updateComicSearch(
				elements[i],
				Number( document.querySelector( `[name="${elements[i].getAttribute( 'data-input' )}"]` ).value )
			);
		}
	}

	/**
	 * Update the comic search.
	 *
	 * @param {object} element The comic search element.
	 * @param {int}    post The selected post.
	 * @return {void}
	 */
	function updateComicSearch( element, post ) {
		const input = document.querySelector( `[name="${element.getAttribute( 'data-input' )}"]` );
		const data = new FormData;
		const xhr = new XMLHttpRequest;

		input.value = post;

		if ( input.value !== post ) {
			input.dispatchEvent( new Event( 'change', {
				bubbles: true
			}) );
		}

		data.append( 'action', 'webcomic_update_comic_search' );
		data.append( 'post', post );
		data.append( 'size', element.getAttribute( 'data-size' ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( ! post ) {
				getSearchFields( element );

				element.querySelector( 'input' ).dispatchEvent( new Event( 'input' ) );

				return;
			}

			getSelectedComic( element, JSON.parse( xhr.responseText ) );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get search elements.
	 *
	 * @param {object} element The comic search element.
	 * @return {void}
	 */
	function getSearchFields( element ) {
		if ( ! element.querySelector( 'input' ) ) {
			element.innerHTML = `<input type="search" id="${element.getAttribute( 'data-webcomic-search' )}" class="widefat" placeholder="${webcomicSearchL10n.search}">`;
		}

		if ( ! element.querySelector( 'div' ) ) {
			element.innerHTML += '<div></div>';
		}

		element.querySelector( 'input' ).addEventListener( 'input', ( event )=> {
			searchComics( element, event.target.value );
		});
	}

	/**
	 * Search for comics.
	 *
	 * @param {object} element The comic search element.
	 * @param {string} query The query to search for.
	 * @return {void}
	 */
	function searchComics( element, query ) {
		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_update_comic_search' );
		data.append( 'query', query );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			getSearchResults( element, query, JSON.parse( xhr.responseText ) );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get comic search results.
	 *
	 * @param {object} element The comic search element.
	 * @param {string} query The query to search for.
	 * @param {array}  posts The array of elements returned by the XHR handler.
	 * @return {void}
	 */
	function getSearchResults( element, query, posts ) {
		let results = '<table class="widefat fixed striped media">';

		for ( let i = 0; i < posts.length; i++ ) {
			results += `
			<tr data-id="${posts[i].ID}">
				<td class="title column-title column-primary">
					<strong class="has-media-icon">
						<span class="media-icon image-icon">${posts[i].webcomic_media}</span>
						${posts[i].post_title}
					</strong>
					<p>${posts[i].status_label}</p>
				</td>
			</tr>`;
		}

		if ( query && ! posts.length ) {
			results += `<tr><td>${webcomicSearchL10n.null}</td></tr>`;
		}

		results += '</table>';

		element.querySelector( 'div' ).innerHTML = results.replace(
			'<table class="widefat fixed striped media"></table>',
			`<small>${webcomicSearchL10n.info}</small>`
		);

		if ( ! posts ) {
			return;
		}

		const elements = element.querySelectorAll( '[data-id]' );

		for ( let i = 0; i < elements.length; i++ ) {
			elements[i].addEventListener( 'click', ()=> {
				updateComicSearch( element, Number( elements[i].getAttribute( 'data-id' ) ) );
			});
		}
	}

	/**
	 * Get the selected comic.
	 *
	 * @param {object} element The comic search element.
	 * @param {object} post The post object returned by the XHR handler.
	 * @return {void}
	 */
	function getSelectedComic( element, post ) {
		element.innerHTML = `
			<p>
				<a class="dashicons dashicons-no">
					<span class="screen-reader-text">${webcomicSearchL10n.remove}</span>
				</a>
				<strong>${post.title}</strong>
			</p>
			${post.media}`;

		element.querySelector( 'a' ).addEventListener( 'click', ()=> {
			updateComicSearch( element, 0 );
		});
	}
}() );
