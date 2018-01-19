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

	const touchEvent = {
					minDistance: 150,
					maxTime: 300,
					time: 0,
					posX: 0,
					posY: 0
				},
				infiniteStart = parseInt( ( window.location.search.match( /wi=(\d+)/ ) || [ '', 0 ])[1]);

	window.addEventListener( 'scroll', webcomicInfiniteScroll );
	document.addEventListener( 'change', webcomicSelectNavigation );
	document.addEventListener( 'keyup', webcomicKeyboardNavigation );
	document.addEventListener( 'click', webcomicDynamicComicLoading );
	document.documentElement.addEventListener( 'touchstart', webcomicTouchNavigationStart, false );
	document.documentElement.addEventListener( 'touchend', webcomicTouchNavigationEnd );

	window.dispatchEvent( new Event( 'scroll' ) );

	/**
	 * Handle infinitely-scrolling comic containers.
	 * @return {void}
	 */
	function webcomicInfiniteScroll() {
		const container = document.querySelector( '.webcomic-infinite, [data-webcomic-infinite]' );

		if ( ! container ) {
			window.removeEventListener( 'scroll', webcomicInfiniteScroll );

			return;
		} else if ( container.classList.contains( 'loading' ) ) {
			return;
		}

		const containerLastChild = container.children[ container.children.length - 1 ];

		if ( containerLastChild && containerLastChild.getBoundingClientRect().top + window.scrollY > window.scrollY + window.innerHeight ) {
			return;
		} else if ( container.querySelector( 'wbr.finished' ) ) {
			window.removeEventListener( 'scroll', webcomicInfiniteScroll );

			return;
		}

		container.classList.add( 'loading' );

		const offset = parseInt( container.children.length ) + infiniteStart,
					data   = new FormData,
					xhr    = new XMLHttpRequest;
		let   url    = window.location.href.replace( /wi=\d+/, `wi=${offset}` );

		if ( ! window.location.search ) {
			url = `${window.location.href}?wi=${offset}`;
		}

		data.append( 'action', 'webcomic_infinite' );
		data.append( 'url', window.location.href );
		data.append( 'args', `offset=${offset}&${container.getAttribute( 'data-webcomic-infinite' )}` );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			container.innerHTML += xhr.responseText;
			container.classList.remove( 'loading' );

			history.replaceState({}, '', url );
		};
		xhr.open( 'POST', webcomicCommonJS.ajaxurl );
		xhr.send( data );
	}

	/**
	 * Handle data-webcomic-url attributes on `<select>` elements.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicSelectNavigation( event ) {
		if ( 'select' !== event.target.tagName.toLowerCase() || ! event.target.options[ event.target.selectedIndex ].getAttribute( 'data-webcomic-url' ) ) {
			return;
		}

		const url = event.target.options[ event.target.selectedIndex ].getAttribute( 'data-webcomic-url' );

		if ( url === window.location.href ) {
			return;
		}

		window.location.href = url;
	}

	/**
	 * Handle comic navigation keyboard events.
	 *
	 * NOTE The data-webcomic-shortcuts attribute is deprecated; use the
	 * webcomic-keyboard class instead.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicKeyboardNavigation( event ) {
		if ( event.target.tagName.toLowerCase().match( /button|input|meter|option|output|progress|select|textarea/ ) ) {
			return;
		}

		let container = document.querySelector( '.webcomic-keyboard, [data-webcomic-shortcuts]' ),
				key       = event.key;

		if ( ! container ) {
			return;
		} else if ( event.shiftKey ) {
			key += 'Shift';
		}

		webcomicShortcut( key, container );
	}

	/**
	 * Handle dynamic comic loading.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicDynamicComicLoading( event ) {
		let containers = document.querySelectorAll( '.webcomic-dynamic' );

		if ( ! containers.length ) {
			return;
		}

		let target = event.target;

		while ( target.tagName && ! target.classList.contains( 'webcomic-link' ) ) {
			target = target.parentNode;
		}

		if ( ! target.tagName ) {
			return;
		}

		event.preventDefault();

		const data = new FormData,
					xhr  = new XMLHttpRequest;

		data.append( 'action', 'webcomic_dynamic' );

		xhr.onreadystatechange = ()=> {
			if ( 4 !== xhr.readyState ) {
				return;
			}

			const responseContainer = document.createElement( 'div' );

			responseContainer.innerHTML = xhr.responseText;

			const newContainers = responseContainer.querySelectorAll( '.webcomic-dynamic' );

			if ( ! newContainers.length ) {
				window.location.href = target.href;

				return;
			}

			for ( let i = 0; i < containers.length; i++ ) {
				containers[i].parentNode.replaceChild( newContainers[i], containers[i]);
			}
		};
		xhr.open( 'POST', target.href );
		xhr.send( data );
	}

	/**
	 * Handle touch gesture event coordination.
	 *
	 * NOTE The data-webcomic-gestures attribute is deprecated; use the
	 * webcomic-gestures class instead.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicTouchNavigationStart( event ) {
		const container = document.querySelector( '.webcomic-gestures, [data-webcomic-gestures]' );

		if ( ! container ) {
			document.documentElement.removeEventListener( 'touchstart', webcomicTouchNavigationStart, false );
			document.documentElement.removeEventListener( 'touchend', webcomicTouchNavigationEnd );

			return;
		}

		touchEvent.time = new Date().getTime();
		touchEvent.posX = event.changedTouches[0].pageX;
		touchEvent.posY = event.changedTouches[0].pageY;
	}

	/**
	 * Handle touch gesture navigation events.
	 *
	 * NOTE The data-webcomic-gestures attribute is deprecated; use the
	 * webcomic-gestures class instead.
	 *
	 * @param {object} event The current event object.
	 * @return {void}
	 */
	function webcomicTouchNavigationEnd( event ) {
		const container = document.querySelector( '.webcomic-gestures, [data-webcomic-gestures]' ),
					swipeT    = new Date().getTime() - touchEvent.time,
					swipeX    = event.changedTouches[0].pageX - touchEvent.posX,
					swipeY    = event.changedTouches[0].pageY - touchEvent.posY;
		let shortcut    = 'Arrow';

		if ( ! container ) {
			document.documentElement.removeEventListener( 'touchstart', webcomicTouchNavigationStart, false );
			document.documentElement.removeEventListener( 'touchend', webcomicTouchNavigationEnd );

			return;
		} else if ( swipeT > touchEvent.maxTime ) {
			return;
		}

		if ( Math.abs( swipeX ) >= touchEvent.minDistance && Math.abs( swipeY ) < touchEvent.minDistance ) {
			shortcut += 'Right';

			if ( 0 < swipeX ) {
				shortcut = shortcut.replace( 'Right', 'Left' );
			}
		} else if ( Math.abs( swipeY ) >= touchEvent.minDistance && Math.abs( swipeX ) < touchEvent.minDistance ) {
			shortcut += 'Down';

			if ( 0 < swipeY ) {
				shortcut = shortcut.replace( 'Down', 'Up' );
			}
		}

		if ( 'Arrow' === shortcut ) {
			return;
		} else if ( 1 < event.changedTouches.length ) {
			shortcut += 'Shift';
		}

		webcomicShortcut( shortcut, container );
	}

	/**
	 * Handle comic navigation shortcuts.
	 *
	 * @param {string} shortcut The shortcut to execute.
	 * @param {object} container The webcomic shortcut container.
	 */
	function webcomicShortcut( shortcut, container ) {
		const classes = {
						ArrowDownShift: 'random-webcomic-link',
						ArrowLeft: 'previous-webcomic-link',
						ArrowLeftShift: 'first-webcomic-link',
						ArrowRight: 'next-webcomic-link',
						ArrowRightShift: 'last-webcomic-link',
						ArrowUpShift: 'webcomic-print-link'
					},
					anchor  = container.querySelector( `.${classes[ shortcut ]}[href]` );

		if ( ! shortcut || ! container || ! anchor ) {
			return;
		}

		anchor.dispatchEvent( new MouseEvent( 'click', {
			bubbles: true,
			cancelable: true
		}) );
	}
}() );
