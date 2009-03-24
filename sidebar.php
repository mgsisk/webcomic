<?php
/**
 * @package InkBlot
 * @since 1.0
 */
?>
<div id="sidebar1">
	<ul class="pad">
	<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar 1')): ?>
		<li><?php inkblot_bookmark() ?></li>
		<li><?php get_calendar() ?></li>
		<li><?php get_search_form() ?></li>
		<?php wp_list_categories('title_li=<h2>'.__('Categories','inkblot').'</h2>') ?>
		<li>
			<h2><?php _e('Tags','inkblot') ?></h2>
			<p class="cloud"><?php wp_tag_cloud('largest=18&smallest=8') ?></p>
		</li>
		<?php wp_list_bookmarks() ?>
		<li>
			<h2><?php _e('Meta','inkblot') ?></h2>
			<ul>
				<?php wp_register() ?>
				<li><?php wp_loginout() ?></li>
				<li><a href="http://wordpress.org/" title="Powered by WordPress">WordPress</a></li>
				<?php wp_meta() ?>
			</ul>
		</li>
	<?php endif; ?>
	</ul>
</div>