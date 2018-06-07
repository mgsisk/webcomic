---
title: \[webcomic_terms_list\]
permalink: webcomic_terms_list__
---

> Display a list of comic terms.

```php
[webcomic_terms_list]
```

Shortcode content overrides the `link` attribute.

## Attributes

### `int` cloud_max
Optional weighted list maximum font size.

### `int` cloud_min
Optional weighted list minimum font size.

### `array` current
Optional term ID of the current term or terms.

### `string` feed_type
Optional term feed type; one of atom, rss, or
rss2.

### `string` feed
Optional term feed link text.

### `string` format
Optional flat list format, like before\{\{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
webcomics_optgroup as a join will replace collection
links with a list of comic `<option>` elements
wrapped in an `<optgroup>`. When $hierarchical is
true, before and after are mapped to the $start and
$end arguments.

### `string` end_el
Optional text to append to list items when
$hierarchical is true.

### `string` end_lvl
Optional text to append to a list level when
$hierarchical is true.

### `string` end
Optional text to append to the list when $hierarchical
is true.

### `array` link_args
Optional arguments for term links.

### `mixed` link_post
Optional reference post for term links.

### `array` link_post_args
Optional post arguments for term links.

### `string` link
Optional link text, like before\{\{text}}after.

### `string` start_el
Optional text to prepend to list items when
$hierarchical is true.

### `string` start_lvl
Optional text to prepend to a list level when
$hierarchical is true.

### `string` start
Optional text to prepend to the list when
$hierarchical is true.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Taxonomy\Walker\TermLister.

### `array` webcomics
Optional get_webcomics_list() arguments.

### `int` webcomics_depth
Optional depth to list comics at.

### `string` collection
Optional collection ID; combined with $type to
produce a taxonomy when $type is specified.
If no $collection is specified, the requested
collection (if any) will be used.

This behavior changes if an integer
$object_ids has been specified; in this case,
$collections will be determined as follows:
- If $collection is empty, $collection will
include all collections.
- If $collection is own, $collection will be
the $object_ids collection.
- If $collection is crossover, $collection will
be all collections except the $object_ids
collection.

### `string` type
Optional taxonomy type, like character or storyline.
Specifying a $type overrides any specified
$taxonomy; it will be combined with the $collection
argument to produce a taxonomy.

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
- [get_webcomic_terms_list()](get_webcomic_terms_list())
