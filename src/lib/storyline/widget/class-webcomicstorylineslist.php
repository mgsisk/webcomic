<?php
/**
 * Mgsisk\Webcomic\Storyline\Widget\WebcomicStorylinesList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermsList;

/**
 * Comic storylines list widget implementation.
 */
class WebcomicStorylinesList extends WebcomicTermsList {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->taxonomy_type           = 'storyline';
		$this->taxonomy_label          = __( 'Storylines', 'webcomic' );
		$this->taxonomy_singular_label = __( 'Storyline', 'webcomic' );

		parent::__construct();
	}
}
