<?php
/**
 * Mgsisk\Webcomic\Character\Widget\WebcomicCharacterLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic character link widget implementation.
 *
 * @name Webcomic Character Link
 * @summary Display a link to a comic character.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Character: The character to link to, or the current page's character
 * (if any).
 */
class WebcomicCharacterLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->description    = __( 'Display a link to a comic character.', 'webcomic' );
		$this->taxonomy_type  = 'character';
		$this->taxonomy_label = __( 'Character', 'webcomic' );

		parent::__construct();
	}
}
