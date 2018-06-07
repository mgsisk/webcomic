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

	document.addEventListener( 'click', webcomicToggleContainer );
	document.addEventListener( 'click', webcomicAddTranscribeCheckBox );
	document.addEventListener( 'click', webcomicRemoveTranscribeCheckBox );

	/**
	 * Toggle container properties when a parent comic image is clicked.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicToggleContainer( event ) {
		if ( 'img' !== event.target.tagName.toLowerCase() ) {
			return;
		}

		let dataId = event.target;

		while ( dataId.tagName && ! dataId.hasAttribute( 'data-id' ) ) {
			dataId = dataId.parentNode;
		}

		if ( dataId && dataId.tagName && dataId.hasAttribute( 'data-id' ) ) {
			return;
		}

		const element = webcomicGetSearchElement( event );

		if ( ! element ) {
			return;
		}

		webcomicChangeContainerProperties( event, element );
	}

	/**
	 * Change comic container properties.
	 *
	 * @param {object} event The current event object.
	 * @param {object} element The container element object.
	 * @return {void}
	 */
	function webcomicChangeContainerProperties( event, element ) {
		if ( element.classList.contains( 'contain' ) ) {
			element.querySelector( 'p' ).style.display = null;
			element.classList.remove( 'contain' );
			element.style.height = 'auto';

			return;
		}

		const width = Number( event.target.getAttribute( 'width' ) );
		const height = Number( event.target.getAttribute( 'height' ) );
		const ratio = width / height;
		const scale = height / 2 * ratio;

		element.querySelector( 'p' ).style.display = 'none';
		element.classList.add( 'contain' );
		element.style.height = `${scale}px`;
	}

	/**
	 * Add the webcomic_transcribe checkbox when a comic is selected.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicAddTranscribeCheckBox( event ) {
		let dataId = event.target;

		while ( dataId && dataId.tagName && ! dataId.hasAttribute( 'data-id' ) ) {
			dataId = dataId.parentNode;
		}

		if ( ! dataId || ! dataId.tagName || ! dataId.hasAttribute( 'data-id' ) ) {
			return;
		}

		const element = webcomicGetSearchElement( event );

		if ( ! element ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;

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
	}

	/**
	 * Remove the webcomic_transcribe checkbox when a comic is removed.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicRemoveTranscribeCheckBox( event ) {
		if ( 'a' !== event.target.tagName.toLowerCase() ) {
			return;
		}

		const element = webcomicGetSearchElement( event );

		if ( ! element || ! element.parentNode || ! element.parentNode.querySelector( 'p' ) ) {
			return;
		}

		element.parentNode.querySelector( 'p' ).parentNode.removeChild( element.parentNode.querySelector( 'p' ) );
		element.classList.remove( 'contain' );
		element.style.height = 'auto';
	}

	/**
	 * Get the comic search element.
	 *
	 * @param {object} event The current event object.
	 * @return {object}
	 */
	function webcomicGetSearchElement( event ) {
		let element = event.target;

		while ( element && element.tagName && ! element.hasAttribute( 'data-webcomic-search' ) ) {
			element = element.parentNode;
		}

		if ( ! element || ! element.tagName || ! element.hasAttribute( 'data-webcomic-search' ) ) {
			return {};
		}

		return element;
	}
}() );
