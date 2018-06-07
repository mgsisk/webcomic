---
title: webcomic_media()
permalink: webcomic_media()
---

> Display comic media.

```php
webcomic_media( string $format = 'full', mixed $post = null, array $args = [] ) : void
```

## Parameters

### `string` $format
Optional media format, like before\{\{join}}after{size}.
Size may be be any valid image size or a
comma-separated list of width and height values in
pixels (in that order), and may be specified without
the rest of the format arguments.

### `mixed` $post
Optional post to get comic media for.

### `array` $args
Optional arguments.

## Return

`void`

## Uses
- [get_webcomic_media()](get_webcomic_media())
