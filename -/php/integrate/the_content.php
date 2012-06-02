<?php
/** Automagic integration for the_content.
 * 
 * @package Webcomic
 * @uses WebcomicTag::webcomic_print_form()
 * @uses WebcomicTag::webcomic_collection_link()
 * @uses WebcomicTag::get_the_webcomic_term_list()
 * @uses WebcomicTag::the_webcomic()
 * @uses WebcomicTag::webcomic_dropdown_transcript_terms()
 * @uses WebcomicTag::get_webcomic_transcript_authors()
 * @uses WebcomicTag::get_the_webcomic_transcript_term_list()
 * @uses is_webcomic()
 * @uses webcomic_prints_available()
 * @uses is_webcomic_archive()
 * @uses is_webcomic_storyline()
 * @uses is_webcomic_character()
 */
if ( is_webcomic() ) {
	global $post;
	
	if ( webcomic_prints_available() ) {
		$prints   = array();
		$prints[] = WebcomicTag::webcomic_print_form( 'domestic', sprintf( __( '%s%s%%total Domestic', 'webcomic' ), __( '%dec.', 'webcomic' ), __( '%sep,', 'webcomic' ) ) );
		$prints[] = WebcomicTag::webcomic_print_form( 'international', sprintf( __( '%s%s%%total International', 'webcomic' ), __( '%dec.', 'webcomic' ), __( '%sep,', 'webcomic' ) ) );
		
		if ( webcomic_prints_available( 'original' ) ) {
			$prints[] = WebcomicTag::webcomic_print_form( 'original', sprintf( __( '%s%s%%total Original', 'webcomic' ), __( '%dec.', 'webcomic' ), __( '%sep,', 'webcomic' ) ) );
		}
		
		if ( '_cart' === self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'method' ] ) {
			$prints[] = WebcomicTag::webcomic_print_form( 'cart' );
		}
		
		$append .= sprintf( '<div class="webcomic-prints"><style scoped>.webcomic-prints form{display:inline}</style><h2>%s</h2><div>%s</div></div><!-- .webcomic-prints -->', __( 'Prints', 'webcomic' ), join( "\n", $prints ) );
	}
	
	$append .= WebcomicTag::webcomic_collection_link( '<div class="webcomic-collection">%link</div><!-- .webcomic-collection -->', '%title' );
	$append .= WebcomicTag::get_the_webcomic_term_list( $post->ID, 'storyline', sprintf( '<div class="webcomic-storylines"><b>%s</b>', __( 'Part of ', 'webcomic' ) ), ', ', '</div><!-- .webcomic-storylines -->' );
	$append .= WebcomicTag::get_the_webcomic_term_list( $post->ID, 'character', sprintf( '<div class="webcomic-characters"><b>%s</b>', __( 'Featuring ', 'webcomic' ) ), ', ', '</div><!-- .webcomic-characters -->' );
	$append .= WebcomicTag::webcomic_dropdown_transcript_terms( array( 'before' => '<div class="webcomic-transcript-languages">', 'sep' => __( ' | ', 'webcomic' ), 'after' => '</div><!-- .webcomic-transcript-languages-->', 'show_option_all' => __( '- Transcript Language -', 'webcomic' ), 'taxonomy' => 'webcomic_language' ) );
} else if ( is_webcomic_archive() or is_webcomic_storyline() or is_webcomic_character() or is_search() ) {
	$prepend = sprintf( '<div class="integrated-webcomic"><div class="webcomic-img">%s</div><!-- .webcomic-img --></div><!-- .integrated-webcomic -->', WebcomicTag::the_webcomic( 'medium', 'self' ) );
}