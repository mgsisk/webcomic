<?php
/**
 * Authors meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

?>

<p>
	<?php
	// Translators: 1: The dashicons + symbol. 2: The dashicons X symbol.
	printf(
		esc_html__( "This box shows you the authors that have contributed to this transcript. Click the %1\$s to add details for a new author, or click the %2\$s to remove an author. You can update an author's name, email, URL, and transcription date here, as well as view the IP address that they transcribed from. Authors are always sorted by contribution date, from earliest to most recent.", 'webcomic' ),
		'<span class="dashicons dashicons-plus"><span class="screen-reader-text">+</span></span>',
		'<span class="dashicons dashicons-no"><span class="screen-reader-text">X</span></span>'
	);
	?>
</p>
<p><?php esc_html_e( "Don't forget to save your transcript after you've updated author information.", 'webcomic' ); ?></p>
