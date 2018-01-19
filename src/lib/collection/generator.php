<?php
/**
 * Comic generator functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add comic generator hooks.
 *
 * @return void
 */
function generator() {
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_generator_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_generator_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_generator_field_collection' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_generator_field_start_date' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_generator_field_publish_very' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_generator_field_draft' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_generate_comics' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_generator_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_generator_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_generator_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_generator_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_generator_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_generator_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_generator_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_generator_tool_help' );
	add_filter( 'tool_box', __NAMESPACE__ . '\hook_add_generator_tool' );
	add_filter( 'wp_ajax_webcomic_generator_preview', __NAMESPACE__ . '\hook_get_generator_preview' );
	add_filter( 'wp_ajax_webcomic_generator_start_date', __NAMESPACE__ . '\hook_get_generator_start_day' );
}

/**
 * Add the comic generator page.
 *
 * @return void
 */
function hook_add_generator_page() {
	add_management_page(
		__( 'Webcomic Generator', 'webcomic' ),
		__( 'Webcomic Generator', 'webcomic' ),
		'manage_options',
		'webcomic_generator',
		function() {
			$args = [
				'file'  => __DIR__ . '/generator-inc-page.php',
				'media' => webcomic( 'GLOBALS._REQUEST.webcomic_generate' ),
			];

			if ( ! is_array( $args['media'] ) ) {
				$args['media'] = [];
			}

			foreach ( $args['media'] as $key => $media ) {
				$args['media'][ $key ] = abs( (int) $media );
			}

			require $args['file'];
		}
	);
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_generator_settings_section() {
	add_settings_section(
		'webcomic_generator',
		'',
		function() {
			echo '<div>';

			wp_nonce_field( __NAMESPACE__ . 'GeneratorNonce', __NAMESPACE__ . 'GeneratorNonce' );

			echo '</div>';
		},
		'webcomic_generator'
	);
}

/**
 * Add the generator collection field.
 *
 * @return void
 */
function hook_add_generator_field_collection() {
	add_settings_field(
		'webcomic_generator_collection',
		__( 'Collection', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_generator',
		'webcomic_generator', [
			'file'      => __DIR__ . '/generator-inc-field-collection.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_generator.collection' ),
			'label_for' => 'webcomic_generator[collection]',
		]
	);
}

/**
 * Add the generator start date field.
 *
 * @return void
 */
function hook_add_generator_field_start_date() {
	add_settings_field(
		'webcomic_generator_start_date',
		__( 'Start on&hellip;', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_generator',
		'webcomic_generator', [
			'file'      => __DIR__ . '/generator-inc-field-start-date.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_generator.start_date' ),
			'label_for' => 'webcomic_generator[start_date]',
		]
	);
}

/**
 * Add the generator publish every field.
 *
 * @return void
 */
function hook_add_generator_field_publish_very() {
	$option = webcomic( 'GLOBALS._REQUEST.webcomic_generator.publish_every' );

	if ( ! is_array( $option ) ) {
		$option = [];
	}

	foreach ( $option as $key => $value ) {
		$option[ $key ] = (int) $value;
	}

	add_settings_field(
		'webcomic_generator_publish_every',
		__( 'Publish every&hellip;', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_generator',
		'webcomic_generator', [
			'file'      => __DIR__ . '/generator-inc-field-publish-every.php',
			'option'    => $option,
			'label_for' => 'webcomic_generator[publish_every]',
		]
	);
}

/**
 * Add the generator draft field.
 *
 * @return void
 */
function hook_add_generator_field_draft() {
	add_settings_field(
		'webcomic_generator_draft',
		'',
		function( $args ) {
			require $args['file'];
		},
		'webcomic_generator',
		'webcomic_generator', [
			'file'      => __DIR__ . '/generator-inc-field-draft.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_generator.draft' ),
			'label_for' => 'webcomic_generator[draft]',
		]
	);
}

/**
 * Generate comics.
 *
 * @return void
 */
function hook_generate_comics() {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_generator.generate' ) ) {
		return;
	}

	check_admin_referer( __NAMESPACE__ . 'GeneratorNonce', __NAMESPACE__ . 'GeneratorNonce' );

	$collection    = webcomic( 'GLOBALS._REQUEST.webcomic_generator.collection' );
	$media         = webcomic( 'GLOBALS._REQUEST.webcomic_generate' );
	$now           = strtotime( webcomic( 'GLOBALS._REQUEST.webcomic_generator.start_date' ) );
	$publish_every = array_filter( webcomic( 'GLOBALS._REQUEST.webcomic_generator.publish_every' ) );
	$status        = 'publish';
	$warning       = util_validate_generator_data( $collection, $now, $media, $publish_every );

	if ( $warning ) {
		webcomic_notice( $warning, 'warning' );

		return;
	} elseif ( webcomic( 'GLOBALS._REQUEST.webcomic_generator.draft' ) ) {
		$status = 'draft';
	}

	list( $count, $error ) = util_generate_comics( $collection, $now, $media, $publish_every, $status );

	if ( $error ) {
		// Translators: The number of comics not generated.
		webcomic_notice( sprintf( _n( '%s comic not generated.', '%s comics not generated.', $error, 'webcomic' ), number_format_i18n( $error ) ), 'error' );
	}

	// Translators: The number of comics generated.
	webcomic_notice( sprintf( _n( '%s comic generated.', '%s comics generated.', $count, 'webcomic' ), number_format_i18n( $count ) ) );
}

/**
 * Register generator stylesheets.
 *
 * @return void
 */
function hook_register_generator_styles() {
	wp_register_style(
		__NAMESPACE__ . 'GeneratorCSS',
		plugins_url( 'srv/collection/generator.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register generator javascript.
 *
 * @return void
 */
function hook_register_generator_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'GeneratorJS',
		plugins_url( 'srv/collection/generator.js', webcomic( 'file' ) ),
		[ 'jquery-ui-sortable' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Localize generator javascript.
 *
 * @return void
 */
function hook_localize_generator_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'GeneratorJS',
		'webcomicGeneratorL10n',
		[
			'publish' => __( 'Publishing on&hellip;', 'webcomic' ),
		]
	);
}

/**
 * Enqueue generator stylesheets.
 *
 * @return void
 */
function hook_enqueue_generator_styles() {
	if ( 'tools_page_webcomic_generator' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'GeneratorCSS' );
}

/**
 * Enqueue generator javascript.
 *
 * @return void
 */
function hook_enqueue_generator_scripts() {
	if ( 'tools_page_webcomic_generator' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'GeneratorJS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_generator_help_sidebar() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_generator' !== $screen->id ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the comic generator help.
 *
 * @return void
 */
function hook_add_generator_help() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_generator' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/generator-inc-help.php';
			},
		]
	);
}

/**
 * Add the comic generator tool box help.
 *
 * @return void
 */
function hook_add_generator_tool_help() {
	$screen = get_current_screen();

	if ( 'tools' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_generator',
			'title'    => __( 'Webcomic Generator', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/generator-inc-tool-help.php';
			},
		]
	);
}

/**
 * Add the comic generator tool box.
 *
 * @return void
 */
function hook_add_generator_tool() {
	require __DIR__ . '/generator-inc-tool.php';
}

/**
 * Handle generator preview requests.
 *
 * @return void
 */
function hook_get_generator_preview() {
	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_generate' ) ) {
		wp_die();
	}

	$now           = strtotime( webcomic( 'GLOBALS._REQUEST.webcomic_generator.start_date' ) );
	$media         = webcomic( 'GLOBALS._REQUEST.webcomic_generate' );
	$weekdays      = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];
	$publish_every = array_filter( webcomic( 'GLOBALS._REQUEST.webcomic_generator.publish_every' ) );

	if ( ! $now ) {
		wp_die();
	} elseif ( ! $media ) {
		wp_die();
	} elseif ( ! $publish_every && 1 < count( $media ) ) {
		wp_die();
	}

	while ( current( $publish_every ) && current( $publish_every ) < intval( date( 'N', $now ) ) ) {
		next( $publish_every );
	}

	$output = [
		[
			'id'   => $media[0],
			'date' => date( get_option( 'date_format' ), $now ),
		],
	];

	unset( $media[0] );

	foreach ( $media as $image ) {
		if ( ! next( $publish_every ) ) {
			reset( $publish_every );
		}

		$now = strtotime( "next {$weekdays[current( $publish_every ) - 1]}", $now );

		$output[] = [
			'id'   => $image,
			'date' => date( get_option( 'date_format' ), $now ),
		];
	}

	wp_send_json( $output );
}

/**
 * Handle start day requests.
 *
 * @return void
 */
function hook_get_generator_start_day() {
	if ( null === webcomic( 'GLOBALS._REQUEST.date' ) ) {
		wp_die();
	}

	$days = [
		__( 'Monday', 'webcomic' ),
		__( 'Tuesday', 'webcomic' ),
		__( 'Wednesday', 'webcomic' ),
		__( 'Thursday', 'webcomic' ),
		__( 'Friday', 'webcomic' ),
		__( 'Saturday', 'webcomic' ),
		__( 'Sunday', 'webcomic' ),
	];

	$output = [ __( 'Start on&hellip;', 'webcomic' ) ];
	$time   = strtotime( webcomic( 'GLOBALS._REQUEST.date' ) );
	$day    = date( 'N', $time );

	if ( $time && isset( $days[ $day - 1 ] ) ) {
		$output = [
			// Translators: A day of the week, Monday - Sunday.
			sprintf( __( 'Start on a %s', 'webcomic' ), $days[ $day - 1 ] ),
		];
	}

	wp_send_json( $output );
}

/* ===== Utility Functions ================================================== */

/**
 * Generate comics for the specified media.
 *
 * @param string $collection The collection to generate comics in.
 * @param mixed  $now The starting timestamp.
 * @param array  $media The media to generate comics for.
 * @param array  $publish_every The days to publish generated comics.
 * @param string $status The status to use for generated comics.
 * @return array
 * @internal For hook_generate_comics().
 */
function util_generate_comics( string $collection, $now, array $media, array $publish_every, string $status ) : array {
	while ( current( $publish_every ) && current( $publish_every ) < intval( date( 'N', $now ) ) ) {
		next( $publish_every );
	}

	$count    = 0;
	$error    = 0;
	$weekdays = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];

	foreach ( $media as $index => $image ) {
		if ( $index ) {
			if ( ! next( $publish_every ) ) {
				reset( $publish_every );
			}

			$now = strtotime( "next {$weekdays[current( $publish_every ) - 1]}", $now );
		}

		$post = wp_insert_post(
			[
				'post_type'     => $collection,
				'post_date'     => date( 'Y-m-d H:i:s', $now ),
				'post_title'    => get_the_title( $image ),
				'post_status'   => $status,
				'post_content'  => '&nbsp;',
				'post_date_gmt' => get_gmt_from_date( date( 'Y-m-d H:i:s', $now ) ),
			]
		);

		if ( ! $post || ! add_post_meta( $post, 'webcomic_media', $image ) ) {
			$error++;

			continue;
		}

		$count++;
	}

	return [ $count, $error ];
}

/**
 * Validate submitted comic generator data.
 *
 * @param string $collection The collection to generate comics in.
 * @param mixed  $now The starting timestamp.
 * @param array  $media The media to generate comics for.
 * @param array  $publish_every The days to publish generated comics.
 * @return string
 * @internal For hook_generate_comics().
 * @suppress PhanPluginAlwaysReturnFunction - Incorrectly triggered.
 */
function util_validate_generator_data( string $collection, $now, array $media, array $publish_every ) : string {
	if ( ! webcomic_collection_exists( $collection ) ) {
		return __( 'Invalid collection, please try again.', 'webcomic' );
	} elseif ( ! $now ) {
		return __( 'The start date could not be understood, please try again.', 'webcomic' );
	} elseif ( ! $media ) {
		return __( 'Please select at least one media item to generate a comic for.', 'webcomic' );
	} elseif ( ! $publish_every && 1 < count( $media ) ) {
		return __( 'Please select at least one day to publish on.', 'webcomic' );
	}

	return '';
}
