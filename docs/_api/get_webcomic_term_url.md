---
title: get_webcomic_term_url
permalink: get_webcomic_term_url
---

> Alter the comic term URL.

```php
apply_filters( 'get_webcomic_term_url', string $url, WP_Term $comic_term, array $post_args, mixed $post, array $args, mixed $term )
```

This filter allows hooks to alter the requested term's URL. Note that
this filter does not run if the $query_url argument has been specified;
it will run during the query URL redirect.

## Parameters

### `string` $url
The comic term URL.

### `WP_Term` $comic_term
The comic term the URL points to.

### `array` $post_args
Optional post arguments.

### `mixed` $post
Optional reference post.

### `array` $args
Optional arguments.

### `mixed` $term
Optional reference term.
