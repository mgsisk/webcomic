/**
 * Page metabox utilities.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const template = document.querySelector( '[name="webcomic_page_template"]' );

	template.addEventListener( 'change', ( event )=> {
		const options = event.target.parentNode.parentNode.querySelectorAll( '[class^="webcomic-template-"]' );

		for ( let i = 0; i < options.length; i++ ) {
			options[i].classList.add( 'hidden' );

			if ( options[i].classList.contains( `webcomic-template-${template.value}` ) ) {
				options[i].classList.remove( 'hidden' );
			}
		}
	});

	template.dispatchEvent( new Event( 'change' ) );
}() );
