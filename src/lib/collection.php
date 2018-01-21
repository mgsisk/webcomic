<?php
/**
 * Collection component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the collection component.
 *
 * @return void
 */
function collection() {
	require __DIR__ . '/collection/common.php';
	require __DIR__ . '/collection/customize.php';
	require __DIR__ . '/collection/filters.php';
	require __DIR__ . '/collection/globals.php';
	require __DIR__ . '/collection/shortcodes.php';
	require __DIR__ . '/collection/walker/class-collectionlister.php';
	require __DIR__ . '/collection/walker/class-comiclister.php';
	require __DIR__ . '/collection/widget/class-webcomiclink.php';
	require __DIR__ . '/collection/widget/class-firstwebcomiclink.php';
	require __DIR__ . '/collection/widget/class-lastwebcomiclink.php';
	require __DIR__ . '/collection/widget/class-nextwebcomiclink.php';
	require __DIR__ . '/collection/widget/class-previouswebcomiclink.php';
	require __DIR__ . '/collection/widget/class-randomwebcomiclink.php';
	require __DIR__ . '/collection/widget/class-webcomiccollectionlink.php';
	require __DIR__ . '/collection/widget/class-webcomiccollectionslist.php';
	require __DIR__ . '/collection/widget/class-webcomicmedia.php';
	require __DIR__ . '/collection/widget/class-webcomicslist.php';

	Collection\common();
	Collection\customize();
	Collection\filters();
	Collection\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/collection/custom.php';
		require __DIR__ . '/collection/generator.php';
		require __DIR__ . '/collection/matcher.php';
		require __DIR__ . '/collection/media.php';
		require __DIR__ . '/collection/page.php';
		require __DIR__ . '/collection/record.php';
		require __DIR__ . '/collection/search.php';
		require __DIR__ . '/collection/settings.php';
		require __DIR__ . '/collection/metabox/media.php';
		require __DIR__ . '/collection/metabox/page.php';
		require __DIR__ . '/collection/settings/delete.php';
		require __DIR__ . '/collection/settings/general.php';
		require __DIR__ . '/collection/settings/theme.php';

		Collection\custom();
		Collection\generator();
		Collection\matcher();
		Collection\media();
		Collection\page();
		Collection\record();
		Collection\search();
		Collection\settings();
		Collection\MetaBox\media();
		Collection\MetaBox\page();
		Collection\Settings\delete();
		Collection\Settings\general();
		Collection\Settings\theme();
	}
}
