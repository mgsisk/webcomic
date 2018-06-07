---
title: \[webcomics_list\]
permalink: webcomics_list__
---

> Display a list of comics.

```php
[webcomics_list]
```

Shortcode content overrides the `link` attribute.

## Attributes

### `string` format
Optional list format, like before\{\{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements.

### `string` link
Optional link text, like before\{\{text}}after.

### `array` link_args
Optional link arguments.

### `int` cloud_min
Optional weighted list minimum font size.

### `int` cloud_max
Optional weighted list maximum font size.

### `int` current
Optional post ID of the current comic.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Collection\Walker\ComicLister.

### `mixed` related_to
Optional post the comics must be related to.

### `mixed` related_by
Optional taxonomies the comics must be related
by.

### `mixed` not_related_by
Optional taxonomies the comics must not be
related by.

### `string` order
Optional post sort order; one of asc or desc.

### `string` orderby
Optional post sort field; one of date, none, name,
author, title, modified, menu_order, parent, ID,
rand, relevance, or comment_count.

### `int` posts_per_page
Optional number of posts to retrieve.

## Uses
- [get_webcomics_list()](get_webcomics_list())
