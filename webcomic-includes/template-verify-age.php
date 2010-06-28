<?php
/**
 * The default age verification template for Webcomic.
 *
 * This template is displayed if a user attempts to view
 * an age-restricted webcomic or webcomic-related page and
 * no age information can be found. Themes should provide
 * their own template, but this generic template will be
 * loaded if they don't. Template based on the 404.php
 * template from TwentyTen.
 * 
 * @package webcomic
 * @since 3
 */

global $webcomic; $webcomic->domain();
?>

<?php the_post(); get_header(); ?>

<div id="container">
	<div id="content">
		<div id="post-0" class="post error404 not-found">
			<h1 class="entry-title"><?php _e( 'Restricted', 'webcomic' ); ?></h1>
			<div class="entry-content wp-caption">
				<p><?php _e( 'This webcomic is age restricted. Please enter your birth date to proceed:', 'webcomic' ); ?></p>
				<?php echo $webcomic->get_webcomic_verify_form(); ?>
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->
	</div><!-- #content -->
</div><!-- #container -->

<?php get_footer(); ?>