<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\FirstWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * First comic link widget implementation.
 */
class FirstWebcomicLink extends WebcomicLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'first';
		$this->description    = __( 'Display a link to the first comic.', 'webcomic' );
		$this->relation_label = __( 'First', 'webcomic' );

		parent::__construct();
	}
}
