---
title: get_webcomics()
permalink: get_webcomics()
---

> Get comics.

```php
get_webcomics( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`mixed` post_type**  
Optional post type. May be a collection ID, an
array of collection ID's, or all.
- **`mixed` related_to**  
Optional post the comics must be related to.
- **`mixed` related_by**  
Optional taxonomies the comics must be related
by.
- **`mixed` not_related_by**  
Optional taxonomies the comics must not be
related by.

## Return

`array`

## Uses
- [get_posts()](https://developer.wordpress.org/reference/functions/get_posts/)  
Accepts
get_posts() arguments as well.
