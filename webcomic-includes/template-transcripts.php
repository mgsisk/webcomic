<?php
/**
 * The default "transcript" template for Webcomic.
 *
 * This template displays any existing transcripts
 * and/or the submit transcript form for a webcomic.
 * Themes should provide their own template, but this
 * generic template will be loaded if they don't.
 * Template based on the comments.php template from TwentyTen.
 *
 * @package webcomic
 * @since 3
 * 
 * new teags: webcomic_transcripts_template, webcomic_have_transcripts, webcomic_list_transcripts, webcomic_transcripts_open, webcomic_transcript_form
 */

global $webcomic, $post; $webcomic->domain();
?>

<div id="webcomic-transcript">
<?php if ( post_password_required() ) { ?>
	<div class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any transcripts.', 'webcomic' ); ?></div>
</div><!-- #webcomic-transcript -->
	<?php return; } ?>
	<?php if ( $webcomic->have_webcomic_transcripts() ) { ?>
	<h3 id="transcript-title"><?php _e( 'Transcripts', 'webcomic' ); ?></h3>
	<?php $webcomic->list_webcomic_transcripts(); ?>	
	<?php } else { //Displayed if there are no transcripts so far ?>
		<?php if ( $webcomic->webcomic_transcripts_open() ) { //If transcribing is enabled, but there are no transcripts ?>
		
		<?php } else { //If transcribing is disabled ?>
		
		<?php } ?>
	<?php } echo $webcomic->webcomic_transcribe_form(); ?>
</div><!-- #webcomic-transcript -->