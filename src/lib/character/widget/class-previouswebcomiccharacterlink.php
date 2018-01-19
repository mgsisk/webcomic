<?php
/**
 * Mgsisk\Webcomic\Character\Widget\PreviousWebcomicCharacterLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character\Widget;

use Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink;

/**
 * Comic character link widget implementation.
 */
class PreviousWebcomicCharacterLink extends WebcomicTermLink {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		$this->relation       = 'previous';
		$this->description    = __( 'Display a link to the previous comic character.', 'webcomic' );
		$this->relation_label = __( 'Previous', 'webcomic' );
		$this->taxonomy_type  = 'character';
		$this->taxonomy_label = __( 'Character', 'webcomic' );

		parent::__construct();
	}
}
