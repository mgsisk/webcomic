<?php
/**
 * Mgsisk\Webcomic\Storyline\Widget\FirstWebcomicStorylineLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic storyline link widget implementation.
 */
class FirstWebcomicStorylineLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'first';
		$this->description    = __( 'Display a link to the first comic storyline.', 'webcomic' );
		$this->relation_label = __( 'First', 'webcomic' );
		$this->taxonomy_type  = 'storyline';
		$this->taxonomy_label = __( 'Storyline', 'webcomic' );

		parent::__construct();
	}
}
