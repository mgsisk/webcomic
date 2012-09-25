<?php
/** Contains WebcomicWidgets and related classes.
 * 
 * @package Webcomic
 */

/** Handle widget-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicWidgets extends Webcomic {
	/** Register action and filter hooks.
	 * 
	 * @uses WebcomicWidgets::widgets_init()
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}
	
	/** Register widgets.
	 * 
	 * @hook widgets_init
	 * @uses WebcomicWidgetLink
	 * @uses WebcomicWidgetDonation
	 * @uses WebcomicWidgetStorylines
	 * @uses WebcomicWidgetCharacters
	 * @uses WebcomicWidgetCollections
	 */
	public function widgets_init() {
		register_widget( 'Widget_WebcomicLink' );
		register_widget( 'Widget_DynamicWebcomic' );
		register_widget( 'Widget_RecentWebcomics' );
		register_widget( 'Widget_WebcomicDonation' );
		register_widget( 'Widget_WebcomicStorylines' );
		register_widget( 'Widget_WebcomicCharacters' );
		register_widget( 'Widget_WebcomicCollections' );
	}
}

/** Webcomic Characters widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicLink extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Webcomic Link', 'webcomic' ), array( 'description' => __( 'Link to the first, last, or a random webcomic.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( $output = WebcomicTag::relative_webcomic_link( '%link', $link, $relative, false, false, 'storyline', $collection ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]           = $new[ 'link' ];
		$old[ 'relative' ]       = $new[ 'relative' ];
		$old[ 'collection' ]     = $new[ 'collection' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config = Webcomic::config();
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'relative' ); ?>">
					<option value="first"<?php echo empty( $relative ) ? '' : selected( 'first', $relative, false ); ?>><?php _e( 'First', 'webcomic' ); ?></option>
					<option value="last"<?php echo empty( $relative ) ? '' : selected( 'last', $relative, false ); ?>><?php _e( 'Last', 'webcomic' ); ?></option>
					<option value="random-nocache"<?php echo empty( $relative ) ? '' : selected( 'random-nocache', $relative, false ); ?>><?php _e( 'Random', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $config[ 'collections' ] as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								empty( $collection ) ? '' : selected( $k, $collection, false ),
								esc_html( $v[ 'name' ] )
							);
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link:', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<table class="widefat">
					<thead>
						<tr>
							<th><?php _e( 'Token', 'webcomic' ); ?></th>
							<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>%title</td>
							<td><?php _e( 'Webcomic Title', 'webcomic' ); ?></td>
						</tr>
						<tr>
							<td>%date</td>
							<td><?php _e( 'Publish Date', 'webcomic' ); ?></td>
						</tr>
						<?php
							$count = 1;
							$sizes = '<tr><td>%full</td></tr>';
							
							foreach ( get_intermediate_image_sizes() as $size ) {
								$count++;
								$sizes .= "<tr><td>%{$size}</td></tr>";
							}
							
							echo preg_replace( '/<\/td><\/tr>/', sprintf( '</td><td rowspan="%s" style="border-left:thin solid #dfdfdf">%s</td></tr>', $count, __( 'Image Preview', 'webcomic' ) ), $sizes, 1 );
						?>
					</tbody>
				</table>
			</label>
		</p>
		<?php
	}
}

/** Dynamic Webcomic widget.
 * 
 * @package Webcomic
 */
class Widget_DynamicWebcomic extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Dynamic Webcomic', 'webcomic' ), array( 'description' => __( 'A dynamic browser for your webcomics.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses Webcomic::dir()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function widget( $args, $instance ) {
		$config = Webcomic::config();
		
		if ( $config[ 'dynamic' ] ) {
			extract( $args );
			extract( $instance );
			
			if ( !$collection ) {
				$collection = WebcomicTag::get_webcomic_collection();
			} else if ( -1 === ( int ) $collection ) {
				$collection = WebcomicTag::get_webcomic_collections();
			}
			
			if ( $collection ) {
				$webcomic = new WP_Query( array( 'post_type' => $collection, 'posts_per_page' => 1, 'order' => $reverse ? 'ASC' : 'DESC' ) );
				
				if ( $webcomic->have_posts() ) {
					echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, sprintf( '<div data-webcomic-container="%s">', $widget_id );
					
					while ( $webcomic->have_posts() ) { $webcomic->the_post();
						if ( !locate_template( array( "webcomic/dynamic-{$widget_id}-{$collection}.php", "webcomic/dynamic-{$widget_id}.php", "webcomic/dynamic-{$collection}.php", 'webcomic/dynamic.php' ), true, false ) ) {
							require Webcomic::dir() . '-/php/integrate/dynamic.php';
						}
					}
					
					echo  '</div>', $after_widget;
				}
				
				wp_reset_postdata();
			}
		}
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]       = strip_tags( $new[ 'title' ] );
		$old[ 'reverse' ]     = $new[ 'reverse' ];
		$old[ 'collection' ]  = $new[ 'collection' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * The Dynamic Webcomic widget requires that the dynamic navigation
	 * option be enabled. If dynamic navigation is not enabled an error
	 * message will be displayed in place of the widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config     = Webcomic::config();
		
		if ( $config[ 'dynamic' ] ) { ?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<option value="-1"<?php echo empty( $collection ) ? '' : selected( -1, $collection, false ); ?>><?php _e( '(all collections)', 'webcomic' ); ?></option>
					<?php
						foreach ( $config[ 'collections' ] as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								empty( $collection ) ? '' : selected( $k, $collection, false ),
								esc_html( $v[ 'name' ] )
							);
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'reverse' ); ?>" value="1"<?php echo empty( $reverse ) ? '' : checked( $reverse, true, false ); ?>> <?php _e( 'Start with first webcomic', 'webcomic' ); ?></label><br>
		</p>
		<?php } else { ?>
		<p style="color:#bc0b0b"><strong><?php _e( 'Please enable the dynamic navigation option on the Settings > Webcomic administrative page to use this widget.', 'webcomic' ); ?></strong></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'collection' ); ?>" value="<?php echo empty( $collection ) ? '' : $collection; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'reverse' ); ?>" value="<?php echo empty( $reverse ) ? '' : $reverse; ?>">
		<?php	
		}
	}
}

/** Recent Webcomics widget.
 * 
 * @package Webcomic
 */
class Widget_RecentWebcomics extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Recent Webcomics', 'webcomic' ), array( 'description' => __( 'The most recent webcomics on your site.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::the_webcomic()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( !$collection ) {
			$collection = WebcomicTag::get_webcomic_collection();
		} else if ( -1 === ( int ) $collection ) {
			$collection = WebcomicTag::get_webcomic_collections();
		}
		
		if ( $collection ) {
			$the_posts = new WP_Query( array( 'post_type' => $collection, 'posts_per_page' => $numberposts ) );
			
			if ( $the_posts->have_posts() ) {
				echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, '<ul class="recent-webcomics">';
				
				while ( $the_posts->have_posts() ) { $the_posts->the_post();
					echo '<li>', $image ? WebcomicTag::the_webcomic( $image, 'self' ) : sprintf( '<a href="%s">%s</a>', get_permalink(), get_the_title( '', '', false ) ), '</li>';
				}
				
				echo $after_widget;
			}
			
			wp_reset_postdata();
		}
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]       = strip_tags( $new[ 'title' ] );
		$old[ 'image' ]       = $new[ 'image' ];
		$old[ 'collection' ]  = $new[ 'collection' ];
		$old[ 'numberposts' ] = $new[ 'numberposts' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config = Webcomic::config();
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<option value="-1"<?php echo empty( $collection ) ? '' : selected( -1, $collection, false ); ?>><?php _e( '(all collections)', 'webcomic' ); ?></option>
					<?php
						foreach ( $config[ 'collections' ] as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								empty( $collection ) ? '' : selected( $k, $collection, false ),
								esc_html( $v[ 'name' ] )
							);
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Webcomics to show:', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" value="<?php echo empty( $numberposts ) ? 5 : $numberposts; ?>" size="3">
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'image' ); ?>" value="%thumbnail"<?php echo empty( $image ) ? '' : checked( '%thumbnail' === $image, true, false ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Donation widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicDonation extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Webcomic Donation', 'webcomic' ), array( 'description' => __( 'A donation form for your webcomics.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_donation_form()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( $output = WebcomicTag::webcomic_donation_form( $label, $collection ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'label' ]      = $new[ 'label' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * The Webcomic Donation widget requires that at least one
	 * collection have a business email. If no collection has a business
	 * email an error message will be displayed in place of the widget
	 * settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config   = Webcomic::config();
		$commerce = array();
		
		foreach( $config[ 'collections' ] as $k => $v ) {
			if ( $v[ 'commerce' ][ 'business' ] ) {
				$commerce[ $k ] = $v[ 'name' ];
			}
		}
		
		if ( $commerce ) { ?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Label', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>" id="<?php echo $this->get_field_id( 'collection' ); ?>">
				<?php
					foreach ( $commerce as $k => $v ) {
						printf( '<option value="%s"%s>%s</option>',
							$k,
							empty( $collection ) ? '' : selected( $k, $collection, false ),
							esc_html( $v )
						);
					}
				?>
				</select>
			</label>
		</p>
		<?php } else { ?>
		<p style="color:#bc0b0b"><strong><?php _e( 'Please add a business email to one or more of your collections to use this widget.', 'webcomic' ); ?></strong></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'collection' ); ?>" value="<?php echo empty( $collection ) ? '' : $collection; ?>">
		<?php	
		}
	}
}

/** Webcomic Storylines widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicStorylines extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Webcomic Storylines', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic storylines.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_terms()
	 * @uses WebcomicTag::webcomic_term_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		$a[ 'target' ]           = 'first' === $a[ 'target' ] ? $a[ 'target' ] : 'archive';
		$a[ 'taxonomy' ]         = $collection ? "{$collection}_storyline" : WebcomicTag::get_webcomic_collection() . '_storyline';
		$a[ 'show_option_none' ] = __( 'Select Storyline', 'webcomic' );
		
		if ( !empty( $object->taxonomy ) and $a[ 'taxonomy' ] === $object->taxonomy ) {
			$a[ 'selected' ] = $object->term_id;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_terms( $a );
		} else if ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_terms( $a );
		} else {
			$output = WebcomicTag::webcomic_term_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'collection' ]     = $new[ 'collection' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config = Webcomic::config();
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php echo empty( $format ) ? '' : selected( 'list', $format, false ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php echo empty( $format ) ? '' : selected( 'dropdown', $format, false ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php echo empty( $format ) ? '' : selected( 'cloud', $format, false ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $config[ 'collections' ] as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								empty( $collection ) ? '' : selected( $k, $collection, false ),
								esc_html( $v[ 'name' ] )
							);
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php echo empty( $webcomics ) ? '' : checked( $webcomics, true, false ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php echo empty( $show_image ) ? '' : checked( 'thumbnail' === $show_image, true, false ); ?>> <?php _e( 'Show storyline covers', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php echo empty( $show_count ) ? '' : checked( $show_count, true, false ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php echo empty( $webcomic_image ) ? '' : checked( 'thumbnail' === $webcomic_image, true, false ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>" value="first"<?php echo empty( $target ) ? '' : checked( 'first' === $target, true, false ); ?>> <?php _e( 'Link to the beginning of storylines', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Characters widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCharacters extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Webcomic Characters', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic characters.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_terms()
	 * @uses WebcomicTag::webcomic_term_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		$a[ 'target' ]           = 'first' === $a[ 'target' ] ? $a[ 'target' ] : 'archive';
		$a[ 'taxonomy' ]         = $collection ? "{$collection}_character" : WebcomicTag::get_webcomic_collection() . '_character';
		$a[ 'show_option_none' ] = __( 'Select Character', 'webcomic' );
		
		if ( !empty( $object->taxonomy ) and $a[ 'taxonomy' ] === $object->taxonomy ) {
			$a[ 'selected' ] = $object->term_id;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_terms( $a );
		} else if ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_terms( $a );
		} else {
			$output = WebcomicTag::webcomic_term_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'collection' ]     = $new[ 'collection' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses Webcomic::config()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config = Webcomic::config();
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php echo empty( $format ) ? '' : selected( 'list', $format, false ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php echo empty( $format ) ? '' : selected( 'dropdown', $format, false ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php echo empty( $format ) ? '' : selected( 'cloud', $format, false ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $config[ 'collections' ] as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								empty( $collection ) ? '' : selected( $k, $collection, false ),
								esc_html( $v[ 'name' ] )
							);
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php echo empty( $webcomics ) ? '' : checked( $webcomics, true, false ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php echo empty( $show_image ) ? '' : checked( 'thumbnail' === $show_image, true, false ); ?>> <?php _e( 'Show character avatars', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php echo empty( $show_count ) ? '' : checked( $show_count, true, false ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php echo empty( $webcomic_image ) ? '' : checked( 'thumbnail' === $webcomic_image, true, false ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>" value="first"<?php echo empty( $target ) ? '' : checked( 'first' === $target, true, false ); ?>> <?php _e( 'Link to the first appareance of characters', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Characters widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCollections extends WP_Widget {
	/** Initialize the widget.
	 */
	public function __construct() {
		parent::__construct( false, __( 'Webcomic Collections', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic collections.', 'webcomic' ) ) );
	}
	
	/** Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_collections()
	 * @uses WebcomicTag::webcomic_collection_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_collections()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		$a[ 'target' ]           = 'first' === $a[ 'target' ] ? $a[ 'target' ] : 'archive';
		$a[ 'show_option_none' ] = __( 'Select Collection', 'webcomic' );
		
		if ( !empty( $object->query_var ) and preg_match( '/^webcomic\d+$/', $object->query_var ) ) {
			$a[ 'selected' ] = $object->query_var;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_collections( $a );
		} else if ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_collections( $a );
		} else {
			$output = WebcomicTag::webcomic_collection_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/** Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/** Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$title          = empty( $title ) ? '' : $title;
		$format         = empty( $format ) ? '' : $format;
		$target         = empty( $target ) ? '' : $target;
		$webcomics      = empty( $webcomics ) ? '' : $webcomics;
		$show_count     = empty( $show_count ) ? '' : $show_count;
		$show_image     = empty( $show_image ) ? '' : $show_image;
		$webcomic_image = empty( $webcomic_image ) ? '' : $webcomic_image;
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format:', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php echo empty( $format ) ? '' : selected( 'list', $format, false ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php echo empty( $format ) ? '' : selected( 'dropdown', $format, false ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php echo empty( $format ) ? '' : selected( 'cloud', $format, false ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php echo empty( $webcomics ) ? '' : checked( $webcomics, true, false ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php echo empty( $show_image ) ? '' : checked( 'thumbnail' === $show_image, true, false ); ?>> <?php _e( 'Show collection posters', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php echo empty( $show_count ) ? '' : checked( $show_count, true, false ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php echo empty( $webcomic_image ) ? '' : checked( 'thumbnail' === $webcomic_image, true, false ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>" value="first"<?php echo empty( $target ) ? '' : checked( 'first' === $target, true, false ); ?>> <?php _e( 'Link to the beginning of collections', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}