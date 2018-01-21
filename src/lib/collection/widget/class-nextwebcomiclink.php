<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\NextWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Next comic link widget implementation.
 *
 * @name Next Webcomic Link
 * @summary Display a link to the next comic.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Will be used as alternate
 * text if an image is selected.
 * @option Collection: The collection to limit navigation to. The (current
 * collection) can't always be determined.
 * @option Related by: The collection or taxonomy the linked comics must be
 * related by. The (current collection) can't always be determined.
 */
class NextWebcomicLink extends WebcomicLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'next';
		$this->description    = __( 'Display a link to the next comic.', 'webcomic' );
		$this->relation_label = __( 'Next', 'webcomic' );

		parent::__construct();
	}
}
