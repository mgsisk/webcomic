---
title: \[webcomic_transcript_terms_list\]
permalink: webcomic_transcript_terms_list__
---

> Display a list of comic transcript languages.

```php
[webcomic_transcript_terms_list]
```

## Attributes

### `array` current
Optional term ID of the current term or terms.

### `string` format
Optional flat list format, like before\{\{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
webcomics_optgroup as a join will replace collection
links with a list of comic `<option>` elements
wrapped in an `<optgroup>`. When $hierarchical is
true, before and after are mapped to the $start and
$end arguments.

### `string` start
Optional text to prepend to the list when
$hierarchical is true.

### `string` start_lvl
Optional text to prepend to a list level when
$hierarchical is true.

### `string` start_el
Optional text to prepend to list items when
$hierarchical is true.

### `string` end_el
Optional text to append to list items when
$hierarchical is true.

### `string` end_lvl
Optional text to append to a list level when
$hierarchical is true.

### `string` end
Optional text to append to the list when $hierarchical
is true.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Transcribe\TermLister.

### `array` related
Optional arguments to use when retrieving post
transcripts to determine what terms are related
to a post.

### `string` type
Optional taxonomy type, like character or storyline.
Specifying a $type overrides any specified $taxonomy;
it will be combined with the $collection argument to
produce a taxonomy.

### `mixed` taxonomy
Taxonomy name, or array of taxonomies, to which
results should be limited.

### `bool` hide_empty
Whether to hide terms not assigned to any posts.

### `mixed` object_ids
Optional object ID or an array of object IDs.
Results will be limited to terms associated with
these objects.

### `string` order
Whether to order terms in ascending or descending
order. Accepts 'asc' (ascending) or 'desc'
(descending).

### `string` orderby
Field to order terms by. Accepts term fields (
'name', 'slug', 'term_group', 'term_id', 'id',
'description', 'parent') and 'count' for term
taxonomy count.

### `int` depth
The maximum hierarchical depth.

## Uses
- [get_webcomic_transcript_terms_list()](get_webcomic_transcript_terms_list())
