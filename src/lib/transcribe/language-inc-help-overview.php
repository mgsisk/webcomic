<?php
/**
 * Language overview help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

?>

<p>
	<?php
	printf(
		// Translators: Hyperlink to to ISO-639-2.
		esc_html__( 'You can use languages to tag transcripts that translate your comic into the selected language. For maximum usability, the slug should be %s.', 'webcomic' ),
		'<a href="https://loc.gov/standards/iso639-2/php/code_list.php" target="_blank">' . esc_html__( 'an appropriate language code', 'webcomic' ) . '</a>'
	);
	?>
</p>
