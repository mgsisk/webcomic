<?php
/**
 * Generate API documentation
 *
 * @package Webcomic
 */

$path = dirname( dirname( __DIR__ ) ) . '/src/';
$cwd  = dirname( __DIR__ );

require_once 'utils.php';

if ( ! is_dir( $cwd . '/_api' ) ) {
	mkdir( $cwd . '/_api' );
}

foreach ( glob( $cwd . '/_api/*' ) as $file ) {
	unlink( $file );
}

$directory = new RecursiveDirectoryIterator( $path );
$iterator  = new RecursiveIteratorIterator( $directory );
$files     = new RegexIterator( $iterator, '/^.+\.php$/', RecursiveRegexIterator::GET_MATCH );

foreach ( $files as $file ) {
	if ( false !== strpos( $file[0], '/compat/' ) ) {
		continue;
	}

  // @codingStandardsIgnoreLine
	$tokens = token_get_all( file_get_contents( $file[0] ) );

	foreach ( $tokens as $key => $token ) {
		$type = '';

		if ( ! is_array( $token ) || ! in_array( $token[1], [ 'do_action', 'apply_filters', 'add_shortcode', 'function', 'extends' ], true ) ) {
			continue;
		} elseif ( 'function' === $token[1] && ! preg_match( '/\/globals\.php$/', $file[0] ) ) {
			continue;
		} elseif ( 'extends' === $token[1] && ! preg_match( '/\/widget\/class-(?:(?!-inc).)+\.php$/', $file[0] ) ) {
			continue;
		}

		list( $filepath, $filedata ) = get_api_data( $key, $tokens );

		if ( ! $filepath || ! $filedata ) {
			continue;
		}

		preg_match( '/\/lib\/(.+?)(\/|$)/', dirname( $file[0] ), $match );

		// @codingStandardsIgnoreLine
		file_put_contents( $cwd . '/_api/' . $filepath . '.md', $filedata );
		// @codingStandardsIgnoreLine
		file_put_contents( $cwd . '/_api/!' . $token[1] . '.md', '- ' . $match[1] . ' ' . $filepath . "\n", FILE_APPEND );
	}
}
