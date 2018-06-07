---
title: is_webcomic_location()
permalink: is_webcomic_location()
---

> Is the query for a comic taxonomy archive?

```php
is_webcomic_location( mixed $collections = null, mixed $terms = null, mixed $relative = null, array $args = [] ) : bool
```

## Parameters

### `mixed` $collections
Optional collections to check for.

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
The taxonomies argument is always set to a location
taxonomy or location taxonomies.
