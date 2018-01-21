---
title: is_previous_webcomic_tax()
permalink: is_previous_webcomic_tax()
---

> Is the query for the previous comic taxonomy archive?

```php
is_previous_webcomic_tax( mixed $taxonomies = null, mixed $terms = null, mixed $relative = null, array $args = [] ) : bool
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
The relation argument is always set to `previous`.
