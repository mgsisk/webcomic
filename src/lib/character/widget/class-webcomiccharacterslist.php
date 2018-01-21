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
 *
 * @name Webcomic Characters List
 * @summary Display a list of comic characters.
 * @option Title: Optional widget title.
 * @option Number of terms to show: Optional number of characters to show; 0
 * shows all characters.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens).
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Related to: The collection or comic the characters must be related
 * to. The (current collection) can't always be determined.
 * @option Comic link: Optional comic link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Some formats never list
 * comics.
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
