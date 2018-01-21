<?php
/**
 * Transcribe component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

use WP_Post;

/**
 * Initialize the transcribe component.
 *
 * @return void
 */
function transcribe() {
	require __DIR__ . '/transcribe/common.php';
	require __DIR__ . '/transcribe/customize.php';
	require __DIR__ . '/transcribe/filters.php';
	require __DIR__ . '/transcribe/globals.php';
	require __DIR__ . '/transcribe/shortcodes.php';
	require __DIR__ . '/transcribe/walker/class-authorlister.php';
	require __DIR__ . '/transcribe/walker/class-termlister.php';
	require __DIR__ . '/transcribe/walker/class-transcriptlister.php';
	require __DIR__ . '/transcribe/widget/class-webcomictranscriptform.php';
	require __DIR__ . '/transcribe/widget/class-webcomictranscriptslink.php';
	require __DIR__ . '/transcribe/widget/class-webcomictranscriptslist.php';

	Transcribe\common();
	Transcribe\customize();
	Transcribe\filters();
	Transcribe\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/transcribe/collection.php';
		require __DIR__ . '/transcribe/custom.php';
		require __DIR__ . '/transcribe/language.php';
		require __DIR__ . '/transcribe/settings.php';
		require __DIR__ . '/transcribe/metabox/authors.php';
		require __DIR__ . '/transcribe/metabox/comic.php';
		require __DIR__ . '/transcribe/metabox/transcripts.php';

		Transcribe\collection();
		Transcribe\custom();
		Transcribe\language();
		Transcribe\settings();
		Transcribe\MetaBox\authors();
		Transcribe\MetaBox\comic();
		Transcribe\MetaBox\transcripts();
	}
}
