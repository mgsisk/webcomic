<?php
/**
 * Template Name: Comic
 *
 * @package InkBlot
 * @since 1.2
 */
global $comic_series;
if(have_posts()): while(have_posts()): the_post();
	$comic_series = get_post_meta($post->ID,'comic_series',true);
endwhile; endif;

get_header();
$comics = comic_loop(1,$comic_series); if($comics->have_posts()): while($comics->have_posts()): $comics->the_post(); ?>
	<div id="comic">
		<h1><?php bloginfo('name'); ?></h1>
		<?php the_comic(false,get_option('comic_image_link'),get_option('comic_image_link_previous')); ?>
		<div class="navi">
			<?php if(get_the_chapter()): ?><div class="alignleft"><?php the_chapter_link('volume'); the_chapter_link(); ?></div><?php endif; ?>
			<div class="alignright"><?php comics_nav_link(); ?></div>
			<br class="clear" />
		</div>
	</div>
<?php endwhile; inkblot_begin_content(); $comics->rewind_posts(); while($comics->have_posts()): $comics->the_post(); ?> 
	<div <?php post_class() ?> id="post-<?php the_id(); ?>">
		<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></h2>
		<p class="date"><?php the_time(get_option('date_format')) ?></p>
		<div class="content"><?php the_content(); ?></div>
		<p class="foot"><!-- <?php printf(__('By %1$s at %2$s','inkblot'),get_the_author(),get_the_time()) ?> &bull; --><?php the_tags(__('Tags: ','inkblot'),', ',' &bull; '); ?><?php $wp_query->is_page = false; comments_popup_link(); $wp_query->is_page = true; ?><?php edit_post_link(__('Edit This','inkblot'), ' &bull; '); ?></p>
	</div>
<?php
	endwhile; endif;
	get_footer();
?>