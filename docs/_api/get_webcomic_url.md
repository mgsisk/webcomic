---
title: get_webcomic_url
permalink: get_webcomic_url
---

> Alter the comic URL.

```php
apply_filters( 'get_webcomic_url', string $url, WP_Post $comic, array $args, mixed $post )
```

This filter allows hooks to alter the requested comic's URL. Note that
this filter does not run if the $query_url argument has been specified;
it will run during the query URL redirect.

## Parameters

### `string` $url
The comic URL.

### `WP_Post` $comic
The comic the URL points to.

### `array` $args
Optional arguments.

### `mixed` $post
Optional reference post.
