<?php
/** Automagic integration for loop_end.
 * 
 * @package Webcomic
 * @uses WebcomicTag::get_webcomic_transcript_authors()
 * @uses WebcomicTag::get_the_webcomic_transcript_term_list()
 * @uses is_webcomic()
 * @uses get_webcomic_transcripts()
 * @uses webcomic_transcripts_open()
 * @uses have_webcomic_transcripts()
 * @uses webcomic_transcript_form()
 */
if ( is_webcomic() ) {
	global $post;
	
	if ( $transcripts = get_webcomic_transcripts() ) {
		?>
		<h3 id="webcomic-transcripts"><?php sprintf( __( '%s Transcripts', 'webcomic' ), the_title( '', '', false ) ); ?></h3>
		<?php foreach ( $transcripts as $post ) { setup_postdata( $post ); ?>
		<div class="webcomic-transcript-content"><?php the_content(); ?></div>
		<div class="webcomic-transcript-meta"><?php printf( __( 'Transcribed by %s%s', 'webcomic' ), WebcomicTag::get_webcomic_transcript_authors(), WebcomicTag::get_the_webcomic_transcript_term_list( 0, 'webcomic_language', __( ' in ', 'webcomic' ), __( ', ', 'webcomic' ), __( '', 'webcomic' ) ) ); ?></div>
		<?php wp_reset_postdata(); }
	}
	
	if ( webcomic_transcripts_open() ) {
		if ( have_webcomic_transcripts( true ) and $transcripts = get_webcomic_transcripts( true ) ) {
			foreach ( $transcripts as $transcript ) {
				webcomic_transcript_form( array(), $transcript );
			}
		}
		
		webcomic_transcript_form();
	}	
}