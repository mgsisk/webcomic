<?php
/**
 * Mgsisk\Webcomic\Storyline\Widget\WebcomicStorylineLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic storyline link widget implementation.
 *
 * @name Random Webcomic Storyline Link
 * @summary Display a link to a random comic storyline.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Collection: The collection to limit navigation to. The (current
 * collection) can't always be determined.
 */
class RandomWebcomicStorylineLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'random';
		$this->description    = __( 'Display a link to a random comic storyline.', 'webcomic' );
		$this->relation_label = __( 'Random', 'webcomic' );
		$this->taxonomy_type  = 'storyline';
		$this->taxonomy_label = __( 'Storyline', 'webcomic' );

		parent::__construct();
	}
}
