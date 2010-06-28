<?php
/**
 * Webcomic donation widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Donation extends WP_Widget {
	function webcomic_Widget_Donation() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'Allow users to support your webcomic through donations.', 'webcomic' ) );
		$this->WP_Widget( 'webcomic-donation'	, __( 'Webcomic Donation', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		echo $before_widget;
		
		if ( !empty( $instance[ 'title' ] ) )
			echo $before_title . $instance[ 'title' ] . $after_title;
		
		echo $webcomic->get_webcomic_donation_form( $instance[ 'label' ] );
		
		if ( !empty( $instance[ 'text' ] ) ) {
			if ( !empty( $instance[ 'format' ] ) )
				echo wpautop( $instance[ 'text' ] );
			else
				echo $instance[ 'text' ];
		}
		
		echo $after_widget;
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]  = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]  = strip_tags( stripslashes( $new[ 'label' ] ) );
		$instance[ 'text' ]   = stripslashes( $new[ 'text' ] );
		$instance[ 'format' ] = ( $new[ 'format' ] ) ? true : false;
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'    => __( 'Donate', 'webcomic' ),
			'text'      => ''
		) ); extract( $instance );
		
		$title = htmlspecialchars( $instance[ 'title' ], ENT_QUOTES );
		$label = htmlspecialchars( $instance[ 'label' ], ENT_QUOTES );
		$text  = htmlspecialchars( $instance[ 'text' ], ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?> <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Label:', 'webcomic' ); ?> <input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" class="widefat"></label></p>
		<label><?php _e( 'Message:', 'webcomic' ); ?> <textarea name="<?php echo $this->get_field_name( 'text' ); ?>" rows="16" cols="20" class="widefat"><?php echo $text; ?></textarea></label>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'format' ); ?>"<?php if ( $format ) echo ' checked'; ?>> <?php _e( 'Automatically add paragraphs', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Relative webcomic widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Relative extends WP_Widget {
	function webcomic_Widget_Relative() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'Link to the first, last, or a random webcomic.', 'webcomic' ) );
		$this->WP_Widget( 'relative-webcomic', __( 'Relative Webcomic', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$before_widget = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		
		echo $webcomic->get_relative_webcomic_link( $instance[ 'key' ], $before_widget . '%link' . $after_widget, $instance[ 'format' ], $instance[ 'taxonomy' ], $instance[ 'term' ], false, true );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]    = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'key' ]      = $new[ 'key' ];
		$instance[ 'format' ]   = $new[ 'format' ];
		$instance[ 'term' ]     = $new[ 'term' ];
		$instance[ 'taxonomy' ] = ( $new[ 'term' ] ) ? 'webcomic_collection' : '';
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'    => __( 'Random Webcomic', 'webcomic' ),
			'key'      => 'random',
			'format'   => '%label',
			'term'     => false,
			'taxonomy' => false
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Format:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'format' ); ?>" value="<?php echo $format; ?>" class="widefat"><small><?php _e( '%label, %title, %date, %thumb-{size}-#', 'webcomic' ); ?></small></label></p>
		<p>
			<label>
				<?php _e( 'Link to:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'key' ); ?>">
					<option value="random"<?php if ( 'random' == $key ) echo ' selected'; ?>><?php _e( 'Random', 'webcomic' ); ?></option>
					<option value="first"<?php if ( 'first' == $key ) echo ' selected'; ?>><?php _e( 'First', 'webcomic' ); ?></option>
					<option value="last"<?php if ( 'last' == $key ) echo ' selected'; ?>><?php _e( 'Last', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Collection:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'term' ); ?>">
					<option value=""><?php _e( 'Any', 'webcomic' ); ?></option>
					<?php
						$walker = new webcomic_Walker_AdminTermDropdown();
						$selected = ( $term ) ? array( $term ) : array();
						echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => true ) );
					?>
				</select>
			</label>
		</p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic buffer widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Buffer extends WP_Widget {
	function webcomic_Widget_Buffer() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'A list of scheduled webcomics.', 'webcomic' ) );
		$this->WP_Widget( 'buffer-webcomics', __( 'Webcomic Buffer', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$instance[ 'before' ] = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		$instance[ 'after' ]  = $after_widget;
		
		echo $webcomic->get_the_buffer_webcomics( $instance );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]    = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'number' ]   = $new[ 'number' ];
		$instance[ 'image' ]    = $new[ 'image' ];
		$instance[ 'terms' ]    = $new[ 'terms' ];
		$instance[ 'taxonomy' ] = ( $new[ 'terms' ] ) ? 'webcomic_collection' : '';
		$instance[ 'order' ]    = ( $new[ 'order' ] ) ? 'DESC' : 'ASC';
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'    => __( 'Scheduled Webcomics', 'webcomic' ),
			'number'   => 1,
			'image'    => false,
			'terms'    => false,
			'taxonomy' => false,
			'order'    => 'DESC'
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Number:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $number; ?>" size="3"></label></p>
		<p>
			<label>
				<?php _e( 'Preview:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'image' ); ?>">
					<option value=""><?php _e( 'Title', 'webcomic' ); ?></option>
					<option value="small"<?php if ( 'small' == $image ) echo ' selected'; ?>><?php _e( 'Small', 'webcomic' ); ?></option>
					<option value="medium"<?php if ( 'medium' == $image ) echo ' selected'; ?>><?php _e( 'Medium', 'webcomic' ); ?></option>
					<option value="large"<?php if ( 'large' == $image ) echo ' selected'; ?>><?php _e( 'Large', 'webcomic' ); ?></option>
					<option value="full"<?php if ( 'full' == $image ) echo ' selected'; ?>><?php _e( 'Full', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Collection:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'terms' ); ?>">
					<option value="0"><?php _e( 'Any', 'webcomic' ); ?></option>
					<?php
						$walker = new webcomic_Walker_AdminTermDropdown();
						$selected = ( $terms ) ? array( $terms ) : array();
						echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => true ) );
					?>
				</select>
			</label>
		</p>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'order' ); ?>" value="1"<?php if ( 'DESC' == $order ) echo ' checked'; ?>> <?php _e( 'Show webcomics in reverse order', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic bookmark widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Bookmark extends WP_Widget {
	function webcomic_Widget_Bookmark() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'Links that allow users to save their place while reading.', 'webcomic' ) );
		$this->WP_Widget( 'bookmark-webcomic', __( 'Webcomic Bookmark', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		echo $before_widget;
		
		if ( !empty( $instance[ 'title' ] ) )
			$before_title . $instance[ 'title' ] . $after_title;
		
		echo $webcomic->get_bookmark_webcomic_link( 'bookmark', '%link', $instance[ 'format1' ] );
		echo $webcomic->get_bookmark_webcomic_link( 'return', '%link', $instance[ 'format2' ] );
		echo $webcomic->get_bookmark_webcomic_link( 'remove', '%link', $instance[ 'format3' ] );
		
		echo  $after_widget;
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]   = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'format1' ] = $new[ 'format1' ];
		$instance[ 'format2' ] = $new[ 'format2' ];
		$instance[ 'format3' ] = $new[ 'format3' ];
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'   => __( 'Bookmark Webcomic', 'webcomic' ),
			'format1' => '%label',
			'format2' => '%label',
			'format3' => '%label'
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Bookmark:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'format1' ); ?>" value="<?php echo $format1; ?>" class="widefat"><small><?php _e( '%label, %title, %date, %thumb-{size}-#', 'webcomic' ); ?></small></label></p>
		<p><label><?php _e( 'Return:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'format2' ); ?>" value="<?php echo $format2; ?>" class="widefat"><small><?php _e( '%label, %title, %date, %thumb-{size}-#', 'webcomic' ); ?></small></label></p>
		<p><label><?php _e( 'Remove:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'format3' ); ?>" value="<?php echo $format3; ?>" class="widefat"><small><?php _e( '%label, %title, %date, %thumb-{size}-#', 'webcomic' ); ?></small></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic collections widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Collections extends WP_Widget {
	function webcomic_Widget_Collections() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'A list, dropdown, or cloud of webcomic collections.', 'webcomic' ) );
		$this->WP_Widget( 'webcomic-collections', __( 'Webcomic Collections', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$instance[ 'before' ] = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		$instance[ 'after' ]  = $after_widget;
		
		echo $webcomic->get_the_webcomic_terms( 'webcomic_collection', $instance );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]      = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]      = strip_tags( stripslashes( $new[ 'label' ] ) );
		$instance[ 'format' ]     = $new[ 'format' ];
		$instance[ 'image' ]      = ( 'dropdown' == $new[ 'format' ] ) ? '' : $new[ 'image' ];
		$instance[ 'hide_empty' ] = ( $new[ 'hide_empty' ] ) ? false : true;
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'      => __( 'Collections', 'webcomic' ),
			'label'      => '',
			'format'     => 'ulist',
			'image'      => false,
			'hide_empty' => true
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Label:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" class="widefat"><small><?php _e( 'For the first option in dropdowns.', 'webcomic' ); ?></small></label></p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="ulist"<?php if ( 'ulist' == $format ) echo ' selected'; ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="cloud"<?php if ( 'cloud' == $format ) echo ' selected'; ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
					<option value="dropdown"<?php if ( 'dropdown' == $format ) echo ' selected'; ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Preview:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'image' ); ?>">
					<option value=""><?php _e( 'Title', 'webcomic' ); ?></option>
					<option value="small"<?php if ( 'small' == $image ) echo ' selected'; ?>><?php _e( 'Small', 'webcomic' ); ?></option>
					<option value="medium"<?php if ( 'medium' == $image ) echo ' selected'; ?>><?php _e( 'Medium', 'webcomic' ); ?></option>
					<option value="large"<?php if ( 'large' == $image ) echo ' selected'; ?>><?php _e( 'Large', 'webcomic' ); ?></option>
					<option value="full"<?php if ( 'full' == $image ) echo ' selected'; ?>><?php _e( 'Full', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="1"<?php if ( !$hide_empty ) echo ' checked'; ?>> <?php _e( 'Show empty collections', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic storylines widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Storylines extends WP_Widget {
	function webcomic_Widget_Storylines() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'A list, dropdown, or cloud of webcomic storylines.', 'webcomic' ) );
		$this->WP_Widget( 'webcomic-storylines', __( 'Webcomic Storylines', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$instance[ 'before' ] = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		$instance[ 'after' ]  = $after_widget;
		
		echo $webcomic->get_the_webcomic_terms( 'webcomic_storyline', $instance );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]      = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]      = strip_tags( stripslashes( $new[ 'label' ] ) );
		$instance[ 'format' ]     = $new[ 'format' ];
		$instance[ 'image' ]      = ( 'dropdown' == $new[ 'format' ] ) ? '' : $new[ 'image' ];
		$instance[ 'term_group' ] = $new[ 'term_group' ];
		$instance[ 'hide_empty' ] = ( $new[ 'hide_empty' ] ) ? false : true;
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'      => __( 'Storylines', 'webcomic' ),
			'label'      => '',
			'format'     => 'ulist',
			'image'      => false,
			'term_group' => false,
			'hide_empty' => true
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Label:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" class="widefat"><small><?php _e( 'For the first option in dropdowns.', 'webcomic' ); ?></small></label></p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="ulist"<?php if ( 'ulist' == $format ) echo ' selected'; ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="cloud"<?php if ( 'cloud' == $format ) echo ' selected'; ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
					<option value="dropdown"<?php if ( 'dropdown' == $format ) echo ' selected'; ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Preview:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'image' ); ?>">
					<option value=""><?php _e( 'Title', 'webcomic' ); ?></option>
					<option value="small"<?php if ( 'small' == $image ) echo ' selected'; ?>><?php _e( 'Small', 'webcomic' ); ?></option>
					<option value="medium"<?php if ( 'medium' == $image ) echo ' selected'; ?>><?php _e( 'Medium', 'webcomic' ); ?></option>
					<option value="large"<?php if ( 'large' == $image ) echo ' selected'; ?>><?php _e( 'Large', 'webcomic' ); ?></option>
					<option value="full"<?php if ( 'full' == $image ) echo ' selected'; ?>><?php _e( 'Full', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Collection:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'term_group' ); ?>">
					<?php
						$walker = new webcomic_Walker_AdminTermDropdown();
						$selected = ( $term_group ) ? array( $term_group ) : array();
						echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => true ) );
					?>
				</select>
			</label>
		</p>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="1"<?php if ( !$hide_empty ) echo ' checked'; ?>> <?php _e( 'Show empty storylines', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic characters widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Characters extends WP_Widget {
	function webcomic_Widget_Characters() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'A list, dropdown, or cloud of webcomic characters.', 'webcomic' ) );
		$this->WP_Widget( 'webcomic-characters', __( 'Webcomic Characters', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$instance[ 'before' ] = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		$instance[ 'after' ]  = $after_widget;
		
		echo $webcomic->get_the_webcomic_terms( 'webcomic_character', $instance );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]      = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]      = strip_tags( stripslashes( $new[ 'label' ] ) );
		$instance[ 'format' ]     = $new[ 'format' ];
		$instance[ 'image' ]      = ( 'dropdown' == $new[ 'format' ] ) ? '' : $new[ 'image' ];
		$instance[ 'term_group' ] = $new[ 'term_group' ];
		$instance[ 'hide_empty' ] = ( $new[ 'hide_empty' ] ) ? false : true;
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'      => __( 'Characters', 'webcomic' ),
			'label'      => '',
			'format'     => 'ulist',
			'image'      => false,
			'term_group' => false,
			'hide_empty' => true
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Label:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" class="widefat"><small><?php _e( 'For the first option in dropdowns.', 'webcomic' ); ?></small></label></p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="ulist"<?php if ( 'ulist' == $format ) echo ' selected'; ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="cloud"<?php if ( 'cloud' == $format ) echo ' selected'; ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
					<option value="dropdown"<?php if ( 'dropdown' == $format ) echo ' selected'; ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Preview:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'image' ); ?>">
					<option value=""><?php _e( 'Title', 'webcomic' ); ?></option>
					<option value="small"<?php if ( 'small' == $image ) echo ' selected'; ?>><?php _e( 'Small', 'webcomic' ); ?></option>
					<option value="medium"<?php if ( 'medium' == $image ) echo ' selected'; ?>><?php _e( 'Medium', 'webcomic' ); ?></option>
					<option value="large"<?php if ( 'large' == $image ) echo ' selected'; ?>><?php _e( 'Large', 'webcomic' ); ?></option>
					<option value="full"<?php if ( 'full' == $image ) echo ' selected'; ?>><?php _e( 'Full', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Collection:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'term_group' ); ?>">
					<?php
						$walker = new webcomic_Walker_AdminTermDropdown();
						$selected = ( $term_group ) ? array( $term_group ) : array();
						echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => true ) );
					?>
				</select>
			</label>
		</p>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="1"<?php if ( !$hide_empty ) echo ' checked'; ?>> <?php _e( 'Show empty characters', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}



/**
 * Webcomic archive widget
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Widget_Archive extends WP_Widget {
	function webcomic_Widget_Archive() {
		global $webcomic;
		
		$webcomic->domain();
		
		$o = array( 'description' => __( 'Display webcomics in various ways.', 'webcomic' ) );
		$this->WP_Widget( 'webcomic-archive', __( 'Webcomic Archive', 'webcomic' ), $o );
	}
	
	function widget( $args, $instance ) {
		global $webcomic;
		
		extract( $args );
		
		$instance[ 'before' ] = ( !empty( $instance[ 'title' ] ) ) ? $before_widget . $before_title . $instance[ 'title' ] . $after_title : $before_widget;
		$instance[ 'after' ]  = $after_widget;
		
		echo $webcomic->get_the_webcomic_archive( $instance );
	}
	
	function update( $new, $old ) {
		if ( !isset( $new[ 'submit' ] ) )
			return false;
		
		$instance = $old;
		$instance[ 'title' ]      = strip_tags( stripslashes( $new[ 'title' ] ) );
		$instance[ 'label' ]      = strip_tags( stripslashes( $new[ 'label' ] ) );
		$instance[ 'format' ]     = $new[ 'format' ];
		$instance[ 'image' ]      = ( 'dropdown' == $new[ 'format' ] ) ? '' : $new[ 'image' ];
		$instance[ 'group' ]      = $new[ 'group' ];
		$instance[ 'term_group' ] = $new[ 'term_group' ];
		$instance[ 'limit' ]      = intval( $new[ 'limit' ] );
		$instance[ 'order' ]      = ( $new[ 'order' ] ) ? false : true;
		
		return $instance;
	}
	
	function form( $instance ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'      => __( 'Archive', 'webcomic' ),
			'label'      => '',
			'format'     => 'ulist',
			'image'      => false,
			'group'      => false,
			'term_group' => false,
			'limit'      => 0,
			'order'      => true
		) ); extract( $instance );
		
		$title = htmlspecialchars( $title, ENT_QUOTES );
		?>
		<p><label><?php _e( 'Title:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat"></label></p>
		<p><label><?php _e( 'Label:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $label; ?>" class="widefat"><small><?php _e( 'For the first option in dropdowns.', 'webcomic' ); ?></small></label></p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="ulist"<?php if ( 'ulist' == $format ) echo ' selected'; ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php if ( 'dropdown' == $format ) echo ' selected'; ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="grid"<?php if ( 'grid' == $format ) echo ' selected'; ?>><?php _e( 'Grid', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Group By:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'group' ); ?>">
					<option value=""><?php _e( 'Nothing', 'webcomic' ); ?></option>
					<option value="storyline"<?php if ( 'storyline' == $group ) echo ' selected'; ?>><?php _e( 'Storyline', 'webcomic' ); ?></option>
					<option value="character"<?php if ( 'character' == $group ) echo ' selected'; ?>><?php _e( 'Character', 'webcomic' ); ?></option>
					<option value="year"<?php if ( 'year' == $group ) echo ' selected'; ?>><?php _e( 'Year', 'webcomic' ); ?></option>
					<option value="month"<?php if ( 'month' == $group ) echo ' selected'; ?>><?php _e( 'Month', 'webcomic' ); ?></option>
					<option value="day"<?php if ( 'day' == $group ) echo ' selected'; ?>><?php _e( 'Day', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Preview:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'image' ); ?>">
					<option value=""><?php _e( 'Title', 'webcomic' ); ?></option>
					<option value="small"<?php if ( 'small' == $image ) echo ' selected'; ?>><?php _e( 'Small', 'webcomic' ); ?></option>
					<option value="medium"<?php if ( 'medium' == $image ) echo ' selected'; ?>><?php _e( 'Medium', 'webcomic' ); ?></option>
					<option value="large"<?php if ( 'large' == $image ) echo ' selected'; ?>><?php _e( 'Large', 'webcomic' ); ?></option>
					<option value="full"<?php if ( 'full' == $image ) echo ' selected'; ?>><?php _e( 'Full', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Collection:', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'term_group' ); ?>">
					<?php
						$walker = new webcomic_Walker_AdminTermDropdown();
						$selected = ( $term_group ) ? array( $term_group ) : array();
						echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => true ) );
					?>
				</select>
			</label>
		</p>
		<p><label><?php _e( 'Number:', 'webcomic' ); ?><input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $limit; ?>" size="3"><small><?php _e( '0 to show all webcomics.', 'webcomic' ); ?></small></label></p>
		<p><label><input type="checkbox" name="<?php echo $this->get_field_name( 'order' ); ?>" value="1"<?php if ( !$order ) echo ' checked'; ?>> <?php _e( 'Show webcomics in reverse order', 'webcomic' ); ?></label></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1">
		<?php
	}
}
?>