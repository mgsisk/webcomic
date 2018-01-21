<?php
/**
 * Comic media meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<div data-input="webcomic_media" data-webcomic-media-manager="multiple">
	<?php webcomic_media( '<div>{{</div>{medium}<div>}}</div>' ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" name="webcomic_media" value="<?php echo esc_attr( $args['media'] ); ?>">
