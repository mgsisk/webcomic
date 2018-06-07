<?php
/**
 * Mgsisk\Webcomic\Location\Widget\WebcomicLocationsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Location\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermsList;

/**
 * Comic locations list widget implementation.
 *
 * @name Webcomic Locations List
 * @summary Display a list of comic locations.
 * @option Title: Optional widget title.
 * @option Number of terms to show: Optional number of locations to show; 0
 * shows all locations.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens).
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Related to: The collection or comic the locations must be related
 * to. The (current collection) can't always be determined.
 * @option Comic link: Optional comic link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Some formats never list
 * comics.
 */
class WebcomicLocationsList extends WebcomicTermsList {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->taxonomy_type           = 'location';
		$this->taxonomy_label          = __( 'Locations', 'webcomic' );
		$this->taxonomy_singular_label = __( 'Location', 'webcomic' );

		parent::__construct();
	}
}
