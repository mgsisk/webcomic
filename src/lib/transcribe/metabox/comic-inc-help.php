<?php
/**
 * Parent meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

?>

<p><?php esc_html_e( "This box shows you the comic this transcript belongs to. If the transcript has not been assigned to a comic you can search for and assign it to a comic using the Search Comics field. Don't forget to save your transcript after you've selected a comic.", 'webcomic' ); ?></p>
<p>
	<?php
	// Translators: The dashicons X symbol.
	printf( esc_html__( "Once you've assigned the transcript to a comic that comic's title and media will appear in this box for reference. Click the comic media to toggle media resizing. You can also enable or disable transcribing of the selected comic from here by checking or unchecking Allow transcripts and saving the transcript. To change the comic this transcript is assigned to, click the %s to remove the assigned comic and search for a new one.", 'webcomic' ), '<span class="dashicons dashicons-no"><span class="screen-reader-text">X</span></span>' );
	?>
</p>
<p><?php esc_html_e( 'Like comments, transcripts will be deleted along with their comic if their parent comic is permanently deleted.', 'webcomic' ); ?></p>
