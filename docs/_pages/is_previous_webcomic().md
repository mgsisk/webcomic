---
title: is_previous_webcomic()
permalink: is_previous_webcomic()
---

> Is the query for the previous comic?

```php
is_previous_webcomic( mixed $collections = null, mixed $posts = null, mixed $relative = null, array $args = [] ) : bool
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
The relation argument is always set to `previous`.
