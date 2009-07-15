<?php
/**
 * This document contains all of the new widgets provided by Webcomic.
 * 
 * @package Webcomic
 * @since 1.0.0
 */
 
/**
 * Registers the Webcomic widgets.
 * 
 * @package Webcomic
 * @since 2.1.0
 */
function webcomic_widgets_init() {
	register_widget( 'WP_Widget_Bookmark_Comic' );
	register_widget( 'WP_Widget_Comic_Buffer' );
	register_widget( 'WP_Widget_Random_Comic' );
	register_widget( 'WP_Widget_Recent_Comics' );
	register_widget( 'WP_Widget_Dropdown_Comics' );
	register_widget( 'WP_Widget_Comic_Archive' );
	register_widget( 'WP_Widget_Webcomic_Recent_Posts' );
} add_action( 'widgets_init', 'webcomic_widgets_init' );

/**
 * Initializes and displays the Bookmark Comic widget.
 * 
 * @package @ebComic
 * @since 2.0.0
 * 
 * @uses bookmark_comic()
 */
class WP_Widget_Bookmark_Comic extends WP_Widget {
	//constructor
	function WP_Widget_Bookmark_Comic() {
		$widget_ops = array( 'description' => __( 'Displays comic bookmark links that allow readers to save their place and return later', 'webcomic' ) );
		$this->WP_Widget( 'bookmark-comic', __( 'Bookmark Comic', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		echo $before_widget;
		bookmark_comic();
		echo $after_widget;
	}
}

/**
 * Initializes, manages, and displays the Comic Buffer widget.
 * 
 * @package @ebComic
 * @since 2.1.0
 * 
 * @uses the_comic_buffer()
 */
class WP_Widget_Comic_Buffer extends WP_Widget {
	//constructor
	function WP_Widget_Comic_Buffer() {
		$widget_ops = array( 'description' => __( 'Displays buffer comic information', 'webcomic' ) );
		$this->WP_Widget( 'comic-buffer', __( 'Comic Buffer', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		the_comic_buffer( $instance[ 'type' ], $instance[ 'series' ] );
		echo $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ]  = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'type' ]   = $new[ 'type' ];
		$instance[ 'series' ] = $new[ 'series' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Buffer Comics', 'webcomic'), 'type' => 'count', 'series' => 0 ) );
		
		$title  = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$type   = $instance[ 'type' ];
		$series = $instance[ 'series' ];
		?>
			<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Type:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat">
						<option value="count"><?php _e( 'Count', 'webcomic' ); ?></option>
						<option value="date"<?php if ( 'date' == $type ) echo ' selected="selected"'; ?>><?php _e( 'Date', 'webcomic' ); ?></option>
						<option value="datetime"<?php if ( 'datetime' == $type ) echo ' selected="selected"'; ?>><?php _e( 'Date and Time', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'series' ); ?>" class="widefat">
						<option value="0"><?php _e( 'Current', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $series == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

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
class WP_Widget_Random_Comic extends WP_Widget {
	//contructor
	function WP_Widget_Random_Comic() {
		load_webcomic_domain();
		
		$widget_ops = array( 'description' => __( 'Displays a link to a single, randomly selected comic', 'webcomic' ) );
		$this->WP_Widget( 'random-comic', __( 'Random Comic', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		random_comic_link( $instance[ 'label' ], $instance[ 'limit' ] );
		echo $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ] = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ] = $new[ 'label' ];
		$instance[ 'limit' ] = $new[ 'limit' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Random Comic', 'webcomic'), 'label' => '', 'limit' => 0 ) );
		
		$title = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$label = $instance[ 'label' ];
		$limit = $instance[ 'limit' ];
		?>
			<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Format:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'label' ); ?>" class="widefat">
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
					<select name="<?php echo $this->get_field_name( 'limit' ); ?>" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $limit == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

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
class WP_Widget_Recent_Comics extends WP_Widget {
	//constructor
	function WP_Widget_Recent_Comics() {
		load_webcomic_domain();
		
		$widget_ops = array( 'description' => __( 'Displays a list of recently posted comics', 'webcomic' ) );
		$this->WP_Widget( 'recent-comics', __( 'Recent Comics', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		echo '<ul>';
		recent_comics( $instance[ 'number' ], $instance[ 'label' ], $instance[ 'limit' ] );
		echo '</ul>' . $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ]  = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'number' ] = ( int ) $new[ 'number' ];
		$instance[ 'label' ]  = $new[ 'label' ];
		$instance[ 'limit' ]  = $new[ 'limit' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Recent Comics', 'webcomic' ), 'number' => 5, 'label' => '', 'limit' => 0 ) );
		
		$title  = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$number = $instance[ 'number' ];
		$label  = $instance[ 'label' ];
		$limit  = $instance[ 'limit' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ); ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Number of comics to show: ', 'webcomic'); ?><input type="text" name="<?php echo $this->get_field_name( 'number' ); ?>" style="width: 25px; text-align: center;" value="<?php echo $number; ?>" /></label></p>
			<p>
				<label><?php _e( 'Label:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'label' ); ?>" class="widefat">
						<option value=""><?php _e( 'Text', 'webcomic' ); ?></option>
						<option value="thumb"<?php if ( 'thumb' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Thumbnails', 'webcomic' ); ?></option>
						<option value="medium"<?php if ( 'medium' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Medium Images', 'webcomic' ); ?></option>
						<option value="large"<?php if ( 'large' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Large Images', 'webcomic' ); ?></option>
						<option value="full"<?php if ( 'full' == $label ) echo ' selected="selected"'; ?>><?php _e( 'Full Images', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label><?php _e( 'Series:', 'webcomic' ); ?>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'limit' ); ?>" class="widefat">
						<option value="0"><?php _e( 'All', 'webcomic' ); ?></option>
					<?php $categories = get_comic_category( 'all' ); foreach ( $categories as $cat ) { ?>
						<option value="<?php echo $cat ?>"<?php if ( $limit == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
					<?php } ?>
					</select>
				</label>
			</p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php	
	}
}

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
class WP_Widget_Dropdown_Comics extends WP_Widget {
	//constructor
	function WP_Widget_Dropdown_Comics() {
		load_webcomic_domain();
		
		$widget_ops = array( 'description' => __( 'Displays a dropdown list of comic posts', 'webcomic' ) );
		$this->WP_Widget( 'dropdown-comics', __( 'Dropdown Comics', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		load_webcomic_domain();
		
		extract( $args );
		
		$args    = ( $instance[ 'label' ] ) ? 'label=' . $instance[ 'label' ] : 'label=' . __( 'Quick Archive', 'webcomic' );
		$args   .= ( $instance[ 'post_order' ] ) ? '&post_order=ASC' : '';
		$args   .= '&number=' . $instance[ 'number' ];
		$args   .= '&series=' . $instance[ 'series' ];
		$args   .= '&groupby=' . $instance[ 'groupby' ];
		$args   .= '&orderby=' . $instance[ 'orderby' ];
		$args   .= ( $instance[ 'order' ] ) ? '&order=DESC' : '';
		$args   .= '&bound=' . $instance[ 'bound' ];
		$args   .= '&pages=' . $instance[ 'pages' ];
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		dropdown_comics( $args );
		echo $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ]      = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]      = strip_tags( stripslashes ($new[ 'label' ] ) );
		$instance[ 'post_order' ] = $new[ 'post_order' ];
		$instance[ 'number' ]     = $new[ 'number' ];
		$instance[ 'series' ]     = $new[ 'series' ];
		$instance[ 'groupby' ]    = $new[ 'groupby' ];
		$instance[ 'orderby' ]    = $new[ 'orderby' ];
		$instance[ 'bound' ]      = $new[ 'bound' ];
		$instance[ 'order' ]      = $new[ 'order' ];
		$instance[ 'pages' ]      = $new[ 'pages' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Dropdown Comics', 'webcomic'), 'label' => __( 'Quick Archive', 'webcomic' ) ) );
		
		$title      = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$label      = $instance[ 'label' ];
		$post_order = $instance[ 'post_order' ];
		$number     = $instance[ 'number' ];
		$series     = $instance[ 'series' ];
		$groupby    = $instance[ 'groupby' ];
		$orderby    = $instance[ 'orderby' ];
		$order      = $instance[ 'order' ];
		$bound      = $instance[ 'bound' ];
		$pages      = $instance[ 'pages' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ) ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Label: ', 'webcomic' ) ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Series:', 'webcomic' ); ?>
					<select name="<?php echo $this->get_field_name( 'series' ); ?>" class="widefat">
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
					<select name="<?php echo $this->get_field_name( 'groupby' ); ?>" class="widefat">
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
					<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat">
						<option value="id"<?php if ( 'id' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Default', 'webcomic' ); ?></option>
						<option value="name"<?php if ( 'name' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Title', 'webcomic' ); ?></option>
						<option value="count"<?php if ( 'count' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Page Count', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Chapters Should Link To:','webcomic') ?>
					<select name="<?php echo $this->get_field_name( 'bound' ); ?>" class="widefat">
						<option value="first"<?php if ( 'first' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'First Comic', 'webcomic' ); ?></option>
						<option value="last"<?php if ( 'last' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Last Comic', 'webcomic' ); ?></option>
						<option value="page"<?php if ( 'page' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Archive Page', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'number' ); ?>" value="1"<?php if ( $number ) echo ' checked="checked"' ?> /> <?php _e( 'Automatically number posts and chapters', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'post_order' ); ?>" value="1"<?php if ( $post_order ) echo ' checked="checked"' ?> /> <?php _e( 'Show posts in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'order' ); ?>" value="1"<?php if ( $order ) echo ' checked="checked"' ?> /> <?php _e( 'Show volumes and chapters in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'pages' ); ?>" value="1"<?php if ( $pages ) echo ' checked="checked"' ?> /> <?php _e( 'Show volume and chapter page counts', 'webcomic' ); ?></label></p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

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
 *
class WP_Widget_Comic_Archive extends WP_Widget {
}*/
class WP_Widget_Comic_Archive extends WP_Widget {
	//constructor
	function WP_Widget_Comic_Archive() {
		load_webcomic_domain();
		
		$widget_ops = array( 'description' => __( 'Displays an archive of your comics', 'webcomic' ) );
		$this->WP_Widget( 'comic-archive', __( 'Comic Archive', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		extract( $args );
		
		$args  = 'groupby=' . $instance[ 'groupby' ];
		$args .= '&format=' . $instance[ 'format' ];
		$args .= ( $instance[ 'post_order' ] ) ? '&post_order=ASC' : '';
		$args .= '&series=' . $instance[ 'series' ];
		$args .= '&orderby=' . $instance[ 'orderby' ];
		$args .= ( $instance[ 'order' ] ) ? '&order=DESC' : '';
		$args .= '&bound=' . $instance[ 'bound' ];
		$args .= '&descriptions=' . $instance[ 'descriptions' ];
		$args .= '&pages=' . $instance[ 'pages' ];
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		comic_archive( $args );
		echo $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ]        = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'groupby' ]      = $new[ 'groupby' ];
		$instance[ 'format' ]       = $new[ 'format' ];
		$instance[ 'post_order' ]   = $new[ 'post_order' ];
		$instance[ 'series' ]       = $new[ 'series' ];
		$instance[ 'orderby' ]      = $new[ 'orderby' ];
		$instance[ 'order' ]        = $new[ 'order' ];
		$instance[ 'bound' ]        = $new[ 'bound' ];
		$instance[ 'descriptions' ] = $new[ 'descriptions' ];
		$instance[ 'pages' ]        = $new[ 'pages' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Comic Archive', 'webcomic') ) );
		
		$title        = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$groupby      = $instance[ 'groupby' ];
		$format       = $instance[ 'format' ];
		$post_order   = $instance[ 'post_order' ];
		$series       = $instance[ 'series' ];
		$orderby      = $instance[ 'orderby' ];
		$order        = $instance[ 'order' ];
		$bound        = $instance[ 'bound' ];
		$descriptions = $instance[ 'descriptions' ];
		$pages        = $instance[ 'pages' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ) ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label>
					<?php _e( 'Group Comics By:', 'webcomic' ) ?>
					<select name="<?php echo $this->get_field_name( 'groupby' ); ?>" class="widefat">
						<option value="date"><?php _e( 'Year, Month, and Day', 'webcomic' ); ?></option>
						<option value="chapter"<?php if ( 'chapter' == $groupby ) echo ' selected="selected"'; ?>><?php _e( 'Series, Volume, and Chapter', 'webcomic' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Display Links As:', 'webcomic' ) ?>
					<select name="<?php echo $this->get_field_name( 'format' ); ?>" class="widefat">
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
					<select name="<?php echo $this->get_field_name( 'series' ); ?>" class="widefat">
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
					<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat">
						<option value="id"<?php if ( 'id' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Default', 'webcomic' ); ?></option>
						<option value="name"<?php if ( 'name' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Title', 'webcomic' ); ?></option>
						<option value="count"<?php if ( 'count' == $orderby ) echo ' selected="selected"'; ?>><?php _e( 'Page Count', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label>
					<?php _e('Chapters Should Link To:','webcomic') ?>
					<select name="<?php echo $this->get_field_name( 'bound' ); ?>" class="widefat">
						<option value="first"<?php if ( 'first' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'First Comic', 'webcomic' ); ?></option>
						<option value="last"<?php if ( 'last' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Last Comic', 'webcomic' ); ?></option>
						<option value="page"<?php if ( 'page' == $bound ) echo ' selected="selected"'; ?>><?php _e( 'Archive Page', 'webcomi' ); ?></option>
					</select>
				</label>
			</p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'post_order' ); ?>" value="1"<?php if ( $post_order ) echo ' checked="checked"' ?> /> <?php _e( 'Show posts in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'order' ); ?>" value="1"<?php if ( $order ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volumes, and chapters in reverse order', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'descriptions' ); ?>" value="1"<?php if ( $descriptions ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volume, and chapter descriptions', 'webcomic' ); ?></label></p>
			<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'pages' ); ?>" value="1"<?php if ( $pages ) echo ' checked="checked"' ?> /> <?php _e( 'Show series, volume, and chapter page counts', 'webcomic' ); ?></label></p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

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
class WP_Widget_Webcomic_Recent_Posts extends WP_Widget {
	//constructor
	function WP_Widget_Webcomic_Recent_Posts() {
		$widget_ops = array( 'description' => __( 'The most recent posts on your blog (ignores comic posts)', 'webcomic' ) );
		$this->WP_Widget( 'recent-posts', __( 'Recent Posts', 'webcomic' ), $widget_ops );
	}
	
	//display
	function widget( $args, $instance ) {
		extract( $args );
		
		$posts               = ignore_comics( $instance[ 'number' ] );
		
		echo $before_widget;
		if ( !empty( $instance[ 'title' ] ) ) echo $before_title . $instance[ 'title' ] . $after_title;
		if ( $posts->have_posts() ) :
			echo '<ul>';
			while ( $posts->have_posts() ) :
				$posts->the_post();
				echo '<li><a href="' . get_permalink() . '" title="' . __( 'Permanent link to ', 'webcomic' ) . the_title_attribute( 'echo=0' ) . '">' . the_title_attribute( 'echo=0' ) . '</a></li>';
			endwhile;
			echo '</ul>';
		endif;
		echo $after_widget;
	}
	
	//update
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return;
		
		$instance = $old;
		$instance[ 'title' ]  = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'number' ] = ( int ) $new[ 'number' ];
		
		return $instance;
	}
	
	//form
	function form( $instance ) {
		load_webcomic_domain();
		
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'Recent Posts', 'webcomic'), 'number' => 5 ) );
		
		$title  = $instance[ 'title' ];
		$number = $instance[ 'number' ];
		?>
			<p><label><?php _e( 'Title: ', 'webcomic' ); ?><input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e( 'Number of posts to show: ', 'webcomic' ); ?><input name="<?php echo $this->get_field_name( 'number' ); ?>" style="width: 25px; text-align: center;" value="<?php echo $number; ?>" /></label></p>
			<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}
?>