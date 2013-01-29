<?php
/** Contains the WebcomicUpgrade class.
 * 
 * @package Webcomic
 */

/** Handle version-specific upgrades.
 * 
 * @package Webcomic
 */
class WebcomicUpgrade extends Webcomic {
	/** Upgrade existing installations.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 * @uses WebcomicUpgrade::_403
	 */
	public function __construct() {
		if ( version_compare( self::$config[ 'version' ], '4.0.3', '<' ) ) {
			$this->_403();
		}
		
		self::$config[ 'version' ] = self::$version;
		
		update_option( 'webcomic_options', self::$config );
	}
	
	/** Upgrade to 4.0.3
	 * 
	 * @see github.com/mgsisk/webcomic/commit/73bbd5a1c21ba227c5023374a98f49d8bf05a54b
	 * @see github.com/mgsisk/webcomic/commit/7d3ba3918121fe5c78754a0f012ab3b9d21fd2ae
	 * @uses Webcomic::$config
	 */
	private function _403() {
		$themes = wp_get_themes();
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			if ( $v[ 'theme' ] and false === strpos( $v[ 'theme' ], '|' ) ) {
				foreach ( $themes as $theme ) {
					if ( $theme[ 'Template' ] === $v[ 'theme' ] ) {
						self::$config[ 'collections' ][ $k ][ 'theme' ] = $theme[ 'Template' ] . '|' . $theme[ 'Stylesheet' ];
					}
				}
			}
		}
	}
}