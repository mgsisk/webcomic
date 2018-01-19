<?php
/**
 * OAuth field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

if ( ! isset( $args ) ) {
	return;
}

?>
<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_consumer_key'] ); ?>">
<input type="hidden" name="<?php echo esc_attr( $args['label_secret'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_consumer_secret'] ); ?>">
<input type="hidden" name="<?php echo esc_attr( $args['label_token'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_token'] ); ?>">
<input type="hidden" name="<?php echo esc_attr( $args['label_token_secret'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_token_secret'] ); ?>">

<div class="webcomic-twitter-account-info">
	<?php
	echo $args['account']; // WPCS: xss ok.
	?>
</div>

<?php if ( ! $args['option']['oauth_token'] && ! $args['option']['oauth_token_secret'] ) : ?>
	<p class="description">
		<label for="<?php echo esc_attr( $args['label_for'] ); ?>"><small><?php esc_html_e( 'Consumer Key', 'webcomic' ); ?></small></label><br>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_consumer_key'] ); ?>" class="regular-text">
	</p>
	<p class="description">
		<label for="<?php echo esc_attr( $args['label_secret'] ); ?>"><small><?php esc_html_e( 'Consumer Secret', 'webcomic' ); ?></small></label><br>
		<input type="text" id="<?php echo esc_attr( $args['label_secret'] ); ?>" name="<?php echo esc_attr( $args['label_secret'] ); ?>" value="<?php echo esc_attr( $args['option']['oauth_consumer_secret'] ); ?>" class="regular-text">
	</p>
<?php endif; ?>
