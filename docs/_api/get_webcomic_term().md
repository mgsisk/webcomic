---
title: get_webcomic_term()
permalink: get_webcomic_term()
---

> Get a comic term.

```php
get_webcomic_term( mixed $term = null, array $args = [] ) : mixed
```

## Parameters

### `mixed` $term
Optional term to get.

### `array` $args
Optional arguments.

- **`string` collection**  
Optional collection ID; combined with $type to
produce a taxonomy when $type is specified.
If no $collection is specified, the requested
collection (if any) will be used.
- **`bool` hierarchical_skip**  
Whether to skip over nested terms when
finding a previous or next term in a
hierarchical taxonomy. When true, a
previous term cannot be an ancestor of
the reference term, and a next term
cannot be a child of the reference term.
- **`string` relation**  
Optional relational term to get; one of first,
previous, next, last, or random. When set, $term
becomes the point of reference for determining the
related term to get.
- **`string` type**  
Optional taxonomy type, like character or storyline.
Specifying a $type overrides any specified $taxonomy;
it will be combined with the $collection argument to
produce a taxonomy.

## Return

`mixed`

## Uses
- [get_term()](https://developer.wordpress.org/reference/functions/get_term/)
- [get_terms()](https://developer.wordpress.org/reference/functions/get_terms/)  
Accepts
get_terms() arguments as well.
