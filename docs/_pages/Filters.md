---
title: Filters
permalink: Filters
---

See the [WordPress Plugin API documentation][url-1] for more information about
working with filters.

## Core filters

These filters are always available, regardless of which components you have
enabled.

- [get_webcomic_collection_link_class](get_webcomic_collection_link_class)
- [get_webcomic_collection_link_tokens](get_webcomic_collection_link_tokens)
- [get_webcomic_collection](get_webcomic_collection)
- [get_webcomic_collections_args](get_webcomic_collections_args)
- [get_webcomic_collections_list_args](get_webcomic_collections_list_args)
- [get_webcomic_collections](get_webcomic_collections)
- [get_webcomic_link_class](get_webcomic_link_class)
- [get_webcomic_link_tokens](get_webcomic_link_tokens)
- [get_webcomic_url](get_webcomic_url)
- [get_webcomics_args](get_webcomics_args)
- [get_webcomics_list_args](get_webcomics_list_args)
- [webcomic_collection_allowed_options](webcomic_collection_allowed_options)
- [webcomic_current_collection](webcomic_current_collection)
- [webcomic_enqueue_comic_search](webcomic_enqueue_comic_search)
- [webcomic_enqueue_media_manager](webcomic_enqueue_media_manager)
- [webcomic_enqueue_table_record](webcomic_enqueue_table_record)
- [webcomic_integrate_infinite_args](webcomic_integrate_infinite_args)
- [webcomic_integrate_landing_page_args](webcomic_integrate_landing_page_args)
- [webcomic_new_collection](webcomic_new_collection)
- [webcomic_permalink_tokens](webcomic_permalink_tokens)
- [webcomic_rewrite_rules](webcomic_rewrite_rules)
- [webcomic_rewrite_tokens](webcomic_rewrite_tokens)
- [webcomic_search_info](webcomic_search_info)

## Commerce filters

These filters are available with the Commerce component.

- [get_webcomic_collection_cart_link_class](get_webcomic_collection_cart_link_class)
- [get_webcomic_collection_donation_link_class](get_webcomic_collection_donation_link_class)
- [get_webcomic_prints_args](get_webcomic_prints_args)
- [get_webcomic_prints_list_args](get_webcomic_prints_list_args)
- [get_webcomic_prints](get_webcomic_prints)

## Restrict filters

These filters are available with the Restrict component.

- [webcomic_age_required_comments](webcomic_age_required_comments)
- [webcomic_age_required_content](webcomic_age_required_content)
- [webcomic_referrers_required_comments](webcomic_referrers_required_comments)
- [webcomic_referrers_required_content](webcomic_referrers_required_content)
- [webcomic_roles_required_comments](webcomic_roles_required_comments)
- [webcomic_roles_required_content](webcomic_roles_required_content)

## Taxonomy filters

These filters are available with the Character and Storyline components.

- [get_webcomic_adjacent_term](get_webcomic_adjacent_term)
- [get_webcomic_term_args](get_webcomic_term_args)
- [get_webcomic_term_link_class](get_webcomic_term_link_class)
- [get_webcomic_term_link_tokens](get_webcomic_term_link_tokens)
- [get_webcomic_term_url](get_webcomic_term_url)
- [get_webcomic_term](get_webcomic_term)
- [get_webcomic_terms_args](get_webcomic_terms_args)
- [get_webcomic_terms_list_args](get_webcomic_terms_list_args)
- [webcomic_enqueue_comic_term_search](webcomic_enqueue_comic_term_search)

## Transcribe filters

These filters are available with the Transcribe component.

- [get_webcomic_transcript_author_link_tokens](get_webcomic_transcript_author_link_tokens)
- [get_webcomic_transcript_authors_args](get_webcomic_transcript_authors_args)
- [get_webcomic_transcript_authors_list_args](get_webcomic_transcript_authors_list_args)
- [get_webcomic_transcript_authors](get_webcomic_transcript_authors)
- [get_webcomic_transcript_form_args](get_webcomic_transcript_form_args)
- [get_webcomic_transcript_form_fields](get_webcomic_transcript_form_fields)
- [get_webcomic_transcript_terms_args](get_webcomic_transcript_terms_args)
- [get_webcomic_transcript_terms_list_args](get_webcomic_transcript_terms_list_args)
- [get_webcomic_transcripts_args](get_webcomic_transcripts_args)
- [get_webcomic_transcripts_list_args](get_webcomic_transcripts_list_args)
- [get_webcomic_transcripts_list_item_tokens](get_webcomic_transcripts_list_item_tokens)

## Twitter filters

These filters are available with the Twitter component.

- [webcomic_twitter_card_data](webcomic_twitter_card_data)
- [webcomic_twitter_status_tokens](webcomic_twitter_status_tokens)
- [webcomic_twitter_update_status](webcomic_twitter_update_status)

[url-1]: https://codex.wordpress.org/Plugin_API
