---
title: get_webcomic_adjacent_term
permalink: get_webcomic_adjacent_term
---

> Alter the adjacent term.

```php
apply_filters( 'get_webcomic_adjacent_term', int $id, array $terms, WP_Term $reference, array $args, mixed $term )
```

This filter allows hooks to alter which term is the adjacent term.

## Parameters

### `int` $id
The adjacent term ID.

### `array` $terms
The term ID's.

### `WP_Term` $reference
The reference term.

### `array` $args
Optional arguments.

### `mixed` $term
Optional term to get.
