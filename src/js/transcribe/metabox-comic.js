/* global ajaxurl, webcomicTranscriptL10n */

/**
 * Update parent transcription setting.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	/**
	 * Toggle container properties when a parent comic image is clicked.
	 *
	 * @return {void}
	 */
	document.addEventListener( 'click', ( event )=> {
		if ( 'img' !== event.target.tagName.toLowerCase() ) {
			return;
		}

		let element = event.target;

		while ( element.tagName && ! element.hasAttribute( 'data-webcomic-search' ) ) {
			element = element.parentNode;
		}

		if ( ! element.tagName || ! element.hasAttribute( 'data-webcomic-search' ) ) {

			return;
		}

		let dataId = event.target;

		while ( dataId.tagName && ! dataId.hasAttribute( 'data-id' ) ) {
			dataId = dataId.parentNode;
		}

		if ( dataId.tagName && dataId.hasAttribute( 'data-id' ) ) {
			return;
		}

		if ( element.classList.contains( 'contain' ) ) {
			element.querySelector( 'p' ).style.display = null;
			element.classList.remove( 'contain' );
			element.style.height = 'auto';

			return;
		}

		const width  = Number( event.target.getAttribute( 'width' ) ),
					height = Number( event.target.getAttribute( 'height' ) ),
					ratio  = width / height,
					scale  = height / 2 * ratio;

		element.querySelector( 'p' ).style.display = 'none';
		element.classList.add( 'contain' );
		element.style.height = `${scale}px`;
	});

	/**
	 * Add the webcomic_transcribe checkbox when a comic is selected.
	 *
	 * @return {void}
	 */
	document.addEventListener( 'click', ( event )=> {
		let dataId = event.target;

		while ( dataId.tagName && ! dataId.hasAttribute( 'data-id' ) ) {
			dataId = dataId.parentNode;
		}

		if ( ! dataId.tagName || ! dataId.hasAttribute( 'data-id' ) ) {
			return;
		}

		let element = event.target;

		while ( element.tagName && ! element.hasAttribute( 'data-webcomic-search' ) ) {
			element = element.parentNode;
		}

		if ( ! element.tagName || ! element.hasAttribute( 'data-webcomic-search' ) ) {
			return;
		}

		const data = new FormData,
					xhr  = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcript_comic_search' );
		data.append( 'post', dataId.getAttribute( 'data-id' ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const paragraph = document.createElement( 'p' );

			let checked = '';

			if ( 'true' === xhr.responseText ) {
				checked = ' checked';
			}

			// label.className = 'selectit transcribe';
			paragraph.innerHTML = `
				<label class="selectit">
					<input type="hidden" name="webcomic_transcribe">
					<input type="checkbox" name="webcomic_transcribe" value="1"${checked}>
					${webcomicTranscriptL10n.allow}
				</label>
			`;

			element.parentNode.insertBefore( paragraph, element );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	});

	/**
	 * Remove the webcomic_transcribe checkbox when a comic is removed.
	 *
	 * @return {void}
	 */
	document.addEventListener( 'click', ( event )=> {
		if ( 'a' !== event.target.tagName.toLowerCase() ) {
			return;
		}

		let element = event.target;

		while ( element.tagName && ! element.hasAttribute( 'data-webcomic-search' ) ) {
			element = element.parentNode;
		}

		if ( ! element.tagName || ! element.hasAttribute( 'data-webcomic-search' ) ) {
			return;
		}

		element.parentNode.querySelector( 'p' ).parentNode.removeChild( element.parentNode.querySelector( 'p' ) );
		element.classList.remove( 'contain' );
		element.style.height = 'auto';
	});
}() );
