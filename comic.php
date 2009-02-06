<?php
/**
 * Template Name: Comic
 *
 * @package InkBlot
 * @since 1.2
 */
get_header();
$comics = comic_loop(); if($comics->have_posts()): while($comics->have_posts()): $comics->the_post(); ?>
	<div id="comic">
		<h1><?php bloginfo('name'); ?></h1>
		<?php the_comic(); ?>
		<div class="navi">
			<?php if(get_the_chapter()): ?><div class="alignleft"><?php the_volume() ?> &bull; <?php the_chapter(); ?></div><?php endif; ?>
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
<?php endwhile; endif;
	get_footer();
?>