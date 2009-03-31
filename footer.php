<?php
/**
 * @package InkBlot
 * @since 1.0
 */
?>
				</div>
			</div>
			<?php if('c1' != get_option('inkblot_layout')) get_sidebar(); if(strstr(get_option('inkblot_layout'),'3')) get_sidebar('2') //Delete this line and terrible things will happen ?>
			<br class="clear" />
		</div>
		<div id="foot">
			<div class="alignleft"><?php printf(__('Subscribe to <a href="%s">Entries</a> or <a href="%s">Comments</a>','inkblot'),get_bloginfo('rss2_url'),get_bloginfo('comments_rss2_url')) ?></div>
			<div class="alignright"><?php printf(__('Powered by <a href="%s">WordPress</a> with <a href="%s">WebComic &amp; InkBlot</a>','inkblot'),'http://wordpress.org/','http://maikeruon.com/wcib/') ?></div>
			<br class="clear" />
		</div>
	</div>
</div>
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
<?php wp_footer() ?>
</body>
</html>