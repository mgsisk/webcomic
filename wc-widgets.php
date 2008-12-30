<?php
//Initiates the Random Comic widget
function widget_init_random_comic(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	//Displays the Random Comic widget
	function widget_random_comic($args){
		extract($args);
		
		$options = get_option('widget_random_comic');
		$title = $options['title'];
		$format = ($options['format']) ? $options['format'] : 'full' ;
		$display = ('link' != $format) ? 'thumb' : false;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		echo '<p>';
		random_comic($format,$display);
		echo '</p>';
		echo $after_widget;
	}
	
	//Administrative controls for the Random Comic widget
	function widget_random_comic_control(){
		$options = get_option('widget_random_comic');
		
		if($_POST['random-comic-submit']):
			$newoptions['title'] = strip_tags(stripslashes($_POST['random-comic-title']));
			$newoptions['format'] = $_POST['random-comic-format'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_random_comic', $options);
			endif;
		endif;
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$format = $options['format'];
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="random-comic-title" name="random-comic-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Format:','webcomic') ?>
			<select name="random-comic-format" id="random-comic-format" class="widefat">
				<option value="full"<?php if('full' == $format) echo ' selected="selected"'; ?>><?php _e('Image','webcomic') ?></option>
				<option value="link"<?php if('link' == $format) echo ' selected="selected"'; ?>><?php _e('Text','webcomic') ?></option>
			</select>
			</label></p>
			<input type="hidden" name="random-comic-submit" id="random-comic-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __("Links to a single, randomly selected comic"));
	wp_register_sidebar_widget('random-comic',__('Random Comic','webcomic'),'widget_random_comic',$widget_ops);
	wp_register_widget_control('random-comic',__('Random Comic','webcomic'), 'widget_random_comic_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_random_comic');



//Initiates the Recent Comics widget
function widget_init_recent_comics(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	//Displays the Recent Comics widget
	function widget_recent_comics($args){
		extract($args);
		
		$options = get_option('widget_recent_comics');
		$title = $options['title'];
		$number = ($options['number']) ? $options['number'] : 5;
		$format = ($options['format']) ? $options['format'] : 'link';
		$display = ('link' != $format) ? 'thumb' : false;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		echo '<ul>';
		recent_comics($number,$format,$display);
		echo '</ul>';
		echo $after_widget;
	}
	
	//Administrative controls for the Recent Comics widget
	function widget_recent_comics_control(){
		$options = get_option('widget_recent_comics');
		
		if($_POST['recent-comics-submit']):
			$newoptions['title'] = strip_tags(stripslashes($_POST['recent-comics-title']));
			$newoptions['number'] = $_POST['recent-comics-number']; 
			$newoptions['format'] = intval($_POST['recent-comics-format']);
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_recent_comics', $options);
			endif;
		endif;
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$format = $options['format'];
		$number = $options['number'];
		if (!$number = (int) $options['number'])
			$number = 5;
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="recent-comics-title" name="recent-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Number of comics to show: ','webcomic') ?><input type="text" name="recent-comics-number" id="recent-comics-number" style="width: 25px; text-align: center;" value="<?php echo $number ?>" /></label></p>
			<p><label><?php _e('Format: ','webcomic') ?>
			<select name="recent-comics-format" id="random-comics-format" class="widefat"> 
				<option value="full"<?php if('full' == $format) echo ' selected="selected"'; ?>><?php _e('Image','webcomic') ?></option>
				<option value="link"<?php if('link' == $format) echo ' selected="selected"'; ?>><?php _e('Text','webcomic') ?></option>
			</select>
			</label></p>
			<input type="hidden" name="recent-comics-submit" id="recent-comics-submit" value="1" />
		<?php
	}
	$widget_ops = array('description' => __("The most recent comics posted to your site"));
	wp_register_sidebar_widget('recent-comics',__('Recent Comics','webcomic'),'widget_recent_comics',$widget_ops);
	wp_register_widget_control('recent-comics',__('Recent Comics','webcomic'), 'widget_recent_comics_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_recent_comics');



//Initiates the Comics Dropdown widget
function widget_init_dropdown_comics(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	//Displays the Comics Dropdown widget
	function widget_dropdown_comics($args){
		extract($args);
		
		$options = get_option('widget_dropdown_comics');
		$title   = $options['title'];
		$label   = $options['label'];
		$numbers = $options['numbers'];
		$pages   = $options['pages'];
		$reverse = $options['reverse'];
		switch($options['group']):
			case 2: $group = 'volumes'; break;
			case 1: $group = true;
		endswitch;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		dropdown_comics($label,$group,$numbers,$pages,$reverse);
		echo $after_widget;
	}
	
	//Administrative controls for the Comics Dropdown widget
	function widget_dropdown_comics_control(){
		$options = get_option('widget_dropdown_comics');
		
		if($_POST['dropdown-comics-submit']):
			$newoptions['title']   = strip_tags(stripslashes($_POST['dropdown-comics-title']));
			$newoptions['label']   = strip_tags(stripslashes($_POST['dropdown-comics-label']));
			$newoptions['numbers'] = $_POST['dropdown-comics-numbers'];
			$newoptions['pages']   = $_POST['dropdown-comics-pages'];
			$newoptions['group']   = $_POST['dropdown-comics-group'];
			$newoptions['reverse'] = $_POST['dropdown-comics-reverse'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_dropdown_comics',$options);
			endif;
		endif;
		
		$title   = htmlspecialchars($options['title'], ENT_QUOTES);
		$label   = $options['label'];
		$numbers = $options['numbers'];
		$pages   = $options['pages'];
		$group   = $options['group'];
		$reverse = $options['reverse'];
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="dropdown-comics-title" name="dropdown-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Label: ','webcomic') ?><input type="text" class="widefat" id="dropdown-comics-label" name="dropdown-comics-label" value="<?php echo $label; ?>" /></label></p>
			<p>
				<label><?php _e('Group By:','webcomic') ?>
					<select id="dropdown-comics-group" name="dropdown-comics-group">
						<option value="0"<?php if(!$group) echo ' selected="selected"'; ?>><?php _e('N/A','webcomic') ?></option>
						<option value="1"<?php if(1==$group) echo ' selected="selected"'; ?>><?php _e('Chapters','webcomic') ?></option>
						<option value="2"<?php if(2==$group) echo ' selected="selected"'; ?>><?php _e('Volumes','webcomic') ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" id="dropdown-comics-numbers" name="dropdown-comics-numbers" value="on"<?php if($numbers) echo ' checked="checked"' ?> /> <?php _e('Automatically number comics','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-pages" name="dropdown-comics-pages" value="on"<?php if($pages) echo ' checked="checked"' ?> /> <?php _e('Show volume and chapter page counts','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-reverse" name="dropdown-comics-reverse" value="on"<?php if($reverse) echo ' checked="checked"' ?> /> <?php _e('Show volumes, chapters, and pages in reverse order','webcomic') ?></label></p>
			<input type="hidden" name="dropdown-comics-submit" id="dropdown-comics-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __("Displays a dropdown list of all comic posts."));
	wp_register_sidebar_widget('dropdown-comics',__('Dropdown Comics','webcomic'),'widget_dropdown_comics',$widget_ops);
	wp_register_widget_control('dropdown-comics',__('Dropdown Comics','webcomic'),'widget_dropdown_comics_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_dropdown_comics');



//Initiates the Comic Archive widget
function widget_init_comic_archive(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	//Displays the Comic Archive widget
	function widget_comic_archive($args){
		extract($args);
		
		$options = get_option('widget_comic_archive');
		$title = $options['title'];
		$descriptions = $options['descriptions'];
		$pages = $options['pages'];
		$reverse = $options['reverse'];
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		comic_archive($descriptions,$pages,$reverse);
		echo $after_widget;
	}
	
	//Administrative controls for the Comic Archive widget
	function widget_comic_archive_control(){
		$options = get_option('widget_comic_archive');
		
		if($_POST['comic-archive-submit']):
			$newoptions['title'] = $_POST['comic-archive-title'];
			$newoptions['descriptions'] = $_POST['comic-archive-descriptions'];
			$newoptions['pages'] = $_POST['comic-archive-pages'];
			$newoptions['reverse'] = $_POST['comic-archive-reverse'];
			if($options != $newoptions):
				$options = $newoptions;
				update_option('widget_comic_archive', $options);
			endif;
		endif;
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$descriptions = $options['descriptions'];
		$pages = $options['pages'];
		$reverse = $options['reverse'];
		?>
			<p><label><?php _e('Title: ','webcomic') ?><input type="text" class="widefat" id="comic-archive-title" name="comic-archive-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><input type="checkbox" id="comic-archive-descriptions" name="comic-archive-descriptions" value="on"<?php if($descriptions) echo ' checked="checked"' ?> /> <?php _e('Show volume and chapter descriptions','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-pages" name="comic-archive-pages" value="on"<?php if($pages) echo ' checked="checked"' ?> /> <?php _e('Show volume and chapter page counts','webcomic') ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-reverse" name="comic-archive-reverse" value="on"<?php if($reverse) echo ' checked="checked"' ?> /> <?php _e('Show volumes, chapters, and pages in reverse order','webcomic') ?></label></p>
			<input type="hidden" name="comic-archive-submit" id="comic-archive-submit" value="1" />
		<?php
	}
	
	$widget_ops = array('description' => __("Displays your complete comic library organized by volume and chapter."));
	wp_register_sidebar_widget('comic-archive',__('Comic Archive','webcomic'),'widget_comic_archive',$widget_ops);
	wp_register_widget_control('comic-archive',__('Comic Archive','webcomic'),'widget_comic_archive_control',$widget_ops);
}
add_action('widgets_init', 'widget_init_comic_archive');



//Initiates the modified Recent Posts widget
function widget_webcomic_recent_posts_init(){
	if(!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	//Displays the modified Recent Posts widget
	function widget_webcomic_recent_posts($args){
		extract($args);
		
		$options = get_option('widget_webcomic_recent_posts');
		$title = $options['title'];
		$number = ($options['number']) ? $options['number'] : 5;
		
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		
		$posts = ignore_comics(true,$number);
		if($posts->have_posts()):
			echo '<ul>';
			while($posts->have_posts()) : $posts->the_post();
				echo $before;
				echo '<li><a href="';
				the_permalink();
				echo '" title="'.__('Permanent link to ','webcomic');
				the_title();
				echo '">';
				the_title();
				echo '</a></li>';
				echo $after;
			endwhile;
			echo '</ul>';
		else:
			_e('No recent posts to display.','webcomic');
		endif;
		echo $after_widget;
	}

	//Administrative controls for the modified Recent Posts widget
	function widget_webcomic_recent_posts_control(){
		$options = get_option('widget_webcomic_recent_posts');
		
		if($_POST['webcomic-recent-posts-submit']):
			$newoptions['title'] = strip_tags(stripslashes($_POST['webcomic-recent-posts-title']));
			$newoptions['number'] = intval($_POST['webcomic-recent-posts-number']);
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
			<p><label>Title: <input type="text" class="widefat" id="webcomic-recent-posts-title" name="webcomic-recent-posts-title" value="<?php echo $title; ?>" /></label></p>
			<p><label>Number of posts to show: <input name="webcomic-recent-posts-number" id="webcomic-recent-posts-number" style="width: 25px; text-align: center;" value="<?php echo $number ?>" /></label></p>
			<input type="hidden" name="webcomic-recent-posts-submit" id="webcomic-recent-posts-submit" value="1" />
		<?php
	}
	$widget_ops = array('description' => __("The most recent posts on your blog (ignores comic posts)"));
	wp_register_sidebar_widget('recent-posts','Recent Posts','widget_webcomic_recent_posts',$widget_ops);
	wp_register_widget_control('recent-posts','Recent Posts', 'widget_webcomic_recent_posts_control',$widget_ops);
}
add_action('widgets_init', 'widget_webcomic_recent_posts_init');
?>