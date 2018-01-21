---
title: is_webcomic_tax()
permalink: is_webcomic_tax()
---

> Is the query for a comic taxonomy archive?

```php
is_webcomic_tax( mixed $taxonomies = null, mixed $terms = null, mixed $relative = null, array $args = [] ) : bool
```

## Parameters

### `mixed` $taxonomies
Optional taxonomies to check for.

### `mixed` $terms
Optional terms to check for.

### `mixed` $relative
Optional reference term.

### `array` $args
Optional arguments.

- **`mixed` crossover**  
Whether to check for a taxonomy crossover archive.
May be boolean, a collection ID, or an array of
collection ID's.

## Return

`bool`

## Uses
- [is_a_webcomic_term()](is_a_webcomic_term())  
The term argument is always set to the queried
object.
