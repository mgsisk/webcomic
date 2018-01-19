<?php
/**
 * Mgsisk\Webcomic\Transcribe\Walker\TranscriptLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Walker;

use Walker;
use WP_Post;

/**
 * Standard walker for get_webcomic_transcripts_list().
 */
class TranscriptLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_transcript';

	/**
	 * Database fields.
	 *
	 * @var array
	 */
	public $db_fields = [
		'id'     => 'ID',
		'parent' => 'post_parent',
	];

	/**
	 * Start element output.
	 *
	 * @param string  $output The current output.
	 * @param WP_Post $transcript The current comic transcript.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @param int     $id The current comic transcript ID.
	 * @return void
	 */
	public function start_el( &$output, $transcript, $depth = 0, $args = [], $id = 0 ) {
		preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['item'], $match );

		$match += [ '', '', $args['item'], '' ];

		/**
		 * Alter the list item tokens.
		 *
		 * This filter allows hooks to alter the replaceable list item text tokens
		 * and their values.
		 *
		 * ## Core tokens
		 *
		 * |Token        |Value                         |Example         |
		 * |-------------|------------------------------|----------------|
		 * |%authors     |The transcript's authors.     |Angie, Mike     |
		 * |%content     |The transcript's content.     |                |
		 * |%date        |The transcript's publish date.|May 1, 2099     |
		 * |%edit-link   |The transcript's edit link.   |[Edit this](/)  |
		 * |%languages   |The transcript's languages.   |English, Spanish|
		 * |%parent-link |The transcript comic's link.  |[Page 1](/)     |
		 * |%parent-title|The transcript comic's title. |Page 1          |
		 * |%time        |The transcript's publish time.|4:52 pm         |
		 *
		 * @param array   $tokens The token values.
		 * @param string  $link The list item text to search for tokens.
		 * @param WP_Post $collection The transcript the list item is for.
		 * @param array   $args The transcript list arguments.
		 */
		$tokens   = apply_filters( 'get_webcomic_transcripts_list_item_tokens', [], $match[2], $transcript, $args );
		$item     = str_replace( array_keys( $tokens ), $tokens, $match[2] );
		$match[1] = sprintf( $match[1], $id, implode( ' ', array_map( 'esc_attr', get_post_class( '', $id ) ) ) );
		$output   = $match[1] . $item . $match[3];
	}
}
