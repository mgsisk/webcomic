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
 *
 * @name Webcomic Storylines List
 * @summary Display a list of comic storylines.
 * @option Title: Optional widget title.
 * @option Number of terms to show: Optional number of storylines to show; 0
 * shows all storylines.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens).
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Related to: The collection or comic the storylines must be related
 * to. The (current collection) can't always be determined.
 * @option Comic link: Optional comic link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Some formats never list
 * comics.
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
