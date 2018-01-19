<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\RandomWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Random comic link widget implementation.
 */
class RandomWebcomicLink extends WebcomicLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'random';
		$this->description    = __( 'Display a link to a random comic.', 'webcomic' );
		$this->relation_label = __( 'Random', 'webcomic' );

		parent::__construct();
	}
}
