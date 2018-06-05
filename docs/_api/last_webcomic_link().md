---
title: last_webcomic_link()
permalink: last_webcomic_link()
---

> Display a link to the last comic.

```php
last_webcomic_link( string $link = '&raquo;', mixed $post = null, array $args = [] ) : void
```

## Parameters

### `string` $link
Optional link text, like before\{\{text}}after.

### `mixed` $post
Optional reference post.

### `array` $args
Optional arguments.

## Return

`void`

## Uses
- [get_webcomic_link()](get_webcomic_link())  
The relation argument is always set to `last`.
