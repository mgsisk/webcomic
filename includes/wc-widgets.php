<?php
/**
 * This document contains all of the new widgets provided by WebComic.
 * 
 * @package WebComic
 * @since 1.0.0
 */

/**
 * Initializes and displays the Bookmark Comic widget.
 * 
 * @package @ebComic
 * @since 2.0.0
 * 
 * @uses bookmark_comic()
 */
function widget_init_bookmark_comic(){
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	
	load_webcomic_domain();
	
	/** Display the Random Comic Widget */
	function widget_bookmark_comic() {
		echo $before_widget;
		bookmark_comic();
		echo $after_widget;
		
		echo $output;
	}
	
	$widget_ops = array( 'description' => __( 'Displays comic bookmark links that allow readers to save their place and return later', 'webcomic' ) );
	wp_register_sidebar_widget( 'bookmark-comic', __( 'Bookmark Comic', 'webcomic' ), 'widget_bookmark_comic', $widget_ops );
} add_action( 'widgets_init', 'widget_init_bookmark_comic' );

/**
 * Initializes, manages, and displays the Random Comic widget.
 * 
 * This function initializes the Random Comic widget. It includes the
 * function that displays the widget (widget_random_comic) and the
 * function that manages the widget (widget_random_comic_control).
 * 
 * @package @ebComic
 * @since 1.0.0
 * 
 * @uses random_comic_link()
 */
function widget_init_random_comic(){
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	load_webcomic_domain();
	
	/** Display the Random Comic Widget */
	function widget_random_comic( $args ) {
		extract( $args );
		
		$options = get_option( 'widget_random_comic' );
		
		echo $before_widget;
		if ( !empty( $options[ 'title' ] ) )	echo $before_title . $options[ 'title' ] . $after_title;
		random_comic_link( $options[ 'label' ], $options[ 'limit' ] );
		echo $after_widget;
		
		echo $output;
	}
	
	/** Display settings for the Random Comic Widget */
	function widget_random_comic_control(){
		load_webcomic_domain();
		
		$options = get_option( 'widget_random_comic' );
		
		if ( $_POST[ 'random-comic-submit' ] ) {
			$newoptions[ 'title']  = strip_tags( stripslashes( $_POST[ 'random-comic-title' ] ) );
			$newoptions[ 'label'] = $_POST[ 'random-comic-label' ];
			$newoptions[ 'limit'] = $_POST[ 'random-comic-limit' ];
			
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option( 'widget_random_comic', $options );
			}
		}
		
		$title = htmlspecialchars( $options[ 'title' ], ENT_QUOTES );
		$label = $options[ 'label' ];
		$limit = $options[ 'limit' ];
		?>
			<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" class="widefat" id="random-comic-title" name="random-comic-title" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Format:', 'webcomic' ); ?>
					<select name="random-comic-label" id="random-comic-label" class="widefat">
						<option value=""><?php _e( 'Text', 'webcomic' ); ?></option>
						<option value="thumb"<?php if ( 'thumb' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Thumbnail', 'webcomic' ); ?></option>
						<option value="medium"<?php if ( 'medium' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Medium Image', 'webcomic' ); ?></option>
						<option value="large"<?php if ( 'large' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Large Image', 'webcomic' ); ?></option>
						<option value="full"<?php if ( 'full' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Full Image', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="random-comic-limit" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $limit == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<input type="hidden" name="random-comic-submit" id="random-comic-submit" value="1" />
		<?php
	}
	
	$widget_ops = array( 'description' => __( 'Displays a link to a single, randomly selected comic', 'webcomic' ) );
	wp_register_sidebar_widget( 'random-comic', __( 'Random Comic', 'webcomic' ), 'widget_random_comic', $widget_ops );
	wp_register_widget_control( 'random-comic', __( 'Random Comic', 'webcomic' ), 'widget_random_comic_control', $widget_ops );
} add_action( 'widgets_init', 'widget_init_random_comic' );

/**
 * Initializes, manages, and displays the Recent Comics widget.
 * 
 * This function initializes the Recent Comics widget. It includes the
 * function that displays the widget (widget_recent_comics) and the
 * function that manages the widget (widget_recent_comics_control).
 * 
 * @package @ebComic
 * @since 1.0.0
 * 
 * @uses recent_comics()
 */
function widget_init_recent_comics(){
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	load_webcomic_domain();
	
	function widget_recent_comics( $args ) {
		extract( $args );
		
		$options = get_option( 'widget_recent_comics' );
		
		echo $before_widget;
		if ( !empty( $options[ 'title' ] ) )	echo $before_title . $options[ 'title' ] . $after_title;
		echo '<ul>';
		recent_comics( $options[ 'number' ], $options[ 'format' ], $options[ 'limit' ] );
		echo '</ul>' . $after_widget;
	}
	
	function widget_recent_comics_control() {
		load_webcomic_domain();
		
		$options = get_option( 'widget_recent_comics' );
		
		if ( $_POST[ 'recent-comics-submit' ] ) {
			$newoptions[ 'title' ]  = strip_tags( stripslashes( $_POST[ 'recent-comics-title' ] ) );
			$newoptions[ 'number' ] = ( int ) $_POST[ 'recent-comics-number' ];
			$newoptions[ 'format' ] = $_POST[ 'recent-comics-format' ];
			$newoptions[ 'limit' ]  = $_POST[ 'recent-comics-limit' ];
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option( 'widget_recent_comics', $options );
			}
		}
		
		$title   = htmlspecialchars( $options[ 'title' ], ENT_QUOTES );
		$format = $options[ 'format' ];
		$limit  = $options[ 'limit' ];
		
		if ( !$options[ 'number' ] )
			$number = 5;
		else
			$number = $options[ 'number' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ); ?><input type="text" class="widefat" id="recent-comics-title" name="recent-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Number of comics to show: ', 'webcomic'); ?><input type="text" name="recent-comics-number" id="recent-comics-number" style="width: 25px; text-align: center;" value="<?php echo $number; ?>" /></label></p>
			<p>
				<label><?php _e( 'Format:', 'webcomic' ); ?>
					<select name="recent-comics-format" id="recent-comics-format" class="widefat">
						<option value=""><?php _e( 'Text', 'webcomic' ); ?></option>
						<option value="thumb"<?php if ( 'thumb' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Thumbnails', 'webcomic' ); ?></option>
						<option value="medium"<?php if ( 'medium' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Medium Images', 'webcomic' ); ?></option>
						<option value="large"<?php if ( 'large' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Large Images', 'webcomic' ); ?></option>
						<option value="full"<?php if ( 'full' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Full Images', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label><?php _e( 'Series:', 'webcomic' ); ?>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="recent-comics-limit" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $limit == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<input type="hidden" name="recent-comics-submit" id="recent-comics-submit" value="1" />
		<?php
	}
	
	$widget_ops = array( 'description' => __( 'Displays a list of recently posted comics', 'webcomic' ) );
	wp_register_sidebar_widget( 'recent-comics', __( 'Recent Comics', 'webcomic'), 'widget_recent_comics', $widget_ops );
	wp_register_widget_control( 'recent-comics', __( 'Recent Comics', 'webcomic'), 'widget_recent_comics_control', $widget_ops );
} add_action( 'widgets_init', 'widget_init_recent_comics' );

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
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	load_webcomic_domain();
	
	function widget_dropdown_comics( $args ) {
		extract( $args );
		
		$options = get_option( 'widget_dropdown_comics' );
		$args    = ( $options[ 'label' ] ) ? 'label=' . $options[ 'label' ] : 'label=' . __( 'Quick Archive', 'webcomic' );
		$args   .= ( $options[ 'post_order' ] ) ? '&post_order=ASC' : '';
		$args   .= '&number=' . $options[ 'number' ];
		$args   .= '&series=' . $options[ 'series' ];
		$args   .= '&group=' . $options[ 'groupby' ];
		$args   .= '&orderby=' . $options[ 'orderby' ];
		$args   .= ( $options[ 'order' ] ) ? '&order=DESC' : '';
		$args   .= '&bound=' . $options[ 'bound' ];
		$args   .= '&pages=' . $options[ 'pages' ];
		
		echo $before_widget;
		if ( !empty( $options[ 'title' ] ) ) echo $before_title . $options[ 'title' ] . $after_title;
		dropdown_comics( $args );
		echo $after_widget;
	}
	
	function widget_dropdown_comics_control(){
		load_webcomic_domain();
		
		$options = get_option( 'widget_dropdown_comics' );
		
		if ( $_POST[ 'dropdown-comics-submit' ] ) {
			$newoptions[ 'title' ]      = strip_tags( stripslashes( $_POST[ 'dropdown-comics-title' ] ) );
			$newoptions[ 'label' ]      = strip_tags( stripslashes ($_POST[ 'dropdown-comics-label' ] ) );
			$newoptions[ 'post_order' ] = $_POST[ 'dropdown-comics-post-order' ];
			$newoptions[ 'number' ]     = $_POST[ 'dropdown-comics-number' ];
			$newoptions[ 'series' ]     = $_POST[ 'dropdown-comics-series' ];
			$newoptions[ 'groupby' ]      = $_POST[ 'dropdown-comics-groupby' ];
			$newoptions[ 'orderby' ]    = $_POST[ 'dropdown-comics-orderby' ];
			$newoptions[ 'bound' ]      = $_POST[ 'dropdown-comics-bound' ];
			$newoptions[ 'order' ]      = $_POST[ 'dropdown-comics-order' ];
			$newoptions[ 'pages' ]      = $_POST[ 'dropdown-comics-pages' ];
			
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option( 'widget_dropdown_comics', $options );
			}
		}
		
		$title      = htmlspecialchars( $options[ 'title' ], ENT_QUOTES );
		$label      = $options[ 'label' ];
		$post_order = $options[ 'post_order' ];
		$number     = $options[ 'number' ];
		$series     = $options[ 'series' ];
		$groupby    = $options[ 'groupby' ];
		$orderby    = $options[ 'orderby' ];
		$order      = $options[ 'order' ];
		$bound      = $options[ 'bound' ];
		$pages      = $options[ 'pages' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ) ?><input type="text" class="widefat" id="dropdown-comics-title" name="dropdown-comics-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Label: ', 'webcomic' ) ?><input type="text" class="widefat" id="dropdown-comics-label" name="dropdown-comics-label" value="<?php echo $label; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="dropdown-comics-series" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $series == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Group By:','webcomic') ?>
					<select id="dropdown-comics-groupby" name="dropdown-comics-groupby" class="widefat">
						<option value="0"<?php if ( !$groupby ) echo ' selected="selected"'; ?>><?php _e( 'None', 'webcomic' ); ?></option>
						<option value="chapter"<?php if ( 'chapter' == $groupby ) echo ' selected="selected"'; ?>><?php _e( 'Chapters', 'webcomic' ); ?></option>
						<option value="volume"<?php if ( 'volume' == $groupby ) echo ' selected="selected"'; ?>><?php _e( 'Volumes', 'webcomi' ); ?></option>
						<option value="series"<?php if ( 'series' == $groupby ) echo ' selected="selected"'; ?>><?php _e( 'Series', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Order Chapters By:','webcomic') ?>
					<select id="dropdown-comics-orderby" name="dropdown-comics-orderby" class="widefat">
						<option value="id"<?php if ( 'id' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Default', 'webcomic' ); ?></option>
						<option value="name"<?php if ( 'name' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Title', 'webcomic' ); ?></option>
						<option value="count"<?php if ( 'count' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Page Count', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Chapters Should Link To:','webcomic') ?>
					<select id="dropdown-comics-bound" name="dropdown-comics-bound" class="widefat">
						<option value="first"<?php if ( 'first' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'First Comic', 'webcomic' ); ?></option>
						<option value="last"<?php if ( 'last' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Last Comic', 'webcomic' ); ?></option>
						<option value="page"<?php if ( 'page' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Archive Page', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" id="dropdown-comics-number" name="dropdown-comics-number" value="1"<?php if ( $number ) echo ' checked="checked"' ?> /> <?php _e( 'Automatically number posts and chapters', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-post-order" name="dropdown-comics-post-order" value="1"<?php if ( $post_order ) echo ' checked="checked"' ?> /> <?php _e( 'Show posts in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-order" name="dropdown-comics-order" value="1"<?php if ( $order ) echo ' checked="checked"' ?> /> <?php _e( 'Show volumes and chapters in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="dropdown-comics-pages" name="dropdown-comics-pages" value="1"<?php if ( $pages ) echo ' checked="checked"' ?> /> <?php _e( 'Show volume and chapter page counts', 'webcomic' ); ?></label></p>
			<input type="hidden" name="dropdown-comics-submit" id="dropdown-comics-submit" value="1" />
		<?php
	}
	
	$widget_ops = array( 'description' => __( 'Displays a dropdown list of comic posts', 'webcomic' ) );
	wp_register_sidebar_widget( 'dropdown-comics', __( 'Dropdown Comics', 'webcomic' ), 'widget_dropdown_comics', $widget_ops );
	wp_register_widget_control( 'dropdown-comics', __( 'Dropdown Comics', 'webcomic' ), 'widget_dropdown_comics_control', $widget_ops );
} add_action( 'widgets_init', 'widget_init_dropdown_comics' );

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
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	load_webcomic_domain();
	
	function widget_comic_archive( $args ) {
		extract( $args );
		
		$options = get_option( 'widget_comic_archive' );
		$args    = 'group=' . $options[ 'groupby' ];
		$args   .= '&format=' . $options[ 'format' ];
		$args   .= ( $options[ 'post_order' ] ) ? '&post_order=ASC' : '';
		$args   .= '&series=' . $options[ 'series' ];
		$args   .= '&orderby=' . $options[ 'orderby' ];
		$args   .= ( $options[ 'order' ] ) ? '&order=DESC' : '';
		$args   .= '&bound=' . $options[ 'bound' ];
		$args   .= '&descriptions=' . $options[ 'descriptions' ];
		$args   .= '&pages=' . $options[ 'pages' ];
		
		echo $before_widget;
		if ( !empty( $options[ 'title' ] ) ) echo $before_title . $options[ 'title' ] . $after_title;
		comic_archive( $args );
		echo $after_widget;
	}
	
	function widget_comic_archive_control() {
		load_webcomic_domain();
		
		$options = get_option( 'widget_comic_archive' );
		
		if ( $_POST[ 'comic-archive-submit' ] ) {
			$newoptions[ 'title' ]        = strip_tags( stripslashes( $_POST[ 'comic-archive-title' ] ) );
			$newoptions[ 'groupby' ]      = $_POST[ 'comic-archive-groupby' ];
			$newoptions[ 'format' ]       = $_POST[ 'comic-archive-format' ];
			$newoptions[ 'post_order' ]   = $_POST[ 'comic-archive-post-order' ];
			$newoptions[ 'series' ]       = $_POST[ 'comic-archive-series' ];
			$newoptions[ 'orderby' ]      = $_POST[ 'comic-archive-orderby' ];
			$newoptions[ 'order' ]        = $_POST[ 'comic-archive-order' ];
			$newoptions[ 'bound' ]        = $_POST[ 'comic-archive-bound' ];
			$newoptions[ 'descriptions' ] = $_POST[ 'comic-archive-descriptions' ];
			$newoptions[ 'pages' ]        = $_POST[ 'comic-archive-pages' ];
			
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option( 'widget_comic_archive', $options );
			}
		}
		
		$title        = htmlspecialchars( $options[ 'title' ], ENT_QUOTES );
		$groupby      = $options[ 'groupby' ];
		$format       = $options[ 'format' ];
		$post_order   = $options[ 'post_order' ];
		$series       = $options[ 'series' ];
		$orderby      = $options[ 'orderby' ];
		$order        = $options[ 'order' ];
		$bound        = $options[ 'bound' ];
		$descriptions = $options[ 'descriptions' ];
		$pages        = $options[ 'pages' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ) ?><input type="text" class="widefat" id="comic-archive-title" name="comic-archive-title" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Group Comics By:', 'webcomic' ) ?>
					<select name="comic-archive-groupby" id="comic-archive-groupby" class="widefat">
						<option value="date"><?php _e( 'Year, Month, and Day', 'webcomic' ); ?></option>
						<option value="chapter"<?php if ( 'chapter' == $groupby ) echo ' selected="selected"'; ?>><?php _e( 'Series, Volume, and Chapter', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Display Links As:', 'webcomic' ) ?>
					<select name="comic-archive-format" id="comic-archive-format" class="widefat">
						<option value=""><?php _e( 'Text', 'webcomic' ); ?></option>
						<option value="number"<?php if ( 'number' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Numbers (Chapters Only)', 'webcomic' ); ?></option>
						<option value="thumb"<?php if ( 'thumb' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Thumbnail Images', 'webcomic' ); ?></option>
						<option value="medium"<?php if ( 'medium' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Medium Images', 'webcomic' ); ?></option>
						<option value="large"<?php if ( 'large' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Large Images', 'webcomic' ); ?></option>
						<option value="full"<?php if ( 'full' == $format ) echo ' selected="selected"'; ?>><?php _e( 'Full Images', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="comic-archive-series" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $series == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Order Chapters By:','webcomic') ?>
					<select id="comic-archive-orderby" name="comic-archive-orderby" class="widefat">
						<option value="id"<?php if ( 'id' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Default', 'webcomic' ); ?></option>
						<option value="name"<?php if ( 'name' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Title', 'webcomic' ); ?></option>
						<option value="count"<?php if ( 'count' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Page Count', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Chapters Should Link To:','webcomic') ?>
					<select id="comic-archive-bound" name="comic-archive-bound" class="widefat">
						<option value="first"<?php if ( 'first' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'First Comic', 'webcomic' ); ?></option>
						<option value="last"<?php if ( 'last' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Last Comic', 'webcomic' ); ?></option>
						<option value="page"<?php if ( 'page' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Archive Page', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" id="comic-archive-post-order" name="comic-archive-post-order" value="1"<?php if ( $post_order ) echo ' checked="checked"' ?> /> <?php _e( 'Show posts in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-order" name="comic-archive-order" value="1"<?php if ( $order ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volumes, and chapters in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-descriptions" name="comic-archive-descriptions" value="1"<?php if ( $descriptions ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volume, and chapter descriptions', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" id="comic-archive-pages" name="comic-archive-pages" value="1"<?php if ( $pages ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volume, and chapter page counts', 'webcomic' ); ?></label></p>
			<input type="hidden" name="comic-archive-submit" id="comic-archive-submit" value="1" />
		<?php
	}
	
	$widget_ops = array( 'description' => __( 'Displays an archive of your comics', 'webcomic' ) );
	wp_register_sidebar_widget( 'comic-archive', __( 'Comic Archive', 'webcomic' ), 'widget_comic_archive', $widget_ops );
	wp_register_widget_control( 'comic-archive', __( 'Comic Archive', 'webcomic' ), 'widget_comic_archive_control', $widget_ops );
} add_action( 'widgets_init', 'widget_init_comic_archive' );

/**
 * Initializes, manages, and displays a modified Recent Posts widget.
 * 
 * This function alters the standard Recent Posts widget so that it
 * ignores comic posts.
 * 
 * @package @ebComic
 * @since 1.0
 * 
 * @uses ignore_comics()
 */
function widget_webcomic_recent_posts_init(){
	if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) ) return;
	load_webcomic_domain();
	
	function widget_webcomic_recent_posts( $args ) {
		extract( $args );
		
		$options             = get_option( 'widget_webcomic_recent_posts' );
		$options[ 'number' ] = ( $options[ 'number' ] ) ? $options[ 'number' ] : 5;
		$posts               = ignore_comics( $options[ 'number' ] );
		
		echo $before_widget;
		if ( !empty( $options[ 'title' ] ) )
			echo $before_title . $options[ 'title' ] . $after_title;
		if ( $posts->have_posts() ) :
			echo '<ul>';
			while ( $posts->have_posts() ) :
				$posts->the_post();
				echo '<li><a href="' . get_permalink() . '" title="'.__('Permanent link to ','webcomic') . the_title_attribute( 'echo=0' ) . '">' . the_title_attribute( 'echo=0' ) . '</a></li>';
			endwhile;
			echo '</ul>';
		endif;
		echo $after_widget;
	}
	
	function widget_webcomic_recent_posts_control() {
		load_webcomic_domain();
		
		$options = get_option( 'widget_webcomic_recent_posts' );
		
		if ( $_POST[ 'webcomic-recent-posts-submit' ] ) {
			$newoptions[ 'title' ] = strip_tags( stripslashes( $_POST[ 'webcomic-recent-posts-title' ] ) );
			$newoptions[ 'number' ] = ( int ) $_POST[ 'webcomic-recent-posts-number' ];
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option( 'widget_webcomic_recent_posts', $options );
			}
		}
		
		$title  = htmlspecialchars( $options[ 'title' ], ENT_QUOTES );
		if ( !$options[ 'number' ] )
			$number = 5;
		else
			$number = $options[ 'number' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ); ?><input type="text" class="widefat" id="webcomic-recent-posts-title" name="webcomic-recent-posts-title" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Number of posts to show: ', 'webcomic' ); ?><input name="webcomic-recent-posts-number" id="webcomic-recent-posts-number" style="width: 25px; text-align: center;" value="<?php echo $number; ?>" /></label></p>
			<input type="hidden" name="webcomic-recent-posts-submit" id="webcomic-recent-posts-submit" value="1" />
		<?php
	}
	$widget_ops = array( 'description' => __( 'The most recent posts on your blog (ignores comic posts)', 'webcomic' ) );
	wp_register_sidebar_widget( 'recent-posts', __( 'Recent Posts', 'webcomic' ), 'widget_webcomic_recent_posts', $widget_ops );
	wp_register_widget_control( 'recent-posts', __( 'Recent Posts', 'webcomic' ), 'widget_webcomic_recent_posts_control', $widget_ops );
} add_action( 'widgets_init', 'widget_webcomic_recent_posts_init' );
?>