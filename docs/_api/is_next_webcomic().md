---
title: is_next_webcomic()
permalink: is_next_webcomic()
---

> Is the query for the next comic?

```php
is_next_webcomic( mixed $collections = null, mixed $posts = null, mixed $relative = null, array $args = [] ) : bool
```

## Parameters

### `mixed` $collections
Optional collections to check for.

### `mixed` $posts
Optional posts to check for.

### `mixed` $relative
Optional reference post.

### `array` $args
Optional arguments.

## Return

`bool`

## Uses
- [is_webcomic()](is_webcomic())  
The relation argument is always set to `next`.
