<?php
/**
 * Generate wiki documentation
 *
 * @package Webcomic
 */

$path = dirname( __DIR__ );

if ( ! is_readable( $path . '/_api' ) || ! is_readable( $path . '/_pages' ) ) {
	return;
} elseif ( ! is_dir( $path . '/_wiki' ) ) {
	mkdir( $path . '/_wiki' );
}

if ( ! is_writable( $path . '/_wiki' ) ) {
	return;
}

foreach ( glob( $path . '/_wiki/*' ) as $file ) {
	unlink( $file );
}

$files = array_merge( glob( $path . '/_api/*.md' ), glob( $path . '/_pages/*.md' ) );

foreach ( $files as $file ) {
  // @codingStandardsIgnoreLine
	$content = preg_replace( '/^---[\S\s]+---\s+/', '', file_get_contents( $file ) );

	// @codingStandardsIgnoreLine
	file_put_contents( str_replace( [ '_api', '_pages' ], '_wiki', $file ), $content );
}
