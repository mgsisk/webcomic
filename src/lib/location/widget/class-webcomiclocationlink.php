<?php
/**
 * Mgsisk\Webcomic\Location\Widget\WebcomicLocationLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Location\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic location link widget implementation.
 *
 * @name Webcomic Location Link
 * @summary Display a link to a comic location.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Location: The location to link to, or the current page's location
 * (if any).
 */
class WebcomicLocationLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->description    = __( 'Display a link to a comic location.', 'webcomic' );
		$this->taxonomy_type  = 'location';
		$this->taxonomy_label = __( 'Location', 'webcomic' );

		parent::__construct();
	}
}
