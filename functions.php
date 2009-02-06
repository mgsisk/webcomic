<?php
/**
 * @package InkBlot
 * @since 1.0
 */
load_theme_textdomain('inkblot');
register_sidebars(2);

/**
 * Creates the InkBlot default settings.
 * 
 * This funciton should only run when the theme is first activated.
 * It will attempt to create all of the default InkBlots stetings.
 * 
 * @package InkBlot
 * @since 1.0
 */
if(!get_option('comic_width') || !get_option('comic_archive_format') || !get_option('comic_archive_images') || !get_option('comic_archive_images_size') || !get_option('comic_embed_code') || !get_option('comic_embed_code_size') || !get_option('comic_archive_descriptions') || !get_option('comic_archive_pages') || !get_option('comic_archive_reverse') || !get_option('inkblot_layout') || !get_option('inkblot_sidebar1_size') || !get_option('inkblot_sidebar1_position') || !get_option('inkblot_sidebar2_size') || !get_option('inkblot_sidebar2_position') || !get_option('inkblot_content_position') || !get_option('inkblot_site_position') || !get_option('comic_front_page')):
	function inkblot_set_defaults(){
		add_option('comic_width','800');
		add_option('comic_front_page','on');
		add_option('comic_archive_format','date');
		add_option('comic_archive_images','on');
		add_option('comic_archive_images_size','medium');
		add_option('comic_embed_code','off');
		add_option('comic_embed_code_size','medium');
		add_option('comic_archive_descriptions','off');
		add_option('comic_archive_pages','off');
		add_option('comic_archive_reverse','off');
		add_option('inkblot_layout','c2o');
		add_option('inkblot_sidebar1_size','standard');
		add_option('inkblot_sidebar1_position','right');
		add_option('inkblot_sidebar2_size','standard');
		add_option('inkblot_sidebar2_position','left');
		add_option('inkblot_content_position','center');
		add_option('inkblot_site_position','center');
		echo '<div id="comic-warning" class="updated fade"><p><strong>'.sprintf(__('Thanks for choosing InkBlot!</strong> Please check the <a href="%s">setting page</a> to configure the theme.','inkblot'),'themes.php?page=functions.php').'</p></div>';
	}
	add_action('admin_notices', 'inkblot_set_defaults');
endif;

/**
 * Enqueue the javascripts InkBlot relies on.
 * 
 * This function ensures that the necessary javascript (the jQuery
 * package and the 'scripts.js' file) are loaded to provide various
 * advanced effects.
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_init(){
	wp_enqueue_script('scripts',get_bloginfo('template_directory').'/scripts.js',array('jquery','jquery-form'));
}
add_action('init','inkblot_init');

/**
 * Outputs the necessary structural CSS
 * 
 * This function ensures that the necessary structural CSS is loaded
 * based on use settings.
 * 
 * @package InkBlot
 * @since 1.0
 * 
 * @uses inkblot_dimensions()
 * @uses inkblot_positions()
 */
function inkblot_head(){
	echo '<style type="text/css">#wrap{width:'.inkblot_dimensions().';'.inkblot_positions().'}#content{width:'.inkblot_dimensions('content').';'.inkblot_positions('content').'}#sidebar1{width:'.inkblot_dimensions('sidebar1').';'.inkblot_positions("sidebar1").'}#sidebar2{width:'.inkblot_dimensions('sidebar2').';'.inkblot_positions("sidebar2").'}</style>';
}
add_action('wp_head','inkblot_head');

/**
 * Displays a link to the WebComic & InkBlot site
 * 
 * This function displays a link pointing back to the WebComic & InkBlot site.
 * Show your support be keeping this link (or some kind of link) on your site;
 * it's the best form of advertising I can get.
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_meta(){
	echo '<li><a href="http://maikeruon.com/wcib/" title="'.__('Enhanced by WebComic and InkBlot','inkblot').'">'.__('WebComic &amp; InkBlot','inkblot').'</a></li>';
}
add_action('wp_meta','inkblot_meta');

/**
 * Powers the InkBlot dynamic layout.
 * 
 * This function enables the dynamic layout provided by InkBlot.
 * Based on user settings (and an option parameter), it outputs the necessary
 * opening <div> tags and sidebar calls.
 * 
 * @package InkBlot
 * @since 1.0
 * 
 * @param str $type 
 */
function inkblot_begin_content($type=false){
	if('i' ==  $type):
		if(strstr(get_option('inkblot_layout'),'i') || 'c1' == get_option('inkblot_layout')):
			if('center' == get_option('inkblot_content_position') && strstr(get_option('inkblot_layout'),'3')):
				if('left' == get_option('inkblot_sidebar1_position'))
					get_sidebar();
				else
					get_sidebar('2');
			endif;
			echo '<div id="content">';
		endif;
	else:
		if(strstr(get_option('inkblot_layout'),'o')):
			if(strstr(get_option('inkblot_layout'),'3') && 'center' == get_option('inkblot_content_position')):
				if('left' == get_option('inkblot_sidebar1_position'))
					get_sidebar();
				else
					get_sidebar('2');
			endif;
			echo '<div id="content"><div class="pad">';
		else:
			echo '<div class="pad">';
		endif;
	endif;
}

/**
 * Returns the specified block position formatted for CSS.
 * 
 * This function returns a CSS formated style rule for various block
 * positions based on user settings.
 * 
 * @package InkBlot
 * @since 1.0
 * 
 * @param str $type The block position to return.
 * @return str A CSS formatted string based on the specified block position.
 */
function inkblot_positions($type='site'){
	$layout   = get_option('inkblot_layout');
	$site     = get_option('inkblot_site_position');
	$content  = get_option('inkblot_content_position');
	$sidebar1 = get_option('inkblot_sidebar1_position');
	$sidebar2 = get_option('inkblot_sidebar2_position');
	
	if('c1' == $layout):
		return;
	elseif(strstr($layout,'2')):
		$content = ('right' == $sidebar1) ? 'float:left;' : 'float:right;';
	else:
		switch($content):
			case 'center':
			case 'left':  $content = 'float:left;'; break;
			case 'right': $content = 'float:right;';
		endswitch;
	endif;
	
	$sidebar1 = 'float:'.$sidebar1.';';
	$sidebar2 = 'float:'.$sidebar2.';';
	
	switch($site):
		case 'center': $site = 'margin:auto;'; break;
		case 'left':   $site =''; break;
		case 'right':  $site = 'margin:0 0 0 auto;';
	endswitch;
	
	switch($type):
		case 'site': return $site;
		case 'content': return $content;
		case 'sidebar1': return $sidebar1;
		case 'sidebar2': return $sidebar2;
	endswitch;
}

/**
 * Returns the specified block dimension formatted for CSS.
 * 
 * This function calculates and returns various block dimensions,
 * properly formatted for CSS, based on user settings.
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_dimensions($type='site'){
	$comic = get_option('comic_width');
	$layout = get_option('inkblot_layout');
	$sidebar1 = get_option('inkblot_sidebar1_size');
	$sidebar2 = get_option('inkblot_sidebar2_size');
	
	$ratio_standard = (strstr($layout,'2')) ? .38 : .25 ;
	$ratio_small = (strstr($layout,'2')) ? .333 : .2 ;
	$ratio_xsmall = (strstr($layout,'2')) ? .25 : .15 ;
	$ratio_xxsmall = (strstr($layout,'2')) ? .2 : .1 ;
	
	switch($sidebar1):
		case 'standard': $sidebar1_width = round($comic*$ratio_standard); break;
		case 'small': $sidebar1_width = round($comic*$ratio_small); break;
		case 'x-small': $sidebar1_width = round($comic*$ratio_xsmall); break;
		case 'xx-small': $sidebar1_width = round($comic*$ratio_xxsmall);
	endswitch;
	
	switch($sidebar2):
		case 'standard': $sidebar2_width = round($comic*$ratio_standard); break;
		case 'small': $sidebar2_width = round($comic*$ratio_small); break;
		case 'x-small': $sidebar2_width = round($comic*$ratio_xsmall); break;
		case 'xx-small': $sidebar2_width = round($comic*$ratio_xxsmall);
	endswitch;
	
	if('c1' == $layout || 'c2i' == $layout || 'c3i' == $layout)
		$content_width = $comic;
	elseif('c2o' == $layout)
		$content_width = $comic - $sidebar1_width;
	else
		$content_width = $comic - $sidebar1_width - $sidebar2_width;
	
	if('c1' == $layout || 'c2o' == $layout || 'c3o' == $layout)
		$site_width = $comic;
	elseif('c2i' == $layout)
		$site_width = $content_width + $sidebar1_width;
	else
		$site_width = $content_width + $sidebar1_width + $sidebar2_width;
	
	switch($type):
		case 'site': return $site_width.'px';
		case 'content': return $content_width.'px';
		case 'sidebar1': return $sidebar1_width.'px';
		case 'sidebar2':default: return $sidebar2_width.'px';
	endswitch;
}

/**
 * Displays the bookmark controls.
 * 
 * This funciton displays the bookmark controls. Note that this function
 * only outputs the XHTML; the necessary javascript required to make
 * these links work can be found in scripts.js:
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_bookmark(){
	global $post;
	
	if(is_home() || (is_single() && in_category(get_comic_category())))
		echo '<div id="inkblot-bookmark"><a class="bookmark-this" title="'.__('Saves your place so that you can continue reading from where you left off.','inkblot').'"><span>'.__('Bookmark','inkblot').'</span></a><a class="goto-bookmark" title="'.__('Return to your bookmarked comic.','inkblot').'"><span>'.__('Return','inkblot').'</span></a><a class="clear-bookmark" title="'.__('Remove your comic bookmark.','inkblot').'"><span>'.__('Clear','inkblot').'</span></a></div>';
}

/**
 * Initializes and displays the Comic Bookmark widget.
 * 
 * This funciton initializes and displays the Comic Bookmark
 * widget. It has no options.
 * 
 * @package InkBlot
 * @since 1.0
 * 
 * @uses inkblot_bookmark()
 */
function widget_init_inkblot_bookmark(){
	if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) return;
	
	function widget_inkblot_bookmark(){
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		inkblot_bookmark();
		echo $after_widget;
	}
	$widget_ops = array('description' => __("Displays controls that allow visitors to bookmark the current comic and return to it later.","inkblot"));
	wp_register_sidebar_widget('inkblot-bookmark','Comic Bookmark','widget_inkblot_bookmark',$widget_ops);
}
add_action('widgets_init', 'widget_init_inkblot_bookmark');

/**
 * Updates the theme settings.
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_theme_page(){
	if($_GET['page'] == basename(__FILE__)):
		if('inkblot_save_settings' == $_REQUEST['action']):
			check_admin_referer('inkblot_save_settings');
			
			$comic_front_page           = ($_REQUEST['comic_front_page']) ? 'on' : 'off' ;
			$comic_embed_code           = ($_REQUEST['comic_embed_code']) ? 'on' : 'off' ;
			$comic_archive_images       = ($_REQUEST['comic_archive_images']) ? 'on' : 'off' ;
			$comic_archive_descriptions = ($_REQUEST['comic_archive_descriptions']) ? 'on' : 'off' ;
			$comic_archive_pages        = ($_REQUEST['comic_archive_pages']) ? 'on' : 'off' ;
			$comic_archive_reverse      = ($_REQUEST['comic_archive_reverse']) ? 'on' : 'off' ;
			$inkblot_content_position   = ('center' == $_REQUEST['inkblot_content_position'] && $_REQUEST['inkblot_sidebar1_position'] == $_REQUEST['inkblot_sidebar2_position']) ? 'left' : $_REQUEST['inkblot_content_position'];
			
			update_option('comic_width',$_REQUEST['comic_width']);
			update_option('comic_front_page',$comic_front_page);
			update_option('comic_archive_format',$_REQUEST['comic_archive_format']);
			update_option('comic_embed_code',$comic_embed_code);
			update_option('comic_embed_code_size',$_REQUEST['comic_embed_code_size']);
			update_option('comic_archive_images',$comic_archive_images);
			update_option('comic_archive_images_size',$_REQUEST['comic_archive_images_size']);
			update_option('comic_archive_descriptions',$comic_archive_descriptions);
			update_option('comic_archive_pages',$comic_archive_pages);
			update_option('comic_archive_reverse',$comic_archive_reverse);
			update_option('inkblot_layout',$_REQUEST['inkblot_layout']);
			update_option('inkblot_sidebar1_size',$_REQUEST['inkblot_sidebar1_size']);
			update_option('inkblot_sidebar1_position',$_REQUEST['inkblot_sidebar1_position']);
			update_option('inkblot_sidebar2_size',$_REQUEST['inkblot_sidebar2_size']);
			update_option('inkblot_sidebar2_position',$_REQUEST['inkblot_sidebar2_position']);
			update_option('inkblot_content_position',$inkblot_content_position);
			update_option('inkblot_site_position',$_REQUEST['inkblot_site_position']);
		endif;
	endif;
	add_theme_page(__('InkBlot'), __('InkBlot'), 'edit_themes', basename(__FILE__), 'inkblot_theme_page_display');
}

/**
 * Displays the InkBlot settings page.
 * 
 * @package InkBlot
 * @since 1.0
 */
function inkblot_theme_page_display(){
	if (isset($_REQUEST['action']))
		echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
?>
	<div class="wrap">
		<div id="icon-themes" class="icon32"><br /></div>
		<h2><?php _e('InkBlot','inkblot') ?></h2>
		<form method="post" action="">
			<?php wp_nonce_field('inkblot_save_settings'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('Comic','inkblot') ?></th>
					<td><label><input type="checkbox" name="comic_front_page" id="comic_front_page" value="on"<?php if('on' == get_option('comic_front_page')) echo ' checked="checked"'; ?> /> <?php _e('Show the most recent comic on the front page','inkblot') ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_embed_code"><?php _e('Embed','inkblot') ?></label></th>
					<td><input type="checkbox" name="comic_embed_code" id="comic_embed_code" value="on"<?php if('on' == get_option('comic_embed_code')) echo ' checked="checked"'; ?> />
					<label><?php _e('Include comic embed code with','inkblot') ?>
						<select name="comic_embed_code_size">
							<option value="full"<?php if('full' == get_option('comic_embed_code_size')) echo ' selected="selected"'; ?>><?php _e('Full','inkblot') ?></option>
							<option value="large"<?php if('large' == get_option('comic_embed_code_size')) echo ' selected="selected"'; ?>><?php _e('Large','inkblot') ?></option>
							<option value="medium"<?php if('medium' == get_option('comic_embed_code_size')) echo ' selected="selected"'; ?>><?php _e('Medium','inkblot') ?></option>
							<option value="thumb"<?php if('thumb' == get_option('comic_embed_code_size')) echo ' selected="selected"'; ?>><?php _e('Thumbnail','inkblot') ?></option>
						</select>
					<?php _e('comic images on single comic pages','inkblot') ?></label></td>
				</tr>
			</table>
			<h3><?php _e('Archive','inkblot') ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_archive_images"><?php _e('Pages','inkblot') ?></label></th>
					<td><input type="checkbox" name="comic_archive_images" id="comic_archive_images" value="on"<?php if('on' == get_option('comic_archive_images')) echo ' checked="checked"'; ?> />
					<label><?php _e('Include','inkblot') ?>
						<select name="comic_archive_images_size">
							<option value="full"<?php if('full' == get_option('comic_archive_images_size')) echo ' selected="selected"'; ?>><?php _e('Full','inkblot') ?></option>
							<option value="large"<?php if('large' == get_option('comic_archive_images_size')) echo ' selected="selected"'; ?>><?php _e('Large','inkblot') ?></option>
							<option value="medium"<?php if('medium' == get_option('comic_archive_images_size')) echo ' selected="selected"'; ?>><?php _e('Medium','inkblot') ?></option>
							<option value="thumb"<?php if('thumb' == get_option('comic_archive_images_size')) echo ' selected="selected"'; ?>><?php _e('Thumbnail','inkblot') ?></option>
						</select>
					<?php _e('comic images on WordPress archive and search pages','inkblot') ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="comic_archive_format" value="date"<?php if('date' == get_option('comic_archive_format')) echo ' checked="checked"'; ?> /> <?php _e('Date','inkblot') ?></label></th>
					<td><?php _e('Your archive will be organized by year, month, and day.','inkblot') ?></td>
				</tr>
				<tr>
					<th><label><input type="radio" name="comic_archive_format" value="chapter"<?php if('chapter' == get_option('comic_archive_format')) echo ' checked="checked"'; if(!get_the_collection()) echo ' disabled="disabled"'; ?> /> <?php _e('Chapters','inkblot') ?></label></th>
					<td>
					<?php if(!get_the_collection()): ?>
						<?php _e('You must create volumes and chapters and assign comics to them before you can use the chapters archive.','inkblot') ?>
					<?php else: ?>
						<?php _e('Your archive will be organized by volume, chapter, and page.','inkblot') ?><br />
						<label><input type="checkbox" name="comic_archive_descriptions" id="comic_archive_descriptions" value="on"<?php if('on' == get_option('comic_archive_descriptions')) echo ' checked="checked"'; ?> /> <?php _e('Show volume and chapter descriptions','inkblot') ?></label><br />
						<label><input type="checkbox" name="comic_archive_pages" id="comic_archive_pages" value="on"<?php if('on' == get_option('comic_archive_pages')) echo ' checked="checked"'; ?> /> <?php _e('Show volume and chapter page counts','inkblot') ?></label><br />
						<label><input type="checkbox" name="comic_archive_reverse" id="comic_archive_reverse" value="on"<?php if('on' == get_option('comic_archive_reverse')) echo ' checked="checked"'; ?> /> <?php _e('Show volumes, chapters, and pages in reverse order','inkblot') ?></label>
					<?php endif; ?>
					</td>
				</tr>
			</table>
			<h3><?php _e('Layout','inkblot') ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_width"><?php _e('Comic Width','inkblot') ?></label></th>
					<td><input type="text" name="comic_width" id="comic_width" value="<?php echo get_option('comic_width'); ?>" class="small-text" /> <span class="setting-description"><?php _e('Enter the width of your comic in pixels.','inkblot') ?></span></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="inkblot_layout" value="c1"<?php if('c1' == get_option('inkblot_layout')) echo ' checked="checked"'; ?> /> <img src="<?php bloginfo('template_directory') ?>/images/inkblot/c1.png" alt="<?php _e('1 Columnn','inkblot') ?>" style="vertical-align: middle;" /></label></th>
					<td><?php _e('A one column linear layout that focuses entirely on the content.','inkblot') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="inkblot_layout" value="c2o"<?php if('c2o' == get_option('inkblot_layout')) echo ' checked="checked"'; ?> /> <img src="<?php bloginfo('template_directory') ?>/images/inkblot/c2o.png" alt="<?php _e('2 Columnn','inkblot') ?>" style="vertical-align: middle;" /></label></th>
					<td><?php _e('A two column layout that separates the comic from the rest of the site content.','inkblot') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="inkblot_layout" value="c3o"<?php if('c3o' == get_option('inkblot_layout')) echo ' checked="checked"'; ?> /> <img src="<?php bloginfo('template_directory') ?>/images/inkblot/c3o.png" alt="<?php _e('3 Columnn','inkblot') ?>" style="vertical-align: middle;" /></label></th>
					<td><?php _e('A three column layout that separates the comic from the rest of the site content.','inkblot') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="inkblot_layout" value="c2i"<?php if('c2i' == get_option('inkblot_layout')) echo ' checked="checked"'; ?> /> <img src="<?php bloginfo('template_directory') ?>/images/inkblot/c2i.png" alt="<?php _e('2 Columnn (Alternate)','inkblot') ?>" style="vertical-align: middle;" /></label></th>
					<td><?php _e('A two column layout that places the comic next to the sidebar.','inkblot') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="inkblot_layout" value="c3i"<?php if('c3i' == get_option('inkblot_layout')) echo ' checked="checked"'; ?> /> <img src="<?php bloginfo('template_directory') ?>/images/inkblot/c3i.png" alt="<?php _e('3 Columnn (Alternate)','inkblot') ?>" style="vertical-align: middle;" /></label></th>
					<td><?php _e('A three column layout that places the comic next to the sidebars.','inkblot') ?></td>
				</tr>
			</table>
			<table class="form-table">
				<tr>
					<td scope="col"></td>
					<td><h3><?php _e('Sidebar 1','inkblot') ?></h3></td>
					<td><h3><?php _e('Sidebar 2','inkblot') ?></h3></td>
					<td><h3><?php _e('Content','inkblot') ?></h3></td>
					<td style="width:20%;"><h3><?php _e('Site','inkblot') ?></h3></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Position','inkblot') ?></th>
					<td style="vertical-align:top">
					<fieldset>
						<legend class="hidden"><?php _e('Position','inkblot') ?></legend>
						<label><input type="radio" name="inkblot_sidebar1_position" value="right"<?php if('right' == get_option('inkblot_sidebar1_position')) echo ' checked="checked"'; ?> /> <?php _e('Right','inkblot') ?></label><br />
						<label><input type="radio" name="inkblot_sidebar1_position" value="left"<?php if('left' == get_option('inkblot_sidebar1_position')) echo ' checked="checked"'; ?> /> <?php _e('Left','inkblot') ?></label>
					</fieldset>
					</td>
					<td style="vertical-align:top">
						<fieldset>
							<legend class="hidden"><?php _e('Position','inkblot') ?></legend>
							<label><input type="radio" name="inkblot_sidebar2_position" value="right"<?php if('right' == get_option('inkblot_sidebar2_position')) echo ' checked="checked"'; ?> /> <?php _e('Right','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_sidebar2_position" value="left"<?php if('left' == get_option('inkblot_sidebar2_position')) echo ' checked="checked"'; ?> /> <?php _e('Left','inkblot') ?></label>
						</fieldset>   
					</td>
					<td>
						<fieldset>
							<legend class="hidden"><?php _e('Position','inkblot') ?></legend>
							<label><input type="radio" name="inkblot_content_position" value="right"<?php if('right' == get_option('inkblot_content_position')) echo ' checked="checked"'; ?> /> <?php _e('Right','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_content_position" value="left"<?php if('left' == get_option('inkblot_content_position')) echo ' checked="checked"'; ?> /> <?php _e('Left','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_content_position" value="center"<?php if('center' == get_option('inkblot_content_position')) echo ' checked="checked"' ?> /> <?php _e('Center','inkblot') ?></label>
						</fieldset>   
					</td>
					<td>
						<fieldset>
							<legend class="hidden"><?php _e('Position','inkblot') ?></legend>
							<label><input type="radio" name="inkblot_site_position" value="right"<?php if('right' == get_option('inkblot_site_position')) echo ' checked="checked"'; ?> /> <?php _e('Right','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_site_position" value="left"<?php if('left' == get_option('inkblot_site_position')) echo ' checked="checked"'; ?> /> <?php _e('Left','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_site_position" value="center"<?php if('center' == get_option('inkblot_site_position')) echo ' checked="checked"' ?> /> <?php _e('Center','inkblot') ?></label>
						</fieldset>   
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Size','inkblot') ?></th>
					<td>
					<fieldset>
						<legend class="hidden"><?php _e('Size','inkblot') ?></legend>
						<label><input type="radio" name="inkblot_sidebar1_size" value="standard"<?php if('standard' == get_option('inkblot_sidebar1_size')) echo ' checked="checked"'; ?> /> <?php _e('Standard','inkblot') ?></label><br />
						<label><input type="radio" name="inkblot_sidebar1_size" value="small"<?php if('small' == get_option('inkblot_sidebar1_size')) echo ' checked="checked"'; ?> /> <?php _e('Small','inkblot') ?></label><br />
						<label><input type="radio" name="inkblot_sidebar1_size" value="x-small"<?php if('x-small' == get_option('inkblot_sidebar1_size')) echo ' checked="checked"'; ?> /> <?php _e('X-Small','inkblot') ?></label><br />
						<label><input type="radio" name="inkblot_sidebar1_size" value="xx-small"<?php if('xx-small' == get_option('inkblot_sidebar1_size')) echo ' checked="checked"'; ?> /> <?php _e('XX-Small','inkblot') ?></label>
					</fieldset>
					</td>
					<td colspan="3">
						<fieldset>
							<legend class="hidden"><?php _e('Size','inkblot') ?></legend>
							<label><input type="radio" name="inkblot_sidebar2_size" value="standard"<?php if('standard' == get_option('inkblot_sidebar2_size')) echo ' checked="checked"'; ?> /> <?php _e('Standard','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_sidebar2_size" value="small"<?php if('small' == get_option('inkblot_sidebar2_size')) echo ' checked="checked"'; ?> /> <?php _e('Small','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_sidebar2_size" value="x-small"<?php if('x-small' == get_option('inkblot_sidebar2_size')) echo ' checked="checked"'; ?> /> <?php _e('X-Small','inkblot') ?></label><br />
							<label><input type="radio" name="inkblot_sidebar2_size" value="xx-small"<?php if('xx-small' == get_option('inkblot_sidebar2_size')) echo ' checked="checked"'; ?> /> <?php _e('XX-Small','inkblot') ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Width','inkblot') ?></th>
					<td><code><?php echo inkblot_dimensions('sidebar1') ?></code></td>
					<td><code><?php echo inkblot_dimensions('sidebar2') ?></code></td>
					<td><code><?php echo inkblot_dimensions('content') ?></code></td>
					<td><code><?php echo inkblot_dimensions() ?></code></td>
				</tr>
				<tr>
					<th></th>
					<td colspan="3"><span class="setting-description"><?php _e("The options for <strong>Sidebar 2</strong> and <strong>Content</strong> are only used in three column layouts. If you've made any changes to the layout settings remember to click <strong>Save Changes</strong> to update the <strong>Width</strong> information.","inkblot") ?></span></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes','inkblot') ?>" /><input type="hidden" name="action" value="inkblot_save_settings" /></p>
		</form>
	</div>
<?php
}
add_action('admin_menu', 'inkblot_theme_page');
?>