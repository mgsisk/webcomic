---
title: is_first_webcomic_tax()
permalink: is_first_webcomic_tax()
---

> Is the query for the first comic taxonomy archive?

```php
is_first_webcomic_tax( mixed $taxonomies = null, mixed $terms = null, mixed $relative = null, array $args = [] ) : bool
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

## Return

`bool`

## Uses
- [is_webcomic_tax()](is_webcomic_tax())  
The relation argument is always set to `first`.
