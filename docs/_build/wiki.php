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

$footer = '[![Support forum][img-1]][url-1]
[![Discord server][img-2]][url-2]
[![Contact Mike][img-3]][url-3]
[![Support development][img-4]][url-4]

[img-1]: https://img.shields.io/badge/support-forum-blue.svg
[img-2]: https://img.shields.io/discord/361857773874446339.svg
[img-3]: https://img.shields.io/badge/contact-mike-red.svg
[img-4]: https://img.shields.io/badge/donate-paypal-yellow.svg
[url-1]: https://wordpress.org/support/plugin/webcomic
[url-2]: https://discord.gg/TNTfzzg
[url-3]: mailto:help@mgsisk.com?subject=Webcomic%20Help
[url-4]: https://mgsisk.com/#support
';

// @codingStandardsIgnoreLine
file_put_contents( $path . '/_wiki/_Footer.md', $footer );
