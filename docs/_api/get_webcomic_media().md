---
title: get_webcomic_media()
permalink: get_webcomic_media()
---

> Get comic media.

```php
get_webcomic_media( string $format = 'full', mixed $post = null, array $args = [] ) : string
```

## Parameters

### `string` $format
Optional media format, like before\{\{join}}after{size}.
Size may be be any valid image size or a
comma-separated list of width and height pixel values
(in that order), and may be specified without the rest
of the format arguments.

### `mixed` $post
Optional post to get comic media for.

### `array` $args
Optional arguments.

- **`array` attr**  
Optional attributes for the media markup.
- **`int` length**  
Optional length of the media array slice to use.
- **`int` offset**  
Optional zero-based media index to use.

## Return

`string`
