<?php
/** Contains the WebcomicUpgrade class.
 * 
 * @package Webcomic
 */

/** Upgrade old installations.
 * 
 * @package Webcomic
 */
class WebcomicUpgrade extends Webcomic {
	/** Upgrade existing installations.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 */
	public function __construct() {
		self::$config[ 'version' ] = self::$version;
		
		update_option( 'webcomic', self::$config );
	}
}