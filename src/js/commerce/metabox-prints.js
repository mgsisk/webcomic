/**
 * Print cost calculator.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	webcomicPreparePrintCalc( document.querySelectorAll( '[name^="webcomic_commerce_prints_adjust"]' ) );

	/**
	 * Prepare elements for print cost preview.
	 *
	 * @param {array} elements The list of comic calculator elements.
	 * @return {void}
	 */
	function webcomicPreparePrintCalc( elements ) {
		for ( let i = 0; i < elements.length; i++ ) {
			elements[i].addEventListener( 'change', ()=> {
				const adjust = Number( elements[i].value );
				const price = Number( elements[i].getAttribute( 'data-price' ) );
				const output = elements[i].parentNode.parentNode.nextElementSibling.querySelector( 'output' );

				output.innerHTML = parseFloat( price * ( adjust / 100 + 1 ) ).toFixed( 2 );
			});
		}
	}
}() );
