<?php
/**
 * Template Name: Archive
 *
 * @package InkBlot
 * @since 1.2
 */
global $comic_series;
if(have_posts()): while(have_posts()): the_post();
	$comic_series          = get_post_meta($post->ID,'comic_series',true);
	$archive_group         = get_post_meta($post->ID,'comic_archive_group',true);
	$archive_format        = get_post_meta($post->ID,'comic_archive_format',true);
	$archive_reverse_posts = get_post_meta($post->ID,'comic_archive_reverse_posts',true);
	$archive_descriptions  = get_post_meta($post->ID,'comic_archive_descriptions',true);
	$archive_pages         = get_post_meta($post->ID,'comic_archive_pages',true);
	$archive_reverse       = get_post_meta($post->ID,'comic_archive_reverse',true);
endwhile; endif;

get_header(); inkblot_begin_content();

if(have_posts()): while (have_posts()): the_post(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<h1><?php the_title(); ?></h1>
	<div class="content">
		<?php the_content(); wp_link_pages(); edit_post_link(__('Edit This','inkblot'), '<p>', '</p>'); ?>
	</div>
</div>
<?php
endwhile; endif;
?>
<?php
if('date' == $archive_group):
	$archive_reverse_posts = ($archive_reverse_posts) ? 'ASC' : 'DESC';
	$comics = comic_loop(-1,$comic_series,'&order='.$archive_reverse_posts);
	if($archive_format):
		if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
			$comic = get_the_comic();
			if($the_year != get_the_time('Y')):
				if($the_year) echo '</div>';
?>
			<h2><?php the_date('Y'); ?></h2>
			<div class="comic-archive">
<?php
				$the_year = get_the_time('Y');
			endif;
			if($the_month != get_the_time('n')):
				if($the_month) echo '</div>';
?>
			<h3><?php the_time('F'); ?></h3>
			<div class="comic-archive-month">
<?php
				$the_month = get_the_time('n');
			endif;
?>
			<a href="<?php echo $comic['link']; ?>" title="<?php echo $comic['description']; ?>"><img src="<?php echo get_comic_image($comic,$archive_format); ?>" alt="<?php echo $comic['title']; ?>" /></a>
			<?php endwhile; ?>
			</div>
		</div>
<?php endif;
	else:
		if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
			if($the_year != get_the_time('Y')):
				$i = 0; if($the_year) echo '</table>';
?>
			<h2><?php the_date('Y'); ?></h2>
			<table class="comic-archive">
				<colgroup><col class="date" /><col /></colgroup>
<?php
				$the_year = get_the_time('Y');
			endif;
?>
				<tr<?php if($i%2 != 0) echo ' class="alt"'; ?>>
					<th scope="row"><span class="pad"><?php the_time('F jS'); ?></span></th>
					<td><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s','inkblot'),the_title_attribute('echo=0')) ?>"><?php the_title(); ?></a></td>
				</tr>
		<?php $i++; endwhile; ?>
			</table>
		<?php
		endif;
	endif;
else:
	comic_archive($comic_series,$archive_format,$archive_reverse_posts,$archive_reverse,$archive_descriptions,$archive_pages);
endif;
get_footer();
?>