<?php
/**
 * Transcript language functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

/**
 * Add language hooks.
 *
 * @return void
 */
function language() {
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_overview_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_sidebar' );
}

/**
 * Add the overview help tab.
 *
 * @return void
 */
function hook_add_overview_help() {
	$screen = get_current_screen();

	if ( 'edit-webcomic_transcript_language' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/language-inc-help-overview.php';
			},
		]
	);
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_help_sidebar() {
	$screen = get_current_screen();

	if ( 'edit-webcomic_transcript_language' !== $screen->id ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}
