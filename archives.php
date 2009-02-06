<?php
/**
 * Template Name: Archive
 *
 * @package InkBlot
 * @since 1.2
 */
get_header(); inkblot_begin_content();

if (have_posts()) : while (have_posts()) : the_post(); ?>
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
endif;

get_footer(); ?>