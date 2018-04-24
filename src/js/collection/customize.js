/**
 * Customizer functionality.
 */
wp.customize.bind( 'ready', ()=> {
	prepareComicCustomizer();

	/**
	 * Prepare Webcomic's customizer section and controls.
	 *
	 * @return {void}
	 */
	function prepareComicCustomizer() {
		toggleFrontPageControls( wp.customize( 'webcomic_integrate_front_page_order' ).get() );
		toggleIntegrateControls( wp.customize( 'webcomic_integrate' ).get() );

		wp.customize.control( 'webcomic_integrate', ( control )=> {
			control.setting.bind( value=> toggleIntegrateControls( value ) );
		});

		wp.customize.control( 'webcomic_integrate_archive_preview', ( control )=> {
			control.setting.bind( value=> toggleArchiveControls( value ) );
		});

		wp.customize.control( 'webcomic_integrate_front_page_order', ( control )=> {
			control.setting.bind( value=> toggleFrontPageControls( value ) );
		});
	}

	/**
	 * Toggle the visibility of a customizer control.
	 *
	 * @param {string} id The control ID to show or hide.
	 * @param {bool} onoff Whether to show or hide the control.
	 * @return {void}
	 */
	function toggleControl( id, onoff ) {
		if ( ! wp.customize.control( id ) ) {
			return;
		}

		wp.customize.control( id ).container.toggle( onoff );
	}

	/**
	 * Toggle the visibility of integration controls.
	 *
	 * @param {string} value The current webcomic_integrate value.
	 * @return {void}
	 */
	function toggleIntegrateControls( value ) {
		toggleControl( 'webcomic_integrate_styles', '' !== value );
		toggleControl( 'webcomic_integrate_navigation_gestures', '' !== value );
		toggleControl( 'webcomic_integrate_navigation_keyboard', '' !== value );
		toggleControl( 'webcomic_integrate_navigation_above', '' !== value );
		toggleControl( 'webcomic_integrate_navigation_below', '' !== value );
		toggleControl( 'webcomic_integrate_archive_preview', '' !== value );
		toggleControl( 'webcomic_integrate_front_page_order', '' !== value );
		toggleArchiveControls( '' );
		toggleFrontPageControls( '' );

		if ( value ) {
			toggleArchiveControls( wp.customize( 'webcomic_integrate_archive_preview' ).get() );
			toggleFrontPageControls( wp.customize( 'webcomic_integrate_front_page_order' ).get() );
		}
	}

	/**
	 * Toggle the visibility of archive controls.
	 *
	 * @param {string} value The current webcomic_integrate_archive_preview value.
	 * @return {void}
	 */
	function toggleArchiveControls( value ) {
		toggleControl( 'webcomic_integrate_archive_preview_content', '' !== value );
	}

	/**
	 * Toggle the visibility of front page controls.
	 *
	 * @param {string} value The current webcomic_integrate_front_page_order value.
	 * @return {void}
	 */
	function toggleFrontPageControls( value ) {
		toggleControl( 'webcomic_integrate_front_page_collection', '' !== value );
		toggleControl( 'webcomic_integrate_front_page_content', '' !== value );
		toggleControl( 'webcomic_integrate_front_page_meta', '' !== value );
		toggleControl( 'webcomic_integrate_front_page_comments', '' !== value );
	}
});
