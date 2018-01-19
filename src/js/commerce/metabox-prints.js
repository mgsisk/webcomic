/* global ajaxurl */

/**
 * Print cost calculator.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const elements = document.querySelectorAll( '[name^="webcomic_commerce_prints_adjust"]' );

	for ( let i = 0; i < elements.length; i++ ) {
		elements[ i ].addEventListener( 'change', ()=> {
			const adjust = Number( elements[ i ].value ),
						price  = Number( elements[ i ].getAttribute( 'data-price' ) ),
						output = elements[ i ].parentNode.parentNode.nextElementSibling.querySelector( 'output' );

			output.innerHTML = parseFloat( price * ( adjust / 100 + 1 ) ).toFixed( 2 );
		});
	}
}() );
