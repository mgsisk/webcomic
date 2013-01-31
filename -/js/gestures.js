/** Enable touch gesture navigation. */
jQuery( function( $ ) {
	var canvas, end, start, scroll;
	
	$( '[data-webcomic-gestures]' ).on( 'touchstart', function( e ) {
		canvas = $( this );
		start  = e.originalEvent;
		scroll = $( e.target );
		
		if ( scroll.width() > $( this ).width() || scroll.height() > $( this ).height() ) {
			scroll = {
				x: scroll.width() > $( this ).width(),
				y: scroll.height() > $( this ).height(),
				e: scroll
				
			};
			
			while ( scroll.e = scroll.e.parent() ) {
				if ( scroll.e.prop( 'scrollWidth' ) > scroll.e.width() || scroll.e.prop( 'scrollHeight' ) > scroll.e.height() ) {
					break;
				}
				
				if ( scroll.e[ 0 ] === $( this )[ 0 ] ) {
					scroll = false;
					
					break;
				}
			}
		} else {
			scroll = false;
		}
	} );
	
	$( '[data-webcomic-gestures]' ).on( 'touchmove', function( e ) {
		end = e.originalEvent;
	} );
	
	$( '[data-webcomic-gestures]' ).on( 'touchend', function( e ) {
		if ( end ) {
			var x, y, a, b, d, $e;
			
			x = end.pageX - start.pageX;
			y = end.pageY - start.pageY;
			a = Math.abs( x );
			b = Math.abs( y );
			
			if ( a > b ) {
				d = 0 > x ? 'left' : 'right';
			} else {
				d = 0 > y ? 'up' : 'down';
			}
			
			if ( !webcomic_scrolling( d ) ) {
				if ( 1 === end.touches.length ) {
					if ( 'left' === d ) {
						$e = $( '.previous-webcomic-link[href]:first', canvas );
					} else if ( 'right' === d ) {
						$e = $( '.next-webcomic-link[href]:first', canvas );
					}
				} else if ( 2 == end.touches.length ) {
					if ( 'left' === d ) {
						$e = $( '.first-webcomic-link[href]:first', canvas );
					} else if ( 'right' === d ) {
						$e = $( '.last-webcomic-link[href]:first', canvas );
					} else if ( 'up' === d ) {
						$e = $( '.purchase-webcomic-link[href]:first', canvas );
					} else if ( 'down' === d ) {
						$e = $( '.random-webcomic-link[href]:first', canvas );
					}
				}
				
				if ( $e && $e.length ) {
					e.preventDefault();
					
					if ( $e.closest( '[data-webcomic-container]' ).length ) {
						$e.trigger( 'click' );
					} else {
						window.location.href = $e.attr( 'href' );
					}
				}
			}
		}
	} );
	
	$( '[data-webcomic-gestures]' ).on( 'touchcancel', function( e ) {
		end = start = scroll = false;
	} );
	
	function webcomic_scrolling( direction ) {
		if ( scroll && 1 === end.touches.length ) {
			if ( 'left' === direction && scroll.x ) {
				return scroll.e.prop( 'scrollWidth' ) - scroll.e.scrollLeft() !== scroll.e.outerWidth();
			} else if ( 'right' === direction && scroll.x ) {
				return 0 !== scroll.e.scrollLeft();
			} else if ( 'up' === direction && scroll.y ) {
				return scroll.e.prop( 'scrollHeight' ) - scroll.e.scrollTop() === scroll.e.outerHeight();
			} else if ( 'down' === direction && scroll.y ) {
				return 0 !== scroll.e.scrollTop();
			}
		}
		
		return false;
	}
} );