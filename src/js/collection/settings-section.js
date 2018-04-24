/**
 * Manage collection settings functionality.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const sections = document.querySelectorAll( '#wpbody .wrap form > h2' );

	for ( let i = 0; i < sections.length; i++ ) {
		const ruler = document.createElement( 'hr' );
		const anchor = document.createElement( 'a' );

		anchor.innerHTML = sections[i].innerHTML;

		sections[i].innerHTML = anchor.outerHTML;
		sections[i].style.cursor = 'pointer';
		sections[i].parentNode.insertBefore( ruler, sections[i]);
		sections[i].addEventListener( 'click', ( event )=> {
			event.preventDefault();

			let next = event.target.nextElementSibling;

			if ( ! next ) {
				next = event.target;

				while ( 'h2' !== next.tagName.toLowerCase() ) {
					next = next.parentNode;
				}

				next = next.nextElementSibling;
			}

			while ( next.tagName.toLowerCase().match( /div|table/ ) ) {
				if ( next.classList.contains( 'hidden' ) ) {
					next.classList.remove( 'hidden' );
				} else {
					next.classList.add( 'hidden' );
				}

				next = next.nextElementSibling;
			}
		});

		sections[i].dispatchEvent( new MouseEvent( 'click' ) );
	}
}() );
