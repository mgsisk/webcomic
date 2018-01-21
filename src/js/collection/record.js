/* global webcomicRecordL10n */

/**
 * Table records implementation.
 */
( function load() {
	if ( 'loading' === document.readyState ) {
		return document.addEventListener( 'DOMContentLoaded', load );
	}

	const tables = document.querySelectorAll( 'table.webcomic-records' );

	for ( let i = 0; i < tables.length; i++ ) {
		prepareTable( tables[ i ]);
	}

	/**
	 * Prepare a table for adding and removing alerts.
	 *
	 * @param {object} table The table to prepare.
	 * @return {void}
	 */
	function prepareTable( table ) {
		const rows     = table.querySelectorAll( 'tbody > tr' ),
					template = table.querySelector( 'tbody > tr' );

		template.parentNode.removeChild( template );

		getAddButton( table, template );

		for ( let i = 1; i < rows.length; i++ ) {
			getDeleteButton( rows[ i ]);
		}
	}

	/**
	 * Get the add row button.
	 *
	 * @param {object} table The table to get an add alert button for.
	 * @param {object} template The new row template.
	 * @return {void}
	 */
	function getAddButton( table, template ) {
		const cell = document.createElement( 'td' );

		cell.style.width = '3rem';
		cell.innerHTML   = `
		<button type="button" class="button button-secondary delete small">
			<span class="dashicons dashicons-plus">
				<span class="screen-reader-text">${webcomicRecordL10n.add}</span>
			</span>
		</button>`;
		cell.querySelector( 'button' ).addEventListener( 'click', ( event )=> getNewRow( table, template ) );

		table.querySelector( 'thead tr' ).appendChild( cell );
	}

	/**
	 * Get a delete row button.
	 *
	 * @param {object} row The row to get a delete button for.
	 * @return {void}
	 */
	function getDeleteButton( row ) {
		const cell = document.createElement( 'td' );

		cell.innerHTML = `
		<button type="button" class="button button-secondary delete small">
			<span class="dashicons dashicons-no">
				<span class="screen-reader-text">${webcomicRecordL10n.delete}</span>
			</span>
		</button>`;
		cell.querySelector( 'button' ).addEventListener( 'click', ( event )=> {
			let row = event.target;

			while ( 'tr' !== row.tagName.toLowerCase() ) {
				row = row.parentNode;
			}

			row.parentNode.removeChild( row );
		});

		row.appendChild( cell );
	}

	/**
	 * Get a new row.
	 *
	 * @param {object} table The table to get a new row for.
	 * @param {object} template The new row template.
	 * @return {void}
	 */
	function getNewRow( table, template ) {
		const top = table.querySelector( 'tbody > tr' );

		table.querySelector( 'tbody' ).insertBefore( template.cloneNode( true ), top );

		getDeleteButton( table.querySelector( 'tbody > tr' ) );
	}
}() );
