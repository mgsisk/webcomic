<?php
/**
 * This document contains all of the standard page templates.
 * 
 * Thanks for choosing InkBlot! If you'd like to customize your site this
 * is the place. Find the block that corresponds to the template you'd like
 * to edit and have at it. Each block begins and ends with the name of the
 * template. Templates included in this file:
 * 
 * home.php       - Front page of the site.
 * single.php     - Single post pages.
 * page.php       - Standard WordPress Pages.
 * archives.php   - The Comic Archive template.
 * archive.php    - Standard WordPress Archive pages.
 * search.php     - Search results pages.
 * attachment.php - Standard WordPress attachment pages.
 * 404.php        - 404 error pages.
 * 
 * If you'd like to create major customizations, you can easily override any
 * of the templates in this file simply by creating the missing template file
 * and adding it to the InkBlot theme directory.
 * 
 * For more information on WordPress themes, the template hierarchy, and
 * conditional tags, please see http://codex.wordpress.org/Template_Hierarchy
 * and http://codex.wordpress.org/Conditional_Tags.
 * 
 * @package InkBlot
 * @since 1.0
 */

if(!function_exists('get_the_comic'))
	die(__("It looks like you don't have WebComic installed. Please install and activate WebComic before using InkBlot.","inkblot"));

if(!get_option('comic_width') || !get_option('comic_archive_format') || !get_option('comic_archive_images') || !get_option('comic_archive_images_size') || !get_option('comic_embed_code') || !get_option('comic_embed_code_size') || !get_option('comic_archive_descriptions') || !get_option('comic_archive_pages') || !get_option('comic_archive_reverse') || !get_option('inkblot_layout') || !get_option('inkblot_sidebar1_size') || !get_option('inkblot_sidebar1_position') || !get_option('inkblot_sidebar2_size') || !get_option('inkblot_sidebar2_position') || !get_option('inkblot_content_position'))
	die(__('InkBlot could not load because critical setting information is missing. Activating the theme or saving your settings from the InkBlot theme page should correct this.','inkblot'));

get_header();
?>

<?php
//home.php
if(is_home()):
?>
	<?php if(!is_paged() && 'on' == get_option('comic_front_page')): //Only show the comic on the front of the home page ?>
		<?php $comics = comic_loop(); if($comics->have_posts()): while($comics->have_posts()): $comics->the_post(); ?>
			<div id="comic">
				<h1><?php bloginfo('name'); ?></h1>
				<?php the_comic(); ?>
				<div class="navi">
					<?php if(get_the_chapter()): ?><div class="alignleft"><?php the_volume() ?> &bull; <?php the_chapter() ?></div><?php endif; ?>
					<div class="alignright"><?php comics_nav_link(); ?></div>
					<br class="clear" />
				</div>
			</div>
		<?php endwhile; inkblot_begin_content(); $comics->rewind_posts(); while($comics->have_posts()): $comics->the_post(); ?> 
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time(get_option('date_format')) ?></p>
				<div class="content"><?php the_content(); ?></div>
				<p class="foot"><!-- <?php printf(__('By %1$s at %2$s','inkblot'),get_the_author(),get_the_time()) ?> &bull; --><?php the_tags(__('Tags: ','inkblot'),', ',' &bull; '); ?><?php comments_popup_link(); ?><?php edit_post_link(__('Edit This','inkblot'), ' &bull; '); ?></p>
			</div>
		<?php endwhile; endif; ?>
	<?php else: inkblot_begin_content(); endif; ?>
	
	<?php ignore_comics(); if(have_posts()): //Now show all of our non-comic posts ?>
		<?php if('on' == get_option('comic_front_page')): ?><div class="blog-title"><span>Blog</span></div><?php endif ?>
		<?php while (have_posts()) : the_post(); ?>
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time(get_option('date_format')) ?><!-- at <?php the_time() ?>--></p>
				<div class="content"><?php the_content(); ?></div>
				<p class="foot"><!-- <?php printf(__('By %1$s at %2$s','inkblot'),get_the_author(),get_the_time()) ?> &bull; --><?php _e('Categories: ','inkblot'); the_category(', '); ?> &bull; <?php the_tags(__('Tags: ','inkblot'),', ',' &bull; '); ?><?php comments_popup_link(); ?><?php edit_post_link(__('Edit This','inkblot'), ' &bull; '); ?></p>
			</div>
		<?php endwhile; ?>
		<div class="page-navi">
			<div class="alignright"><?php next_posts_link() ?></div>
			<div class="alignleft"><?php previous_posts_link() ?></div>
			<br class="clear" />
		</div>
	<?php endif; ?>
<?php endif; //end home.php ?>



<?php
//single.php
if(is_single() && !is_attachment()):
?>
	<?php if(have_posts()): while (have_posts()): the_post(); if(in_category(get_comic_category())): //Show the comic if this is a comic post ?>
		<div id="comic">
			<?php the_comic(); ?>
				<div class="navi">
					<?php if(get_the_chapter()): ?><div class="alignleft"><?php the_volume() ?> &bull; <?php the_chapter(); ?></div><?php endif; ?>
					<div class="alignright"><?php comics_nav_link(); ?></div>
					<br class="clear" />
				</div>
		</div>
	<?php endif; endwhile; endif; inkblot_begin_content(); ?>
	
	<?php if (have_posts()): while (have_posts()): the_post(); if(in_category(get_comic_category())): //And then show the comic post itself ?>
		<div <?php post_class() ?> id="post-<?php the_id(); ?>">
			<h1><?php the_title(); ?></h1>
			<p class="date"><?php the_time(get_option('date_format')) ?></p>
			<div class="content"><?php the_content(); ?></div>
			<?php the_comic_transcript(); if('on' == get_option('comic_embed_code')): ?><div class="embed-title"><span><?php _e('Share This Comic','inkblot') ?></span></div><?php the_comic_embed(get_option('comic_embed_code_size')); endif; ?>
			<p class="meta">
				<?php printf(__('Posted at %s<!-- by %s-->','inkblot'),get_the_time(),get_the_author()); if(get_the_tags()): _e(' and tagged with '); the_tags('',', '); endif; ?>.
				<?php
				printf(__('Follow responses to this comic with the <a href="%s">comments feed</a>. ','inkblot'),get_post_comments_feed_link());
				if (('open' == $post-> comment_status) && ('open' == $post->ping_status)):
					printf(__('You can <a href="#comment">leave a comment</a> or <a href="%s" rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
				elseif(!('open' == $post-> comment_status) && ('open' == $post->ping_status)):
					printf(__('Comments are closed, but you can <a href="%s " rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
				elseif(('open' == $post-> comment_status) && !('open' == $post->ping_status)):
					_e('You can <a href="#comment">leave a comment</a>. ','inkblot');
				endif;
				edit_post_link(__('Edit This','inkblot'));
				?>
			</p>
		</div>
		
		<?php else: //Show a regular blog post ?>
		
		<div <?php post_class() ?> id="post-<?php the_id(); ?>">
			<div class="navi">
				<div class="alignleft"><?php previous_post_link('%link','&laquo; Previous',false,get_comic_category()) ?></div>
				<div class="alignright"><?php next_post_link('%link','Next &raquo;',false,get_comic_category()) ?></div>
				<br class="clear" />
			</div>
			<h1><?php the_title(); ?></h1>
			<p class="date"><?php the_time(get_option('date_format')) ?></p>
			<div class="content"><?php the_content(); ?></div>
			<p class="meta">
				<?php printf(__('Posted at %s<!-- by %s--> in ','inkblot'),get_the_time(),get_the_author()); the_category(', '); ?><?php if(get_the_tags()): _e(' and tagged with ','inkblot'); the_tags('',', '); endif; ?>.
				<?php
				printf(__('Follow responses to this post with the <a href="%s">comments feed</a>. ','inkblot'),get_post_comments_feed_link());
				if (('open' == $post-> comment_status) && ('open' == $post->ping_status)):
					printf(__('You can <a href="#comment">leave a comment</a> or <a href="%s" rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
				elseif(!('open' == $post-> comment_status) && ('open' == $post->ping_status)):
					printf(__('Comments are closed, but you can <a href="%s " rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
				elseif(('open' == $post-> comment_status) && !('open' == $post->ping_status)):
					_e('You can <a href="#comment">leave a comment</a>. ','inkblot');
				endif;
				edit_post_link(__('Edit This','inkblot'));
				?>
			</p>
		</div>
		<?php endif; ?>
		<?php comments_template(); ?>
	<?php endwhile; endif; ?>
<?php endif; //end single.php ?>



<?php
//page.php
if(is_page() && !is_page('Archive') && !is_attachment()): inkblot_begin_content();
?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="content">
				<?php the_content(); wp_link_pages(); edit_post_link(__('Edit This','inkblot'), '<p>', '</p>'); ?>
			</div>
		</div>
	<?php endwhile; endif; ?>
<?php endif; //end page.php ?>



<?php
//archives.php
if(is_page('Archive')): inkblot_begin_content();
?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="content">
				<?php the_content(); wp_link_pages(); edit_post_link(__('Edit This','inkblot'), '<p>', '</p>'); ?>
			</div>
		</div>
	<?php endwhile; endif; ?>
	
	<?php if('date' == get_option('comic_archive_format')): ?>
		<?php $comics = comic_loop(-1); if($comics->have_posts()): while($comics->have_posts()) : $comics->the_post(); ?>
			<?php if($the_year != get_the_time('Y')): $i = 0; if($the_year) echo '</table>' ?>
			<h2><?php the_date('Y'); ?></h2>
			<table class="comic-archive">
				<colgroup><col class="date" /><col /></colgroup>
				<?php $the_year = get_the_time('Y'); endif; ?>
				<tr<?php if($i%2 != 0) echo ' class="alt"'; ?>>
					<th scope="row"><span class="pad"><?php the_time('F jS'); ?></span></th>
					<td><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></td>
				</tr>
		<?php $i++; endwhile; ?>
			</table>
		<?php endif; ?>
		
	<?php else:
		$archive_descriptions = ('on' == get_option('comic_archive_descriptions')) ? true : false;
		$archive_pages = ('on' == get_option('comic_archive_pages')) ? true : false;
		$archive_reverse = ('on' == get_option('comic_archive_reverse')) ? true : false;
		comic_archive($archive_descriptions,$archive_pages,$archive_reverse);
	endif; ?>
<?php endif; //end archives.php ?>



<?php
//archive.php
if(is_archive()): inkblot_begin_content();
?>
	<?php if (have_posts()): ?>
	<?php /* If this is a category archive */if(is_category()){ ?><h1><?php single_cat_title(); _e(' Posts','inkblot') ?></h1>
	<?php /* If this is a tag archive */}elseif(is_tag()){ ?><h1><q><?php single_tag_title(); _e(' Posts','inkblot') ?></q></h1>
	<?php /* If this is a daily archive */}elseif(is_day()){ ?><h1><?php the_time(get_option('date_format')); _e(' Archive','inkblot') ?></h1>
	<?php /* If this is a monthly archive */}elseif(is_month()){ ?><h1><?php the_time('F, Y'); _e(' Archive','inkblot') ?></h1>
	<?php /* If this is a yearly archive */}elseif(is_year()){ ?><h1><?php the_time('Y'); _e(' Archive','inkblot') ?></h1>
	<?php /* If this is an author archive */}elseif(is_author()){ ?><h1><?php  _e('Author Archive','inkblot') ?></h1>
	<?php } while (have_posts()) : the_post(); ?>
		<?php if(in_category(get_comic_category())): //If this is a comic post ?>
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<a href="<?php the_permalink() ?>" class="comic">
					<?php if('on' == get_option('comic_archive_images')) the_comic(get_option('comic_archive_images_size')); ?>
					<span class="title"><?php the_title() ?></span>
					<span class="date"><?php the_time(get_option('date_format')) ?></span>
				</a>
			</div>
		<?php else: //If this is a regular post ?>
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time(get_option('date_format')) ?><!-- at <?php the_time() ?>--></p>
				<div class="content"><?php the_content(); ?></div>
				<p class="foot"><!-- <?php printf(__('By %1$s at %2$s','inkblot'),get_the_author(),get_the_time()) ?> &bull; --><?php _e('Categories: ','inkblot'); the_category(', '); ?> &bull; <?php the_tags(__('Tags: ','inkblot'),', ',' &bull; '); ?><?php comments_popup_link(); ?><?php edit_post_link(__('Edit This','inkblot'), ' &bull; '); ?></p>
			</div>
		<?php endif; endwhile; ?>
		<div class="page-navi">
			<div class="alignright"><?php next_posts_link() ?></div>
			<div class="alignleft"><?php previous_posts_link() ?></div>
			<br class="clear" />
		</div>
	<?php endif; ?>
<?php endif; //end archive.php ?>



<?php
//search.php
if(is_search()):
inkblot_begin_content();
?>
	<?php if(have_posts()): ?>
	<h1><?php _e('Search Results','inkblot') ?></h1>
	<?php while (have_posts()) : the_post(); if(in_category(get_comic_category())): ?>
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<a href="<?php the_permalink() ?>" class="comic">
					<?php if('on' == get_option('comic_archive_images')) the_comic(get_option('comic_archive_images_size')); ?>
					<span class="title"><?php the_title() ?></span>
					<span class="date"><?php the_time(get_option('date_format')) ?></span>
				</a>
			</div>
		<?php else: ?>
			<div <?php post_class() ?> id="post-<?php the_id(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time(get_option('date_format')) ?><!-- at <?php the_time() ?>--></p>
				<div class="content"><?php the_content(); ?></div>
				<p class="foot"><!-- <?php printf(__('By %1$s at %2$s','inkblot'),get_the_author(),get_the_time()) ?> &bull; --><?php _e('Categories: ','inkblot'); the_category(', '); ?> &bull; <?php the_tags(__('Tags: ','inkblot'),', ',' &bull; '); ?><?php comments_popup_link(); ?><?php edit_post_link(__('Edit This','inkblot'), ' &bull; '); ?></p>
			</div>
		<?php endif; endwhile; ?>
		<div class="group page-navi">
			<div class="alignright"><?php next_posts_link() ?></div>
			<div class="alignleft"><?php previous_posts_link() ?></div>
			<br class="clear" />
		</div>
	<?php else: ?>
		<h1><?php _e('No Posts Found','inkblot') ?></h1>
	<?php endif; ?>
<?php endif; //end search.php ?>



<?php
//attachment.php
if(is_attachment()): inkblot_begin_content();
?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="content"><?php the_content(); ?></div>
			<div class="navi">
				<div class="alignleft thumb"><?php previous_image_link() ?></div>
				<div class="alignright thumb"><?php next_image_link() ?></div>
				<div class="meta">
					<?php printf(__('Posted on %1$s at %2$s<!-- by %3$s-->.','inkblot'),get_the_time(get_option('date_format')),get_the_time(),get_the_author()) ?>
					<?php
					printf(__('Follow responses to this image with the <a href="%s">comments feed</a>. ','inkblot'),get_post_comments_feed_link());
					if (('open' == $post-> comment_status) && ('open' == $post->ping_status)):
						printf(__('You can <a href="#comment">leave a comment</a> or <a href="%s" rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
					elseif(!('open' == $post-> comment_status) && ('open' == $post->ping_status)):
						printf(__('Comments are closed, but you can <a href="%s " rel="trackback">trackback</a> from your own site. ','inkblot'),get_trackback_url());
					elseif(('open' == $post-> comment_status) && !('open' == $post->ping_status)):
						_e('You can <a href="#comment">leave a comment</a>. ','inkblot');
					endif;
					edit_post_link(__('Edit This','inkblot'));
					?>
				</div>
			</div>
		</div>
		<?php comments_template(); ?>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no attachments could be found.','inkblot') ?></p>
	<?php endif; ?>
<?php endif; //end attachment.php ?>



<?php
//404.php
if(is_404()): inkblot_begin_content();
?>
	<h1><?php _e('404 - Not Found','inkblot') ?></h1>
	<p><?php _e("Whatever you're looking for doesn't seem to exist.","inkblot") ?></p>
	<?php get_search_form() ?>
	<h2><?php _e('Recent Comics','inkblot') ?></h2>
	<ul class="recent-list-404"><?php recent_comics(4,'image','thumb'); ?></ul>
	<p class="clear"><?php $comic = get_the_comic('random'); printf(__('Or try your luck with a <a href="'.$comic['link'].'" title="'.$comic['description'].'">%s</a>.'),'random comic') ?></p>
<?php
	endif; //end 404.php
	get_footer();
?>