<?php
/**
 * Mgsisk\Webcomic\Character\Widget\WebcomicCharactersList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermsList;

/**
 * Comic characters list widget implementation.
 */
class WebcomicCharactersList extends WebcomicTermsList {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->taxonomy_type           = 'character';
		$this->taxonomy_label          = __( 'Characters', 'webcomic' );
		$this->taxonomy_singular_label = __( 'Character', 'webcomic' );

		parent::__construct();
	}
}
