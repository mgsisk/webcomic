<?php
/**
 * This is an example transcript.php file. Themes should provide
 * their own transcript.php file, but this one will be loaded in
 * the event that no theme transcript.php file exists. Use this
 * file as a template when creating new transcript.php files.
 * 
 * @package WebComic
 * @since 2.0.0
 */
	if ( !empty( $_SERVER[ 'SCRIPT_FILENAME' ] ) && 'transcripts.php' == basename( $_SERVER[ 'SCRIPT_FILENAME' ] ) )
		die( 'Please do not load this page directly. Thanks!' );
	if ( post_password_required() ) {
		echo '<p class="notranscript">' . __('This post is password protected. Enter the password to view the transcript.', 'webcomic' ) . '</p>';
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( 'publish' == $transcript_status ) : //A published transcript exists?>

	<h2><?php _e( 'Transcript', 'webcomic' ); ?></h2>
	<div id="transcript"><?php echo $transcript; ?></div>

<?php elseif ( get_option( 'comic_transcripts_allowed' ) && 'draft' != $transcript_status ) : //Transcribing is allowed and no draft is awaiting moderation ?>

	<h2><?php transcript_form_title(); ?></h2>
	<div id="transcript">
	<?php if ( get_option( 'comic_transcripts_loggedin' ) && !$user_ID) : //Users must be registered and logged in to transcript ?>
		<p><?php printf( __( 'You must be <a href="%s">logged in</a> to transcribe.', 'webcomic' ), get_option( 'siteurl' ) . '/wp-login.php?redirect_to=' . urlencode( get_permalink() ) ); ?></p>
	<?php else : ?>
		<form action="" method="post" id="transcriptform">
		<div id="transcript-response"></div> <!-- For AJAX Responses -->
		<?php if ( $user_ID ) : ?>
			<p><?php printf( __( 'Transcribing as <a href="%s">%s</a>', 'webcomic' ), get_option( 'siteurl' ) . '/wp-admin/profile.php', $user_identity ); ?> <a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php _e( 'Log Out &raquo;', 'webcomic' ); ?></a></p>
		<?php else : ?>
			<p><label for="trans_author"><?php _e( 'Name', 'webcomic' ); ?></label><input type="text" name="trans_author" id="trans_author" /><?php if ( $req ) _e( ' (required)', 'webcomic' ); ?></p>
			<p><label for="trans_email"><?php _e( 'E-mail', 'webcomic' ); ?></label><input type="text" name="trans_email" id="trans_email" /><?php if ( $req ) _e( ' (required)', 'webcomic' ); ?></p>
			<p><label for="trans_captcha"><?php _e( 'Is fire hot or cold?', 'webcomic' ); ?></label><input type="text" name="trans_captcha" id="trans_captcha" /></p>
		<?php endif; ?>
			<p><textarea rows="7" cols="40" name="transcript" id="transcript"><?php if ( 'pending' == $transcript_status ) echo $transcript; ?></textarea></p>
			<!--<p><strong><?php _e( 'XHTML Allowed:', 'webcomic' ); ?></strong><code><?php echo allowed_tags(); ?></code></p>-->
			<p><input type="submit" value="<?php _e( 'Transcribe', 'webcomic' ); ?>" /><?php transcript_id_fields( 'hot' ); ?></p>
		</form>
	<?php endif; ?>
	</div>

<?php endif; ?>