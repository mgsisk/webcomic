/* global ajaxurl, jQuery, webcomicMediaManagerL10n, wp  */

/**
 * Media manager implementation.
 *
 * Utilizes WordPress' own media features to provide consistent media handling
 * for various Webcomic features.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const elements = document.querySelectorAll( '[data-webcomic-media-manager]' );

	for ( let i = 0; i < elements.length; i++ ) {
		updateMediaManager(
			elements[ i ],
			document.querySelector( `[name="${elements[ i ].getAttribute( 'data-input' )}"]` ).value.split( ',' ),
			Boolean( elements[ i ].getAttribute( 'data-webcomic-media-manager' ) )
		);
	}

	/**
	 * Update the media manager during widget events.
	 *
	 * Widgets have their own, jQuery-enabled events, so we have to use jQuery
	 * to listen for these events and update the media manager as necessary.
	 */
	jQuery( document ).on( 'widget-added widget-updated', ( event, widget )=> {
		if ( ! widget[0].id.match( /_mgsisk_webcomic_/ ) ) {
			return;
		}

		const elements = widget[0].querySelectorAll( '[data-webcomic-media-manager]' );

		for ( let i = 0; i < elements.length; i++ ) {
			updateMediaManager(
				elements[ i ],
				document.querySelector( `[name="${elements[ i ].getAttribute( 'data-input' )}"]` ).value.split( ',' ),
				Boolean( elements[ i ].getAttribute( 'data-webcomic-media-manager' ) )
			);
		}
	});

	/**
	 * Update the media manager.
	 *
	 * @param {object} element The media manager element.
	 * @param {array}  media The selected media.
	 * @param {bool}   multiple Wether the media manager supports multiple media.
	 * @return {void}
	 */
	function updateMediaManager( element, media, multiple ) {
		const input    = document.querySelector( `[name="${element.getAttribute( 'data-input' )}"]` ),
					data     = new FormData,
					xhr      = new XMLHttpRequest,
					oldValue = input.value.split( ',' );

		input.value = media.toString();

		if ( oldValue.toString() !== media.toString() ) {
			input.dispatchEvent( new Event( 'change', {
				bubbles: true
			}) );
		}

		data.append( 'action', 'webcomic_update_media_manager' );
		data.append( 'media', media );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			getMediaManager( element, media, multiple, JSON.parse( xhr.responseText ) );
		};
		xhr.open( 'POST', ajaxurl );
		xhr.send( data );
	}

	/**
	 * Get the media manager.
	 *
	 * @param {object} element The media manager element.
	 * @param {array}  media The selected media.
	 * @param {bool}   multiple Wether the media manager supports multiple media.
	 * @param {array}  elements The array of elements returned by the XHR handler.
	 * @return {void}
	 */
	function getMediaManager( element, media, multiple, elements ) {
		element.innerHTML = '';

		for ( let i = 0; i < elements.length; i++ ) {
			element.innerHTML += getMediaElement( elements[ i ].id, elements[ i ].media );
		}

		element.innerHTML += getMediaButton( elements, multiple );

		if ( multiple ) {
			enableMediaSorting( element, multiple );
		}

		element.querySelector( 'button' ).addEventListener( 'click', ()=> {
			loadMediaManager( element, multiple );
		});

		const anchors = element.querySelectorAll( 'a' );

		for ( let i = 0; i < anchors.length; i++ ) {
			anchors[ i ].addEventListener( 'click', ( event )=> {
				event.preventDefault();

				const index = media.indexOf( Number( anchors[ i ].parentNode.getAttribute( 'data-id' ) ) );

				console.log( index );

				media.splice( index, 1 );

				updateMediaManager( element, media, multiple );
			});
		}
	}

	/**
	 * Get an individual media element's HTML.
	 *
	 * @param {int}    id The media element ID.
	 * @param {object} element The media element to display.
	 * @return {string}
	 */
	function getMediaElement( id, element ) {
		return `
			<div data-id="${id}">
				${element}
				<a class="dashicons dashicons-no" style="cursor:pointer">
					<span class="screen-reader-text">${webcomicMediaManagerL10n.remove}</span>
				</a>
			</div>`;
	}

	/**
	 * Get the media manager button and help text.
	 *
	 * @param {array} elements The array of elements returns by the XHR handler.
	 * @param {bool}  multiple Wether the media manager supports multiple media.
	 * @return {string}
	 */
	function getMediaButton( media, multiple ) {
		let icon   = 'dashicons-format-image',
				label  = webcomicMediaManagerL10n.add,
				button = '';

		if ( multiple ) {
			icon = 'dashicons-images-alt2';
		}

		if ( media.length ) {
			label = webcomicMediaManagerL10n.change;
		}

		button = `
			<p>
				<button type="button" class="button button-large">
					<span class="dashicons ${icon}"></span> ${label}
				</button>
			</p>`;

		if ( 1 < media.length && multiple ) {
			button += `<p class="description">${webcomicMediaManagerL10n.drag}</p>`;
		}

		return button;
	}

	/**
	 * Enable media sorting for multi-media managers.
	 *
	 * @param {object} element The media manager element.
	 * @param {bool}   multiple Wether the media manager supports multiple media.
	 * @return {void}
	 */
	function enableMediaSorting( element, multiple ) {
		jQuery( element ).sortable({
			items: 'div',
			start: ( event, ui )=> {
				ui.placeholder.height( ui.helper.outerHeight() );
			},
			stop: ()=> {
				updateMediaManager(
					element,
					jQuery( element ).sortable( 'toArray', {
						attribute: 'data-id'
					}),
					multiple
				);
			}
		});
	}

	/**
	 * Load the WordPress media manager when the media manager button is clicked.
	 *
	 * @param {object} element The media manager element.
	 * @param {bool}   multiple Wether the media manager supports multiple media.
	 * @return {void}
	 */
	function loadMediaManager( element, multiple ) {
		wp.media.frames.webcomicMedia = wp.media({
			title: webcomicMediaManagerL10n.title,
			button: {
				text: webcomicMediaManagerL10n.update
			},
			library: {
				type: 'image'
			},
			multiple
		}).on( 'select', ()=> {
			const media     = [],
						selection = wp.media.frames.webcomicMedia.state().get( 'selection' );

			for ( let i = 0; i < selection.length; i++ ) {
				media.push( selection.models[ i ].id );
			}

			updateMediaManager( element, media, multiple );
		}).open();
	}
}() );
