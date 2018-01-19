<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\PreviousWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Previous comic link widget implementation.
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
