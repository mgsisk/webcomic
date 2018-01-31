---
title: get_webcomic()
permalink: get_webcomic()
---

> Get a comic.

```php
get_webcomic( mixed $post = null, array $args = [] ) : mixed
```

## Parameters

### `mixed` $post
Optional post to get.

### `array` $args
Optional arguments.

- **`string` relation**  
Optional relational post to get; one of first,
previous, next, last, or random. When set, $post
becomes the point of reference for determining
the related post to get.

## Return

`mixed`

## Uses
- [get_post()](https://developer.wordpress.org/reference/functions/get_post/)
