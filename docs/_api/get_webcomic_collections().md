---
title: get_webcomic_collections()
permalink: get_webcomic_collections()
---

> Get collections.

```php
get_webcomic_collections( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`bool` crossover**  
Whether to get collections based on the current
taxonomy crossover query, if any (taxonomy
components only).
- **`string` fields**  
Optional collection fields to get; one of ids,
options, or objects.
- **`bool` hide_empty**  
Whether to include or exclude empty
collections.
- **`array` id__in**  
Optional ID's the collections must match.
- **`array` id__not_in**  
Optional ID's the collections must not match.
- **`int` limit**  
Optional maximum number of collections to return.
- **`mixed` not_related_by**  
Optional taxonomies the collections must
not be related by.
- **`string` order**  
Optional collection sort order; one of asc or desc.
- **`string` orderby**  
Optional collection sort field; one of name,
slug, count, updated, or rand.
- **`mixed` related_to**  
Optional post object, term object, or
collection ID the collections must be related
to.
- **`mixed` related_by**  
Optional taxonomies the collections must be
related by.
- **`array` slug__in**  
Optional slugs the collections must match.
- **`array` slug__not_in**  
Optional slugs the collections must not
match.

## Return

`array`
