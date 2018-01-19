/* global ajaxurl, webcomicTranscriptL10n */

/**
 * Handle transcript parent column events.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	/**
	 * Updates column CSS and parent transcript display after quick edits.
	 */
	const elements = document.querySelectorAll( '.column-webcomic_transcript_comic' ),
				observer = new MutationObserver( ( mutations )=> {
					for ( let i = 0; i < mutations.length; i++ ) {
						if ( ! mutations[i].addedNodes.length ) {
							continue;
						}

						for ( let n = 0; n < mutations[i].addedNodes.length; n++ ) {
							let element = mutations[i].addedNodes[n];

							if ( ! element || ! element.tagName || ! element.classList.contains( 'type-webcomic_transcript' ) ) {
								continue;
							}

							element.querySelector( '.column-webcomic_transcript_comic' ).classList.add( 'column-response' );

							if ( ! element.querySelector( '[data-comic]' ) ) {
								return;
							}

							const transcripts = element.querySelector( '[data-comic] .post-com-count-wrapper' ),
										elements    = document.querySelectorAll( `[data-comic="${element.querySelector( '[data-comic]' ).getAttribute( 'data-comic' ) }"]` );

							for ( let i = 0; i < elements.length; i++ ) {
								elements[i].replaceChild( transcripts.cloneNode( true ), elements[i].querySelector( '.post-com-count-wrapper' ) );
							}
						}
					}
				});

	observer.observe( document.querySelector( '#the-list' ), {
		childList: true
	});

	for ( let i = 0; i < elements.length; i++ ) {
		if ( elements[i].classList.contains( 'column-response' ) ) {
			continue;
		}

		elements[i].classList.add( 'column-response' );
	}
}() );
