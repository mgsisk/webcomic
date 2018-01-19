<?php
/**
 * Media meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

?>

<p><?php esc_html_e( 'This box lets you add one or more media items to your comic, or change the media already assigned to your comic. These media items are what Webcomic will display as your comic. Click Add Media (or Change Media), select or upload the media items you want to assign to your comic, and then click Update.', 'webcomic' ); ?></p>
<p>
	<?php
	// Translators: The dashicons X symbol.
	printf( esc_html__( "If you have more than one media item assigned to a comic Webcomic will display them in the order in which they appear in the Webcomic Media box; just drag the items around to change their display order. You can remove a single media item by clicking the %s in it's corner.", 'webcomic' ), '<span class="dashicons dashicons-no"><span class="screen-reader-text">X</span></span>' );
	?>
</p>
<p><?php esc_html_e( "Don't forget to save your changes after you're done by saving or publishing your comic.", 'webcomic' ); ?></p>
