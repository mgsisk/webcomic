<?php
/**
 * Utility functions for API generation
 *
 * @package Webcomic
 */

/**
 * Get API documentation file data
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_data( int $key, array $tokens ) : array {
	if ( 'add_shortcode' === $tokens[ $key ][1] ) {
		$comment = get_api_shortcode_comment( $key, $tokens );
		$preview = get_api_shortcode_preview( $comment, $key, $tokens );

		return get_api_shortcode_filedata( $comment, $preview );
	} elseif ( 'do_action' === $tokens[ $key ][1] && isset( $tokens[ $key - 2 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 2 ][0] ) {
		$comment = get_api_action_comment( $key, $tokens );
		$preview = get_api_action_preview( $comment );

		return get_api_action_filedata( $comment, $preview );
	} elseif ( 'apply_filters' === $tokens[ $key ][1] && isset( $tokens[ $key - 6 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 6 ][0] ) {
		$comment = get_api_filter_comment( $key, $tokens );
		$preview = get_api_filter_preview( $comment );

		return get_api_filter_filedata( $comment, $preview );
	} elseif ( 'extends' === $tokens[ $key ][1] && preg_match( '/^(WP_Widget|Webcomic)/', $tokens[ $key + 2 ][1] ) && false === strpos( $tokens[ $key - 2 ][1], 'Term' ) ) {
		$comment = get_api_widget_comment( $key, $tokens );
		$preview = get_api_widget_preview( $comment );

		return get_api_widget_filedata( $comment, $preview );
	} elseif ( 'function' === $tokens[ $key ][1] && isset( $tokens[ $key - 2 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 2 ][0] && false === strpos( $tokens[ $key + 2 ][1], 'util_' ) && false === strpos( $tokens[ $key + 2 ][1], 'sort_' ) ) {
		$comment = get_api_function_comment( $key, $tokens );
		$preview = get_api_function_preview( $comment, $key, $tokens );

		return get_api_function_filedata( $comment, $preview );
	}

	return [ '', '' ];
}

/**
 * Get a shortcode comment.
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_shortcode_comment( int $key, array $tokens ) : array {
	$offset    = 10;
	$shortcode = str_replace( [ "'", '\\' ], '', $tokens[ $key + $offset ][1] );

	while ( isset( $tokens[ $key + $offset ] ) ) {
		if ( ! is_array( $tokens[ $key + $offset ] ) || T_DOC_COMMENT !== $tokens[ $key + $offset ][0] || $shortcode !== $tokens[ $key + $offset + 4 ][1] ) {
			$offset++;

			continue;
		}

		break;
	}

	$output         = parse_comment( $tokens[ $key + $offset ][1] );
	$output['id']   = str_replace( "'", '', $tokens[ $key + 3 ][1] );
	$output['file'] = str_replace( "'", '', $tokens[ $key + 3 ][1] ) . '__';

	return $output;
}

/**
 * Get a shortcode preview.
 *
 * @param array $comment The docblock comment.
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return string
 */
function get_api_shortcode_preview( array $comment, int $key, array $tokens ) : string {
	$output = '[' . $comment['id'] . ']';

	if ( 0 === strpos( $comment['tags']['param']['$content']['description'], 'Content ' ) ) {
		$content = $comment['tags']['param']['$content']['description'];

		if ( preg_match( '/\s+is\s+(for\s+)a\s+/', $content ) && preg_match( '/_(first|previous|next|last|random)_/', $comment['id'], $match ) ) {
			$content = preg_replace( '/\s+is\s+(for\s+)?a(\s+)/', ' is $1the ' . $match[1] . '$2', $content );
		}

		$output .= $content . '[/' . $comment['id'] . ']';
	}

	return "```php\n{$output}\n```";
}

/**
 * Get shortcode file data.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return array
 */
function get_api_shortcode_filedata( array $comment, string $preview ) : array {
	$output = get_filedata_basics( $comment, $preview );

	if ( isset( $comment['tags']['param']['$content'] ) && preg_match( '/ \$args\[\'(.+?)\'\]/', $comment['tags']['param']['$content']['description'], $match ) ) {
		$output .= "\n\nShortcode content overrides the `{$match[1]}` attribute.";
	}

	$output .= get_filedata_examples( $comment );

	if ( isset( $comment['tags']['type'] ) ) {
		$output .= "\n\n## Attributes";

		foreach ( $comment['tags']['param'] as $param ) {
			if ( isset( $comment['tags']['type'][ $param['id'] ] ) ) {
				foreach ( $comment['tags']['type'][ $param['id'] ] as $arg ) {
					$output .= "\n\n### `{$arg['type']}` " . str_replace( '$', '', $arg['id'] ) . "\n{$arg['description']}";
				}
			}
		}
	}

	$output .= get_filedata_refs( $comment );

	return [ $comment['file'], "{$output}\n" ];
}

/**
 * Get an action comment.
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_action_comment( int $key, array $tokens ) : array {
	$output         = parse_comment( $tokens[ $key - 2 ][1] );
	$output['id']   = str_replace( "'", '', $tokens[ $key + 3 ][1] );
	$output['file'] = str_replace( "'", '', $tokens[ $key + 3 ][1] );

	return $output;
}

/**
 * Get an action preview.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_api_action_preview( array $comment ) : string {
	$output = "do_action( '{$comment['id']}'";

	if ( isset( $comment['tags']['param'] ) ) {
		foreach ( $comment['tags']['param'] as $param ) {
			$output .= ", {$param['type']} {$param['id']}";
		}
	}

	return "```php\n{$output} )\n```";
}

/**
 * Get action file data.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return array
 */
function get_api_action_filedata( array $comment, string $preview ) : array {
	$output  = get_filedata_basics( $comment, $preview );
	$output .= get_filedata_examples( $comment );
	$output .= get_filedata_params( $comment );
	$output .= get_filedata_return( $comment );
	$output .= get_filedata_refs( $comment );

	return [ $comment['file'], "{$output}\n" ];
}

/**
 * Get a filter comment.
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_filter_comment( int $key, array $tokens ) : array {
	$output         = parse_comment( $tokens[ $key - 6 ][1] );
	$output['id']   = str_replace( "'", '', $tokens[ $key + 3 ][1] );
	$output['file'] = str_replace( "'", '', $tokens[ $key + 3 ][1] );

	return $output;
}

/**
 * Get a filter preview.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_api_filter_preview( array $comment ) : string {
	$output = "apply_filters( '{$comment['id']}'";

	if ( isset( $comment['tags']['param'] ) ) {
		foreach ( $comment['tags']['param'] as $param ) {
			$output .= ", {$param['type']} {$param['id']}";
		}
	}

	return "```php\n{$output} )\n```";
}

/**
 * Get filter file data.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return array
 */
function get_api_filter_filedata( array $comment, string $preview ) : array {
	$output  = get_filedata_basics( $comment, $preview );
	$output .= get_filedata_examples( $comment );
	$output .= get_filedata_params( $comment );
	$output .= get_filedata_return( $comment );
	$output .= get_filedata_refs( $comment );

	return [ $comment['file'], "{$output}\n" ];
}

/**
 * Get a widget comment.
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_widget_comment( int $key, array $tokens ) : array {
	$output            = parse_comment( $tokens[ $key - 6 ][1] );
	$output['id']      = $output['tags']['name'];
	$output['file']    = str_replace( ' ', '-', $output['tags']['name'] );
	$output['summary'] = $output['tags']['summary'];

	return $output;
}

/**
 * Get a widget preview.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_api_widget_preview( array $comment ) : string {
	return "[![The {$comment['id']} widget.](srv/{$comment['file']}.png)](srv/{$comment['file']}.png)";
}

/**
 * Get widget file data.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return array
 */
function get_api_widget_filedata( array $comment, string $preview ) : array {
	$output  = get_filedata_basics( $comment, $preview );
	$output .= get_filedata_examples( $comment );

	if ( isset( $comment['tags']['option'] ) ) {
		$output .= "\n\n## Options";

		foreach ( $comment['tags']['option'] as $param ) {
			$output .= "\n\n### {$param['id']}\n{$param['description']}";
		}
	}

	$output .= get_filedata_refs( $comment );

	return [ $comment['file'], "{$output}\n" ];
}

/**
 * Get a function comment.
 *
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return array
 */
function get_api_function_comment( int $key, array $tokens ) : array {
	$output         = parse_comment( $tokens[ $key - 2 ][1] );
	$output['id']   = str_replace( "'", '', $tokens[ $key + 2 ][1] );
	$output['file'] = str_replace( "'", '', $tokens[ $key + 2 ][1] ) . '()';

	return $output;
}

/**
 * Get a function preview.
 *
 * @param array $comment The docblock comment.
 * @param int   $key The current token key.
 * @param array $tokens File tokens.
 * @return string
 */
function get_api_function_preview( array $comment, int $key, array $tokens ) : string {
	$output = '';
	$build  = 2;

	while ( '{' !== $tokens[ $key + $build ] ) {
		$part = $tokens[ $key + $build ];

		if ( is_array( $part ) ) {
			$part = $part[1];

			if ( 0 === strpos( $part, '$' ) && ! is_array( $tokens[ $key + $build - 2 ] ) ) {
				$output .= $comment['tags']['param'][ $part ]['type'] . ' ';
			}
		}

		$output .= $part;
		$build++;
	}

	$output = rtrim( $output );

	if ( ! preg_match( '/\) : \S+$/', $output ) ) {
		$output .= ' : ' . $comment['tags']['return']['type'];
	}

	return "```php\n{$output}\n```";
}

/**
 * Get function file data.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return array
 */
function get_api_function_filedata( array $comment, string $preview ) : array {
	$output  = get_filedata_basics( $comment, $preview );
	$output .= get_filedata_examples( $comment );
	$output .= get_filedata_params( $comment );
	$output .= get_filedata_return( $comment );
	$output .= get_filedata_refs( $comment );

	return [ $comment['file'], "{$output}\n" ];
}

/**
 * Get basic file data content.
 *
 * @param array  $comment The docblock comment.
 * @param string $preview The API preview.
 * @return string
 */
function get_filedata_basics( array $comment, string $preview ) : string {
	$title   = preg_replace( '/^(.+)__$/', '\[$1\]', str_replace( '-', ' ', $comment['file'] ) );
	$output  = "---\n";
	$output .= "title: {$title}\n";
	$output .= "permalink: {$comment['file']}\n";
	$output .= "---\n\n";
	$output .= "> {$comment['summary']}\n\n";
	$output .= $preview;
	$output .= rtrim( "\n\n{$comment['description']}" );

	return $output;
}

/**
 * Get API examples.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_filedata_examples( array $comment ) : string {
	if ( ! isset( $comment['tags']['example'] ) ) {
		return '';
	}

	$example = current( $comment['tags']['example'] );

	return "\n\n[Examples ⇝]({$example['ref']})";
}

/**
 * Get API parameters.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_filedata_params( array $comment ) : string {
	if ( ! isset( $comment['tags']['param'] ) ) {
		return '';
	}

	$output = "\n\n## Parameters";

	foreach ( $comment['tags']['param'] as $param ) {
		$output .= "\n\n### `{$param['type']}` {$param['id']}\n{$param['description']}";

		if ( isset( $comment['tags']['type'][ $param['id'] ] ) ) {
			$output .= "\n";

			foreach ( $comment['tags']['type'][ $param['id'] ] as $arg ) {
				$output .= "\n- **`{$arg['type']}` " . str_replace( '$', '', $arg['id'] ) . "**  \n{$arg['description']}";
			}
		}
	}

	return $output;
}

/**
 * Get API return information.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_filedata_return( array $comment ) : string {
	if ( ! isset( $comment['tags']['return'] ) ) {
		return '';
	}

	return rtrim( "\n\n## Return\n\n`{$comment['tags']['return']['type']}` {$comment['tags']['return']['description']}" );
}

/**
 * Get API references.
 *
 * @param array $comment The docblock comment.
 * @return string
 */
function get_filedata_refs( array $comment ) : string {
	if ( ! isset( $comment['tags']['uses'] ) && ! isset( $comment['tags']['see'] ) ) {
		return '';
	}

	$output = "\n\n## Uses";

	if ( isset( $comment['tags']['uses'] ) ) {
		foreach ( $comment['tags']['uses'] as $uses ) {
			$text = $uses['ref'];
			$link = $uses['ref'];

			if ( strpos( $link, '[' ) === 0 ) {
				$link = str_replace( [ '[', ']' ], '', $link ) . '__';
			}

			$output .= rtrim( "\n- [{$text}]({$link})  \n{$uses['description']}" );
		}
	}

	if ( isset( $comment['tags']['see'] ) ) {
		foreach ( $comment['tags']['see'] as $see ) {
			$parts    = explode( '/', rtrim( $see['ref'], '/' ) );
			$function = end( $parts ) . '()';
			$output  .= rtrim( "\n- [{$function}]({$see['ref']})  \n{$see['description']}" );
		}
	}

	return $output;
}

/**
 * Parse a docblock comment.
 *
 * @param string $comment [description].
 * @return array
 */
function parse_comment( string $comment ) : array {
	preg_match( '/^([\S\s]+?(?=\n\n|(?<=\.)\n|$))((?!\n@)[\S\s]+?(?=\n@|$))?(\n@[\S\s]+)?$/', trim( preg_replace( '/^(\/\*|\s+)\* *(\/$)?/m', '', $comment ) ), $parts );

	$parts += [ '', '', '', '' ];

	return [
		'description' => trim( $parts[2] ),
		'summary'     => trim( $parts[1] ),
		'tags'        => parse_tags( trim( $parts[3] ) ),
	];
}

/**
 * Parse docblock tags.
 *
 * @param string $tags [description].
 * @return array
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - We're purposely using a lot of conditionals here.
 */
function parse_tags( string $tags ) : array {
	preg_match_all( '/@\S+[\S\s]+?(?=$|@)/', $tags, $pieces );

	$param  = '';
	$output = [];

	foreach ( $pieces[0] as $tag ) {
		preg_match( '/^@(\S+)/', $tag, $type );

		$type  = strtolower( $type[1] );
		$parts = [];

		if ( 0 === strpos( $type, 'suppress' ) || 'codingstandardsignorestart' === $type ) {
			continue;
		} elseif ( in_array( $type, [ 'example', 'see', 'uses' ], true ) ) {
			$parts                            = parse_see( $tag );
			$output[ $type ][ $parts['ref'] ] = $parts;
		} elseif ( 'name' === $type ) {
			$parts           = explode( ' ', $tag, 2 );
			$output[ $type ] = trim( $parts[1] );
		} elseif ( 'option' === $type ) {
			$parts                           = parse_option( $tag );
			$output[ $type ][ $parts['id'] ] = $parts;
		} elseif ( 'param' === $type ) {
			$parts                     = parse_param( $tag );
			$param                     = $parts['id'];
			$output[ $type ][ $param ] = $parts;
		} elseif ( 'return' === $type ) {
			$parts           = parse_return( $tag );
			$output[ $type ] = $parts;
		} elseif ( 'summary' === $type ) {
			$parts           = explode( ' ', $tag, 2 );
			$output[ $type ] = trim( $parts[1] );
		} elseif ( 'type' === $type ) {
			$parts                                     = parse_param( $tag );
			$output[ $type ][ $param ][ $parts['id'] ] = $parts;
		}
	}

	return $output;
}// @codingStandardsIgnoreStart

/**
 * Parse an `example`, `see`, or `uses` docblock tag.
 *
 * @param string $tag The tag to parse.
 * @return array
 */
function parse_see( string $tag ) : array {
	preg_match( '/^@\S+\s+(\S+)(?:\s+([\S\s]+))?/', $tag, $match );

	$match += [ '', '', '' ];

	return [
		'ref'         => $match[1],
		'description' => trim( $match[2] ),
	];
}

/**
 * Parse a `param` or `type` docblock tag.
 *
 * @param string $tag The tag to parse.
 * @return array
 */
function parse_param( string $tag ) : array {
	preg_match( '/^@\S+\s+(\S+)\s+(\$\S+)(?:\s+([\S\s]+))?/', $tag, $match );

	$match += [ '', '', '', '' ];

	return [
		'id'          => $match[2],
		'type'        => $match[1],
		'description' => preg_replace( '/^{\s+|\s+}$/', '', trim( $match[3] ) ),
	];
}

/**
 * Parse a `return` docblock tag.
 *
 * @param string $tag The tag to parse.
 * @return array
 */
function parse_return( string $tag ) : array {
	preg_match( '/^@\S+\s+(\S+)(?:\s+([\S\s]+))?/', $tag, $match );

	$match += [ '', '', '' ];

	return [
		'type'        => $match[1],
		'description' => trim( $match[2] ),
	];
}

/**
 * Parse an `option` docblock tag ( for widgets ).
 *
 * @param string $tag The tag to parse.
 * @return array
 */
function parse_option( string $tag ) : array {
	preg_match( '/^@\S+\s+(.+):(?:\s+([\S\s]+))?/', $tag, $match );

	$match += [ '', '', '' ];

	return [
		'id'          => $match[1],
		'description' => preg_replace( '/^{\s+|\s+}$/', '', trim( $match[2] ) ),
	];
}
