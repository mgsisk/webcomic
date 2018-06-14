---
title: get_webcomic_location_link()
permalink: get_webcomic_location_link()
---

> Get a comic location link.

```php
get_webcomic_location_link( string $link = '%title', mixed $term = null, array $args = [], mixed $post = null, array $post_args = [] ) : string
```

## Parameters

### `string` $link
Optional link text, like before\{\{text}}after.

### `mixed` $term
Optional reference term.

### `array` $args
Optional arguments.

### `mixed` $post
Optional reference post.

### `array` $post_args
Optional post arguments.

## Return

`string`

## Uses
- [get_webcomic_term_link()](get_webcomic_term_link())  
The type argument is always set to
`location`.
