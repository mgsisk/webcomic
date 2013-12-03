/** Handle keyboard shortcut navigation. */
jQuery( function( $ ) {
	function webcomic_shortcut( target ) {
		var $e;
		
		if ( $( "[data-webcomic-shortcuts]" ).length ) {
			$e = $( "[data-webcomic-shortcuts]:first " + target + "[href]:first" );
		} else {
			$e = $( target + "[href]:first" );
		}
		
		if ( $e.length ) {
			if ( $.fn.webcomicDynamicNavigation && $e.closest( "[data-webcomic-container]" ).length ) {
				$e.trigger( "click" );
			} else {
				window.location.href = $e.attr( "href" );
			}
		}
	}
	
	$.hotkeys.add( "right", function() { webcomic_shortcut( ".next-webcomic-link" ); } );
	$.hotkeys.add( "left", function() { webcomic_shortcut( ".previous-webcomic-link" ); } );
	$.hotkeys.add( "shift+right", function() { webcomic_shortcut( ".last-webcomic-link" ); } );
	$.hotkeys.add( "shift+left", function() { webcomic_shortcut( ".first-webcomic-link" ); } );
	$.hotkeys.add( "shift+up", function() { webcomic_shortcut( ".purchase-webcomic-link" ); } );
	$.hotkeys.add( "shift+down", function() { webcomic_shortcut( ".random-webcomic-link" ); } );
} );