/**
 * Media matcher utilities.
 *
 * @return {void}
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const element = document.querySelector( '[name="webcomic_matcher[match_post]"]' );

	element.addEventListener( 'change', ( event )=> {
		const fields = document.querySelectorAll( 'label[for^="webcomic_matcher[post_"]' );

		for ( let i = 0; i < fields.length; i++ ) {
			fields[ i ].parentNode.parentNode.className = 'hidden';
		}

		if ( 'post_date' === event.target.value ) {
			fields[0].parentNode.parentNode.className = '';
		} else if ( 'post_custom' === event.target.value ) {
			fields[1].parentNode.parentNode.className = '';
		}
	});

	element.dispatchEvent( new Event( 'change' ) );
}() );
