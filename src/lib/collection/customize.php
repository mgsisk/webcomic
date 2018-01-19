<?php
/**
 * Customize API functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

use WP_Customize_Manager;
use WP_Customize_Setting;
use WP_Query;

/**
 * Add customize hooks.
 *
 * @return void
 */
function customize() {
	add_filter( 'webcomic_integrate_infinite_args', __NAMESPACE__ . '\hook_webcomic_integrate_infinite_args' );
	add_filter( 'webcomic_integrate_landing_page_args', __NAMESPACE__ . '\hook_webcomic_integrate_landing_page_args' );
	add_filter( 'webcomic_integrate_media', __NAMESPACE__ . '\hook_webcomic_integrate_media' );
	add_filter( 'webcomic_integrate_navigation', __NAMESPACE__ . '\hook_webcomic_integrate_navigation' );
	add_filter( 'webcomic_integrate_meta', __NAMESPACE__ . '\hook_webcomic_integrate_meta' );
	add_filter( 'webcomic_integrate_media_and_navigation', __NAMESPACE__ . '\hook_webcomic_integrate_media_and_navigation' );
	add_filter( 'webcomic_integrate_infinite', __NAMESPACE__ . '\hook_webcomic_integrate_infinite' );
	add_filter( 'webcomic_integrate_landing_page', __NAMESPACE__ . '\hook_webcomic_integrate_landing_page' );
	add_filter( 'webcomic_integrate_landing_page_content', __NAMESPACE__ . '\hook_webcomic_integrate_landing_page_content' );
	add_filter( 'webcomic_integrate_landing_page_comments', __NAMESPACE__ . '\hook_webcomic_integrate_landing_page_comments' );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_customize_scripts' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_sidebars_media', 99 );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_sidebars_navigation', 99 );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_sidebars_meta', 99 );
	add_filter( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_customize_scripts' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_section' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_integrate' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_navigation_gestures' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_navigation_keyboard' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_navigation_above' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_navigation_below' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_front_page_order' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_front_page_collection' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_front_page_content' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_front_page_meta' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_front_page_comments' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_archive_preview' );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_add_customize_field_archive_preview_content' );
	add_filter( 'wp_get_custom_css', __NAMESPACE__ . '\hook_integrate_custom_css', 10, 2 );
	add_filter( 'body_class', __NAMESPACE__ . '\hook_integrate_body_classes' );
	add_filter( 'the_excerpt', __NAMESPACE__ . '\hook_integrate_archive_preview' );
	add_filter( 'the_content', __NAMESPACE__ . '\hook_integrate_archive_preview' );
	add_filter( 'the_content', __NAMESPACE__ . '\hook_integrate_universal_singular_meta' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_comiceasel_collection_archive_media' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_comiceasel_taxonomy_archive_media' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_front_page' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_landing_page' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_singular_media_and_navigation' );
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_comiceasel_landing_page' );
	add_filter( 'loop_end', __NAMESPACE__ . '\hook_integrate_universal_comiceasel_infinite' );
	add_filter( 'comic-area', __NAMESPACE__ . '\hook_integrate_comiceasel_comic_area_singular_media' );
	add_filter( 'comic-area', __NAMESPACE__ . '\hook_integrate_comiceasel_comic_area_front_page_media' );
	add_filter( 'comic-area', __NAMESPACE__ . '\hook_integrate_comiceasel_comic_area_landing_page_media' );
	add_filter( 'comic-blog-area', __NAMESPACE__ . '\hook_integrate_comiceasel_comic_blog_area_front_page' );
	add_filter( 'comic-post-extras', __NAMESPACE__ . '\hook_integrate_comiceasel_comic_post_extras' );
}

/**
 * Handle webcomic_integrate_infinite action default arguments.
 *
 * @param array $args The infinite arguments.
 * @return array
 */
function hook_webcomic_integrate_infinite_args( array $args ) : array {
	$defaults = [
		'author'         => 0,
		'date_query'     => [],
		'order'          => 'desc',
		'post_type'      => array_values( array_filter( [ get_webcomic_collection(), 'any' ] ) )[0],
		'posts_per_page' => 1,
		'tax_query'      => [],
	];

	if ( is_page() ) {
		$defaults['order'] = (string) get_post_meta( get_the_ID(), 'webcomic_integrate_infinite_order', true );
	} elseif ( is_date() ) {
		$query = [
			'year' => (int) get_the_date( 'Y' ),
		];

		if ( is_month() || is_day() ) {
			$query['month'] = (int) get_the_date( 'n' );
		}

		if ( is_day() ) {
			$query['day'] = (int) get_the_date( 'j' );
		}

		$defaults['date_query'][] = $query;
	} elseif ( is_tax() ) {
		$object                  = get_queried_object();
		$defaults['tax_query'][] = [
			'taxonomy' => $object->taxonomy,
			'field'    => 'term_id',
			'terms'    => $object->term_id,
		];
	} elseif ( is_author() ) {
		$defaults['author'] = (int) get_queried_object()->ID;
	}

	return $args + $defaults;
}

/**
 * Handle webcomic_integrate_landing_page action default arguments.
 *
 * @param array $args The landing page arguments.
 * @return array
 */
function hook_webcomic_integrate_landing_page_args( array $args ) : array {
	$defaults = [
		'author'            => 0,
		'date_query'        => [],
		'fields'            => 'ids',
		'order'             => 'desc',
		'post_type'         => array_values( array_filter( [ get_webcomic_collection(), 'any' ] ) )[0],
		'posts_per_page'    => 1,
		'tax_query'         => [],
		'webcomic_comments' => false,
		'webcomic_content'  => false,
		'webcomic_media'    => true,
		'webcomic_meta'     => false,
	];

	if ( is_front_page() ) {
		$defaults['order']             = get_theme_mod( 'webcomic_integrate_front_page_order', 'desc' );
		$defaults['post_type']         = get_theme_mod( 'webcomic_integrate_front_page_collection', 'any' );
		$defaults['webcomic_comments'] = get_theme_mod( 'webcomic_integrate_front_page_comments' );
		$defaults['webcomic_content']  = get_theme_mod( 'webcomic_integrate_front_page_content' );
		$defaults['webcomic_meta']     = get_theme_mod( 'webcomic_integrate_front_page_meta' );
	} elseif ( is_page() ) {
		$defaults['order']             = (string) get_post_meta( get_the_ID(), 'webcomic_integrate_landing_page_order', true );
		$defaults['webcomic_comments'] = (bool) get_post_meta( get_the_ID(), 'webcomic_integrate_landing_page_comments', true );
		$defaults['webcomic_content']  = (bool) get_post_meta( get_the_ID(), 'webcomic_integrate_landing_page_content', true );
		$defaults['webcomic_meta']     = (bool) get_post_meta( get_the_ID(), 'webcomic_integrate_landing_page_meta', true );
	} elseif ( is_date() ) {
		$query = [
			'year' => (int) get_the_date( 'Y' ),
		];

		if ( is_month() || is_day() ) {
			$query['month'] = (int) get_the_date( 'n' );
		}

		if ( is_day() ) {
			$query['day'] = (int) get_the_date( 'j' );
		}

		$defaults['date_query'][] = $query;
	} elseif ( is_tax() ) {
		$object                  = get_queried_object();
		$defaults['tax_query'][] = [
			'taxonomy' => $object->taxonomy,
			'field'    => 'term_id',
			'terms'    => $object->term_id,
		];
	} elseif ( is_author() ) {
		$defaults['author'] = (int) get_queried_object()->ID;
	}

	return $args + $defaults;
}

/**
 * Integrate comic media.
 *
 * @return void
 */
function hook_webcomic_integrate_media() {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || ! is_a_webcomic() ) {
		return;
	}

	require __DIR__ . '/customize-inc-integrate-media.php';
}

/**
 * Integrate comic navigation.
 *
 * @return void
 */
function hook_webcomic_integrate_navigation() {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || ! is_a_webcomic() ) {
		return;
	}

	require __DIR__ . '/customize-inc-integrate-navigation.php';
}

/**
 * Integrate comic meta.
 *
 * @return void
 */
function hook_webcomic_integrate_meta() {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || ! is_a_webcomic() ) {
		return;
	}

	require __DIR__ . '/customize-inc-integrate-meta.php';
}

/**
 * Integrate comic media with navigation.
 *
 * @return void
 */
function hook_webcomic_integrate_media_and_navigation() {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || ! is_a_webcomic() ) {
		return;
	} elseif ( get_theme_mod( 'webcomic_integrate_navigation_above' ) ) {
		/**
		 * Integrate comic navigation.
		 *
		 * This action provides a way for hooks to add comic navigation links. It
		 * will display the Webcomic Navigation widget area when using integration.
		 * You can use this to selectively display comic navigation, but it's often
		 * better to use the combined `webcomic_integrate_media_and_navigation`
		 * action.
		 */
		do_action( 'webcomic_integrate_navigation' );
	}

	/**
	 * Integrate comic media.
	 *
	 * This action provides a way for hooks to add comic media. It will display
	 * the Webcomic Media widget area when using integration. You can use this to
	 * selectively display comic media, but it's often better to use the combined
	 * `webcomic_integrate_media_and_navigation` action.
	 */
	do_action( 'webcomic_integrate_media' );

	if ( get_theme_mod( 'webcomic_integrate_navigation_below', true ) ) {
		/* This action is documented above in Mgsisk\Webcomic\Collection\hook_webcomic_integrate_media_and_navigation() */
		do_action( 'webcomic_integrate_navigation' );
	}
}

/**
 * Integrate an infinite comic container.
 *
 * @param array $args Optional arguments.
 * @return void
 */
function hook_webcomic_integrate_infinite( $args = [] ) {
	if ( ! get_theme_mod( 'webcomic_integrate' ) ) {
		return;
	}

	/**
	 * Alter the infinite container arguments.
	 *
	 * This filter allows hooks to alter the infinite container arguments before
	 * they're converted for the data-webcomic-infinite attribute and ultimately
	 * passed to `get_webcomics()`. The fields argument is always set to 'ids'.
	 *
	 * @param array $args Optional arguments.
	 */
	$args = apply_filters( 'webcomic_integrate_infinite_args', (array) $args );

	if ( ! $args['order'] ) {
		return;
	}

	echo '<div class="webcomic-infinite" data-webcomic-infinite="' . esc_attr( build_query( $args ) ) . '"></div>';
}

/**
 * Integrate comics into a landing page.
 *
 * @param array $args {
 *     Optional arguments.
 *
 *     @type bool $webcomic_comments Wether to display comic comments.
 *     @type bool $webcomic_content Wether to display comic content.
 *     @type bool $webcomic_media Wether to display comic media and navigation.
 *     @type bool $webcomic_meta Wether to display comic meta data.
 * }
 * @return void
 */
function hook_webcomic_integrate_landing_page( $args = [] ) {
	if ( ! get_theme_mod( 'webcomic_integrate' ) ) {
		return;
	}

	/**
	 * Alter the landing page arguments.
	 *
	 * This filter allows hooks to alter the landing page arguments before they're
	 * passed to `get_webcomics()` to fetch comics and fire actions for the
	 * landing page. The fields argument is always set to `ids` and the
	 * posts_per_page argument is always set to `1`.
	 *
	 * @param array $args {
	 *     Optional arguments.
	 *
	 *     @type bool $webcomic_comments Wether to display comic comments.
	 *     @type bool $webcomic_content Wether to display comic content.
	 *     @type bool $webcomic_media Wether to display comic media and navigation.
	 *     @type bool $webcomic_meta Wether to display comic meta data.
	 * }
	 */
	$args                   = apply_filters( 'webcomic_integrate_landing_page_args', (array) $args );
	$args['fields']         = 'ids';
	$args['posts_per_page'] = 1;

	if ( ! $args['order'] ) {
		return;
	}

	$comics = get_webcomics( $args );

	if ( ! $comics ) {
		return;
	}

	webcomic_setup_postdata( current( $comics ) );

	if ( $args['webcomic_media'] ) {
		/**
		 * Integrate comic media and navigation.
		 *
		 * This action provides a way for hooks to add comic media and navigation.
		 * It will display comic media and navigation based on your Customizer
		 * settings when using integration. You'll want to add this action to your
		 * theme's `single.php` template if you're using integration actions
		 * manually.
		 */
		do_action( 'webcomic_integrate_media_and_navigation' );
	}

	/**
	 * Integrate comic content.
	 *
	 * This action provides a way for hooks to add comic post content on landing
	 * pages. It will display the post content as appropriate based on the
	 * specified $args.
	 *
	 * @param array $args {
	 *     Optional arguments.
	 *
	 *     @type bool $webcomic_content Wether to display comic content.
	 * }
	 */
	do_action( 'webcomic_integrate_landing_page_content', $args );

	if ( $args['webcomic_meta'] ) {
		/**
		 * Integrate comic meta data.
		 *
		 * This action provides a way for hooks to add comic meta data. It will
		 * display the Webcomic Meta widget area when using integration.
		 */
		do_action( 'webcomic_integrate_meta' );
	}

	/**
	 * Integrate comic comments.
	 *
	 * This action provides a way for hooks to add comic comments on landing
	 * pages. It will display comments as appropriate based on the specified
	 * $args.
	 *
	 * @param array $args {
	 *     Optional arguments.
	 *
	 *     @type bool $webcomic_comments Wether to display comic content.
	 * }
	 */
	do_action( 'webcomic_integrate_landing_page_comments', $args );

	webcomic_reset_postdata();
}

/**
 * Integrate comic content on landing pages.
 *
 * @param array $args Landing page arguments.
 * @return void
 */
function hook_webcomic_integrate_landing_page_content( array $args ) {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || empty( $args['webcomic_content'] ) || ! is_a_webcomic() ) {
		return;
	}

	$collection = get_post_type();
	$templates  = [
		"template-paprts/{$collection}/content.php",
		"template-parts/post/content-{$collection}.php",
		"content-{$collection}.php",
		'template-parts/post/content.php',
		'content.php',
	];

	if ( 'comiceasel' === get_theme_mod( 'webcomic_integrate' ) ) {
		array_splice( $templates, 3, 0, 'content-comic.php' );
	}

	$template = locate_template( $templates );

	if ( ! $template ) {
		$template = __DIR__ . '/customize-inc-integrate-landing-page-content.php';
	}

	require $template;
}

/**
 * Integrate comic comments on landing pages.
 *
 * @param array $args Landing page arguments.
 * @return void
 * @SuppressWarnings(PHPMD.Superglobals) - WordPress requires a specific global variable to force comment display.
 */
function hook_webcomic_integrate_landing_page_comments( array $args ) {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || empty( $args['webcomic_comments'] ) || ! is_a_webcomic() ) {
		return;
	}

	$withcomments_backup = (bool) webcomic( 'GLOBALS.witthcomments' );
	// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited - We're purposely changing the $withcomments global.
	$GLOBALS['withcomments'] = true;

	comments_template( '', true );

	// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited - We're purposely changing the $withcomments global.
	$GLOBALS['withcomments'] = $withcomments_backup;
}

/**
 * Register customize scripts.
 *
 * @return void
 */
function hook_register_customize_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'CustomizeJS',
		plugins_url( 'srv/collection/customize.js', webcomic( 'file' ) ),
		[ 'customize-controls' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Register media integration sidebars.
 *
 * @return void
 */
function hook_register_sidebars_media() {
	$args = [
		'id'            => 'webcomic-integrate-media',
		'name'          => __( 'Webcomic Media', 'webcomic' ),
		'description'   => __( 'Add widgets here to change how integrated comic media looks.', 'webcomic' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	];

	if ( current( webcomic( 'GLOBALS.wp_registered_sidebars' ) ) ) {
		$args += current( webcomic( 'GLOBALS.wp_registered_sidebars' ) );
	}

	register_sidebar( $args );

	// Translators: Custom post type name.
	$args['name'] = __( '%s Media', 'webcomic' );
	// Translators: Custom post type name.
	$args['description'] = __( 'Add widgets here to change how integrated %s comic media looks.', 'webcomic' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'media', webcomic( "option.{$collection}.sidebars" ), true ) ) {
			continue;
		}

		$sidebar_args                = $args;
		$sidebar_args['id']          = "{$collection}-integrate-media";
		$sidebar_args['name']        = sprintf( $args['name'], webcomic( "option.{$collection}.name" ) );
		$sidebar_args['description'] = sprintf( $args['description'], webcomic( "option.{$collection}.name" ) );

		register_sidebar( $sidebar_args );
	}
}

/**
 * Register navigation integration sidebars.
 *
 * @return void
 */
function hook_register_sidebars_navigation() {
	$args = [
		'id'            => 'webcomic-integrate-navigation',
		'name'          => __( 'Webcomic Navigation', 'webcomic' ),
		'description'   => __( 'Add widgets here to change how integrated comic navigation looks.', 'webcomic' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	];

	if ( current( webcomic( 'GLOBALS.wp_registered_sidebars' ) ) ) {
		$args += current( webcomic( 'GLOBALS.wp_registered_sidebars' ) );
	}

	register_sidebar( $args );

	// Translators: Custom post type name.
	$args['name'] = __( '%s Navigation', 'webcomic' );
	// Translators: Custom post type name.
	$args['description'] = __( 'Add widgets here to change how integrated %s comic navigation looks.', 'webcomic' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'navigation', webcomic( "option.{$collection}.sidebars" ), true ) ) {
			continue;
		}

		$sidebar_args                = $args;
		$sidebar_args['id']          = "{$collection}-integrate-navigation";
		$sidebar_args['name']        = sprintf( $args['name'], webcomic( "option.{$collection}.name" ) );
		$sidebar_args['description'] = sprintf( $args['description'], webcomic( "option.{$collection}.name" ) );

		register_sidebar( $sidebar_args );
	}
}

/**
 * Register meta integration sidebars.
 *
 * @return void
 */
function hook_register_sidebars_meta() {
	$args = [
		'id'            => 'webcomic-integrate-meta',
		'name'          => __( 'Webcomic Meta', 'webcomic' ),
		'description'   => __( 'Add widgets here to change how integrated comic meta data looks.', 'webcomic' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	];

	if ( current( webcomic( 'GLOBALS.wp_registered_sidebars' ) ) ) {
		$args += current( webcomic( 'GLOBALS.wp_registered_sidebars' ) );
	}

	register_sidebar( $args );

	// Translators: Custom post type name.
	$args['name'] = __( '%s Meta', 'webcomic' );
	// Translators: Custom post type name.
	$args['description'] = __( 'Add widgets here to change how integrated %s comic meta data looks.', 'webcomic' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'meta', webcomic( "option.{$collection}.sidebars" ), true ) ) {
			continue;
		}

		$sidebar_args                = $args;
		$sidebar_args['id']          = "{$collection}-integrate-meta";
		$sidebar_args['name']        = sprintf( $args['name'], webcomic( "option.{$collection}.name" ) );
		$sidebar_args['description'] = sprintf( $args['description'], webcomic( "option.{$collection}.name" ) );

		register_sidebar( $sidebar_args );
	}
}

/**
 * Enqueue customize scripts.
 *
 * @return void
 */
function hook_enqueue_customize_scripts() {
	wp_enqueue_script( __NAMESPACE__ . 'CustomizeJS' );
}

/**
 * Add the Customize section.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_section( WP_Customize_Manager $customize ) {
	$customize->add_section(
		'webcomic', [
			'title'       => __( 'Webcomic', 'webcomic' ),
			'priority'    => 999999999,
			'description' => '<a href="https://github.com/mgsisk/webcomic/wiki" target="_blank" class="button"><span class="dashicons dashicons-book-alt"></span> ' . __( 'User Guide', 'webcomic' ) . '</a>',
		]
	);
}

/**
 * Add the integrate Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_integrate( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate', [
			'default'           => '',
			'sanitize_callback' => __NAMESPACE__ . '\hook_sanitize_customize_field_integrate',
		]
	);

	$customize->add_control(
		'webcomic_integrate', [
			'label'   => __( 'Integration Method', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'select',
			'choices' => [
				''           => __( 'None', 'webcomic' ),
				'universal'  => __( 'Universal', 'webcomic' ),
				'webcomic'   => __( 'Webcomic', 'webcomic' ),
				'comiceasel' => __( 'Comic Easel', 'webcomic' ),
			],
		]
	);
}

/**
 * Add the navigation_gestures Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_navigation_gestures( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_navigation_gestures', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_navigation_gestures', [
			'label'   => __( 'Touch gestures', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the navigation_keyboard Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_navigation_keyboard( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_navigation_keyboard', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_navigation_keyboard', [
			'label'   => __( 'Keyboard shortcuts', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the navigation_above Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_navigation_above( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_navigation_above', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_navigation_above', [
			'label'   => __( 'Navigation above comic', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the navigation_below Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_navigation_below( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_navigation_below', [
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_navigation_below', [
			'label'   => __( 'Navigation below comic', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the front_page_order Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_front_page_order( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_front_page_order', [
			'default'           => 'desc',
			'sanitize_callback' => __NAMESPACE__ . '\hook_sanitize_customize_order',
		]
	);

	$customize->add_control(
		'webcomic_integrate_front_page_order', [
			'label'   => __( 'Front Page', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'select',
			'choices' => [
				''     => __( 'No comic', 'webcomic' ),
				'asc'  => __( 'First comic', 'webcomic' ),
				'desc' => __( 'Last comic', 'webcomic' ),
			],
		]
	);
}

/**
 * Add the front_page_collection Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_front_page_collection( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_front_page_collection', [
			'default'           => 'any',
			'sanitize_callback' => __NAMESPACE__ . '\hook_sanitize_customize_field_front_page_collection',
		]
	);

	$collections = [];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$collections[ $collection ] = get_webcomic_collection_title( $collection );
	}

	$customize->add_control(
		'webcomic_integrate_front_page_collection', [
			'section' => 'webcomic',
			'type'    => 'select',
			'choices' => [
				'any' => __( '(any collection)', 'webcomic' ),
			] + $collections,
		]
	);
}

/**
 * Add the front_page_content Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_front_page_content( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_front_page_content', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_front_page_content', [
			'label'   => __( 'Post content', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the front_page_meta Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_front_page_meta( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_front_page_meta', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_front_page_meta', [
			'label'   => __( 'Meta data', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the front_page_comments Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_front_page_comments( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_front_page_comments', [
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_front_page_comments', [
			'label'   => __( 'Comments', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Add the archive_preview Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_archive_preview( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_archive_preview', [
			'default'           => '%medium',
			'sanitize_callback' => __NAMESPACE__ . '\hook_sanitize_customize_field_archive_preview',
		]
	);

	$customize->add_control(
		'webcomic_integrate_archive_preview', [
			'label'   => __( 'Archive Preview', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'text',
		]
	);
}

/**
 * Add the archive_preview_content Customize field.
 *
 * @param WP_Customize_Manager $customize The customizer object.
 * @return void
 */
function hook_add_customize_field_archive_preview_content( WP_Customize_Manager $customize ) {
	$customize->add_setting(
		'webcomic_integrate_archive_preview_content', [
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);

	$customize->add_control(
		'webcomic_integrate_archive_preview_content', [
			'label'   => __( 'Show post content with preview', 'webcomic' ),
			'section' => 'webcomic',
			'type'    => 'checkbox',
		]
	);
}

/**
 * Sanitize the customize integrate field.
 *
 * @param mixed                $value The value to sanitize.
 * @param WP_Customize_Setting $setting The setting being sanitized.
 * @return string
 */
function hook_sanitize_customize_field_integrate( $value, WP_Customize_Setting $setting ) : string {
	$value = (string) $value;

	if ( ! in_array( $value, [ '', 'universal', 'webcomic', 'comiceasel' ], true ) ) {
		return $setting->default;
	}

	return $value;
}

/**
 * Sanitize the customize font_page_collection field.
 *
 * @param mixed                $value The value to sanitize.
 * @param WP_Customize_Setting $setting The setting being sanitized.
 * @return string
 */
function hook_sanitize_customize_field_front_page_collection( $value, WP_Customize_Setting $setting ) : string {
	$value = (string) $value;

	if ( ! webcomic_collection_exists( $value ) ) {
		return $setting->default;
	}

	return $value;
}

/**
 * Sanitize order customize fields.
 *
 * @param mixed                $value The value to sanitize.
 * @param WP_Customize_Setting $setting The setting being sanitized.
 * @return string
 */
function hook_sanitize_customize_order( $value, WP_Customize_Setting $setting ) : string {
	$value = (string) $value;

	if ( ! in_array( $value, [ '', 'desc', 'asc' ], true ) ) {
		return $setting->default;
	}

	return $value;
}

/**
 * Sanitize the customize archive_preview field.
 *
 * @param mixed                $value The value to sanitize.
 * @param WP_Customize_Setting $setting The setting being sanitized.
 * @return string
 */
function hook_sanitize_customize_field_archive_preview( $value, WP_Customize_Setting $setting ) : string {
	return wp_kses_post( (string) $value );
}

/**
 * Add integration styles to custom CSS output.
 *
 * @param string $css The current theme's custom CSS.
 * @param string $stylesheet The current theme's stylesheet.
 * @return string
 */
function hook_integrate_custom_css( string $css, string $stylesheet ) : string {
	if ( ! get_theme_mod( 'webcomic_integrate' ) ) {
		return $css;
	}

	$integrate  = '.webcomic-media{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.webcomic-media img{height:auto;max-width:100%}';
	$integrate .= '.webcomic-navigation{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;text-align:center}.webcomic-navigation a{display:block;font-size:200%}.webcomic-navigation img{height:auto;max-width:100%}.webcomic-navigation .widget{-webkit-box-flex:1;-ms-flex:1 0 auto;flex:1 0 auto;list-style:none}.webcomic-navigation .current-webcomic{visibility:hidden}';
	$integrate .= '.webcomic-meta img{height:auto;max-width:100%}.webcomic-meta span:after{content:", "}.webcomic-meta span:last-child:after{content:""}.webcomic-meta .widget{list-style:none;margin:0 0 15px}';
	$integrate .= '.webcomic-infinite{clear:both}.webcomic-infinite img{height:auto;max-width:100%}';

	return $integrate . $css;
}

/**
 * Add CSS classes to the body element.
 *
 * @param array $classes The current list of CSS classes.
 * @return array
 */
function hook_integrate_body_classes( array $classes ) : array {
	if ( ! get_theme_mod( 'webcomic_integrate' ) ) {
		return $classes;
	}

	$classes[] = 'webcomic-integrate';
	$classes[] = 'webcomic-integrate-' . get_theme_mod( 'webcomic_integrate' );

	return $classes;
}

/**
 * Add comic previews on archive pages for all integrations.
 *
 * @param string $content The current post content.
 * @return string
 */
function hook_integrate_archive_preview( string $content ) : string {
	if ( ! get_theme_mod( 'webcomic_integrate' ) || ! get_theme_mod( 'webcomic_integrate_archive_preview' ) || is_admin() || ! is_main_query() || ! in_the_loop() || ! ( is_archive() || is_search() ) || ! is_a_webcomic() ) {
		return $content;
	}

	$preview = get_webcomic_link( get_theme_mod( 'webcomic_integrate_archive_preview', '%medium' ) );

	if ( ! get_theme_mod( 'webcomic_integrate_archive_preview_content', true ) ) {
		return $preview;
	}

	return $preview . $content;
}

/**
 * Add comic meta data on singular pages for universal integrations.
 *
 * @param string $content The current post content.
 * @return string
 */
function hook_integrate_universal_singular_meta( string $content ) : string {
	if ( 'universal' !== get_theme_mod( 'webcomic_integrate' ) || is_admin() || ! is_main_query() || ! in_the_loop() || ! is_singular( webcomic( 'option.collections' ) ) || ! is_a_webcomic() ) {
		return $content;
	}

	ob_start();

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_webcomic_integrate_landing_page() */
	do_action( 'webcomic_integrate_meta' );

	$meta = ob_get_clean();

	return $content . $meta;
}

/**
 * Add collection media on archives for universal and comiceasel integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_comiceasel_collection_archive_media( WP_Query $query ) : WP_Query {
	if ( ! in_array( get_theme_mod( 'webcomic_integrate' ), [ 'universal', 'comiceasel' ], true ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( webcomic( 'option.collections' ) ) || ! get_webcomic_collection_media() ) {
		return $query;
	}

	echo '<div class="webcomic-collection-media">';

	webcomic_collection_media();

	echo '</div>';

	return $query;
}

/**
 * Add term media on archives for universal and comiceasel integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_comiceasel_taxonomy_archive_media( WP_Query $query ) : WP_Query {
	if ( ! in_array( get_theme_mod( 'webcomic_integrate' ), [ 'universal', 'comiceasel' ], true ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( webcomic( 'option.collections' ) ) || ! get_webcomic_collection_media() ) {
		return $query;
	}

	echo '<div class="webcomic-term-media">';

	webcomic_term_media();

	echo '</div>';

	return $query;
}

/**
 * Add comics to the front page for universal integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_front_page( WP_Query $query ) : WP_Query {
	if ( 'universal' !== get_theme_mod( 'webcomic_integrate' ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_front_page() || $query->is_paged() ) {
		return $query;
	}

	/**
	 * Integrate a comic landing page.
	 *
	 * This action provides a way for hooks to add a comic with media, navigation,
	 * post content, comments, and meta data on landing pages. It will display
	 * comic media, navigation, post content, comments, and meta data when using
	 * integration. The template the action appears on may change the default
	 * $args:
	 *
	 * - On the site front page (e.g. `index.php` or `home.php`), default values
	 *   for the order, post_type, webcomic_comments, webcomic_content, and
	 *   webcomic_meta arguments use the theme's Customizer settings.
	 * - On a standard page (e.g. `page.php`), default values for the order,
	 *   post_type, webcomic_comments, webcomic_content, and webcomic_meta
	 *   arguments use the page's comic template settings.
	 * - On date archives (e.g. `year.php`, `archive.php`), a default date_query
	 *   argument is set based on the archive date.
	 * - On taxonomy archives (e.g. `category.php`, `tags.php`), a default
	 *   tax_query argument is set based on the archive term.
	 * - On author archives (e.g. `author.php`), a default author argument is set
	 *   based on the archive author.
	 *
	 * @uses get_webcomics() The fields argument is always set to `ids` and the
	 * posts_per_page argument is always set to `1`.
	 * @param array $args {
	 *     Optional arguments.
	 *
	 *     @type bool $webcomic_comments Wether to display comic comments.
	 *     @type bool $webcomic_content Wether to display comic content.
	 *     @type bool $webcomic_media Wether to display comic media and navigation.
	 *     @type bool $webcomic_meta Wether to display comic meta data.
	 * }
	 */
	do_action( 'webcomic_integrate_landing_page' );

	return $query;
}

/**
 * Add comics to pages for universal and comiceasel integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_landing_page( WP_Query $query ) : WP_Query {
	if ( 'universal' !== get_theme_mod( 'webcomic_integrate' ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_page() || $query->is_front_page() ) {
		return $query;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_integrate_universal_front_page() */
	do_action( 'webcomic_integrate_landing_page' );

	return $query;
}

/**
 * Add comic media on singular pages for universal integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_singular_media_and_navigation( WP_Query $query ) : WP_Query {
	if ( 'universal' !== get_theme_mod( 'webcomic_integrate' ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_singular( webcomic( 'option.collections' ) ) ) {
		return $query;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_webcomic_integrate_landing_page() */
	do_action( 'webcomic_integrate_media_and_navigation' );

	return $query;
}

/**
 * Add comic content on landing pages for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_landing_page() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_page() ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_integrate_universal_front_page() */
	do_action(
		'webcomic_integrate_landing_page', [
			'webcomic_media' => false,
		]
	);

	echo '<div id="blogheader"></div>';
}

/**
 * Add infinite scrolling to pages for universal and comiceasel integrations.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_comiceasel_infinite( WP_Query $query ) : WP_Query {
	if ( ! in_array( get_theme_mod( 'webcomic_integrate' ), [ 'universal', 'comiceasel' ], true ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_page() || $query->is_front_page() ) {
		return $query;
	}

	/**
	 * Integrate infinite comic scrolling.
	 *
	 * This action provides a way for hooks to add an infinitely-scrolling comic
	 * container. It will display the Webcomic Infinite widget area for each comic
	 * in the container when using integration. The template the action appears on
	 * may change the default $args:
	 *
	 * - On a standard page (e.g. `page.php`), the default value for order use the
	 *   page's comic template settings.
	 * - On date archives (e.g. `year.php`, `archive.php`), a default date_query
	 *   argument is set based on the archive date.
	 * - On taxonomy archives (e.g. `category.php`, `tags.php`), a default
	 *   tax_query argument is set based on the archive term.
	 * - On author archives (e.g. `author.php`), a default author argument is set
	 *   based on the archive author.
	 *
	 * @uses get_webcomics() The fields argument is always set to 'ids'.
	 * @param array $args Optional arguments.
	 */
	do_action( 'webcomic_integrate_infinite' );

	return $query;
}

/**
 * Add comic media on singular pages for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_comic_area_singular_media() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_singular( webcomic( 'option.collections' ) ) ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_webcomic_integrate_landing_page() */
	do_action( 'webcomic_integrate_media_and_navigation' );
}

/**
 * Add comic media on the front page for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_comic_area_front_page_media() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_front_page() || is_paged() ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_integrate_universal_front_page() */
	do_action(
		'webcomic_integrate_landing_page', [
			'webcomic_content'  => false,
			'webcomic_meta'     => false,
			'webcomic_comments' => false,
		]
	);
}

/**
 * Add comic media on landing pages for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_comic_area_landing_page_media() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_page() ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_integrate_universal_front_page() */
	do_action(
		'webcomic_integrate_landing_page', [
			'webcomic_content'  => false,
			'webcomic_meta'     => false,
			'webcomic_comments' => false,
		]
	);
}

/**
 * Add comic content on the front page for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_comic_blog_area_front_page() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_front_page() || is_paged() ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_integrate_universal_front_page() */
	do_action(
		'webcomic_integrate_landing_page', [
			'webcomic_media' => false,
		]
	);

	echo '<div id="blogheader"></div>';
}

/**
 * Add comic meta data on singular pages for comiceasel integrations.
 *
 * @return void
 */
function hook_integrate_comiceasel_comic_post_extras() {
	if ( 'comiceasel' !== get_theme_mod( 'webcomic_integrate' ) || ! is_singular( webcomic( 'option.collections' ) ) ) {
		return;
	}

	/* This action is documented in Mgsisk\Webcomic\Collection\hook_webcomic_integrate_landing_page() */
	do_action( 'webcomic_integrate_meta' );
}
