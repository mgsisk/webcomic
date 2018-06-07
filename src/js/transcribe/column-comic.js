/**
 * Handle transcript parent column events.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	webcomicTranscriptMutations( document.querySelectorAll( '.column-webcomic_transcript_comic' ) );

	/**
	 * Observe comic transcript mutations.
	 *
	 * @param {array} elements The list of comic transcript elements.
	 * @return {void}
	 */
	function webcomicTranscriptMutations( elements ) {
		const observer = new MutationObserver( webcomicTranscriptMutationObserver );

		observer.observe( document.querySelector( '#the-list' ), {
			childList: true
		});

		for ( let i = 0; i < elements.length; i++ ) {
			if ( elements[i].classList.contains( 'column-response' ) ) {
				continue;
			}

			elements[i].classList.add( 'column-response' );
		}
	}

	/**
	 * Update column CSS and parent transcript display after quick edits.
	 *
	 * @param {array} mutations The list of mutations.
	 * @return {void}
	 */
	function webcomicTranscriptMutationObserver( mutations ) {
		for ( let i = 0; i < mutations.length; i++ ) {
			if ( ! mutations[i].addedNodes.length ) {
				continue;
			}

			for ( let n = 0; n < mutations[i].addedNodes.length; n++ ) {
				const element = mutations[i].addedNodes[n];

				if ( ! element.tagName || ! element.classList.contains( 'type-webcomic_transcript' ) ) { // ! element || TODO Test this!
					continue;
				}

				element.querySelector( '.column-webcomic_transcript_comic' ).classList.add( 'column-response' );

				if ( ! element.querySelector( '[data-comic]' ) ) {
					return;
				}

				const transcripts = element.querySelector( '[data-comic] .post-com-count-wrapper' );
				const elements = document.querySelectorAll( `[data-comic="${element.querySelector( '[data-comic]' ).getAttribute( 'data-comic' )}"]` );

				for ( let x = 0; x < elements.length; x++ ) {
					elements[x].replaceChild( transcripts.cloneNode( true ), elements[x].querySelector( '.post-com-count-wrapper' ) );
				}
			}
		}
	}
}() );
