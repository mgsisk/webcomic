<?php
/** Automagic integration for dynamic requests.
 * 
 * Unlike the other integration templates this file will be loaded
 * for dynamic requests regardless of the integration setting if no
 * dynamic webcomic template exists.
 * 
 * @package Webcomic
 * @uses first_webcomic_link()
 * @uses previous_webcomic_link()
 * @uses random_webcomic_link()
 * @uses next_webcomic_link()
 * @uses last_webcomic_link()
 * @uses the_webcomic()
 * @uses the_webcomic_collection()
 * @uses the_webcomic_storylines()
 * @uses the_webcomic_characters()
 */
?>
<div class="integrated-webcomic">
	<nav class="webcomic-above"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-above -->
	<div class="webcomic-img"><?php the_webcomic( 'full', 'next' ); ?></div><!-- .webcomic-img -->
	<nav class="webcomic-below"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-below -->
</div><!-- .integrated-webcomic -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'webcomic' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1><!-- .entry-title -->
		
		<?php if ( comments_open() and !post_password_required() ) { ?>
		<div class="comments-link">
			<?php comments_popup_link( 0, 1, '%' ); ?>
		</div><!-- .comments-link -->
		<?php } ?>
	</header><!-- .entry-header -->
	
	<div class="entry-content">
		<?php the_content(); wp_link_pages(); ?>
	</div><!-- .entry-content -->
	
	<footer class="entry-meta">
	<?php 
		the_webcomic_collection();
		the_webcomic_storylines( '<span class="sep"> | </span>' );
		the_webcomic_characters( '<span class="sep"> | </span>' );
		
		if ( comments_open() ) { ?>
		<span class="sep"> | </span><span class="comments-link"><?php comments_popup_link(); ?></span>
		<?php }
		
		edit_post_link( __( 'Edit', 'webcomic' ), '<span class="edit-link">', '</span>' );
	?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->