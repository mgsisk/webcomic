<?php
/**
 * Deprecated global functions
 *
 * @package Webcomic
 */

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @return bool
 * @deprecated Use is_webcomic_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function is_webcomic_attachment( $collection = '' ) {
	webcomic_error( __( 'is_webcomic_attachment() is deprecated; use is_webcomic_media() instead.', 'webcomic' ) );

	return WebcomicTag::is_webcomic_attachment_( $collection );
}

/**
 * Deprecated function.
 *
 * @return bool
 * @deprecated Use is_webcomic_collection() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function is_webcomic_archive() {
	webcomic_error( __( 'is_webcomic_archive() is deprecated; use is_webcomic_collection() instead.', 'webcomic' ) );

	return WebcomicTag::is_webcomic_archive_();
}

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @return bool
 * @deprecated Use is_webcomic_tax() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function is_webcomic_crossover( $collection = '' ) {
	webcomic_error( __( 'is_webcomic_archive() is deprecated; use is_webcomic_tax() instead.', 'webcomic' ) );

	return WebcomicTag::is_webcomic_crossover_( $collection );
}

/**
 * Deprecated function.
 *
 * @param mixed  $post Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return bool
 * @deprecated Use is_webcomic_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function is_a_webcomic_attachment( $post = null, $collection = '' ) {
	webcomic_error( __( 'is_a_webcomic_attachment() is deprecated; use is_a_webcomic_media() instead.', 'webcomic' ) );

	return WebcomicTag::is_a_webcomic_attachment_( $post, $collection );
}

/**
 * Deprecated function.
 *
 * @param bool $post Deprecated parameter.
 * @return bool
 * @deprecated Use has_webcomic_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function has_webcomic_attachments( $post = null ) {
	webcomic_error( __( 'has_webcomic_attachments() is deprecated; use has_webcomic_media() instead.', 'webcomic' ) );

	return WebcomicTag::has_webcomic_attachments_( $post );
}

/**
 * Deprecated function.
 *
 * @param string $scope Deprecated parameter.
 * @param string $term Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return bool
 * @deprecated Use has_webcomic_term() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function has_webcomic_crossover( $scope = '', $term = '', $post = null ) {
	webcomic_error( __( 'has_webcomic_crossover() is deprecated; use has_webcomic_term() instead.', 'webcomic' ) );

	return WebcomicTag::has_webcomic_crossover_( $scope, $term, $post );
}

/**
 * Deprecated function.
 *
 * @param bool   $pending Deprecated parameter.
 * @param string $language Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return bool
 * @deprecated Use has_webcomic_transcripts() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function have_webcomic_transcripts( $pending = null, $language = '', $post = null ) {
	webcomic_error( __( 'have_webcomic_transcripts() is deprecated; use has_webcomic_transcripts() instead.', 'webcomic' ) );

	return WebcomicTag::have_webcomic_transcripts_( $pending, $language, $post );
}

/**
 * Deprecated function.
 *
 * @param bool $original Deprecated parameter.
 * @param bool $post Deprecated parameter.
 * @return bool
 * @deprecated Use has_webcomic_print() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_prints_available( $original = null, $post = null ) {
	webcomic_error( __( 'webcomic_prints_available() is deprecated; use has_webcomic_print() instead.', 'webcomic' ) );

	return WebcomicTag::webcomic_prints_available_( $original, $post );
}

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @param mixed  $user Deprecated parameter.
 * @param int    $age Deprecated parameter.
 * @return mixed
 * @deprecated Use webcomic_age_required() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function verify_webcomic_age( $collection = '', $user = null, $age = 0 ) {
	webcomic_error( __( 'verify_webcomic_age() is deprecated; use webcomic_age_required() instead.', 'webcomic' ) );

	return WebcomicTag::verify_webcomic_age_( $collection, $user, $age );
}

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @return int
 * @deprecated Use webcomic_age() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_verify_webcomic_age( $collection = '' ) {
	webcomic_error( __( 'the_verify_webcomic_age() is deprecated; use webcomic_age() instead.', 'webcomic' ) );

	return WebcomicTag::get_verify_webcomic_age_( $collection );
}

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @param mixed  $user Deprecated parameter.
 * @param array  $roles Deprecated parameter.
 * @return bool
 * @deprecated Use webcomic_roles_required()
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function verify_webcomic_role( $collection = '', $user = null, $roles = [] ) {
	webcomic_error( __( 'verify_webcomic_role() is deprecated; use webcomic_roles_required() instead.', 'webcomic' ) );

	return WebcomicTag::verify_webcomic_role_( $collection, $user, $roles );
}

/**
 * Deprecated function.
 *
 * @param string $size Deprecated parameter.
 * @param string $relative Deprecated parameter.
 * @param bool   $in_same_term Deprecated parameter.
 * @param bool   $excluded_terms Deprecated parameter.
 * @param string $taxonomy Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic( $size = 'full', $relative = '', $in_same_term = null, $excluded_terms = null, $taxonomy = 'storyline', $post = null ) {
	webcomic_error( __( 'the_webcomic() is deprecated; use webcomic_media() instead.', 'webcomic' ) );

	echo WebcomicTag::the_webcomic_( $size, $relative, $in_same_term, $excluded_terms, $taxonomy, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param bool $post Deprecated parameter.
 * @return int
 * @deprecated Use webcomic_media_count() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_count( $post = null ) {
	webcomic_error( __( 'webcomic_count() is deprecated; use webcomic_media_count() instead.', 'webcomic' ) );

	return WebcomicTag::webcomic_count_( $post );
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param int    $limit Deprecated parameter.
 * @param mixed  $storylines Deprecated parameter.
 * @param mixed  $characters Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomics_list() instead.
 * @SuppressWarnings(PHPMD.ExcessiveParameterList) - Required for compatibility.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_related_webcomics( $before = '', $sep = ', ', $after = '', $image = '', $limit = 5, $storylines = 'true', $characters = 'true', $post = null ) {
	webcomic_error( __( 'the_related_webcomics() is deprecated; use webcomics_list() instead.', 'webcomic' ) );

	echo WebcomicTag::the_related_webcomics_( $before, $sep, $after, $image, $limit, $storylines, $characters, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $format Deprecated parameter.
 * @param string $link Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_link() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function purchase_webcomic_link( $format = '%link', $link = '', $post = null ) {
	webcomic_error( __( 'purchase_webcomic_link() is deprecated; use webcomic_link() instead.', 'webcomic' ) );

	echo WebcomicTag::purchase_webcomic_link_( $format, $link, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param mixed  $crossover Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collections_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_collections( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $crossover = 'true' ) {
	webcomic_error( __( 'the_webcomic_collections() is deprecated; use webcomic_collections_list() instead.', 'webcomic' ) );

	echo WebcomicTag::get_the_webcomic_collection_list_( 0, $before, $sep, $after, $target, $image, $crossover ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param mixed  $crossover Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_storylines_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_storylines( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $crossover = 'true' ) {
	webcomic_error( __( 'the_webcomic_storylines() is deprecated; use webcomic_storylines_list() instead.', 'webcomic' ) );

	$taxonomy = 'storyline';

	if ( 'only' === $crossover ) {
		$taxonomy = 'xstoryline';
	} elseif ( ! $crossover ) {
		$taxonomy = '!storyline';
	}

	echo WebcomicTag::get_the_webcomic_term_list_( 0, $taxonomy, $before, $sep, $after, $target, $image ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param mixed  $crossover Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_characters( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $crossover = 'true' ) {
	webcomic_error( __( 'the_webcomic_characters() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$taxonomy = 'character';

	if ( 'only' === $crossover ) {
		$taxonomy = 'xcharacter';
	} elseif ( ! $crossover ) {
		$taxonomy = '!character';
	}

	echo WebcomicTag::get_the_webcomic_term_list_( 0, $taxonomy, $before, $sep, $after, $target, $image ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $size Deprecated parameter.
 * @param int    $storyline Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_character_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_storyline_cover( $size = 'full', $storyline = 0, $collection = '' ) {
	webcomic_error( __( 'webcomic_storyline_cover() is deprecated; use webcomic_character_media() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_term_media_( $size, $storyline, str_replace( '_storyline', '', "{$collection}_storyline" ) ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $size Deprecated parameter.
 * @param int    $character Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_character_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_character_avatar( $size = 'full', $character = 0, $collection = '' ) {
	webcomic_error( __( 'webcomic_character_avatar() is deprecated; use webcomic_character_media() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_term_media_( $size, $character, str_replace( '_character', '', "{$collection}_character" ) ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param int    $storyline Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_storylines_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_storyline_crossovers( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $storyline = 0, $collection = '' ) {
	webcomic_error( __( 'webcomic_storyline_crossovers() is deprecated; use webcomic_storylines_list() instead.', 'webcomic' ) );

	$taxonomy = '';

	if ( $storyline ) {
		$taxonomy = "{$collection}_storyline";
	}

	echo WebcomicTag::webcomic_term_crossovers_( $storyline, $taxonomy, $before, $sep, $after, $target, $image ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param int    $character Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_character_crossovers( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $character = 0, $collection = '' ) {
	webcomic_error( __( 'webcomic_character_crossovers() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$taxonomy = '';

	if ( $character ) {
		$taxonomy = "{$collection}_character";
	}

	echo WebcomicTag::webcomic_term_crossovers_( $character, $taxonomy, $before, $sep, $after, $target, $image ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $prefix Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collection_title() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_title( $prefix = '' ) {
	webcomic_error( __( 'webcomic_crossover_title() is deprecated; use webcomic_collection_title() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_crossover_title_( $prefix ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @return void
 * @deprecated Use webcomic_collection_description() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_description() {
	webcomic_error( __( 'webcomic_crossover_description() is deprecated; use webcomic_collection_description() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_crossover_description_(); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $size Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collection_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_poster( $size = 'full' ) {
	webcomic_error( __( 'webcomic_crossover_poster() is deprecated; use webcomic_collection_media() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_crossover_media_( $size ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $size Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collection_media() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_poster( $size = 'full', $collection = '' ) {
	webcomic_error( __( 'webcomic_collection_poster() is deprecated; use webcomic_collection_media() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_collection_media_( $size, $collection ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed  $type Deprecated parameter.
 * @param string $dec Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return string
 * @deprecated Use webcomic_collection_print_price() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_print_amount( $type, $dec = '.', $sep = ',', $collection = '' ) {
	webcomic_error( __( 'webcomic_collection_print_amount() is deprecated; use webcomic_collection_print_price() instead.', 'webcomic' ) );

	return WebcomicTag::webcomic_collection_print_amount_( $type, $dec, $sep, $collection );
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @param string $target Deprecated parameter.
 * @param string $image Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated use webcomic_collections_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_crossovers( $before = '', $sep = ', ', $after = '', $target = 'self', $image = '', $collection = '' ) {
	webcomic_error( __( 'webcomic_collection_crossovers() is deprecated; use webcomic_collections_list() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_collection_crossovers_( $before, $sep, $after, $target, $image, $collection ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $dec Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collection_donation() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_donation_amount( $dec = '.', $sep = ',', $collection = '' ) {
	webcomic_error( __( 'webcomic_donation_amount() is deprecated; use webcomic_collection_donation() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_donation_amount_( $dec, $sep, $collection ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use get_webcomic_collection_donation_url() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_donation_fields( $collection = '' ) {
	webcomic_error( __( 'webcomic_donation_fields() is deprecated; use get_webcomic_collection_donation_url() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_donation_fields_( $collection ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $label Deprecated parameter.
 * @param string $collection Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collection_donation_link() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_donation_form( $label = '', $collection = '' ) {
	webcomic_error( __( 'webcomic_donation_form() is deprecated; use webcomic_collection_donation_link() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_donation_form_( $label, $collection ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed  $type Deprecated parameter.
 * @param string $dec Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_print_price() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_amount( $type, $dec = '.', $sep = ',', $post = null ) {
	webcomic_error( __( 'webcomic_print_amount() is deprecated; use webcomic_print_price() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_print_amount_( $type, $dec, $sep, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed $type Deprecated parameter.
 * @param mixed $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_print_adjust() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_adjustment( $type, $post = null ) {
	webcomic_error( __( 'webcomic_print_adjustment() is deprecated; use webcomic_print_adjust() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_print_adjustment_( $type, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed $type Deprecated parameter.
 * @param mixed $post Deprecated parameter.
 * @return void
 * @deprecated Use get_webcomic_url() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_fields( $type, $post = null ) {
	webcomic_error( __( 'webcomic_print_fields() is deprecated; use get_webcomic_url() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_print_fields_( $type, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed  $type Deprecated parameter.
 * @param string $label Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_link() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_form( $type, $label = '', $post = null ) {
	webcomic_error( __( 'webcomic_print_form() is deprecated; use webcomic_link() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_print_form_( $type, $label, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $template Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_transcripts_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_transcripts_template( $template = '' ) {
	webcomic_error( __( 'webcomic_transcripts_template() is deprecated; use webcomic_transcripts_list() instead.', 'webcomic' ) );

	WebcomicTag::webcomic_transcripts_template_( $template );
}

/**
 * Deprecated function.
 *
 * @param string $format Deprecated parameter.
 * @param string $none Deprecated parameter.
 * @param string $some Deprecated parameter.
 * @param string $off Deprecated parameter.
 * @param bool   $language Deprecated parameter.
 * @param mixed  $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_link() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_transcripts_link( $format = '%link', $none = '', $some = '', $off = '', $language = null, $post = null ) {
	webcomic_error( __( 'webcomic_transcripts_link() is deprecated; use webcomic_link() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_transcripts_link_( $format, $none, $some, $off, $language, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed  $post_author Deprecated parameter.
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_transcript_authors_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_transcript_authors( $post_author = 'true', $before = '', $sep = ', ', $after = '' ) {
	webcomic_error( __( 'the_webcomic_transcript_authors() is deprecated; use webcomic_transcript_authors_list() instead.', 'webcomic' ) );

	echo WebcomicTag::get_webcomic_transcript_authors_( 0, $post_author, $before, $sep, $after ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param string $before Deprecated parameter.
 * @param string $sep Deprecated parameter.
 * @param string $after Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_transcript_languages_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_transcript_languages( $before = '', $sep = ', ', $after = '' ) {
	webcomic_error( __( 'the_webcomic_transcript_languages() is deprecated; use webcomic_transcript_languages_list() instead.', 'webcomic' ) );

	echo WebcomicTag::get_the_webcomic_transcript_term_list_( 0, 'webcomic_language', $before, $sep, $after ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param mixed $transcript Deprecated parameter.
 * @param mixed $post Deprecated parameter.
 * @return void
 * @deprecated Use get_webcomic_transcript_form() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_transcript_fields( $transcript = null, $post = null ) {
	webcomic_error( __( 'webcomic_transcript_fields() is deprecated; use get_webcomic_transcript_form() instead.', 'webcomic' ) );

	echo WebcomicTag::webcomic_transcript_fields_( $transcript, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @param mixed $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_transcript_languages_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_transcript_languages( $args = [], $post = null ) {
	webcomic_error( __( 'webcomic_dropdown_transcript_languages() is deprecated; use webcomic_transcript_languages_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'depth'            => 0,
			'hide_empty'       => ! webcomic_transcripts_open( $post ),
			'hide_if_empty'    => true,
			'hierarchical'     => false,
			'id'               => '',
			'orderby'          => 'name',
			'select_name'      => 'webcomic_terms',
			'show_option_all'  => '',
			'show_option_none' => '',
			'walker'           => '',
		]
	);

	$args['taxonomy'] = 'webcomic_transcript_language';

	echo WebcomicTag::webcomic_dropdown_transcript_terms_( $args, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @param mixed $post Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_transcript_languages_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_transcript_languages( $args = [], $post = null ) {
	webcomic_error( __( 'webcomic_list_transcript_languages() is deprecated; use webcomic_transcript_languages_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'        => '',
			'before'       => '',
			'class'        => '',
			'depth'        => 0,
			'hide_empty'   => ! webcomic_transcripts_open( $post ),
			'hierarchical' => true,
			'id'           => '',
			'orderby'      => 'name',
			'ordered'      => '',
			'selected'     => 0,
			'walker'       => false,
		]
	);

	$args['taxonomy'] = 'webcomic_language';

	echo WebcomicTag::webcomic_list_transcript_terms_( $args, $post ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_storylines( $args = [] ) {
	webcomic_error( __( 'webcomic_dropdown_storylines() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'hide_if_empty'    => true,
			'hierarchical'     => true,
			'id'               => '',
			'orderby'          => 'term_group',
			'select_name'      => 'webcomic_terms',
			'selected'         => 0,
			'show_count'       => false,
			'show_option_all'  => '',
			'show_option_none' => '',
			'target'           => 'self',
			'walker'           => false,
			'webcomics'        => false,
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_storyline" ) ) {
		$args['taxonomy'] = "{$args['collection']}_storyline";

		echo WebcomicTag::webcomic_dropdown_terms_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_characters( $args = [] ) {
	webcomic_error( __( 'webcomic_dropdown_characters() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'hide_if_empty'    => true,
			'hierarchical'     => true,
			'id'               => '',
			'orderby'          => 'name',
			'select_name'      => 'webcomic_terms',
			'selected'         => 0,
			'show_count'       => false,
			'show_option_all'  => '',
			'show_option_none' => '',
			'target'           => 'self',
			'walker'           => false,
			'webcomics'        => false,
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_character" ) ) {
		$args['taxonomy'] = "{$args['collection']}_character";

		echo WebcomicTag::webcomic_dropdown_terms_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collections_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_collections( $args = [] ) {
	webcomic_error( __( 'webcomic_dropdown_collections() is deprecated; use webcomic_collections_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'collection'       => '',
			'hide_empty'       => true,
			'id'               => '',
			'order'            => 'asc',
			'orderby'          => '',
			'select_name'      => 'webcomic_collections',
			'show_count'       => false,
			'show_option_all'  => '',
			'show_option_none' => '',
			'target'           => 'self',
			'webcomic_order'   => 'asc',
			'webcomic_orderby' => 'date',
			'webcomics'        => false,
		]
	);

	echo WebcomicTag::webcomic_dropdown_collections_( $args ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_storylines( $args = [] ) {
	webcomic_error( __( 'webcomic_list_storylines() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'feed_type'        => 'rss2',
			'feed'             => '',
			'hierarchical'     => true,
			'id'               => '',
			'orderby'          => '',
			'ordered'          => '',
			'selected'         => 0,
			'sep'              => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'self',
			'walker'           => false,
			'webcomic_image'   => '',
			'webcomics'        => false,
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_storyline" ) ) {
		$args['taxonomy'] = "{$args['collection']}_storyline";

		echo WebcomicTag::webcomic_list_terms_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_characters( $args = [] ) {
	webcomic_error( __( 'webcomic_list_characters() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'feed_type'        => 'rss2',
			'feed'             => '',
			'hierarchical'     => true,
			'id'               => '',
			'orderby'          => '',
			'ordered'          => '',
			'selected'         => 0,
			'sep'              => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'self',
			'walker'           => false,
			'webcomic_image'   => '',
			'webcomics'        => false,
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_character" ) ) {
		$args['taxonomy'] = "{$args['collection']}_character";

		echo WebcomicTag::webcomic_list_terms_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collections_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_collections( $args = [] ) {
	webcomic_error( __( 'webcomic_list_collections() is deprecated; use webcomic_collections_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'collection'       => '',
			'feed_type'        => 'rss2',
			'feed'             => '',
			'hide_empty'       => true,
			'id'               => '',
			'order'            => 'asc',
			'orderby'          => '',
			'ordered'          => false,
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'self',
			'webcomic_image'   => '',
			'webcomic_order'   => 'asc',
			'webcomic_orderby' => 'date',
			'webcomics'        => false,
		]
	);

	echo WebcomicTag::webcomic_list_collections_( $args ); // WPCS: xss ok.
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_storyline_cloud( $args = [] ) {
	webcomic_error( __( 'webcomic_storyline_cloud() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'      => '',
			'before'     => '',
			'callback'   => '',
			'class'      => '',
			'collection' => get_webcomic_collection(),
			'id'         => '',
			'image'      => '',
			'largest'    => 150,
			'order'      => 'rand',
			'selected'   => 0,
			'sep'        => "\n",
			'show_count' => false,
			'smallest'   => 75,
			'target'     => 'self',
			'unit'       => '%',
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_storyline" ) ) {
		$args['taxonomy'] = "{$args['collection']}_storyline";

		echo WebcomicTag::webcomic_term_cloud_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_characters_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_character_cloud( $args = [] ) {
	webcomic_error( __( 'webcomic_character_cloud() is deprecated; use webcomic_characters_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'      => '',
			'before'     => '',
			'callback'   => '',
			'class'      => '',
			'collection' => get_webcomic_collection(),
			'id'         => '',
			'image'      => '',
			'largest'    => 150,
			'order'      => 'rand',
			'selected'   => 0,
			'sep'        => "\n",
			'show_count' => false,
			'smallest'   => 75,
			'target'     => 'self',
			'unit'       => '%',
		]
	);

	if ( taxonomy_exists( "{$args['collection']}_character" ) ) {
		$args['taxonomy'] = "{$args['collection']}_character";

		echo WebcomicTag::webcomic_term_cloud_( $args ); // WPCS: xss ok.
	}
}

/**
 * Deprecated function.
 *
 * @param array $args Deprecated parameter.
 * @return void
 * @deprecated Use webcomic_collections_list() instead.
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_cloud( $args = [] ) {
	webcomic_error( __( 'webcomic_collection_cloud() is deprecated; use webcomic_collections_list() instead.', 'webcomic' ) );

	$args = wp_parse_args(
		$args, [
			'after'      => '',
			'before'     => '',
			'class'      => '',
			'id'         => '',
			'image'      => '',
			'largest'    => 150,
			'order'      => 'rand',
			'orderby'    => '',
			'sep'        => ' ',
			'show_count' => false,
			'smallest'   => 75,
			'target'     => 'self',
		]
	);

	echo WebcomicTag::webcomic_collection_cloud_( $args ); // WPCS: xss ok.
}
