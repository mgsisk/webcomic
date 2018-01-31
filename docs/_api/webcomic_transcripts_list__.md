---
title: \[webcomic_transcripts_list\]
permalink: webcomic_transcripts_list__
---

> Display a list of comic transcripts.

```php
[webcomic_transcripts_list]
```

NOTE Because of the complexity of this functions arguments, we set all of the
defaults to null and then remove arguments from the list that the user didn't
set themselves, allowing the function defaults to take over without having to
repeat them here.

Shortcode content overrides the `item` attribute.

## Attributes

### `array` authors_list
Optional arguments for
get_webcomic_transcript_authors_list(); only
used when $item contains the %authors token.

### `string` edit_link
Optional edit link format, like
before{{text}}after.

### `string` format
Optional list format, like before{{join}}after.

### `string` item
Optional item format, like before{{item}}after. The
before text should include two sprintf() tokens,
which will be replaced with the transcript ID and
CSS class names, respectively.

### `array` languages_list
Optional arguments for
get_webcomic_transcript_languages_list();
only used when $item contains the %languages
token.

### `string` parent_link
Optional parent link format, like
before{{text}}after.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Transcribe\Walker\TranscriptLister.

### `string` order
Optional post sort order; one of asc or desc.

### `string` orderby
Optional post sort field; one of date, none, name,
author, title, modified, menu_order, parent, ID,
rand, relevance, or comment_count.

### `int` posts_per_page
Optional number of posts to retrieve.

### `mixed` post_status
Optional post status or statuses to check.

## Uses
- [get_webcomic_transcripts_list()](get_webcomic_transcripts_list())
