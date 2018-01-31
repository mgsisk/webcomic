---
title: has_webcomic_term()
permalink: has_webcomic_term()
---

> Does the post have a comic term?

```php
has_webcomic_term( string $taxonomy = '', mixed $term = null, mixed $post = null ) : bool
```

## Parameters

### `string` $taxonomy
Optional taxonomy to check. May be a collection ID
(like webcomic1), a type of taxonomy (like character), a type of taxonomy
prefixed with a scope keyword (like own_character or crossover_character), or
empty.

### `mixed` $term
Optional term to check.

### `mixed` $post
Optional post to check.

## Return

`bool`

## Uses
- [has_term()](https://developer.wordpress.org/reference/functions/has_term/)
