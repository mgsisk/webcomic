<?php
/**
 * The default "failed verification" template for Webcomic.
 *
 * This template is displayed if a user has filled out their
 * birthday and is not old enough to view the current webcomic
 * or webcomic-related page. Themes should provide their own
 * template, but this generic template will be loaded if they
 * don't. Template based on the 404.php template from TwentyTen.
 *
 * @package webcomic
 * @since 3
 */

global $webocmic; $webcomic->domain();
?>

<?php the_post(); get_header(); ?>

<div id="container">
	<div id="content">
		<div id="post-0" class="post error404 not-found">
			<h1 class="entry-title"><?php _e( 'Restricted', 'webcomic' ); ?></h1>
			<div class="entry-content">
				<p><?php _e( 'Sorry, this content is age restricted.', 'webcomic' ); ?></p>
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->
	</div><!-- #content -->
</div><!-- #container -->

<?php get_footer(); ?>