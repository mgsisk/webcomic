<?php
/**
 * Mgsisk\Webcomic\Character\Widget\FirstWebcomicCharacterLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic character link widget implementation.
 *
 * @name First Webcomic Character Link
 * @summary Display a link to the first comic character.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Collection: The collection to limit navigation to. The (current
 * collection) can't always be determined.
 */
class FirstWebcomicCharacterLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'first';
		$this->description    = __( 'Display a link to the first comic character.', 'webcomic' );
		$this->relation_label = __( 'First', 'webcomic' );
		$this->taxonomy_type  = 'character';
		$this->taxonomy_label = __( 'Character', 'webcomic' );

		parent::__construct();
	}
}
