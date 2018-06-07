/* global ajaxurl, quicktags, QTags, webcomicTranscriptL10n */

/**
 * Handle transcript metabox functionality.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	} else if ( 'auto-draft' === document.querySelector( '#original_post_status' ) ) {
		return;
	}

	webcomicPrepareTranscriptMetaBox( document.querySelector( '#Mgsisk_Webcomic_Transcribe_MetaBoxTranscripts .inside' ) );

	/**
	 * Prepare a transcript metabox for use.
	 *
	 * @param {object} transcriptMetabox The transcript metabox to prepare.
	 * @return {void}
	 */
	function webcomicPrepareTranscriptMetaBox( transcriptMetabox ) {
		const transcripts = transcriptMetabox.querySelectorAll( 'tr' );

		QTags.addButton(
			'comic',
			webcomicTranscriptL10n.comic,
			webcomicHandleQuickTag,
			'',
			'g',
			'',
			0,
			'webcomic_transcript_content'
		);

		webcomicAddTranscriptButton( transcriptMetabox );

		for ( let i = 0; i < transcripts.length; i++ ) {
			if ( transcripts[i].classList.contains( 'none' ) ) {
				continue;
			}

			webcomicActivateRowActions( transcripts[i]);
		}
	}

	/**
	 * Handle comic quick tag.
	 *
	 * @param {object} target The current target object.
	 * @return {void}
	 */
	function webcomicHandleQuickTag( target ) {
		let form = target;

		while ( form.tagName && ! form.classList.contains( 'webcomic-transcript-form' ) ) {
			form = form.parentNode;
		}

		if ( ! form.tagName || ! form.classList.contains( 'webcomic-transcript-form' ) ) {
			return;
		}

		const media = form.querySelector( '.webcomic-media' );

		if ( ! media.style.display || 'block' === media.style.display ) {
			media.style.display = 'none';

			return;
		}

		media.style.display = null;
	}

	/**
	 * Get an Add Transcript button.
	 *
	 * @param {object} metabox The metabox to insert the Add Transcript button in.
	 * @return {void}
	 */
	function webcomicAddTranscriptButton( metabox ) {
		if ( metabox.querySelector( '.add-transcript' ) ) {
			return;
		}

		const button = document.createElement( 'p' );

		button.innerHTML = `<a href="#" class="button add-transcript">${webcomicTranscriptL10n.add}</a>`;
		button.addEventListener( 'click', ( event )=> {
			event.preventDefault();

			webcomicGetTranscriptForm( metabox, 0 );
		});

		metabox.insertBefore( button, metabox.querySelector( '.webcomic-transcripts' ) );
	}

	/**
	 * Get a comic transccript form.
	 *
	 * @param {object} container The form container.
	 * @param {int}    post The post form is for.
	 * @return {void}
	 */
	function webcomicGetTranscriptForm( container, post ) {
		let oldForm = container;

		while ( oldForm.tagName && ! oldForm.classList.contains( 'inside' ) ) {
			oldForm = oldForm.parentNode;
		}

		if ( oldForm.tagName && oldForm.classList.contains( 'inside' ) ) {
			oldForm = oldForm.querySelector( 'fieldset' );

			if ( oldForm ) {
				return webcomicRemoveTranscriptForm( oldForm.parentNode, container, post );
			}
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcribe_form' );
		data.append( 'parent', document.querySelector( '#post_ID' ).value );
		data.append( 'post', post );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const form = document.createElement( 'div' );

			form.innerHTML = xhr.responseText;

			webcomicActivateForm( form );

			if ( post ) {
				container.innerHTML = '<td colspan="3"></td>';
				container.children[0].appendChild( form );
			} else {
				container.querySelector( '.add-transcript' ).parentNode.style.display = 'none';
				container.insertBefore( form, container.querySelector( '.webcomic-transcripts' ) );
			}

			quicktags({
				id: 'webcomic_transcript_content',
				buttons: 'strong,em,link,block,del,ins,ul,ol,li,code,close'
			});
			QTags._buttonsInit(); /* eslint-disable-line no-underscore-dangle */
			form.querySelector( 'textarea' ).style.height = `${form.querySelector( 'textarea' ).scrollHeight}px`;
			form.querySelector( 'textarea' ).focus();
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Remove a comic transcript form.
	 *
	 * @param {object} form The form to remove.
	 * @param {object} container The form's container.
	 * @param {int}    post The post the form is for.
	 * @return {void}
	 */
	function webcomicRemoveTranscriptForm( form, container, post ) {
		const textarea = form.querySelector( 'textarea' );
		const saved = textarea.classList.contains( 'saved' );

		/* eslint-disable-next-line no-alert */
		if ( ! saved && textarea.value !== textarea.defaultValue && ! window.confirm( webcomicTranscriptL10n.warn ) ) {
			return;
		} else if ( form.parentNode.classList.contains( 'inside' ) ) {
			form.parentNode.querySelector( '.add-transcript' ).parentNode.style.display = null;
			form.parentNode.removeChild( form );

			if ( container ) {
				webcomicGetTranscriptForm( container, post );
			}

			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcribe_row' );
		data.append( 'post', form.querySelector( '[data-id]' ).getAttribute( 'data-id' ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( 0 === xhr.responseText.indexOf( 'ERROR' ) ) {
				form.querySelector( '.error' ).innerHTML = xhr.responseText;

				return;
			}

			const tbody = document.createElement( 'tbody' );
			let oldRow = form;

			while ( oldRow.tagName && 'tr' !== oldRow.tagName.toLowerCase() ) {
				oldRow = oldRow.parentNode;
			}

			tbody.innerHTML = xhr.responseText;

			webcomicActivateRowActions( tbody );

			oldRow.parentNode.replaceChild( tbody.children[0], oldRow );

			if ( container ) {
				webcomicGetTranscriptForm( container, post );
			}
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Add a transcript row.
	 *
	 * @param {object} table The transcript table.
	 * @param {int}    post The post to get a row for.
	 * @return {void}
	 */
	function webcomicAddTranscriptRow( table, post ) {
		const data = new FormData;
		const xhr = new XMLHttpRequest;

		data.append( 'action', 'webcomic_transcribe_row' );
		data.append( 'post', post );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( 0 === xhr.responseText.indexOf( 'ERROR' ) ) {
				return;
			}

			const tbody = document.createElement( 'tbody' );
			const lastRow = table.querySelector( 'tr:last-child' );

			tbody.innerHTML = xhr.responseText;

			webcomicActivateRowActions( tbody );

			table.insertBefore( tbody.children[0], table.querySelector( 'tr' ) );

			if ( lastRow.classList.contains( 'none' ) ) {
				table.removeChild( lastRow );
			}
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Activate transcript row actions.
	 *
	 * @param {object} row The row to activate actions on.
	 * @return {void}
	 */
	function webcomicActivateRowActions( row ) {
		row.querySelector( '.quickedit a' ).addEventListener( 'click', ( event )=> {
			event.preventDefault();

			const currentRow = event.target.parentNode.parentNode.parentNode.parentNode;

			webcomicGetTranscriptForm( currentRow, currentRow.getAttribute( 'data-id' ) );
		});
		row.querySelector( '.trash a' ).addEventListener( 'click', ( event )=> {
			event.preventDefault();

			webcomicTrashTranscript( event.target.parentNode.parentNode.parentNode.parentNode );
		});
	}

	/**
	 * Activate form functionality.
	 *
	 * @param {object} form The form to activate.
	 * @return {void}
	 */
	function webcomicActivateForm( form ) {
		form.querySelector( '.button-primary' ).addEventListener( 'click', ( event )=> {
			event.preventDefault();

			webcomicSubmitTranscript( form );
		});
		form.querySelector( '.button-secondary' ).addEventListener( 'click', ( event )=> {
			event.preventDefault();

			webcomicRemoveTranscriptForm( form );
		});
		form.querySelector( 'textarea' ).addEventListener( 'keyup', ( event )=> {
			if ( 'escape' !== event.key.toLowerCase() ) {
				return;
			}

			webcomicRemoveTranscriptForm( form );
		});
		form.querySelector( '.webcomic-media' ).addEventListener( 'click', ( event )=> {
			let element = event.target;

			while ( element.tagName && ! element.classList.contains( 'webcomic-media' ) ) {
				element = element.parentNode;
			}

			if ( ! element.tagName || ! element.querySelector( 'img' ) ) {
				return;
			}

			if ( element.classList.contains( 'contain' ) ) {
				element.classList.remove( 'contain' );
				element.style.height = 'auto';

				return;
			}

			const width = Number( element.querySelector( 'img' ).getAttribute( 'width' ) );
			const height = Number( element.querySelector( 'img' ).getAttribute( 'height' ) );
			const ratio = width / height;
			const scale = height / 2 * ratio;

			element.classList.add( 'contain' );
			element.style.height = `${scale}px`;
		});
		form.querySelector( '.webcomic-media' ).style.display = 'none';
	}

	/**
	 * Submit a transcript form.
	 *
	 * @param {object} form The transcript form to submit.
	 * @return {void}
	 */
	function webcomicSubmitTranscript( form ) {
		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const post = form.querySelector( '[data-id]' ).getAttribute( 'data-id' );
		const languages = webcomicGetTranscriptLanguages( form );

		data.append( 'action', 'webcomic_transcribe_submit' );
		data.append( 'post', post );
		data.append( 'post_parent', document.querySelector( '#post_ID' ).value );
		data.append( 'post_content', form.querySelector( 'textarea' ).value );
		data.append( 'post_status', form.querySelector( '.status' ).value );
		data.append( 'post_languages', languages );
		data.append(
			'Mgsisk\\Webcomic\\Transcribe\\MetaBoxSubmitTranscriptNonce',
			form.querySelector( '[type="hidden"]' ).value
		);

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( 0 === xhr.responseText.indexOf( 'ERROR' ) ) {
				form.querySelector( '.error' ).innerHTML = xhr.responseText;

				return;
			} else if ( '0' === post ) {
				let container = form.parentNode;

				while ( container.tagName && ! container.classList.contains( 'inside' ) ) {
					container = container.parentNode;
				}

				webcomicAddTranscriptRow( container.querySelector( 'tbody' ), xhr.responseText );
			}

			form.querySelector( 'textarea' ).classList.add( 'saved' );

			webcomicRemoveTranscriptForm( form );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Delete a transcript.
	 *
	 * @param {object} transcript The transcript to delete.
	 * @return {void}
	 */
	function webcomicTrashTranscript( transcript ) {
		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const post = transcript.getAttribute( 'data-id' );
		const href = transcript.querySelector( '.trash a' ).href;

		data.append( 'action', 'webcomic_transcribe_trash' );
		data.append( 'post', post );
		data.append( '_wpnonce', href.substr( href.indexOf( '_wpnonce' ) + 9 ) );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			} else if ( 0 === xhr.responseText.indexOf( 'ERROR' ) ) {
				transcript.querySelector( '.js-error' ).innerHTML = xhr.responseText;

				return;
			}

			transcript.innerHTML = xhr.responseText;

			transcript.querySelector( 'a' ).addEventListener( 'click', ( event )=> {
				event.preventDefault();

				const untrashData = new FormData;

				untrashData.append( 'action', 'webcomic_transcribe_untrash' );
				untrashData.append( 'post', post );
				untrashData.append( '_wpnonce', event.target.href.substr( event.target.href.indexOf( '_wpnonce' ) + 9 ) );

				xhr.onreadystatechange = ()=> {
					if ( 4 !== xhr.readyState ) {
						return;
					} else if ( 0 === xhr.responseText.indexOf( 'ERROR' ) ) {
						transcript.querySelector( '.js-error' ).innerHTML = xhr.responseText;

						return;
					}

					transcript.innerHTML = xhr.responseText;

					webcomicActivateRowActions( transcript );
				};
				xhr.open( 'POST', ajaxurl );
				xhr.send( untrashData );
			});
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get transcript languages.
	 *
	 * @param {object} form The transcript form.
	 * @return {array}
	 */
	function webcomicGetTranscriptLanguages( form ) {
		const langs = form.querySelectorAll( '[type="checkbox"]' );
		const languages = [];

		for ( let i = 0; i < langs.length; i++ ) {
			if ( ! langs[i].checked ) {
				continue;
			}

			languages.push( langs[i].value );
		}

		return languages;
	}
}() );
