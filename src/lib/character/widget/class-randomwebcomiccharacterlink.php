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
 * @name Random Webcomic Character Link
 * @summary Display a link to a random comic character.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Collection: The collection to limit navigation to. The (current
 * collection) can't always be determined.
 */
class RandomWebcomicCharacterLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'random';
		$this->description    = __( 'Display a link to a random comic character.', 'webcomic' );
		$this->relation_label = __( 'Random', 'webcomic' );
		$this->taxonomy_type  = 'character';
		$this->taxonomy_label = __( 'Character', 'webcomic' );

		parent::__construct();
	}
}
