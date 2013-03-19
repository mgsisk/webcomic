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
	 * @uses WebcomicUpgrade::_406
	 * @uses WebcomicUpgrade::_409
	 */
	public function __construct() {
		if ( version_compare( self::$config[ 'version' ], '4.0.3', '<' ) ) {
			$this->_403();
		}
		
		if ( version_compare( self::$config[ 'version' ], '4.0.6', '<' ) ) {
			$this->_406();
		}
		
		if ( version_compare( self::$config[ 'version' ], '4.0.9', '<' ) ) {
			$this->_409();
		}
		
		self::$config[ 'thanks' ]  = true;
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
	
	/** Upgrade to 4.0.6
	 * 
	 * @see github.com/mgsisk/webcomic/commit/b2d1c321998c62a08d67a0382854afbe8b9ff25b
	 * @uses Webcomic::$config
	 */
	private function _406() {
		if ( empty( self::$config[ 'gestures' ] ) ) {
			self::$config[ 'gestures' ] = false;
		}
	}
	
	/** Upgrade to 4.0.9
	 * 
	 * @see github.com/mgsisk/webcomic/commit/dce9e0cff368ed4bc666f2771543d7734c51c1b1
	 * @see github.com/mgsisk/webcomic/commit/52e726ac13419a3ff16d36ab3e1499f5de0717a7
	 * @uses Webcomic::$config
	 */
	private function _409() {
		self::$config[ 'api' ]     = '';
		self::$config[ 'network' ] = array(
			'showcase' => false
		);
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			self::$config[ 'collections' ][ $k ][ 'twitter' ][ 'media' ] = false;
		}
	}
}