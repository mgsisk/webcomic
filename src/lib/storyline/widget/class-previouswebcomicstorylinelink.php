<?php
/**
 * Mgsisk\Webcomic\Storyline\Widget\PreviousWebcomicStorylineLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic storyline link widget implementation.
 */
class PreviousWebcomicStorylineLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'previous';
		$this->description    = __( 'Display a link to the previous comic storyline.', 'webcomic' );
		$this->relation_label = __( 'Previous', 'webcomic' );
		$this->taxonomy_type  = 'storyline';
		$this->taxonomy_label = __( 'Storyline', 'webcomic' );

		parent::__construct();
	}
}