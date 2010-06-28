<?php
/**
 * The default "paypal purchase" template for Webcomic.
 *
 * This template is displayed if a user clicks a "buy print"
 * link for a webcomic. Themes should provide their own template,
 * but this generic template will be loaded if they don't. Template
 * based on the single.php template from TwentyTen.
 * 
 * @package webcomic
 * @since 3
 */

global $webcomic; $webcomic->domain();
?>

<?php the_post(); get_header(); ?>

<div id="container">
	<div id="content">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1 class="entry-title"><?php printf( 'Buy Print - %s', get_the_title() ); ?></h1>
			<div class="entry-meta">
				<span class="meta-prep meta-prep-author"><?php _e( 'Posted by ', 'webcomic' ); ?></span>
				<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'webcomic' ), get_the_author() ); ?>"><?php the_author(); ?></a></span>
				<span class="meta-sep"><?php _e( ' on ', 'webcomic' ); ?> </span>
				<a href="<?php the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
				<?php edit_post_link( __( 'Edit', 'webcomic' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t" ); ?>
			</div><!-- .entry-meta -->
			<div class="entry-content">
				<div class="wp-caption"><?php the_webcomic_object( 'medium', 'self' ); ?></div>
				<div class="nav-previous"><strong><?php _e( 'Domestic:', 'webcomic' ); ?></strong> <?php echo $webcomic->get_purchase_webcomic_cost( 'price', 'domestic' ); ?> <sup>+ <?php echo $webcomic->get_purchase_webcomic_cost( 'shipping', 'domestic' ); _e( ' shipping', 'webcomic' ); ?></sup><?php echo $webcomic->get_purchase_webcomic_form( 'domestic' ); ?></div>
				<div class="nav-next"><strong><?php _e( 'International:', 'webcomic' ); ?></strong> <?php echo $webcomic->get_purchase_webcomic_cost( 'price', 'international' ); ?> <sup>+ <?php echo $webcomic->get_purchase_webcomic_cost( 'shipping', 'international' ); _e( ' shipping', 'webcomic' ); ?></sup><?php echo $webcomic->get_purchase_webcomic_form( 'international' ); ?></div>
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'webcomic' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
			<?php if ( get_the_author_meta( 'description' ) ) { ?>
			<div id="entry-author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?>
				</div><!-- #author-avatar 	-->
				<div id="author-description">
					<h2><?php _e( 'About ', 'webcomic' ); ?><?php the_author(); ?></h2>
					<?php the_author_meta( 'description' ); ?>
					<div id="author-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'webcomic' ), get_the_author() ); ?>"><?php _e( 'View all posts by ', 'webcomic' ); ?><?php the_author(); ?> &rarr;</a>
					</div><!-- #author-link	-->
				</div><!-- #author-description	-->
			</div><!-- .entry-author-info -->
			<?php } ?>
			<div class="entry-utility">
			<?php
				$tag_list = get_the_tag_list('', ', ');
				
				if ( '' != $tag_list )
					$utility_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>. Follow any comments here with the <a href="%5$s" title="Comments RSS to %4$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'webcomic' );
				else
					$utility_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>. Follow any comments here with the <a href="%5$s" title="Comments RSS to %4$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'webcomic' );
				
				printf(
					$utility_text,
					get_the_category_list( ', ' ),
					$tag_list,
					get_permalink(),
					the_title_attribute( 'echo=0' ),
					get_post_comments_feed_link()
				);
				
				edit_post_link( __( 'Edit', 'webcomic' ), '<span class="edit-link">', '</span>' );
			?>
			</div><!-- .entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->
		<?php comments_template( '', true ); ?>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); get_footer(); ?>