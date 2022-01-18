/* global webcomicCommonJS */

/**
 * Common functionality.
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
	};
	const infiniteStart = parseInt( ( window.location.search.match( /wi=(\d+)/ ) || [ '', 0 ])[1]);

	webcomicCommonEvents();

	/**
	 * Prepare common event handlers.
	 *
	 * @return {void}
	 */
	function webcomicCommonEvents() {
		window.addEventListener( 'scroll', webcomicInfiniteScroll );
		document.addEventListener( 'change', webcomicSelectNavigation );
		document.addEventListener( 'keyup', webcomicKeyboardNavigation );
		document.addEventListener( 'click', webcomicDynamicComicLoading );
		document.documentElement.addEventListener( 'touchstart', webcomicTouchNavigationStart, false );
		document.documentElement.addEventListener( 'touchend', webcomicTouchNavigationEnd );

		window.dispatchEvent( new Event( 'scroll' ) );
	}

	/**
	 * Handle infinitely-scrolling comic containers.
	 *
	 * @return {void}
	 */
	function webcomicInfiniteScroll() {
		const container = document.querySelector( '.webcomic-infinite, [data-webcomic-infinite]' );

		if ( ! webcomicInitInfiniteScroll( container ) ) {
			return;
		}

		const data = new FormData;
		const xhr = new XMLHttpRequest;
		const offset = parseInt( container.children.length ) + infiniteStart;
		let url = window.location.href.replace( /wi=\d+/, `wi=${offset}` );

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
		if ( 'select' !== event.target.tagName.toLowerCase() ) {
			return;
		} else if ( ! event.target.options[event.target.selectedIndex].getAttribute( 'data-webcomic-url' ) ) {
			return;
		}

		const url = event.target.options[event.target.selectedIndex].getAttribute( 'data-webcomic-url' );

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
		if ( event.target.tagName.toLowerCase().match( /button|input|meter|option|output|progress|select|textarea/ ) || event.target.getAttribute('contenteditable') !== null ) {
			return;
		}

		const container = document.querySelector( '.webcomic-keyboard, [data-webcomic-shortcuts]' );
		let key = event.key;

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
		const containers = document.querySelectorAll( '.webcomic-dynamic' );

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

		const data = new FormData;
		const xhr = new XMLHttpRequest;

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
		const container = document.querySelector( '.webcomic-gestures, [data-webcomic-gestures]' );
		const swipeT = new Date().getTime() - touchEvent.time;
		const swipeX = event.changedTouches[0].pageX - touchEvent.posX;
		const swipeY = event.changedTouches[0].pageY - touchEvent.posY;

		if ( ! container ) {
			document.documentElement.removeEventListener( 'touchstart', webcomicTouchNavigationStart, false );
			document.documentElement.removeEventListener( 'touchend', webcomicTouchNavigationEnd );

			return;
		} else if ( swipeT > touchEvent.maxTime ) {
			return;
		}

		const shortcut = webcomicGetTouchNavigationShortcut( event, swipeX, swipeY );

		if ( ! shortcut.replace( /^Arrow(Shift)?$/, '' ) ) {
			return;
		}

		webcomicShortcut( shortcut, container );
	}

	/**
	 * Handle comic navigation shortcuts.
	 *
	 * @param {string} shortcut The shortcut to execute.
	 * @param {object} container The webcomic shortcut container.
	 * @return {void}
	 */
	function webcomicShortcut( shortcut, container ) {
		const classes = {
			ArrowDownShift: 'random-webcomic-link',
			ArrowLeft: 'previous-webcomic-link',
			ArrowLeftShift: 'first-webcomic-link',
			ArrowRight: 'next-webcomic-link',
			ArrowRightShift: 'last-webcomic-link',
			ArrowUpShift: 'webcomic-print-link'
		};
		const anchor = container.querySelector( `.${classes[shortcut]}[href]` );

		if ( ! shortcut || ! container || ! anchor ) {
			return;
		}

		anchor.dispatchEvent( new MouseEvent( 'click', {
			bubbles: true,
			cancelable: true
		}) );
	}

	/**
	 * Get a touch navigation shortcut key.
	 *
	 * @param {object} event The current event object.
	 * @param {int} swipeX The horizontal swipe difference.
	 * @param {int} swipeY The vertical swipe difference.
	 * @return {string}
	 */
	function webcomicGetTouchNavigationShortcut( event, swipeX, swipeY ) {
		let shortcut = 'Arrow';

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

		if ( 1 < event.changedTouches.length ) {
			shortcut += 'Shift';
		}

		return shortcut;
	}

	/**
	 * Initialize Webcomic's infinite scroll
	 *
	 * @param {object} container The webcomic infinite scroll container.
	 * @return {bool}
	 */
	function webcomicInitInfiniteScroll( container ) {
		if ( ! container ) {
			window.removeEventListener( 'scroll', webcomicInfiniteScroll );

			return false;
		} else if ( container.classList.contains( 'loading' ) ) {
			return false;
		}

		const containerLastChild = container.children[container.children.length - 1];
		const windowScroll = window.scrollY + window.innerHeight;
		let containerScroll = 0;

		if ( containerLastChild ) {
			containerScroll = containerLastChild.getBoundingClientRect().top + window.scrollY;
		}

		if ( containerLastChild && containerScroll > windowScroll ) {
			return false;
		} else if ( container.querySelector( 'wbr.finished' ) ) {
			window.removeEventListener( 'scroll', webcomicInfiniteScroll );

			return false;
		}

		container.classList.add( 'loading' );

		return true;
	}
}() );
