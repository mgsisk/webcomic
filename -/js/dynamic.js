/** Enable dynamic webcomic navigation. */
jQuery( function( $ ) {
	if ( $( '[data-webcomic-container]' ) ) {
		var defaults = [];
		
		$( '[data-webcomic-container]' ).each( function() {
			defaults.push( {
				parent: $( this ).find( '[data-webcomic-parent]' ).data( 'webcomic-parent' ),
				container: $( this ).data( 'webcomic-container' )
			} );
		} );
		
		$.getJSON( window.location.href, { webcomic_dynamic_defaults: defaults }, function( data ) {
			defaults = data;
		} );
		
		function dynamic_webcomic( url, container ) {
			$.get( url, { webcomic_dynamic: container }, function( data ) {
				$( '[data-webcomic-container="' + container + '"]' ).html( data ).show();
			} );
		}
		
		$( window ).on( 'popstate', function( e ) {
			if ( e.originalEvent.state ) {
				if ( e.originalEvent.state.webcomicReset ) {
					$.each( defaults, function( i, v ) {
						dynamic_webcomic( v.url, v.container );
					} );
				} else if ( e.originalEvent.state.webcomic ) {
					dynamic_webcomic( e.originalEvent.state.url, e.originalEvent.state.container );
				}
			} else {
				history.replaceState( { webcomicReset: true }, '', window.location.href );
			}
		} );
		
		$( document ).on( 'click', '[data-webcomic-container] [data-webcomic-dynamic][href]', function( e ) {
			e.preventDefault();
			
			var url = $( this ).attr( 'href' );
			var container = $( this ).closest( '[data-webcomic-container]' ).data( 'webcomic-container' );
			
			dynamic_webcomic( url, container );
			
			history.pushState( { webcomic: true, url: url, container: container }, '', container ? window.location.href : url );
		} );
	}
} );