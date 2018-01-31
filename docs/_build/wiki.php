<?php
/**
 * Generate wiki documentation
 *
 * @package Webcomic
 */

build_wiki( dirname( __DIR__ ) );

/**
 * Build the plugin wiki documentation.
 *
 * @param string $path Absolute path to the plugin documentation.
 * @return void
 */
function build_wiki( string $path ) {
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
    $content = file_get_contents( $file );
    $content = preg_replace( '/^---[\S\s]+---\s+/', '', $content );

    file_put_contents( str_replace( [ '_api', '_pages' ], '_wiki', $file ), $content );
  }
}
