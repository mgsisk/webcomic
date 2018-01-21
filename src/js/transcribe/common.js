/* global webcomicCommonJS */

/**
 * Common functionality.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	/**
	 * Handle transcript toggling.
	 *
	 * Enables transcript term `<select>` lists to toggle transcript visibility
	 * when the following conditions are met:
	 *
	 * 1. The select list has a webcomic-transcripts-toggler class.
	 * 2. The select list and a list of comic transcripts have a common ancester
	 * with a webcomic-transcripts-toggle class.
	 */
	document.addEventListener( 'change', ( event )=> {
		if ( 'select' !== event.target.tagName.toLowerCase() || ! event.target.classList.contains( 'webcomic-transcripts-toggler' ) ) {
			return;
		}

		let container = event.target;

		while ( container.tagName && ! container.classList.contains( 'webcomic-transcripts-toggle' ) ) {
			container = container.parentNode;
		}

		if ( ! container.tagName ) {
			return;
		}

		const transcripts    = container.querySelectorAll( '.webcomic_transcript.type-webcomic_transcript' ),
					transcriptTerm = event.target.options[ event.target.selectedIndex ].className.match( /webcomic_transcript_\S+-\S+/ );

		for ( let i = 0; i < transcripts.length; i++ ) {
			transcripts[i].style.display = 'none';

			if ( transcriptTerm && transcripts[i].classList.contains( transcriptTerm[0]) ) {
				transcripts[i].style.display = null;
			}
		}
	});

	/**
	 * Handle dynamic transcript form updates.
	 */
	document.addEventListener( 'click', ( event )=> {
		let target = event.target;

		while ( target && target.tagName && 'a' !== target.tagName.toLowerCase() ) {
			target = target.parentNode;
		}

		if ( ! target.tagName || ! target.classList.contains( 'webcomic-transcribe-link' ) ) {
			return;
		}

		const post   = target.href.match( /(?:\/transcribe\/(\d+)\/)?(#.+)$/ ),
					parent = target.className.match( /webcomic-transcribe-parent-(\d+)/ );

		if ( ! post || ! parent ) {
			return;
		}

		const form = document.querySelector( `${post[2]} form` );

		if ( ! form || ! form.querySelector( '[name="webcomic_transcript"]' ) || ! form.querySelector( '[name="webcomic_transcript_id"]' ) ) {
			return;
		}

		event.preventDefault();

		updateTranscriptForm(
			form, {
				url: target.href,
				hash: post[2],
				id: 0,
				content: '',
				languages: []
			}
		);

		if ( ! post[1]) {
			return;
		}

		const data = new FormData,
					xhr  = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcribe' );
		data.append( 'post', parseInt( post[1]) );
		data.append( 'parent', parseInt( parent[1]) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( ! xhr.responseText ) {
				window.location.href = target.href;

				return;
			}

			let transcript = JSON.parse( xhr.responseText );

			transcript.url  = target.href;
			transcript.hash = post[2];

			updateTranscriptForm( form, transcript );
		};
		xhr.open( 'POST', webcomicCommonJS.ajaxurl );
		xhr.send( data );
	});

	/**
	 * Update transcript form data and focus on the form.
	 *
	 * @param {object} form The form to update.
	 * @param {object} data The data to update the form with.
	 * @return {void}
	 */
	function updateTranscriptForm( form, data ) {
		const idField         = form.querySelector( '[name="webcomic_transcript_id"]' ),
					transcriptField = form.querySelector( '[name="webcomic_transcript"]' ),
					languagesField  = form.querySelector( '[name="webcomic_transcript_language[]"]' ),
					container       = document.querySelector( data.hash );

		idField.value         = data.id;
		transcriptField.value = data.content;

		if ( languagesField ) {
			languagesField.selectedIndex = -1;

			if ( data.languages ) {
				for ( let i = 0; i < languagesField.options.length; i++ ) {
					if ( -1 === data.languages.indexOf( parseInt( languagesField.options[ i ].value ) ) ) {
						continue;
					}

					languagesField.options[ i ].selected = true;
				}
			}
		}

		if ( container ) {
			const cancelLinks = container.querySelectorAll( '.webcomic-transcribe-cancel-link' );

			for ( let i = 0; i < cancelLinks.length; i++ ) {
				cancelLinks[ i ].style.display = 'none';

				if ( data.id ) {
					cancelLinks[ i ].style.display = null;
				}
			}
		}

		window.location.hash  = data.hash;

		if ( data.id ) {
			transcriptField.focus();
		}
	}

	/**
	 * Prepare transcript togglers and cancel links for interaction.
	 *
	 * @return {void}
	 */
	function prepareTogglerLinks() {
		const transcriptTogglers    = document.querySelectorAll( '.webcomic-transcripts-toggle .webcomic-transcripts-toggler' ),
					transcribeCancelLinks = document.querySelectorAll( '.webcomic-transcribe-cancel-link' );

		for ( let i = 0; i < transcriptTogglers.length; i++ ) {
			transcriptTogglers[i].dispatchEvent( new Event( 'change', {
				bubbles: true
			}) );
		}

		for ( let i = 0; i < transcribeCancelLinks.length; i++ ) {
			transcribeCancelLinks[ i ].style.display = 'none';

			if ( window.location.href.match( /\/transcribe\/\d+\/#.+$/ ) && window.location.hash === transcribeCancelLinks[i].querySelector( '.webcomic-transcribe-link' ).hash ) {
				transcribeCancelLinks[ i ].style.display = null;
			}
		}
	}

	/**
	 * Observe DOM mutations and update transcript togglers and links as needed.
	 *
	 * @return {void}
	 */
	function observeTogglerLinks() {
		const observer = new MutationObserver( ( mutations )=> {
						for ( let i = 0; i < mutations.length; i++ ) {
							if ( ! mutations[i].addedNodes.length ) {
								continue;
							}

							for ( let n = 0; n < mutations[i].addedNodes.length; n++ ) {
								let element = mutations[i].addedNodes[n];

								if ( ! element || ! element.tagName || ! element.classList.contains( 'webcomic-dynamic' ) ) {
									continue;
								}

								prepareTogglerLinks();
							}
						}
					});

		observer.observe( document.querySelector( 'body' ), {
			childList: true,
			subtree: true
		});
	}

	prepareTogglerLinks();
	observeTogglerLinks();
}() );
