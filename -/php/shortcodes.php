<?php
/** Contains the WebcomicShortcode class.
 * 
 * @package Webcomic
 */

/** Handle custom shortcodes.
 * 
 * For shortcode examples see `tags.php`.
 * 
 * @package Webcomic
 */
class WebcomicShortcode extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses WebcomicShortcode::init()
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}
	
	/** Register shortcodes.
	 * 
	 * @uses WebcomicShortcode::verify_webcomic_age()
	 * @uses WebcomicShortcode::verify_webcomic_role()
	 * @uses WebcomicShortcode::the_webcomic()
	 * @uses WebcomicShortcode::webcomic_count()
	 * @uses WebcomicShortcode::the_webcomic_link()
	 * @uses WebcomicShortcode::the_webcomic_terms()
	 * @uses WebcomicShortcode::the_related_webcomics()
	 * @uses WebcomicShortcode::the_webcomic_term_link()
	 * @uses WebcomicShortcode::webcomic_collection_link()
	 * @uses WebcomicShortcode::the_webcomic_collections()
	 * @uses WebcomicShortcode::webcomic_term_title()
	 * @uses WebcomicShortcode::webcomic_term_description()
	 * @uses WebcomicShortcode::webcomic_term_image()
	 * @uses WebcomicShortcode::webcomic_term_crossovers()
	 * @uses WebcomicShortcode::webcomic_crossover_title()
	 * @uses WebcomicShortcode::webcomic_crossover_description()
	 * @uses WebcomicShortcode::webcomic_crossover_image()
	 * @uses WebcomicShortcode::webcomic_collection_title()
	 * @uses WebcomicShortcode::webcomic_collection_description()
	 * @uses WebcomicShortcode::webcomic_collection_image()
	 * @uses WebcomicShortcode::webcomic_collection_print_amount()
	 * @uses WebcomicShortcode::webcomic_collection_crossovers()
	 * @uses WebcomicShortcode::webcomic_donation_amount()
	 * @uses WebcomicShortcode::webcomic_donation_form()
	 * @uses WebcomicShortcode::webcomic_print_amount()
	 * @uses WebcomicShortcode::webcomic_print_adjustment()
	 * @uses WebcomicShortcode::webcomic_print_form()
	 * @uses WebcomicShortcode::webcomic_transcripts_link()
	 * @uses WebcomicShortcode::webcomic_dropdown_terms()
	 * @uses WebcomicShortcode::webcomic_dropdown_collections()
	 * @uses WebcomicShortcode::webcomic_list_terms()
	 * @uses WebcomicShortcode::webcomic_list_collections()
	 * @uses WebcomicShortcode::webcomic_term_cloud()
	 * @uses WebcomicShortcode::webcomic_collection_cloud()
	 * @hook init
	 */
	public function init() {
		add_shortcode( 'verify_webcomic_age', array( $this, 'verify_webcomic_age' ) );
		add_shortcode( 'verify_webcomic_role', array( $this, 'verify_webcomic_role' ) );
		add_shortcode( 'the_webcomic', array( $this, 'the_webcomic' ) );
		add_shortcode( 'webcomic_count', array( $this, 'webcomic_count' ) );
		add_shortcode( 'the_related_webcomics', array( $this, 'the_related_webcomics' ) );
		add_shortcode( 'previous_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'next_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'first_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'last_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'random_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'purchase_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'webcomic_collection_link', array( $this, 'webcomic_collection_link' ) );
		add_shortcode( 'the_webcomic_collections', array( $this, 'the_webcomic_collections' ) );
		add_shortcode( 'the_webcomic_storylines', array( $this, 'the_webcomic_terms' ) );
		add_shortcode( 'the_webcomic_characters', array( $this, 'the_webcomic_terms' ) );
		add_shortcode( 'previous_webcomic_storyline_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'next_webcomic_storyline_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'first_webcomic_storyline_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'last_webcomic_storyline_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'random_webcomic_storyline_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'previous_webcomic_character_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'next_webcomic_character_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'first_webcomic_character_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'last_webcomic_character_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'random_webcomic_character_link', array( $this, 'the_webcomic_term_link' ) );
		add_shortcode( 'webcomic_storyline_title', array( $this, 'webcomic_term_title' ) );
		add_shortcode( 'webcomic_character_title', array( $this, 'webcomic_term_title' ) );
		add_shortcode( 'webcomic_storyline_description', array( $this, 'webcomic_term_description' ) );
		add_shortcode( 'webcomic_character_description', array( $this, 'webcomic_term_description' ) );
		add_shortcode( 'webcomic_storyline_cover', array( $this, 'webcomic_term_image' ) );
		add_shortcode( 'webcomic_character_avatar', array( $this, 'webcomic_term_image' ) );
		add_shortcode( 'webcomic_storyline_crossovers', array( $this, 'webcomic_term_crossovers' ) );
		add_shortcode( 'webcomic_character_crossovers', array( $this, 'webcomic_term_crossovers' ) );
		add_shortcode( 'webcomic_crossover_title', array( $this, 'webcomic_crossover_title' ) );
		add_shortcode( 'webcomic_crossover_description', array( $this, 'webcomic_crossover_description' ) );
		add_shortcode( 'webcomic_crossover_poster', array( $this, 'webcomic_crossover_image' ) );
		add_shortcode( 'webcomic_collection_title', array( $this, 'webcomic_collection_title' ) );
		add_shortcode( 'webcomic_collection_description', array( $this, 'webcomic_collection_description' ) );
		add_shortcode( 'webcomic_collection_poster', array( $this, 'webcomic_collection_image' ) );
		add_shortcode( 'webcomic_collection_print_amount', array( $this, 'webcomic_collection_print_amount' ) );
		add_shortcode( 'webcomic_collection_crossovers', array( $this, 'webcomic_collection_crossovers' ) );
		add_shortcode( 'webcomic_donation_amount', array( $this, 'webcomic_donation_amount' ) );
		add_shortcode( 'webcomic_donation_form', array( $this, 'webcomic_donation_form' ) );
		add_shortcode( 'webcomic_print_amount', array( $this, 'webcomic_print_amount' ) );
		add_shortcode( 'webcomic_print_adjustment', array( $this, 'webcomic_print_adjustment' ) );
		add_shortcode( 'webcomic_print_form', array( $this, 'webcomic_print_form' ) );
		add_shortcode( 'webcomic_transcripts_link', array( $this, 'webcomic_transcripts_link' ) );
		add_shortcode( 'webcomic_dropdown_storylines', array( $this, 'webcomic_dropdown_terms' ) );
		add_shortcode( 'webcomic_dropdown_characters', array( $this, 'webcomic_dropdown_terms' ) );
		add_shortcode( 'webcomic_dropdown_collections', array( $this, 'webcomic_dropdown_collections' ) );
		add_shortcode( 'webcomic_list_storylines', array( $this, 'webcomic_list_terms' ) );
		add_shortcode( 'webcomic_list_character', array( $this, 'webcomic_list_terms' ) );
		add_shortcode( 'webcomic_list_collections', array( $this, 'webcomic_list_collections' ) );
		add_shortcode( 'webcomic_storyline_cloud', array( $this, 'webcomic_term_cloud' ) );
		add_shortcode( 'webcomic_character_cloud', array( $this, 'webcomic_term_cloud' ) );
		add_shortcode( 'webcomic_collection_cloud', array( $this, 'webcomic_collection_cloud' ) );
	}
	
	/** Handle verify_webcomic_age shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::verify_webcomic_age()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @filter string webcomic_verify_age_inline Filters the output of the `verify_webcomic_age` shortcode. Defaults to a generic age verification message.
	 */
	public function verify_webcomic_age( $atts, $content ) {
		extract( shortcode_atts( array(
			'collection' => '',
			'age'        => 0
		), $atts ) );
		
		$age        = $age ? $age : -1;
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( $clear = WebcomicTag::verify_webcomic_age( $collection, false, $age ) ) {
			return do_shortcode( $content );
		} else {
			return apply_filters( 'webcomic_verify_age_inline', is_null( $clear ) ? __( 'Please verify your age to view this content:', 'webcomic' ) . '<form method="post"><label>' . __( 'Birthday', 'webcomic' ) . ' <input type="date" name="webcomic_birthday"></label><input type="submit"></form>' : __( "You don't have permission to view this content.", 'webcomic' ), $clear, $collection, $age, $content );
		}
	}
	
	/** Handle verify_webcomic_role shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::verify_webcomic_role()
	 * @filter string webcomic_verify_role_inline Filters the output of the `verify_webcomic_role` shortcode. Defaults to a generic role verification message.
	 */
	public function verify_webcomic_role( $atts, $content ) {
		extract( shortcode_atts( array(
			'collection' => '',
			'roles'      => array()
		), $atts ) );
		
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( WebcomicTag::verify_webcomic_role( $collection, false, $roles ) ) {
			return do_shortcode( $content );
		} else {
			$loggedin = is_user_logged_in();
			
			return apply_filters( 'webcomic_verify_role_inline', $loggedin ? __( "You don't have permission to view this content.", 'webcomic' ) : sprintf( __( 'Please <a href="%s">log in</a> to view this content.', 'webcomic' ), wp_login_url( $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] ) ), $loggedin, $collection, $roles, $content );
		}
	}
	
	/** Handle the_webcomic shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::the_webcomic()
	 */
	public function the_webcomic( $atts ) {
		extract( shortcode_atts( array(
			'size'           => 'full',
			'relative'       => '',
			'in_same_term'   => false,
			'excluded_terms' => '',
			'taxonomy'       => 'storyline',
			'the_post'       => false
		), $atts ) );
		
		return WebcomicTag::the_webcomic( $size, $relative, $in_same_term, $excluded_terms, $taxonomy, $the_post );
	}
	
	/** Handle webcomic_count shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::webcomic_count()
	 */
	public function webcomic_count( $atts, $content ) {
		extract( shortcode_atts( array(
			'if'       => '',
			'the_post' => false
		), $atts ) );
		
		if ( $content ) {
			$c = WebcomicTag::webcomic_count( $the_post );
			$m = array();
			$o = false;
			
			if ( preg_match( '/^\s*(=|!=|lt|gt|lte|gte)\s*(\d+)\s*$/', $if, $m ) ) {
				$m[ 2 ] = ( integer ) $m[ 2 ];
				
				if (
					( '=' === $m[ 1 ] and $c === $m[ 2 ] )
					or ( '!=' === $m[ 1 ] and $c !== $m[ 2 ] )
					or ( 'lt' === $m[ 1 ] and $c < $m[ 2 ] )
					or ( 'gt' === $m[ 1 ] and $c > $m[ 2 ] )
					or ( 'lte' === $m[ 1 ] and $c <= $m[ 2 ] )
					or ( 'gte' === $m[ 1 ] and $c >= $m[ 2 ] )
				) {
					$o = true;
				}
				
				if ( $o ) {
					return do_shortcode( $content );
				}
			}
		} else {
			return WebcomicTag::webcomic_count( $the_post );
		}
	}
	
	/** Handle the_related_webcomics shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_link()
	 */
	public function the_related_webcomics( $atts ) {
		extract( shortcode_atts( array(
			'before' => '',
			'sep'    => ', ',
			'after'  => '',
			'image'  => '',
			'limit'  => 5,
			'storylines' => true,
			'characters' => true,
			'the_post'   => false
		), $atts ) );
		
		return WebcomicTag::the_related_webcomics( $before, $sep, $after, $image, $limit, $storylines, $characters, $the_post );
	}
	
	/** Handle (relative)_webcomic_link shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::relative_webcomic_link()
	 * @uses WebcomicTag::purchase_webcomic_link()
	 */
	public function the_webcomic_link( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'format'         => '%link',
			'link'           => '',
			'in_same_term'   => false,
			'excluded_terms' => '',
			'taxonomy'       => 'storyline',
			'collection'     => '',
			'the_post'       => false,
			'cache'          => true
		), $atts ) );
		
		$relative = substr( $name, 0, strpos( $name, '_' ) );
		
		if ( 'first' === $relative and !$cache ) {
			$relative = 'first-nocache';
		} elseif ( 'last' === $relative and !$cache ) {
			$relative = 'last-nocache';
		} elseif ( 'random' === $relative and !$cache ) {
			$relative = 'random-nocache';
		}
		
		if ( !$link ) {
			if ( $content ) {
				$link = do_shortcode( $content );
			} elseif ( 'previous' === $relative ) {
				$link = '&lsaquo;';
			} elseif ( 'next' === $relative ) {
				$link = '&rsaquo;';
			} elseif ( 'first' === $relative or 'first-nocache' === $relative ) {
				$link = '&laquo;';
			} elseif ( 'last' === $relative or 'last-nocache' === $relative ) {
				$link = '&raquo;';
			} elseif ( 'random' === $relative or 'random-nocache' === $relative ) {
				$link = '&infin;';
			} elseif ( 'purchase' === $relative ) {
				$link = '&curren;';
			}
		}
		
		return 'purchase' === $relative ? WebcomicTag::purchase_webcomic_link( $format, $link, $the_post ) : WebcomicTag::relative_webcomic_link( $format, $link, $relative, $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
	
	/** Handle webcomic_collection_link shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_link()
	 */
	public function webcomic_collection_link( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'format'     => '%link',
			'link'       => '',
			'collection' => ''
		), $atts ) );
		
		if ( !$link and $content ) {
			$link = do_shortcode( $content );
		}
		
		return WebcomicTag::webcomic_collection_link( $format, $link, $collection );
	}
	
	/** Handle the_webcomic_collections shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::get_the_webcomic_collection_list()
	 */
	public function the_webcomic_collections( $atts ) {
		extract( shortcode_atts( array(
			'id'        => 0,
			'before'    => '',
			'sep'       => ', ',
			'after'     => '',
			'target'    => 'archive',
			'image'     => '',
			'crossover' => true
		), $atts ) );
		
		return WebcomicTag::get_the_webcomic_collection_list(  $id, $before, $sep, $after, $target, $image, $crossover  );
	}
	
	/** Handle the_webcomic_(terms) shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::get_the_webcomic_term_list()
	 */
	public function the_webcomic_terms( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'id'       => 0,
			'taxonomy' => 'storyline',
			'before'   => '',
			'sep'      => ', ',
			'after'    => '',
			'target'   => 'archive',
			'image'    => ''
		), $atts ) );
		
		if ( 'the_webcomic_storylines' === $name ) {
			$taxonomy = 'storyline';
		} elseif ( 'the_webcomic_characters' === $name ) {
			$taxonomy = 'character';
		}
		
		return WebcomicTag::get_the_webcomic_term_list( $id, $taxonomy, $before, $sep, $after, $target, $image );
	}
	
	/** Handle (relative)_webcomic_(storyline|character)_link shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 */
	public function the_webcomic_term_link( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'format'     => '%link',
			'link'       => '',
			'target'     => 'archive',
			'args'       => array(),
			'collection' => '',
			'cache'      => true
		), $atts ) );
		
		$array    = array();
		$args     = wp_parse_str( $args, $array );
		$relative = substr( $name, 0, strpos( $name, '_' ) );
		
		if ( 'first' === $relative and !$cache ) {
			$relative = 'first-nocache';
		} elseif ( 'last' === $relative and !$cache ) {
			$relative = 'last-nocache';
		} elseif ( 'random' === $relative and !$cache ) {
			$relative = 'random-nocache';
		}
		
		if ( !$link ) {
			if ( $content ) {
				$link = do_shortcode( $content );
			} elseif ( 'previous' === $relative ) {
				$link = '&lsaquo; %title';
			} elseif ( 'next' === $relative ) {
				$link = '%title &rsaquo;';
			} elseif ( 'first' === $relative or 'first-nocache' === $relative ) {
				$link = '&laquo; %title';
			} elseif ( 'last' === $relative or 'last-nocache' === $relative ) {
				$link = '%title &raquo;';
			} elseif ( 'random' === $relative or 'random-nocache' === $relative ) {
				$link = '%title';
			}
		}
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		if ( 'previous' === $relative or 'next' === $relative ) {
			$taxonomy = ( ( is_tax() or is_single() ) and $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_{$tax}" : '';
		} else {
			$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_{$tax}" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_{$tax}" : '';
		}
		
		return WebcomicTag::relative_webcomic_term_link( $format, $link, $target, $relative, $taxonomy, $args );
	}
	
	/** Handle webcomic_(storyline|character)_title shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_title()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function webcomic_term_title( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'prefix'     => '',
			'term'       => 0,
			'collection' => ''
		), $atts ) );
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		$prefix = $content ? do_shortcode( $content ) : $prefix;
		
		return WebcomicTag::webcomic_term_title( $prefix, $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_(storyline|character)_description shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_description()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function webcomic_term_description( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'term'       => 0,
			'collection' => ''
		), $atts ) );
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		return WebcomicTag::webcomic_term_description( $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_(storyline|character)_(cover|avatar) shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_image()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function webcomic_term_image( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'size'       => 'full',
			'term'       => 0,
			'collection' => ''
		), $atts ) );
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		return WebcomicTag::webcomic_term_image( $size, $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_crossover_title shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::webcomic_crossover_title()
	 */
	public function webcomic_crossover_title( $atts, $content ) {
		extract( shortcode_atts( array(
			'prefix' => ''
		), $atts ) );
		
		$prefix = $content ? do_shortcode( $content ) : $prefix;
		
		return WebcomicTag::webcomic_crossover_title( $prefix );
	}
	
	/** Handle webcomic_collection_description shortcode.
	 * 
	 * @return string
	 * @uses WebcomicTag::webcomic_crossover_description()
	 */
	public function webcomic_crossover_description( $atts, $content, $name ) {
		return WebcomicTag::webcomic_crossover_description();
	}
	
	/** Handle webcomic_crossover_poster shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_crossover_image()
	 */
	public function webcomic_crossover_image( $atts ) {
		extract( shortcode_atts( array(
			'size' => 'full'
		), $atts ) );
		
		return WebcomicTag::webcomic_crossover_image( $size );
	}
	
	/** Handle webcomic_(storyline|character)_crossovers shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_crossovers()
	 */
	public function webcomic_term_crossovers( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'before'     => '',
			'sep'        => ', ',
			'after'      => '',
			'target'     => 'archive',
			'image'      => '',
			'term'       => 0,
			'collection' => ''
		), $atts ) );
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		return WebcomicTag::webcomic_term_crossovers( $term, $collection ? "{$collection}_{$tax}" : '', $before, $sep, $after, $target, $image );
	}
	
	/** Handle webcomic_collection_title shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_title()
	 */
	public function webcomic_collection_title( $atts, $content ) {
		extract( shortcode_atts( array(
			'prefix'     => '',
			'collection' => ''
		), $atts ) );
		
		$prefix = $content ? do_shortcode( $content ) : $prefix;
		
		return WebcomicTag::webcomic_collection_title( $prefix, $collection );
	}
	
	/** Handle webcomic_collection_description shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_description()
	 */
	public function webcomic_collection_description( $atts ) {
		extract( shortcode_atts( array(
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_description( $collection );
	}
	
	/** Handle webcomic_collection_poster shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_image()
	 */
	public function webcomic_collection_image( $atts ) {
		extract( shortcode_atts( array(
			'size'       => 'full',
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_image( $size, $collection );
	}
	
	/** Handle webcomic_collection_print_amount shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_print_amount()
	 */
	public function webcomic_collection_print_amount( $atts ) {
		extract( shortcode_atts( array(
			'type'       => '',
			'dec'        => '.',
			'sep'        => ',',
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_print_amount( $type, $dec, $sep, $collection );
	}
	
	/** Handle webcomic_collection_crossovers shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_crossovers()
	 */
	public function webcomic_collection_crossovers( $atts ) {
		extract( shortcode_atts( array(
			'before'     => '',
			'sep'        => ', ',
			'after'      => '',
			'target'     => 'archive',
			'image'      => '',
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_crossovers( $before, $sep, $after, $target, $image, $collection );
	}
	
	/** Handle webcomic_donation_amount shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_donation_amount()
	 */
	public function webcomic_donation_amount( $atts ) {
		extract( shortcode_atts( array(
			'dec'        => '.',
			'sep'        => ',',
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_donation_amount( $dec, $sep, $collection );
	}
	
	/** Handle webcomic_donation_form shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_donation_form()
	 */
	public function webcomic_donation_form( $atts ) {
		extract( shortcode_atts( array(
			'label'      => '',
			'collection' => ''
		), $atts ) );
		
		$label = $content ? do_shortcode( $content ) : $label;
		
		return WebcomicTag::webcomic_donation_form( $label, $collection );
	}
	
	/** Handle webcomic_print_amount shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_print_amount()
	 */
	public function webcomic_print_amount( $atts ) {
		extract( shortcode_atts( array(
			'type'     => '',
			'dec'      => '.',
			'sep'      => ',',
			'the_post' => false
		), $atts ) );
		
		return WebcomicTag::webcomic_print_amount( $type, $dec, $sep, $the_post );
	}
	
	/** Handle webcomic_print_adjustment shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_print_adjustment()
	 */
	public function webcomic_print_adjustment( $atts ) {
		extract( shortcode_atts( array(
			'type'     => '',
			'the_post' => false
		), $atts ) );
		
		return WebcomicTag::webcomic_print_adjustment( $type, $the_post );
	}
	
	/** Handle webcomic_print_form shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_print_form()
	 */
	public function webcomic_print_form( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'type'     => '',
			'label'    => '',
			'the_post' => false
		), $atts ) );
		
		$label = $content ? do_shortcode( $content ) : $label;
		
		return WebcomicTag::webcomic_print_form( $type, $label, $the_post );
	}
	
	/** Handle webcomic_transcripts_link shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_transcripts_link()
	 */
	public function webcomic_transcripts_link( $atts ) {
		extract( shortcode_atts( array(
			'format'   => '%link',
			'none'     => '',
			'some'     => '',
			'off'      => '',
			'language' => false,
			'the_post' => false
		), $atts ) );
		
		return WebcomicTag::webcomic_transcripts_link( $format, $none, $some, $off, $language, $the_post );
	}
	
	/** Handle webcomic_dropdown_terms shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 */
	public function webcomic_dropdown_terms( $atts, $content, $name ) {
		$r = shortcode_atts( array(
			'name'             => 'webcomic_terms',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hierarchical'     => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'order_by'         => 'term_group',
			'walker'           => false,
			'depth'            => 0,
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => 0
		), $atts );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		if ( taxonomy_exists( "{$collection}_{$tax}" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_{$tax}";
		
			return WebcomicTag::webcomic_dropdown_terms( $r );
		}
	}
	
	/** Handle webcomic_dropdown_collections shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 */
	public function webcomic_dropdown_collections( $atts ) {
		$r = shortcode_atts( array(
			'name'             => 'webcomic_collections',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hide_empty'       => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'orderby'          => '',
			'callback'         => '',
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => ''
		), $atts );
		
		return WebcomicTag::webcomic_dropdown_collections( $r );
	}
	
	/** Handle webcomic_list_terms shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_list_terms()
	 */
	public function webcomic_list_terms( $atts, $content, $name ) {
		$r = shortcode_atts( array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'sep'              => '',
			'ordered'          => '',
			'hierarchical'     => true,
			'collection'       => '',
			'orderby'          => '',
			'walker'           => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'depth'            => 0,
			'webcomics'        => false,
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		), $atts );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		if ( taxonomy_exists( "{$collection}_{$tax}" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_{$tax}";
		
			return WebcomicTag::webcomic_list_terms( $r );
		}
	}
	
	/** Handle webcomic_list_collections shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_list_collections()
	 */
	public function webcomic_list_collections( $atts ) {
		$r = shortcode_atts( array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'hide_empty'       => true,
			'ordered'          => '',
			'collection'       => '',
			'order'            => 'ASC',
			'orderby'          => '',
			'callback'         => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		), $atts );
		
		return WebcomicTag::webcomic_list_collections( $r );
	}
	
	/** Handle webcomic_term_cloud shortcodes.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_term_cloud()
	 */
	public function webcomic_term_cloud( $atts, $content, $name ) {
		$r = shortcode_atts( array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'collection' => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		), $atts );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} elseif ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		if ( taxonomy_exists( "{$collection}_{$tax}" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_{$tax}";
		
			return WebcomicTag::webcomic_term_cloud( $r );
		}
	}
	
	/** Handle webcomic_collection_cloud shortcode.
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_cloud()
	 */
	public function webcomic_collection_cloud( $atts ) {
		$r = shortcode_atts( array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'orderby'    => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		), $atts );
		
		return WebcomicTag::webcomic_collection_cloud( $r );
	}
}