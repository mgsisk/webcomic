---
title: is_last_webcomic()
permalink: is_last_webcomic()
---

> Is the query for the last comic?

```php
is_last_webcomic( mixed $collections = null, mixed $posts = null, mixed $relative = null, array $args = [] ) : bool
```

## Parameters

### `mixed` $collections
Optional collections to check for.

### `mixed` $posts
Optional post to check.

### `mixed` $relative
Optional reference post.

### `array` $args
Optional arguments.

## Return

`bool`

## Uses
- [is_webcomic()](is_webcomic())  
The relation argument is always set to `last`.
