/* global ajaxurl, webcomicTranscriptL10n */

/**
 * Add the column-comments class to transcript columns.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const elements = document.querySelectorAll( '.column-webcomic_transcripts' ),
				observer = new MutationObserver( ( mutations )=> {
					for ( let i = 0; i < mutations.length; i++ ) {
						if ( ! mutations[i].addedNodes.length ) {
							continue;
						}

						for ( let n = 0; n < mutations[i].addedNodes.length; n++ ) {
							let element = mutations[i].addedNodes[n];

							if ( ! element || ! element.tagName || ! element.classList.contains( 'hentry' ) ) {
								continue;
							}

							element.querySelector( '.column-webcomic_transcripts' ).classList.add( 'column-comments' );
						}
					}
				});

	observer.observe( document.querySelector( '#the-list' ), {
		childList: true
	});

	for ( let i = 0; i < elements.length; i++ ) {
		elements[i].classList.add( 'column-comments' );
	}
}() );
