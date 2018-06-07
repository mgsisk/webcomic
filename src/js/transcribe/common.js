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

	document.addEventListener( 'change', webcomicTranscriptToggle );
	document.addEventListener( 'click', webcomicTriggerTranscriptUpdate );

	webcomicTranscriptPrepareTogglers();
	webcomicTranscriptTogglerlMutations();

	/**
	 * Prepare transcript togglers and cancel links for interaction.
	 *
	 * @return {void}
	 */
	function webcomicTranscriptPrepareTogglers() {
		const transcriptTogglers = document.querySelectorAll( '.webcomic-transcripts-toggle .webcomic-transcripts-toggler' );
		const transcribeCancelLinks = document.querySelectorAll( '.webcomic-transcribe-cancel-link' );

		for ( let i = 0; i < transcriptTogglers.length; i++ ) {
			transcriptTogglers[i].dispatchEvent( new Event( 'change', {
				bubbles: true
			}) );
		}

		for ( let i = 0; i < transcribeCancelLinks.length; i++ ) {
			const transcribeCancelLinkHash = transcribeCancelLinks[i].querySelector( '.webcomic-transcribe-link' ).hash;

			transcribeCancelLinks[i].style.display = 'none';

			if ( window.location.href.match( /\/transcribe\/\d+\/#.+$/ ) && window.location.hash === transcribeCancelLinkHash ) {
				transcribeCancelLinks[i].style.display = null;
			}
		}
	}

	/**
	 * Observe DOM mutations and update transcript togglers and links as needed.
	 *
	 * @return {void}
	 */
	function webcomicTranscriptTogglerlMutations() {
		const observer = new MutationObserver( ( mutations )=> {
			for ( let i = 0; i < mutations.length; i++ ) {
				if ( ! mutations[i].addedNodes.length ) {
					continue;
				}

				for ( let n = 0; n < mutations[i].addedNodes.length; n++ ) {
					const element = mutations[i].addedNodes[n];

					if ( ! element || ! element.tagName || ! element.classList.contains( 'webcomic-dynamic' ) ) {
						continue;
					}

					webcomicTranscriptPrepareTogglers();
				}
			}
		});

		observer.observe( document.querySelector( 'body' ), {
			childList: true,
			subtree: true
		});
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
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicTranscriptToggle( event ) {
		const toggler = event.target;
		const container = webcomicGetTranscriptsContainer( toggler );

		if ( ! container ) {
			return;
		}

		const transcripts = container.querySelectorAll( '.webcomic_transcript.type-webcomic_transcript' );
		let transcriptTerm = toggler.options[toggler.selectedIndex].className.match( /webcomic_transcript_\S+-\S+/ );

		if ( ! transcripts ) {
			return;
		} else if ( transcriptTerm ) {
			transcriptTerm = transcriptTerm[0];
		}

		for ( let i = 0; i < transcripts.length; i++ ) {
			transcripts[i].style.display = 'none';

			if ( transcripts[i].classList.contains( transcriptTerm ) ) {
				transcripts[i].style.display = null;
			}
		}
	}

	/**
	 * Get a comic transcript toggle container.
	 *
	 * @param {object} toggler The toggler object.
	 * @return {object}
	 */
	function webcomicGetTranscriptsContainer( toggler ) {
		const isToggler = toggler.classList.contains( 'webcomic-transcripts-toggler' );
		let container = toggler;

		if ( 'select' !== container.tagName.toLowerCase() || ! isToggler ) {
			return;
		}

		while ( container.tagName && ! container.classList.contains( 'webcomic-transcripts-toggle' ) ) {
			container = container.parentNode;
		}

		if ( ! container.tagName ) {
			return;
		}

		return container;
	}

	/**
	 * Handle dynamic transcript form updates.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicTriggerTranscriptUpdate( event ) {
		let target = event.target;

		while ( target.tagName && 'a' !== target.tagName.toLowerCase() ) {
			target = target.parentNode;
		}

		if ( ! target.tagName || ! target.classList.contains( 'webcomic-transcribe-link' ) ) {
			return;
		}

		const post = target.href.match( /(?:\/transcribe\/(\d+)\/)?(#.+)$/ );
		const parent = target.className.match( /webcomic-transcribe-parent-(\d+)/ );

		if ( ! post || ! parent ) {
			return;
		}

		const formElements = document.querySelectorAll( `
			${post[2]} form,
			${post[2]} form [name="webcomic_transcript"],
			${post[2]} form [name="webcomic_transcript_id"]
			` );

		if ( 3 !== formElements.length ) {
			return;
		}

		event.preventDefault();

		webcomicGetTranscriptFormData( target, formElements[0], post[2], post[1], parent[1]);
	}

	/**
	 * Get transcript form data.
	 *
	 * @param {object} element The element that triggered the form data request.
	 * @param {object} form The form element to update.
	 * @param {string} hash The URL has of the trigger element.
	 * @param {int} post The post to request data for.
	 * @param {int} parent Thep posts parent.
	 * @return {void}
	 */
	function webcomicGetTranscriptFormData( element, form, hash, post, parent ) {
		webcomicUpdateTranscriptForm( form, {
			url: element.href,
			hash,
			id: 0,
			content: '',
			languages: []
		});

		if ( ! post ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcribe' );
		data.append( 'post', parseInt( post ) );
		data.append( 'parent', parseInt( parent ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( ! xhr.responseText ) {
				window.location.href = element.href;

				return;
			}

			const transcript = JSON.parse( xhr.responseText );

			transcript.url = element.href;
			transcript.hash = hash;

			webcomicUpdateTranscriptForm( form, transcript );
		};
		xhr.open( 'POST', webcomicCommonJS.ajaxurl );
		xhr.send( data );
	}

	/**
	 * Update transcript form data and focus on the form.
	 *
	 * @param {object} form The form to update.
	 * @param {object} data The data to update the form with.
	 * @return {void}
	 */
	function webcomicUpdateTranscriptForm( form, data ) {
		const idField = form.querySelector( '[name="webcomic_transcript_id"]' );
		const transcriptField = form.querySelector( '[name="webcomic_transcript"]' );

		idField.value = data.id;
		transcriptField.value = data.content;

		webcomicTranscriptSetLanguages( form.querySelector( '[name="webcomic_transcript_language[]"]' ), data.languages );
		webcomicTranscriptCancelLinks( document.querySelector( data.hash ), data.id );

		window.location.hash = data.hash;

		if ( data.id ) {
			transcriptField.focus();
		}
	}

	/**
	 * Set the selected transcript languages.
	 *
	 * @param {object} field The transcript languages field.
	 * @param {array} languages The languages to select.
	 * @return {void}
	 */
	function webcomicTranscriptSetLanguages( field, languages ) {
		if ( ! field ) {
			return;
		}

		field.selectedIndex = -1;

		if ( ! languages ) {
			return;
		}

		for ( let i = 0; i < field.options.length; i++ ) {
			if ( -1 === languages.indexOf( parseInt( field.options[i].value ) ) ) {
				continue;
			}

			field.options[i].selected = true;
		}
	}

	/**
	 * Set the visibility of transcript cancel links.
	 *
	 * @param {object} element The container to check for links.
	 * @param {string} id The element id.
	 * @return {void}
	 */
	function webcomicTranscriptCancelLinks( element, id ) {
		if ( ! element ) {
			return;
		}

		const cancelLinks = element.querySelectorAll( '.webcomic-transcribe-cancel-link' );

		for ( let i = 0; i < cancelLinks.length; i++ ) {
			cancelLinks[i].style.display = 'none';

			if ( id ) {
				cancelLinks[i].style.display = null;
			}
		}
	}
}() );
