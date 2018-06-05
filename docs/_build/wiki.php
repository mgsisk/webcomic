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
	file_put_contents( str_replace( [ '_api', '_pages' ], '_wiki', $file ), str_replace( '\{\{', '{{', $content ) );
}

$counter = 0;
$sidebar = '';
// @codingStandardsIgnoreLine
$navigation = json_decode( file_get_contents( $path . '/_data/navigation.json' ), true );

foreach ( $navigation as $label => $link ) {
	if ( is_array( $link ) ) {
		$sidebar .= "\n### {$label}\n\n";

		foreach ( $link as $text => $url ) {
			$sidebar .= "- [{$text}]($url)\n";
		}

		continue;
	}

	$counter++;
	$sidebar .= "{$counter}. [{$label}]({$link})\n";
}

// @codingStandardsIgnoreLine
file_put_contents( $path . '/_wiki/_Sidebar.md', $sidebar );

$footer = '[![Stable release 5.0.6][img-plugin]][url-plugin]
[![More information][img-info]][url-info]
[![Support development][img-support]][url-support]

[img-plugin]: https://img.shields.io/wordpress/plugin/v/webcomic.svg
[img-info]: https://img.shields.io/badge/more-information-green.svg
[img-support]: https://img.shields.io/badge/support-development-lightgrey.svg

[url-plugin]: https://wordpress.org/plugins/webcomic
[url-info]: https://github.com/mgsisk/webcomic/blob/master/support.md
[url-support]: https://mgsisk.com/#support
';

// @codingStandardsIgnoreLine
file_put_contents( $path . '/_wiki/_Footer.md', $footer );
