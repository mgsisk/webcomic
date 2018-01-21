<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\PreviousWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Previous comic link widget implementation.
 *
 * @name Previous Webcomic Link
 * @summary Display a link to the previous comic.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Will be used as alternate
 * text if an image is selected.
 * @option Collection: The collection to limit navigation to. The (current
 * collection) can't always be determined.
 * @option Related by: The collection or taxonomy the linked comics must be
 * related by. The (current collection) can't always be determined.
 */
class PreviousWebcomicLink extends WebcomicLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'previous';
		$this->description    = __( 'Display a link to the previous comic.', 'webcomic' );
		$this->relation_label = __( 'Previous', 'webcomic' );

		parent::__construct();
	}
}
