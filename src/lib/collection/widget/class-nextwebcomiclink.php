<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\NextWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Next comic link widget implementation.
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
