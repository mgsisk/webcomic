<?php
/**
 * Generate API functionality
 *
 * @package Webcomic
 */

build_api( dirname( dirname( __DIR__ ) ) . '/src/' );

/**
 * Build the plugin API documentation.
 *
 * @param string $path Absolute path to the plugin source.
 * @return void
 */
function build_api( string $path ) {
	$cwd = dirname( __DIR__ );

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

		$tokens = token_get_all( file_get_contents( $file[0] ) );

		foreach ( $tokens as $key => $token ) {
			$comment = [];
			$block   = '';

			if ( ! is_array( $token ) ) {
				continue;
			} elseif ( 'do_action' === $token[1] && isset( $tokens[ $key - 2 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 2 ][0] ) {
				$comment = get_comment( 'action', $tokens, $key );
				$block   = get_action_filter_block( 'do_action', $comment );
			} elseif ( 'apply_filters' === $token[1] && isset( $tokens[ $key - 6 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 6 ][0] ) {
				$comment = get_comment( 'filter', $tokens, $key );
				$block   = get_action_filter_block( 'apply_filters', $comment );
			} elseif ( 'add_shortcode' === $token[1] ) {
				$comment = get_comment( 'shortcode', $tokens, $key );
				$block   = get_shortcode_block( $comment, $tokens, $key );
			} elseif ( preg_match( '/\/globals\.php$/', $file[0] ) && 'function' === $token[1] && isset( $tokens[ $key - 2 ][0] ) && T_DOC_COMMENT === $tokens[ $key - 2 ][0] && false === strpos( $tokens[ $key + 2 ][1], 'util_' ) && false === strpos( $tokens[ $key + 2 ][1], 'sort_' ) ) {
				$comment = get_comment( 'tag', $tokens, $key );
				$block   = get_template_tag_block( $comment, $tokens, $key );
			} elseif ( preg_match( '/\/widget\/class-(?:(?!-inc).)+\.php$/', $file[0] ) && 'extends' === $token[1] && preg_match( '/^(WP_Widget|Webcomic)/', $tokens[ $key + 2 ][1] ) && false === strpos( $tokens[ $key - 2 ][1], 'Term' ) ) {
				$comment = get_comment( 'widget', $tokens, $key );
				$block   = 'widget';
			}

			if ( $comment && $block ) {
				preg_match( '/\/lib\/(.+?)(\/|$)/', dirname( $file[0] ), $match );

				file_put_contents( $cwd . '/_api/' . $comment['file'] . '.md', bulid_file( $block, $comment ) );
				file_put_contents( $cwd . '/_api/!' . $token[1] . '.md', '- ' . $match[1] . ' ' . $comment['file'] . "\n", FILE_APPEND );
			}
		}
	}
}

/**
 * Build file output for API documentation.
 *
 * @param string $block The code block to document.
 * @param array  $comment The code block comment.
 * @return string
 */
function bulid_file( string $block, array $comment ) : string {
	$shortcode = false;

	if ( 0 === strpos( $block, '[' ) ) {
		$shortcode = true;
	}

  $title   = str_replace( '-', ' ', $comment['file'] );
  $title   = preg_replace( '/^(.+)__$/', '\[$1\]', $comment['file'] );
  $output  = "---\ntitle: {$title}\npermalink: {$comment['file']}\n---\n\n";
	$output .= '> ' . $comment['summary'];

	if ( 'widget' === $block ) {
		$output .= "\n\n[![The {$comment['id']} widget.](srv/{$comment['file']}.png )](srv/{$comment['file']}.png)";
	} else {
		$output .= "\n\n```php\n{$block}\n```";
		$output .= rtrim( "\n\n{$comment['description']}" );
	}

	if ( $shortcode && isset( $comment['tags']['param']['$content'] ) && preg_match( '/ \$args\[\'(.+?)\'\]/', $comment['tags']['param']['$content']['description'], $match ) ) {
		$output .= "\n\nShortcode content overrides the `{$match[1]}` attribute.";
	}

	if ( isset( $comment['tags']['example'] ) ) {
		$example = current( $comment['tags']['example'] );
		$output .= "\n\n[Examples ⇝]({$example['ref']})";
	}

	if ( isset( $comment['tags']['type'] ) && $shortcode ) {
		$output .= "\n\n## Attributes";

		foreach ( $comment['tags']['param'] as $param ) {
			if ( isset( $comment['tags']['type'][ $param['id'] ] ) ) {
				foreach ( $comment['tags']['type'][ $param['id'] ] as $arg ) {
					$output .= "\n\n### `{$arg['type']}` " . str_replace( '$', '', $arg['id'] ) . "\n{$arg['description']}";
				}
			}
		}
	} elseif ( isset( $comment['tags']['param'] ) && ! $shortcode ) {
		$output .= "\n\n## Parameters";

		foreach ( $comment['tags']['param'] as $param ) {
			$output .= "\n\n### `{$param['type']}` {$param['id']}\n{$param['description']}";

			if ( isset( $comment['tags']['type'][ $param['id'] ] ) ) {
				$output .= "\n";

				foreach ( $comment['tags']['type'][ $param['id'] ] as $arg ) {
					$output .= "\n- **`{$arg['type']}` " . str_replace( '$', '', $arg['id'] ) . "**  \n{$arg['description']}";
				}
			}
		}
	} elseif ( isset( $comment['tags']['option'] ) ) {
		$output .= "\n\n## Options";

		foreach ( $comment['tags']['option'] as $param ) {
			$output .= "\n\n### {$param['id']}\n{$param['description']}";
		}
	}

	if ( isset( $comment['tags']['return'] ) && ! $shortcode ) {
		$output .= rtrim( "\n\n## Return\n\n`{$comment['tags']['return']['type']}` {$comment['tags']['return']['description']}" );
	}

	if ( isset( $comment['tags']['uses'] ) || isset( $comment['tags']['see'] ) ) {
		$output .= "\n\n## Uses";

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
	}

	$output .= "\n";

	return $output;
}

/**
 * Get an action or filter code block.
 *
 * @param string $prefix The block prefix; one of do_action or apply_filters.
 * @param array  $comment The code block comment.
 * @return string
 */
function get_action_filter_block( string $prefix, array $comment ) : string {
	$block = $prefix . "( '" . $comment['id'] . "'";

	if ( isset( $comment['tags']['param'] ) ) {
		foreach ( $comment['tags']['param'] as $param ) {
			$block .= ", {$param['type']} {$param['id']}";
		}
	}

	$block .= ' )';

	return $block;
}

/**
 * Get a shortcode tag block.
 *
 * @param array $comment The code block comment.
 * @param array $tokens The current file's tokens.
 * @param int   $key The current token key.
 * @return string
 */
function get_shortcode_block( array $comment, array $tokens, int $key ) : string {
	$block = '[' . $comment['id'] . ']';

	if ( strpos( $comment['tags']['param']['$content']['description'], 'Content ' ) === 0 ) {
		$content = $comment['tags']['param']['$content']['description'];

		if ( preg_match( '/\s+is\s+(for\s+)a\s+/', $content ) && preg_match( '/_(first|previous|next|last|random)_/', $comment['id'], $match ) ) {
			$content = preg_replace( '/\s+is\s+( for\s+ )?a( \s+ )/', ' is $1the ' . $match[1] . '$2', $content );
		}

		$block .= $content . '[/' . $comment['id'] . ']';
	}

	return $block;
}

/**
 * Get a template tag code block.
 *
 * @param array $comment The code block comment.
 * @param array $tokens The current file's tokens.
 * @param int   $key The current token key.
 * @return string
 */
function get_template_tag_block( array $comment, array $tokens, int $key ) : string {
	$block = '';
	$build = 2;

	while ( '{' !== $tokens[ $key + $build ] ) {
		$part = $tokens[ $key + $build ];

		if ( is_array( $part ) ) {
			$part = $part[1];

			if ( 0 === strpos( $part, '$' ) && ! is_array( $tokens[ $key + $build - 2 ] ) ) {
				$block .= $comment['tags']['param'][ $part ]['type'] . ' ';
			}
		}

		$block .= $part;
		$build++;
	}

	$block = rtrim( $block );

	if ( ! preg_match( '/\) : \S+$/', $block ) ) {
		$block .= ' : ' . $comment['tags']['return']['type'];
	}

	return $block;
}

/**
 * Get a docblock comment.
 *
 * @param string $type The type of comment to get; one of action, filter,
 * shortcode, or tag.
 * @param array  $tokens The current file's tokens.
 * @param int    $key The current token key.
 * @return array
 */
function get_comment( string $type, array $tokens, int $key ) : array {
	$id_offset      = 3;
	$comment_offset = -2;

	if ( 'filter' === $type ) {
		$comment_offset = -6;
	} elseif ( 'shortcode' === $type ) {
		$comment_offset = 10;
		$shortcode      = str_replace( [ "'", '\\' ], '', $tokens[ $key + $comment_offset ][1] );

		while ( isset( $tokens[ $key + $comment_offset ] ) ) {
			if ( ! is_array( $tokens[ $key + $comment_offset ] ) || T_DOC_COMMENT !== $tokens[ $key + $comment_offset ][0] || $shortcode !== $tokens[ $key + $comment_offset + 4 ][1] ) {
				$comment_offset++;

				continue;
			}

			break;
		}
	} elseif ( 'tag' === $type ) {
		$id_offset = 2;
	} elseif ( 'widget' === $type ) {
		$id_offset      = -2;
		$comment_offset = -6;
	}

	$comment = trim( preg_replace( '/^(\/\*|\s+)\* *(\/$)?/m', '', $tokens[ $key + $comment_offset ][1] ) );

	preg_match( '/^([\S\s]+?(?=\n\n|(?<=\.)\n|$))((?!\n@)[\S\s]+?(?=\n@|$))?(\n@[\S\s]+)?$/', $comment, $parts );

	$parts += [ '', '', '', '' ];
	$output = [
		'id'          => str_replace( "'", '', $tokens[ $key + $id_offset ][1] ),
		'file'        => str_replace( "'", '', $tokens[ $key + $id_offset ][1] ),
		'summary'     => trim( $parts[1] ),
		'description' => trim( $parts[2] ),
		'tags'        => trim( $parts[3] ),
	];

	if ( 'shortcode' === $type ) {
		$output['file'] .= '__';
	} elseif ( 'tag' === $type ) {
		$output['file'] .= '()';
	}

	if ( ! $output['tags'] ) {
		$output['tags'] = [];

		return $output;
	}

	preg_match_all( '/@\S+[\S\s]+?(?=$|@)/', $output['tags'], $tags );

	$param          = '';
	$output['tags'] = [];

	foreach ( $tags[0] as $tag ) {
		preg_match( '/^@(\S+)/', $tag, $type );

		$type  = strtolower( $type[1] );
		$parts = [];

		if ( 0 === strpos( strtolower( $type ), 'suppress' ) || 'codingstandardsignorestart' === $type ) {
			continue;
		} elseif ( in_array( $type, [ 'example', 'see', 'uses' ], true ) ) {
			$parts                                    = parse_see( $tag );
			$output['tags'][ $type ][ $parts['ref'] ] = $parts;
		} elseif ( 'param' === $type ) {
			$parts                                   = parse_param( $tag );
			$param                                   = $parts['id'];
			$output['tags'][ $type ][ $parts['id'] ] = $parts;
		} elseif ( 'type' === $type ) {
			$parts = parse_param( $tag );
			$output['tags'][ $type ][ $param ][ $parts['id'] ] = $parts;
		} elseif ( 'return' === $type ) {
			$parts                   = parse_return( $tag );
			$output['tags'][ $type ] = $parts;
		} elseif ( 'name' === $type ) {
			$parts          = explode( ' ', $tag, 2 );
			$output['id']   = trim( $parts[1] );
			$output['file'] = trim( str_replace( ' ', '-', $parts[1] ) );
		} elseif ( 'summary' === $type ) {
			$parts           = explode( ' ', $tag, 2 );
			$output[ $type ] = trim( $parts[1] );
		} elseif ( 'option' === $type ) {
			$parts                                   = parse_option( $tag );
			$output['tags'][ $type ][ $parts['id'] ] = $parts;
		}

		if ( ! $parts ) {
			echo 'UNKNOWN TAG – ' . $type;
			die;
		}
	}

	return $output;
}

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
