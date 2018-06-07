---
title: previous_webcomic_location_link()
permalink: previous_webcomic_location_link()
---

> Display a link to the previous comic location.

```php
previous_webcomic_location_link( string $link = '&lsaquo; %title', mixed $term = null, array $args = [], mixed $post = null, array $post_args = [] ) : void
```

## Parameters

### `string` $link
Optional link text, like before{{text}}after.

### `mixed` $term
Optional reference term.

### `array` $args
Optional arguments.

### `mixed` $post
Optional reference post.

### `array` $post_args
Optional post arguments.

## Return

`void`

## Uses
- [get_webcomic_location_link()](get_webcomic_location_link())  
The relation argument is always set to
`previous`.
