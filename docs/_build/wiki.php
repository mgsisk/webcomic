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
	if ( preg_match( '/^.+?\/!.[^\/]+\.md/', $file ) ) {
		continue;
	}

  // @codingStandardsIgnoreLine
	$content = preg_replace( '/^---[\S\s]+---\s+/', '', file_get_contents( $file ) );

	// @codingStandardsIgnoreLine
	file_put_contents( str_replace( [ '_api', '_pages' ], '_wiki', $file ), $content );
}

$counter = 0;
$sidebar = '';
// @codingStandardsIgnoreLine
$sections = json_decode( file_get_contents( $path . '/_data/sections.json' ) );

foreach ( $sections as $label => $section ) {
	if ( '_' === $label ) {
		foreach ( $section as $subsection ) {
			$counter++;
			$sidebar .= "{$counter}. [{$subsection}](" . str_replace( ' ', '-', $subsection ) . ")\n";
		}

		continue;
	}

	$sidebar .= "\n### {$label}\n\n";

	foreach ( $section as $subsection ) {
		$sidebar .= "- [{$subsection}](" . str_replace( [ ' ', 'Character', 'Storyline' ], [ '-', 'Taxonomies', 'Taxonomies' ], $subsection ) . ")\n";
	}
}

// @codingStandardsIgnoreLine
file_put_contents( $path . '/_wiki/_Sidebar.md', $sidebar );

$footer = '[![Stable tag 5.0.6][img-stable]][url-stable]
[![Requires PHP 7.0+][img-php]][url-php]
[![Requires WordPress 4.7+][img-requires]][url-requires]
[![Tested up to WordPress 4.9.5][img-tested]][url-tested]
[![Licensed under GPL-2.0+][img-license]][url-license]
[![Support development][img-donate]][url-donate]

[img-stable]: https://img.shields.io/wordpress/plugin/v/webcomic.svg
[img-requires]: https://img.shields.io/badge/wordpress-4.7+-blue.svg
[img-tested]: https://img.shields.io/wordpress/v/webcomic.svg
[img-php]: https://img.shields.io/badge/PHP-7.0+-8893bd.svg
[img-license]: https://img.shields.io/github/license/mgsisk/webcomic.svg
[img-donate]: https://img.shields.io/badge/donate-paypal-yellow.svg
[url-stable]: https://wordpress.org/plugins/webcomic
[url-requires]: https://wordpress.org/news/2016/12/vaughan
[url-tested]: https://wordpress.org/download
[url-php]: https://php.net
[url-license]: https://github.com/mgsisk/webcomic/blob/master/license.md
[url-donate]: https://mgsisk.com/#support
';

// @codingStandardsIgnoreLine
file_put_contents( $path . '/_wiki/_Footer.md', $footer );
