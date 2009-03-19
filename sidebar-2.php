<?php
/**
 * @package InkBlot
 * @since 1.0
 */
?>
<div id="sidebar2">
	<ul class="pad">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar 2')): ?>
		<li><p><?php dropdown_comics() ?></p></li>
		<li>
			<h2><?php _e('Recent Comics','inkblot') ?></h2>
			<ul><?php recent_comics() ?></ul>
		</li>
		<li>
			<h2><?php _e('Random Comic','inkblot') ?></h2>
			<p><?php print random_comic('thumb') ?></p>
		</li>
		<?php endif; ?>
	</ul>
</div>