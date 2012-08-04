<?php
/** Contains the WebcomicShortcode class.
 * 
 * @package Webcomic
 */

/** Handle custom shortcodes.
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
	 * @uses WebcomicShortcode::the_webcomic_link()
	 * @uses WebcomicShortcode::the_webcomic_terms()
	 * @uses WebcomicShortcode::the_related_webcomics()
	 * @uses WebcomicShortcode::the_webcomic_term_link()
	 * @uses WebcomicShortcode::the_webcomic_collection()
	 * @uses WebcomicShortcode::webcomic_term_title()
	 * @uses WebcomicShortcode::webcomic_term_description()
	 * @uses WebcomicShortcode::webcomic_term_image()
	 * @uses WebcomicShortcode::webcomic_collection_title()
	 * @uses WebcomicShortcode::webcomic_collection_description()
	 * @uses WebcomicShortcode::webcomic_collection_image()
	 * @uses WebcomicShortcode::webcomic_collection_print_amount()
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
		add_shortcode( 'the_related_webcomics', array( $this, 'the_related_webcomics' ) );
		add_shortcode( 'previous_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'next_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'first_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'last_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'random_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'purchase_webcomic_link', array( $this, 'the_webcomic_link' ) );
		add_shortcode( 'the_webcomic_collection', array( $this, 'the_webcomic_collection' ) );
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
		add_shortcode( 'webcomic_collection_title', array( $this, 'webcomic_collection_title' ) );
		add_shortcode( 'webcomic_collection_description', array( $this, 'webcomic_collection_description' ) );
		add_shortcode( 'webcomic_collection_poster', array( $this, 'webcomic_collection_image' ) );
		add_shortcode( 'webcomic_collection_print_amount', array( $this, 'webcomic_collection_print_amount' ) );
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
	 * <code>
	 * // Hide content from users based on the collection age setting.
	 * [verify_webcomic_age]This content will be hidden from some users.[/verify_webcomic_age]
	 * 
	 * // Hide content from users younger than 18 years old, regardless of the collection age setting.
	 * [verify_webcomic_age age="18"]You must be 18 years or older to view this content.[/verify_webcomic_age]
	 * 
	 * // Hide content from users based on the age setting of collection 42.
	 * [verify_webcomic_age collection="webcomic42"]It's dangerous to go alone.[/verify_webcomic_age]
	 * </code>
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::verify_webcomic_age()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @filter string webcomic_verify_age_inline $clear, $collection, $age, $content
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
			return apply_filters( 'webcomic_verify_age_inline', is_null( $clear ) ? sprintf( __( 'Please verify your age to view this content:%s', 'webcomic' ), sprintf( '<form method="post"><label>%s <input type="date" name="webcomic_birthday"></label><input type="submit"></form>', __( 'Birthday', 'webcomic' ) ) ) : __( "You don't have permission to view this content.", 'webcomic' ), $clear, $collection, $age, $content );
		}
	}
	
	/** Handle verify_webcomic_role shortcode.
	 * 
	 * <code>
	 * // Hide content from users based on the collection role setting.
	 * [verify_webcomic_role]This content will be hidden from some users.[/verify_webcomic_role]
	 * 
	 * // Hide content from users that are not contributors or administrators, regardless of the collection role setting.
	 * [verify_webcomic_role roles="administrator,contributor"]You must be a contributor or administrator to view this content.[/verify_webcomic_role]
	 * 
	 * // Hide content from users based on the role setting of collection 42.
	 * [verify_webcomic_role collection="webcomic42"]It's dangerous to go alone.[/verify_webcomic_role]
	 * </code>
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 * @uses WebcomicTag::verify_webcomic_role()
	 * @filter string webcomic_verify_role_inline $loggedin, $collection, $roles, $content
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
	 * <code>
	 * // render webcomic attachments for the current post
	 * [the_webcomic]
	 * 
	 * // render small webcomic attachments for the post with an ID of 42 linked to the first webcomic in the collection
	 * [the_webcomic size="thumbnail" relative="first" the_post="42"]
	 * 
	 * // render large webcomic attachments for the current post linked to the next webcomic in the storyline with an ID of 42
	 * [the_webomic size="large" relative="next", in_same_term="42" taxonomy="storyline"]
	 * </code>
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
	
	/** Handle the_related_webcomics shortcode.
	 * 
	 * <code>
	 * // render a comma-separated list of up to five related webcomics
	 * [the_related_webcomics]
	 * 
	 * // render an ordered list of up to to ten webcomics related by characters using small images
	 * [the_related_webcomics before="<ol><li>" sep="</li><li>" after="</li></ol>" image="thumbnail" limit="10" storylines="false"]
	 * 
	 * // render a comma-separated list of all webcomics related by storyline to the post with an ID of 42
	 * [the_related_webcomics before="<h2>Related Webcomics</h2><p>" after="</p>" limit="0" characters="false" the_post="42"]
	 * </code>
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
	 * <code>
	 * // render a link to the first webcomic in the collection
	 * [first_webcomic_link]
	 * 
	 * // render a link to the next webcomic in the collection with a small preview
	 * [next_webcomic_link link="%thumbnail"]
	 * 
	 * // render a bold link to the last webcomic in the current storylines, excluding the storyline with an ID of 42
	 * [last_webcomic_link format="<b>%link</b>" in_same_term="true" excluded_terms="42"]Last &raquo;[/last_webcomic_storyline]
	 * 
	 * // render a link to a random webcomic with a large preview in collection 42 using a parameterized url
	 * [random_webcomic_link link="%large" collection="webcomic42" cache="false"]
	 * </code>
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
		
		if ( 'random' === $relative and !$cache ) {
			$relative = 'random-nocache';
		}
		
		if ( !$link ) {
			if ( $content ) {
				$link = do_shortcode( $content );
			} else if ( 'previous' === $relative ) {
				$link = '&lsaquo;';
			} else if ( 'next' === $relative ) {
				$link = '&rsaquo;';
			} else if ( 'first' === $relative ) {
				$link = '&laquo;';
			} else if ( 'last' === $relative ) {
				$link = '&raquo;';
			} else if ( 'random' === $relative or 'random-nocache' === $relative ) {
				$link = '&infin;';
			} else if ( 'purchase' === $relative ) {
				$link = '&curren;';
			}
		}
		
		return 'purchase' === $relative ? WebcomicTag::purchase_webcomic_link( $format, $link, $the_post ) : WebcomicTag::relative_webcomic_link( $format, $link, $relative, $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
	
	/** Handle the_webcomic_collection shortcode.
	 * 
	 * <code>
	 * // render a link to the collection archive page for the collection the current webcomic belongs to
	 * [the_webcomic_collection]
	 * 
	 * // render a link to the beginning of collection 42 with a small poster preview
	 * [the_webcomic_collection link="%thumbnail" target="first" collection="webcomic42"]
	 * </code>
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_link()
	 */
	public function the_webcomic_collection( $atts ) {
		extract( shortcode_atts( array(
			'format'     => '%link',
			'link'       => '%title',
			'target'     => 'archive',
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_link( $format, $link, $target, $collection );
	}
	
	/** Handle the_webcomic_(terms) shortcodes.
	 * 
	 * <code>
	 * // render a comma-separated list of storylines related to the current webcomic
	 * [the_webcomic_storylines]
	 * 
	 * // render an unordered list of characters related to the current webcomic
	 * [the_webcomic_characters before="<ul><li>" sep="</li><li>" after="</li></ul>"]
	 * 
	 * // render links to the first webcomic in each storyline related to the current webcomic with a small storyline cover
	 * [the_webcomic_storylines before="<div><h2>Storylines</h2><figure>" sep="</figure><figure>" after="</figure></div>" target="first" image="thumbnail"]
	 * </code>
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
		} else if ( 'the_webcomic_characters' === $name ) {
			$taxonomy = 'character';
		}
		
		return WebcomicTag::get_the_webcomic_term_list( $id, $taxonomy, $before, $sep, $after, $target, $image );
	}
	
	/** Handle (relative)_webcomic_(storyline|character)_link shortcodes.
	 * 
	 * <code>
	 * // render a link to the archive page for the first storyline
	 * [first_webcomic_storyline_link]
	 * 
	 * // render a link to the first webcomic in the next character with a small cover preview
	 * [next_webcomic_character_link link="%thumbnail" target="first"]
	 * 
	 * // render a bold link to the archive page for the last storyline, even if it doesn't have any webcomics
	 * [last_webcomic_storyline_link format="<b>%link</b>" args="hide_empty=0"]Last Arc &gt;&gt;[/last_webcomic_storyline]
	 * 
	 * // render a link to the last webcomic in a random character with a large avatar in collection 42 using a parameterized url
	 * [random_webcomic_character_link link="%large" target="last" collection="42" cache="false"]
	 * </code>
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
		
		if ( 'random' === $relative and !$cache ) {
			$relative = 'random-nocache';
		}
		
		if ( !$link ) {
			if ( $content ) {
				$link = do_shortcode( $content );
			} else if ( 'previous' === $relative ) {
				$link = '&lsaquo; %title';
			} else if ( 'next' === $relative ) {
				$link = '%title &rsaquo;';
			} else if ( 'first' === $relative ) {
				$link = '&laquo; %title';
			} else if ( 'last' === $relative ) {
				$link = '%title &raquo;';
			} else if ( 'random' === $relative or 'random-nocache' === $relative ) {
				$link = '%title';
			}
		}
		
		if ( false !== strpos( $name, 'storyline' ) ) {
			$tax = 'storyline';
		} else if ( false !== strpos( $name, 'character' ) ) {
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
	 * <code>
	 * // render the storyline title
	 * [webcomic_storyline_title]
	 * 
	 * // render the title of the character with an id of 1 in the 'webcomic42' collection with a prefix
	 * [webcomic_character_title term="1" collection="webcomic42"]Character: [/webcomic_character_title]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		$prefix = $content ? do_shortcode( $content ) : $prefix;
		
		return WebcomicTag::webcomic_term_title( $prefix, $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_(storyline|character)_description shortcode.
	 * 
	 * <code>
	 * // render the description of a webcomic storyline on the storyline archive page
	 * [webcomic_storyline_description]
	 * 
	 * // render the description of the webcomic character with an ID of 1 from collection 42 with a prefix.
	 * [webcomic_character_description term="1" collection="webcomic42"]<h2>Description</h2>[/webcomic_character_description]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		return WebcomicTag::webcomic_term_description( $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_(storyline|character)_(cover|avatar) shortcode.
	 * 
	 * <code>
	 * // render the full size storyline cover
	 * [webcomic_storyline_cover]
	 * 
	 * // render the medium size character avatar for for the character with an ID of 1 in collection 42
	 * [webcomic_character_avatar size="medium" term="1" collection="webcomic42"]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
			$tax = 'character';
		} else {
			$tax = '';
		}
		
		return WebcomicTag::webcomic_term_image( $size, $term, $collection ? "{$collection}_{$tax}" : '' );
	}
	
	/** Handle webcomic_collection_title shortcode.
	 * 
	 * <code>
	 * // render the collection title
	 * [webecomic_collection_title]
	 * 
	 * // render the collection title for collection 42 with a prefix
	 * [webecomic_collection_title collection="webcomic42" prefix="Collection: "]
	 * </code>
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_title()
	 */
	public function webcomic_collection_title( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'prefix'     => '',
			'collection' => ''
		), $atts ) );
		
		$prefix = $content ? do_shortcode( $content ) : $prefix;
		
		return WebcomicTag::webcomic_collection_title( $prefix, $collection );
	}
	
	/** Handle webcomic_collection_description shortcode.
	 * 
	 * <code>
	 * // render the description of a webcomic collection on the collection archive page
	 * [webcomic_collection_description]
	 * 
	 * // render the description of webcomic collection 42
	 * [webcomic_collection_description collection="webcomic42"]
	 * </code>
	 * 
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @uses WebcomicTag::webcomic_collection_description()
	 */
	public function webcomic_collection_description( $atts, $content, $name ) {
		extract( shortcode_atts( array(
			'collection' => ''
		), $atts ) );
		
		return WebcomicTag::webcomic_collection_description( $collection );
	}
	
	/** Handle webcomic_collection_poster shortcode.
	 * 
	 * <code>
	 * // render the full size collection poster
	 * [webcomic_collection_poster]
	 * 
	 * // render the medium size collection poster for collection 42
	 * [webcomic_collection_poster size="medium" collection="webcomic42"]
	 * </code>
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
	 * <code>
	 * // render the current collection's print amount for a domestic print.
	 * [webcomic_collection_print_amount type="domestic"]
	 * 
	 * // render the original-print shipping amount for collection 42 using ',' for the decimal and '.' for the thousands separator.
	 * [webcomic_collection_print_amount type="original-shipping" dec="," sep="." collection="42"]
	 * </code>
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
	
	/** Handle webcomic_donation_amount shortcode.
	 * 
	 * <code>
	 * // render the current collections donation amount
	 * [webcomic_donation_amount]
	 * 
	 * // render the donation amount for collection 42 using ',' for the decimal and '.' for the thousands separator
	 * [webcomic_donation_amount dec="," sep="." collection="webcomic42"]
	 * </code>
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
	 * <code>
	 * // render a donation form for the current collection
	 * [webcomic_donation_form]
	 * 
	 * // render a donation form for collection 42 with a custom label
	 * [webcomic_donation_form collection="webcomic42"]Support This Webcomic[/webcomic_donation_form]
	 * </code>
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
	 * <code>
	 * // render the current webcomic's print amount for a domestic print
	 * [webcomic_print_amount type="domestic"]
	 * 
	 * // render the original-print shipping amount for webcomic 42 using ',' for the decimal and '.' for the thousands separator
	 * [webcomic_print_amount type="original-shipping" dec="," sep="." the_post="42"]
	 * </code>
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
	 * <code>
	 * // render the current webcomic's adjustment for a domestic print
	 * [webcomic_print_amount type="domestic"]
	 * 
	 * // render the original-print shipping adjustment for webcomic 42
	 * [webcomic_print_amount type="original-shipping" the_post="42"]
	 * </code>
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
	 * <code>
	 * // render a purchase domestic webcomic print form
	 * [webcomic_print_form type="domestic"]
	 * 
	 * // render a purchase international webcomic print form for webcomic 42
	 * [webcomic_print_form type="international" the_post="42"]
	 * 
	 * // render a shopping cart form with a custom label
	 * [webcomic_print_form type="cart" label="View Your Cart"]
	 * </code>
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
	 * <code>
	 * // render a transcripts link
	 * [webcomic_transcripts_link]
	 * 
	 * // render a transcripts link for the language with a slug of 'en'
	 * [webcomic_transcripts_link language="en"]
	 * 
	 * // render a transcript lik for webcomic 42 with custom link text
	 * [webcomic_transcripts_link none="Transcribe Me!" some="Read Transcripts" off="Transcription Disabled" the_post="42"]
	 * </code>
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
	 * <code>
	 * // render a dropdown of storylines with at least one webcomic in the current collection
	 * [webcomic_dropdown_storylines]
	 * 
	 * // render a dropdown of all characters in collection 42 linked to the beginning of each character with a default option
	 * [webcomic_dropdown_characters collection="webcomic42" hide_empty="false" target="first" show_option_all="Characters"]
	 * 
	 * // render a dropdown of published webcomics grouped by storyline in collection 42
	 * [webcomic_dropdown_storylines collection="webcomic42" show_option_all="- Comics by Storyline -" webcomics="true"]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
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
	 * <code>
	 * // render a dropdown of all webcomic collections with at least one post
	 * [webcomic_dropdown_collections]
	 * 
	 * // render a dropdown of all webcomic collections linked to the beginning of each collection with a default option
	 * [webcomic_dropdown_collections hide_empty="false" target="first" show_option_all="- Collections -"]
	 * 
	 * // render a dropdown of published webcomics grouped by collection only for collection 42
	 * [webcomic_dropdown_collections collection="webcomic42" webcomics="true"]
	 * </code>
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
	 * <code>
	 * // render a list of storylines with at least one webcomic in the current collection
	 * [webcomic_dropdown_storylines]
	 * 
	 * // render an ordered list of all characters in collection 42 linked to the beginning of each character
	 * [webcomic_list_characters collection="webcomic42" hide_empty="false" target="first" ordered="true"]
	 * 
	 * // render a list of published webcomic thumbnails grouped by storyline in collection 42 with storyline descriptions
	 * [webcomic_list_storylines collection="webcomic42" show_description="true" webcomics="true" webcomic_image="thumbnail"]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
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
	 * <code>
	 * // render a list of all webcomic collections with at least one post
	 * [webcomic_list_collections]
	 * 
	 * // render an ordered list of all webcomic collections linked to the beginning of each collection
	 * [webcomic_list_collections hide_empty="false" target="first" ordered="true"]
	 * 
	 * // render a list of published webcomic thumbnails grouped by collection only for collection 42 with collection descriptions
	 * [webcomic_list_collections collection="webcomic42" show_description="true" webcomics="true" webcomic_image="thumbnail"]
	 * </code>
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
		
		echo WebcomicTag::webcomic_list_collections( $r );
	}
	
	/** Handle webcomic_term_cloud shortcodes.
	 * 
	 * <code>
	 * // render a cloud of webcomic storylines
	 * [webcomic_storyline_cloud]
	 * 
	 * // render a list cloud of webcomic characters in collection 42 linked to the beginning of each character
	 * [webcomic_character_cloud collection="webcomic42" target="first" sep=""]
	 * 
	 * // render a cloud of thumbnail-sized webcomic storyline covers
	 * [webcomic_storyline_cloud image="thumbnail"]
	 * </code>
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
		} else if ( false !== strpos( $name, 'character' ) ) {
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
	 * <code>
	 * // render a cloud of webcomic collections
	 * [webcomic_collection_cloud]
	 * 
	 * // render a list cloud of webcomic collections linked to the beginning of each character
	 * [webcomic_collection_cloud target="first" sep=""]
	 * 
	 * // render a cloud of thumbnail-sized webcomic collections
	 * [webcomic_collection_cloud image="thumbnail"]
	 * </code>
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