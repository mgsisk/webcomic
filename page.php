<?php
/**
 * @package InkBlot
 * @since 1.0
 */
global $comic_series;
if(have_posts()): while(have_posts()): the_post();
	$comic_series = get_post_meta($post->ID,'comic_series',true);
endwhile; endif;

get_header(); inkblot_begin_content();
?>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
	<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>
		<div class="content">
			<?php the_content(); wp_link_pages(); edit_post_link(__('Edit This','inkblot'), '<p>', '</p>'); ?>
		</div>
	</div>
<?php
	endwhile; endif;
	get_footer();
?>