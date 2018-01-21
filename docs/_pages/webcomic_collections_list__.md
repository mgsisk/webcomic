---
title: \[webcomic_collections_list\]
permalink: webcomic_collections_list__
---

> Display a list of collections.

```php
[webcomic_collections_list]
```

Shortcode content overrides the `link` attribute.

## Attributes

### `string` format
Optional list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
'webcomics_optgroup' as the join will replace
collection links with a list of comic `<option>`
elements wrapped in an `<optgroup>`.

### `string` link
Optional link text, like before{{text}}after.

### `mixed` link_post
Optional reference post for collection links.

### `array` link_args
Optional link arguments.

### `string` feed
Optional collection feed link text.

### `string` feed_type
Optional collection feed type; one of atom,
rss, or rss2.

### `int` cloud_min
Optional weighted list minimum font size.

### `int` cloud_max
Optional weighted list maximum font size.

### `string` current
Optional collection ID of the current collection.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Collection\Walker\CollectionLister.

### `array` webcomics
Optional get_webcomics_list() arguments.

### `bool` hide_empty
Whether to include or exclude empty
collections.

### `int` limit
Optional maximum number of collections to return.

### `mixed` not_related_by
Optional taxonomies the collections must
not be related by.

### `string` order
Optional collection sort order; one of asc or desc.

### `string` orderby
Optional collection sort field; one of name,
slug, count, updated, or rand.

### `mixed` related_to
Optional post object, term object, or
collection ID the collections must be related
to.

### `mixed` related_by
Optional taxonomies the collections must be
related by.

## Uses
- [get_webcomic_collections_list()](get_webcomic_collections_list())
