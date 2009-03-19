<?php
/**
 * This document contains all of the new widgets provided by WebComic.
 * 
 * @package WebComic
 * @since 1.0
 */
 
/**
 * Initializes, manages, and displays the Random Comic widget.
 * 
 * This function initializes the Random Comic widget. It includes the
 * function that displays the widget (widget_random_comic) and the
 * function that manages the widget (widget_random_comic_control).
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses random_comic()
 */
function widget_init_random_comic(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	load_webcomic_domain();
	
	/** Display the Random Comic Widget */
	function widget_random_comic($args){
		extract($args);
		
		$options = get_option('widget_random_comic');
		$title   = $options['title'];
		$label   = $options['label'];
		$series  = $options['series'];
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		echo'<p>';
		random_comic($label,$series);
		echo '</p>'.$after_widget;
		
		echo $output;
	}
	
	/** Display settings for the Random Comic Widget */
	function widget_random_comic_control(){
		load_webcomic_domain();
		
		$options = get_option('widget_random_comic');
		
		if($_POST['random-comic-submit']):
			$newoptions['title']  = strip_tags(stripslashes($_POST['random-comic-title']));
			$newoptions['label'] = $_POST['random-comic-label'];
			$newoptions['series'] = $_POST['random-comic-series'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_random_comic', $options);
			endif;
		endif;
		
		$title      = htmlspecialchars($options['title'], ENT_QUOTES);
		$label      = $options['label'];
		$the_series = $options['series'];
		?>
			<p><label><?php _e('Title:','webcomic') ?><input type="text" class="widefat" id="random-comic-title" name="random-comic-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Format:','webcomic') ?>
			<select name="random-comic-label" id="random-comic-label" class="widefat">
				<option value=""><?php _e('Text','webcomic') ?></option>
				<option value="thumb"<?php if('thumb' == $label) echo ' selected="selected"'; ?>><?php _e('Thumbnail','webcomic') ?></option>
				<option value="medium"<?php if('medium' == $label) echo ' selected="selected"'; ?>><?php _e('Medium Image','webcomic') ?></option>
				<option value="large"<?php if('large' == $label) echo ' selected="selected"'; ?>><?php _e('Large Image','webcomic') ?></option>
				<option value="full"<?php if('full' == $label) echo ' selected="selected"'; ?>><?php _e('Full Image','webcomic') ?></option>
			</select>
			</label></p>
			<p><label><?php _e('Series:','webcomic') ?>
			<select name="random-comic-series" id="random-comic-series" class="widefat">
				<option value="">All</option>
			<?php $collection = get_the_collection('hide_empty=0'); foreach($collection as $series): ?>
				<option value="<?php echo $series['id'] ?>"<?php if($the_series == $series['id']) echo ' selected="selected"'; ?>><?php echo $series['title'] ?></option>
			<?php endforeach; ?>
			</select>
			</label></p>
			<input type="hidden" name="random-comic-submit" id="random-comic-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __('Links to a single, randomly selected comic','webcomic'));
	wp_register_sidebar_widget('random-comic',__('Random Comic','webcomic'),'widget_random_comic',$widget_ops);
	wp_register_widget_control('random-comic',__('Random Comic','webcomic'), 'widget_random_comic_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_random_comic');

/**
 * Initializes, manages, and displays the Recent Comics widget.
 * 
 * This function initializes the Recent Comics widget. It includes the
 * function that displays the widget (widget_recent_comics) and the
 * function that manages the widget (widget_recent_comics_control).
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses recent_comics()
 */
function widget_init_recent_comics(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	load_webcomic_domain();
	
	function widget_recent_comics($args){
		extract($args);
		
		$options = get_option('widget_recent_comics');
		$title   = $options['title'];
		$number  = ($options['number']) ? $options['number'] : 5;
		$label   = $options['label'];
		$series  = $options['series'];
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		echo '<ul>';
		recent_comics($number,$label,$series);
		echo '</ul>'.$after_widget;
	}
	
	function widget_recent_comics_control(){
		load_webcomic_domain();
		
		$options = get_option('widget_recent_comics');
		
		if($_POST['recent-comics-submit']):
			$newoptions['title']  = strip_tags(stripslashes($_POST['recent-comics-title']));
			$newoptions['number'] = (int) $_POST['recent-comics-number'];
			$newoptions['label']  = $_POST['recent-comics-label'];
			$newoptions['series'] = $_POST['recent-comics-series'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_recent_comics', $options);
			endif;
		endif;
		
		$title      = htmlspecialchars($options['title'], ENT_QUOTES);
		$label      = $options['label'];
		$number     = $options['number'];
		$the_series = $options['series'];
		if(!$number = (int) $options['number'])
			$number = 5;
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="recent-comics-title" name="recent-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Number of comics to show: ','webcomic') ?><input type="text" name="recent-comics-number" id="recent-comics-number" style="width: 25px; text-align: center;" value="<?php echo $number ?>" /></label></p>
			<p><label><?php _e('Format:','webcomic') ?>
			<select name="recent-comics-label" id="recent-comics-label" class="widefat">
				<option value=""><?php _e('Text','webcomic') ?></option>
				<option value="thumb"<?php if('thumb' == $label) echo ' selected="selected"'; ?>><?php _e('Thumbnail','webcomic') ?></option>
				<option value="medium"<?php if('medium' == $label) echo ' selected="selected"'; ?>><?php _e('Medium Image','webcomic') ?></option>
				<option value="large"<?php if('large' == $label) echo ' selected="selected"'; ?>><?php _e('Large Image','webcomic') ?></option>
				<option value="full"<?php if('full' == $label) echo ' selected="selected"'; ?>><?php _e('Full Image','webcomic') ?></option>
			</select>
			</label></p>
			<p><label><?php _e('Series:','webcomic') ?>
			<select name="recent-comics-series" id="recent-comics-series" class="widefat">
				<option value="0">All</option>
			<?php $collection = get_the_collection('hide_empty=0'); foreach($collection as $series): ?>
				<option value="<?php echo $series['id'] ?>"<?php if($the_series == $series['id']) echo ' selected="selected"'; ?>><?php echo $series['title'] ?></option>
			<?php endforeach; ?>
			</select>
			</label></p>
			<input type="hidden" name="recent-comics-submit" id="recent-comics-submit" value="1" />
		<?php
	}
	$widget_ops = array('description' => __('The most recent comics posted to your site','webcomic'));
	wp_register_sidebar_widget('recent-comics',__('Recent Comics','webcomic'),'widget_recent_comics',$widget_ops);
	wp_register_widget_control('recent-comics',__('Recent Comics','webcomic'), 'widget_recent_comics_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_recent_comics');

/**
 * Initializes, manages, and displays the Dropdown Comics widget.
 * 
 * This function initializes the Dropdown Comics widget. It includes the
 * function that displays the widget (widget_dropdown_comics) and the
 * function that manages the widget (widget_dropdown_comics_control).
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses dropdown_comics()
 */
function widget_init_dropdown_comics(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	load_webcomic_domain();
	
	function widget_dropdown_comics($args){
		extract($args);
		
		$options       = get_option('widget_dropdown_comics');
		$title         = $options['title'];
		$category      = $options['category'];
		$label         = $options['label'];
		$reverse_posts = $options['reverse_posts'];
		$reverse       = $options['reverse'];
		$numbers       = $options['numbers'];
		$pages         = $options['pages'];
		switch($options['group']):
			case 3: $group = 'series'; break;
			case 2: $group = 'volume'; break;
			case 1: $group = true;
		endswitch;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		dropdown_comics($category,$label,$numbers,$reverse_posts,$reverse,$group,$pages);
		echo $after_widget;
	}
	
	function widget_dropdown_comics_control(){
		load_webcomic_domain();
		
		$options = get_option('widget_dropdown_comics');
		
		if($_POST['dropdown-comics-submit']):
			$newoptions['title']   = strip_tags(stripslashes($_POST['dropdown-comics-title']));
			$newoptions['category'] = $_POST['dropdown-comics-category'];
			$newoptions['label']   = strip_tags(stripslashes($_POST['dropdown-comics-label']));
			$newoptions['numbers'] = $_POST['dropdown-comics-numbers'];
			$newoptions['reverse_posts'] = $_POST['dropdown-comics-reverse-posts'];
			$newoptions['reverse'] = $_POST['dropdown-comics-reverse'];
			$newoptions['group']   = $_POST['dropdown-comics-group'];
			$newoptions['pages']   = $_POST['dropdown-comics-pages'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_dropdown_comics',$options);
			endif;
		endif;
		
		$title         = htmlspecialchars($options['title'], ENT_QUOTES);
		$category      = $options['category'];
		$label         = $options['label'];
		$numbers       = $options['numbers'];
		$reverse_posts = $options['reverse_posts'];
		$reverse       = $options['reverse'];
		$group         = $options['group'];
		$pages         = $options['pages'];
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="dropdown-comics-title" name="dropdown-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Label: ','webcomic') ?><input type="text" class="widefat" id="dropdown-comics-label" name="dropdown-comics-label" value="<?php echo $label; ?>" /></label></p>
			<p><label><?php _e('Series:','webcomic') ?>
			<select name="dropdown-comics-category" id="dropdown-comics-category" class="widefat">
				<option value="0">All</option>
			<?php $collection = get_the_collection('hide_empty=0'); foreach($collection as $series): ?>
				<option value="<?php echo $series['id'] ?>"<?php if($category == $series['id']) echo ' selected="selected"'; ?>><?php echo $series['title'] ?></option>
			<?php endforeach; ?>
			</select>
			</label></p>
			<p>
				<label><?php _e('Group By:','webcomic') ?>
					<select id="dropdown-comics-group" name="dropdown-comics-group" class="widefat">
						<option value="0"<?php if(!$group) echo ' selected="selected"'; ?>><?php _e('None','webcomic') ?></option>
						<option value="1"<?php if(1==$group) echo ' selected="selected"'; ?>><?php _e('Chapters','webcomic') ?></option>
						<option value="2"<?php if(2==$group) echo ' selected="selected"'; ?>><?php _e('Volumes','webcomic') ?></option>
						<option value="3"<?php if(3==$group) echo ' selected="selected"'; ?>><?php _e('Series','webcomic') ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" id="dropdown-comics-numbers" name="dropdown-comics-numbers" value="1"<?php if($numbers) echo ' checked="checked"' ?> /> <?php _e('Automatically number comic posts and chapters','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-reverse-posts" name="dropdown-comics-reverse-posts" value="1"<?php if($reverse_posts) echo ' checked="checked"' ?> /> <?php _e('Show comic posts in reverse order','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-reverse" name="dropdown-comics-reverse" value="1"<?php if($reverse) echo ' checked="checked"' ?> /> <?php _e('Show volumes and chapters in reverse order','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-pages" name="dropdown-comics-pages" value="1"<?php if($pages) echo ' checked="checked"' ?> /> <?php _e('Show volume and chapter page counts','webcomic') ?></label></p>
			<input type="hidden" name="dropdown-comics-submit" id="dropdown-comics-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __('Displays a dropdown list of all comic posts.','webcomic'));
	wp_register_sidebar_widget('dropdown-comics',__('Dropdown Comics','webcomic'),'widget_dropdown_comics',$widget_ops);
	wp_register_widget_control('dropdown-comics',__('Dropdown Comics','webcomic'),'widget_dropdown_comics_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_dropdown_comics');

/**
 * Initializes, manages, and displays the Comic Archive widget.
 * 
 * This function initializes the Comic Archive widget. It includes the
 * function that displays the widget (widget_comic_archive) and the
 * function that manages the widget (widget_comic_archive_control).
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses dropdown_comics()
 */
function widget_init_comic_archive(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	load_webcomic_domain();
	
	function widget_comic_archive($args){
		extract($args);
		
		$options       = get_option('widget_comic_archive');
		$title         = $options['title'];
		$category      = $options['category'];
		$format        = $options['format'];
		$reverse_posts = $options['reverse_posts'];
		$reverse       = $options['reverse'];
		$descriptions  = $options['descriptions'];
		$pages         = $options['pages'];
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		comic_archive($category,$format,$reverse_posts,$reverse,$descriptions,$pages);
		echo $after_widget;
	}
	
	function widget_comic_archive_control(){
		load_webcomic_domain();
		
		$options = get_option('widget_comic_archive');
		
		if($_POST['comic-archive-submit']):
			$newoptions['title']         = $_POST['comic-archive-title'];
			$newoptions['category']      = $_POST['comic-archive-category'];
			$newoptions['format']        = $_POST['comic-archive-format'];
			$newoptions['reverse_posts'] = $_POST['comic-archive-reverse-posts'];
			$newoptions['reverse']       = $_POST['comic-archive-reverse'];
			$newoptions['descriptions']  = $_POST['comic-archive-descriptions'];
			$newoptions['pages']         = $_POST['comic-archive-pages'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_comic_archive', $options);
			endif;
		endif;
		
		$title         = htmlspecialchars($options['title'], ENT_QUOTES);
		$category      = $options['category'];
		$format        = $options['format'];
		$reverse_posts = $options['reverse_posts'];
		$reverse       = $options['reverse'];
		$descriptions  = $options['descriptions'];
		$pages         = $options['pages'];
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="comic-archive-title" name="comic-archive-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Series:','webcomic') ?>
			<select name="comic-archive-category" id="comic-archive-category" class="widefat">
				<option value="0">All</option>
			<?php $collection = get_the_collection('hide_empty=0'); foreach($collection as $series): ?>
				<option value="<?php echo $series['id'] ?>"<?php if($category == $series['id']) echo ' selected="selected"'; ?>><?php echo $series['title'] ?></option>
			<?php endforeach; ?>
			</select>
			</label></p>
			<p><label><?php _e('Format:','webcomic') ?>
			<select name="comic-archive-format" id="comic-archive-format" class="widefat">
				<option value=""><?php _e('Text','webcomic') ?></option>
				<option value="thumb"<?php if('thumb' == $format) echo ' selected="selected"'; ?>><?php _e('Thumbnail','webcomic') ?></option>
				<option value="medium"<?php if('medium' == $format) echo ' selected="selected"'; ?>><?php _e('Medium Image','webcomic') ?></option>
				<option value="large"<?php if('large' == $format) echo ' selected="selected"'; ?>><?php _e('Large Image','webcomic') ?></option>
				<option value="full"<?php if('full' == $format) echo ' selected="selected"'; ?>><?php _e('Full Image','webcomic') ?></option>
			</select>
			</label></p>
			<p><label><input type="checkbox" id="comic-archive-reverse-posts" name="comic-archive-reverse-posts" value="on"<?php if($reverse_posts) echo ' checked="checked"' ?> /> <?php _e('Show comic posts in reverse order','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-reverse" name="comic-archive-reverse" value="on"<?php if($reverse) echo ' checked="checked"' ?> /> <?php _e('Show series, volumes, and chapters in reverse order','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-descriptions" name="comic-archive-descriptions" value="on"<?php if($descriptions) echo ' checked="checked"' ?> /> <?php _e('Show series, volume, and chapter descriptions','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-pages" name="comic-archive-pages" value="on"<?php if($pages) echo ' checked="checked"' ?> /> <?php _e('Show series, volume, and chapter page counts','webcomic') ?></label></p>
			<input type="hidden" name="comic-archive-submit" id="comic-archive-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __('Displays your complete comic library organized by volume and chapter.','webcomic'));
	wp_register_sidebar_widget('comic-archive',__('Comic Archive','webcomic'),'widget_comic_archive',$widget_ops);
	wp_register_widget_control('comic-archive',__('Comic Archive','webcomic'),'widget_comic_archive_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_comic_archive');

/**
 * Initializes, manages, and displays a modified Recent Posts widget.
 * 
 * This function modified the standard Recent Posts widget so that it
 * ignores comic posts.
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses ignore_comics()
 */
function widget_webcomic_recent_posts_init(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	load_webcomic_domain();
	
	function widget_webcomic_recent_posts($args){
		extract($args);
		
		$options = get_option('widget_webcomic_recent_posts');
		$title = $options['title'];
		$number = ($options['number']) ? $options['number'] : 5;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		
		$posts = ignore_comics($number);
		if($posts->have_posts()):
			echo '<ul>';
			while($posts->have_posts()) : $posts->the_post();
				echo $before.'<li><a href="'.get_permalink().'" title="'.__('Permanent link to ','webcomic').get_the_title().'">'.get_the_title().'</a></li>'.$after;
			endwhile;
			echo '</ul>';
		else:
			_e('No recent posts to display.','webcomic');
		endif;
		echo $after_widget;
	}
	
	function widget_webcomic_recent_posts_control(){
		load_webcomic_domain();
		
		$options = get_option('widget_webcomic_recent_posts');
		
		if($_POST['webcomic-recent-posts-submit']):
			$newoptions['title'] = strip_tags(stripslashes($_POST['webcomic-recent-posts-title']));
			$newoptions['number'] = (int) $_POST['webcomic-recent-posts-number'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_webcomic_recent_posts', $options);
			endif;
		endif;
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$number = $options['number'];
		$number = $options['number'];
		if (!$number = (int) $options['number'])
			$number = 5;
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="webcomic-recent-posts-title" name="webcomic-recent-posts-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Number of posts to show: ','webcomic') ?><input name="webcomic-recent-posts-number" id="webcomic-recent-posts-number" style="width: 25px; text-align: center;" value="<?php echo $number ?>" /></label></p>
			<input type="hidden" name="webcomic-recent-posts-submit" id="webcomic-recent-posts-submit" value="1" />
		<?php
	}
	$widget_ops = array('description' => __('The most recent posts on your blog (ignores comic posts)','webcomic'));
	wp_register_sidebar_widget('recent-posts',__('Recent Posts','webcomic'),'widget_webcomic_recent_posts',$widget_ops);
	wp_register_widget_control('recent-posts',__('Recent Posts','webcomic'), 'widget_webcomic_recent_posts_control',$widget_ops);
}
add_action('widgets_init', 'widget_webcomic_recent_posts_init');
?>