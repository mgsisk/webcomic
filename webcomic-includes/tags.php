<?php
////
// Template Tags
// 
// All template tags reference functions in the Webcomic
// class or existing WordPress functions. Please see the
// main webcomic.php file or the relevant WordPress core
// file for inline documentation.
////
function get_webcomic_post( $id = false ) { global $webcomic; return $webcomic->get_webcomic_post( $id ); }
function get_webcomic_object( $size = 'full', $type = false, $key = false, $id = false, $format = false ) { global $webcomic; return $webcomic->get_webcomic_object( $size, $type, $key, $id, $format ); }
function the_webcomic_object( $size = 'full', $link = false, $taxonomy = false, $terms = false, $key = false, $id = false ) { global $webcomic; echo $webcomic->get_the_webcomic_object( $size, $link, $taxonomy, $terms, $key, $id ); }
function get_webcomic_embed( $format = 'shtml', $size = 'small', $key = false, $id = false ) { global $webcomic; return $webocmic->get_webcomic_embed( $format, $size, $key, $id ); }
function the_webcomic_embed( $format = 'shtml', $size = 'small', $key = false, $id = false ) { global $webcomic; echo $webcomic->get_the_webcomic_embed( $format, $size, $key, $id ); }
function random_webcomic_url( $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_url( 'random', $taxonomy, $terms, $id, $global ); }
function first_webcomic_url( $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_url( 'first', $taxonomy, $terms, $id, $global ); }
function last_webcomic_url( $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_url( 'last', $taxonomy, $terms, $id, $global ); }
function previous_webcomic_url( $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_url( 'previous', $taxonomy, $terms, $id, $global ); }
function next_webcomic_url( $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_url( 'next', $taxonomy, $terms, $id, $global ); }
function purchase_webcomic_url( $id = false ) { global $webcomic; echo $webcomic->get_purchase_webcomic_url( $id ); }
function bookmark_webcomic_url( $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_url( 'bookmark', $id ); }
function return_webcomic_url( $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_url( 'return', $id ); }
function remove_webcomic_url( $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_url( 'remove', $id ); }
function random_webcomic_link( $format = '%link', $link = '%label', $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_link( 'random', $format, $link, $taxonomy, $terms, $id, $global ); }
function first_webcomic_link( $format = '%link', $link = '%label', $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_link( 'first', $format, $link, $taxonomy, $terms, $id, $global ); }
function last_webcomic_link( $format = '%link', $link = '%label', $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_link( 'last', $format, $link, $taxonomy, $terms, $id, $global ); }
function previous_webcomic_link( $format = '%link', $link = '%label', $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_link( 'previous', $format, $link, $taxonomy, $terms, $id, $global ); }
function next_webcomic_link( $format = '%link', $link = '%label', $taxonomy = false, $terms = false, $id = false, $global = false ) { global $webcomic; echo $webcomic->get_relative_webcomic_link( 'next', $format, $link, $taxonomy, $terms, $id, $global ); }
function purchase_webcomic_link( $format = '%link', $link = '%label', $id = false ) { global $webcomic; echo $webcomic->get_purchase_webcomic_link( $format, $link, $id ); }
function boookmark_webcomic_link( $format = '%link', $link = '%label', $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_link( 'bookmark', $format, $link, $id ); }
function return_webcomic_link( $format = '%link', $link = '%label', $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_link( 'return', $format, $link, $id ); }
function remove_webcomic_link( $format = '%link', $link = '%label', $id = false ) { global $webcomic; echo $webcomic->get_bookmark_webcomic_link( 'remove', $format, $link, $id ); }
function get_related_webcomics( $storylines = true, $characters = true, $id = false ) { global $webcomic; return $webcomic->get_related_webcomics( Sstorylines, $characters, $id ); }
function get_the_related_webcomics( $args = false ) { global $webcomic; echo $webcomic->get_the_related_webcomics( $args ); }
function get_buffer_webcomics( $terms = false, $taxonomy = false ) { global $webcomic; return $webcomic->get_buffer_webcomics( $terms, $taxonomy ); }
function the_buffer_webcomics( $args = false ) { global $webcomic; echo $webcomic->get_the_buffer_webcomics( $args ); }
function webcomic_verify( $type = false, $id = false ) { global $webcomic; return $webcomic->verify( $type, $id ); }
function webcomic_verify_form( $label = false ) { global $webcomic; echo $webcomic->get_webcomic_verify_form( $label ); }
function webcomic_verify_age( $id = false ) { global $webcomic; echo $webcomic->get_webcomic_verify_age( $id ); }
function the_webcomic_donation_amount( $dec = false, $sep = false ) { global $webcomic; echo $webcomic->get_the_webcomic_donation_amount( $dec, $sep ); }
function webcomic_donation_fields() { global $webcomic; echo $webcomic->get_webcomic_donation_fields(); }
function webcomic_donation_form( $label = false ) { global $webcomic; echo $webcomic->get_webcomic_donation_form( $label ); }
function webcomic_prints_open( $id = false, $original = false ) { global $webcomic; return $webcomic->webcomic_prints_open( $id, $original ); }
function get_purchase_webcomic_cost( $cost = 'price', $type = 'domestic', $id = false ) { global $webcomic; return $webcomic->get_purchase_webcomic_cost( $cost, $type, $id ); }
function the_purchase_webcomic_cost( $cost = 'price', $type = 'domestic', $dec = false, $sep = false, $id = false ) { global $webcomic; echo $webcomic->get_the_purchase_webcomic_cost( $cost, $type, $dec, $sep, $id ); }
function get_purchase_webcomic_adjustment( $adjustment = 'collection', $type = 'domestic', $id = false ) { global $webcomic; return $webcomic->get_purchase_webcomic_adjustment( $adjustment, $type, $id ); }
function the_purchase_webcomic_adjustment( $adjustment = 'collection', $type = 'domestic', $dec = false, $sep = false, $id = false ) { global $webcomic; echo $webcomic->get_the_purchase_webcomic_adjustment( $adjustment, $type, $dec, $sep, $id ); }
function purchase_webcomic_fields( $type = 'domestic', $id = false ) { global $webcomic; echo $webcomic->get_purchase_webcomic_fields( $type, $id ); }
function purchase_webcomic_form( $type = 'dmoestic', $label = false, $id = false ) { global $webcomic; echo $webcomic->get_purchase_webcomic_form( $type, $label, $id ); }
function webcomic_transcripts_template( $file = false ) { global $webcomic; $webcomic->webcomic_transcripts_template( $file ); }
function have_webcomic_transcripts( $status = false ) { global $webcomic; return $webcomic->have_webcomic_transcripts( $status ); }
function list_webcomic_transcripts( $args = false ) { global $webcomic; $webcomic->list_webcomic_transcripts( $args ); }
function webcomic_transcripts_open() { global $webcomic; return $webcomic->webcomic_transcripts_open(); }
function webcomic_transcribe_form( $args = false ) { global $webcomic; $webcomic->webcomic_transcribe_form( $args ); }
function webcomic_transcribe_form_fields() { global $webcomic; echo $webcomic->get_webcomic_transcribe_form_fields(); }
function webcomic_transcribe_form_languages() { global $webcomic; echo $webcomic->get_webcomic_transcribe_form_languages(); }
function webcomic_transcript_info( $i = false ) { global $webcomic; echo $webcomic->get_webcomic_transcript_info( $i ); }
function webcomic_transcript_class( $class = false ) { global $webcomic; echo $webcomic->get_webcomic_transcript_class( $class ); }
function in_webcomic_collection( $terms = false, $id = false ) { global $webcomic; return $webcomic->in_webcomic_term( 'webcomic_collection', $terms, $id ); }
function get_webcomic_post_collections( $id = false ) { global $webcomic; return $webcomic->get_webcomic_post_terms( 'webcomic_collection', $id ); }
function the_webcomic_post_collections( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_post_terms( 'webcomic_collection', $args ); }
function get_webcomic_collection_info( $i = false, $term = false ) { global $webcomic; echo $webcomic->get_webcomic_term_info( $i, 'webcomic_collection', $term ); }
function the_webcomic_collections( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_terms( 'webcomic_collection', $args ); }
function random_webcomic_collection_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'random', 'webcomic_collection', $term, $orderby, $hide_empty ); }
function first_webcomic_collection_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'first', 'webcomic_collection', $term, $orderby, $hide_empty ); }
function last_webcomic_collection_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'last', 'webcomic_collection', $term, $orderby, $hide_empty ); }
function previous_webcomic_collection_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'previous', 'webcomic_collection', $term, $orderby, $hide_empty ); }
function next_webcomic_collection_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'next', 'webcomic_collection', $term, $orderby, $hide_empty ); }
function random_webcomic_collection_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'random', 'webcomic_collection', $format, $link, $term, $orderby, $hide_empty ); }
function first_webcomic_collection_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'first', 'webcomic_collection', $format, $link, $term, $orderby, $hide_empty ); }
function last_webcomic_collection_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'last', 'webcomic_collection', $format, $link, $term, $orderby, $hide_empty ); }
function previous_webcomic_collection_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'previous', 'webcomic_collection', $format, $link, $term, $orderby, $hide_empty ); }
function next_webcomic_collection_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'next', 'webcomic_collection', $format, $link, $term, $orderby, $hide_empty ); }
function in_webcomic_storyline( $terms = false, $id = false ) { global $webcomic; return $webcomic->in_webcomic_term( 'webcomic_storyline', $terms, $id ); }
function get_webcomic_post_storylines( $id = false ) { global $webcomic; return $webcomic->get_webcomic_post_terms( 'webcomic_storyline', $id ); }
function the_webcomic_post_storylines( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_post_terms( 'webcomic_storyline', $args ); }
function get_webcomic_storyline_info( $i = false, $term = false ) { global $webcomic; echo $webcomic->get_webcomic_term_info( $i, 'webcomic_storyline', $term ); }
function the_webcomic_storylines( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_terms( 'webcomic_storyline', $args ); }
function random_webcomic_storyline_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'random', 'webcomic_storyline', $term, $orderby, $hide_empty ); }
function first_webcomic_storyline_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'first', 'webcomic_storyline', $term, $orderby, $hide_empty ); }
function last_webcomic_storyline_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'last', 'webcomic_storyline', $term, $orderby, $hide_empty ); }
function previous_webcomic_storyline_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'previous', 'webcomic_storyline', $term, $orderby, $hide_empty ); }
function next_webcomic_storyline_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'next', 'webcomic_storyline', $term, $orderby, $hide_empty ); }
function random_webcomic_storyline_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'random', 'webcomic_storyline', $format, $link, $term, $orderby, $hide_empty ); }
function first_webcomic_storyline_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'first', 'webcomic_storyline', $format, $link, $term, $orderby, $hide_empty ); }
function last_webcomic_storyline_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'last', 'webcomic_storyline', $format, $link, $term, $orderby, $hide_empty ); }
function previous_webcomic_storyline_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'previous', 'webcomic_storyline', $format, $link, $term, $orderby, $hide_empty ); }
function next_webcomic_storyline_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'next', 'webcomic_storyline', $format, $link, $term, $orderby, $hide_empty ); }
function in_webcomic_character( $terms = false, $id = false ) { global $webcomic; return $webcomic->in_webcomic_term( 'webcomic_character', $terms, $id ); }
function get_webcomic_post_characters( $id = false ) { global $webcomic; return $webcomic->get_webcomic_post_terms( 'webcomic_character', $id ); }
function the_webcomic_post_characters( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_post_terms( 'webcomic_character', $args ); }
function webcomic_character_info( $i = false, $term = false ) { global $webcomic; echo $webcomic->get_webcomic_term_info( $i, 'webcomic_character', $term ); }
function the_webcomic_characters( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_terms( 'webcomic_character', $args ); }
function random_webcomic_character_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'random', 'webcomic_character', $term, $orderby, $hide_empty ); }
function first_webcomic_character_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'first', 'webcomic_character', $term, $orderby, $hide_empty ); }
function last_webcomic_character_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'last', 'webcomic_character', $term, $orderby, $hide_empty ); }
function previous_webcomic_character_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'previous', 'webcomic_character', $term, $orderby, $hide_empty ); }
function next_webcomic_character_url( $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_url( 'next', 'webcomic_character', $term, $orderby, $hide_empty ); }
function random_webcomic_character_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'random', 'webcomic_character', $format, $link, $term, $orderby, $hide_empty ); }
function first_webcomic_character_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'first', 'webcomic_character', $format, $link, $term, $orderby, $hide_empty ); }
function last_webcomic_character_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'last', 'webcomic_character', $format, $link, $term, $orderby, $hide_empty ); }
function previous_webcomic_character_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'previous', 'webcomic_character', $format, $link, $term, $orderby, $hide_empty ); }
function next_webcomic_character_link( $format = '%link', $link = '%label', $term = false, $orderby = false, $hide_empty = true ) { global $webcomic; echo $webcomic->get_relative_webcomic_term_link( 'next', 'webcomic_character', $format, $link, $term, $orderby, $hide_empty ); }
function webcomic_archive( $args = false ) { global $webcomic; echo $webcomic->get_the_webcomic_archive( $args ); }
?>