<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\LastWebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

/**
 * Last comic link widget implementation.
 */
class LastWebcomicLink extends WebcomicLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'last';
		$this->description    = __( 'Display a link to the last comic.', 'webcomic' );
		$this->relation_label = __( 'Last', 'webcomic' );

		parent::__construct();
	}
}
