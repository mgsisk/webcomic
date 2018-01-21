---
title: next_webcomic_term_link()
permalink: next_webcomic_term_link()
---

> Display a link to the next comic term.

```php
next_webcomic_term_link( string $link = '%title &rsaquo;', mixed $term = null, array $args = [], mixed $post = null, array $post_args = [] ) : void
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
- [get_webcomic_term_link()](get_webcomic_term_link())  
The relation argument is always set to
`next`.
