---
title: get_webcomic_terms()
permalink: get_webcomic_terms()
---

> Get comic terms.

```php
get_webcomic_terms( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`string` collection**  
Optional collection ID; combined with $type to
produce a taxonomy when $type is specified.
If no $collection is specified, the requested
collection (if any) will be used.

This behavior changes if an integer
$object_ids has been specified; in this case,
$collections will be determined as follows:
- If $collection is empty, $collection will
include all collections.
- If $collection is own, $collection will be
the $object_ids collection.
- If $collection is crossover, $collection will
be all collections except the $object_ids
collection.
- **`string` type**  
Optional taxonomy type, like character or storyline.
Specifying a $type overrides any specified
$taxonomy; it will be combined with the $collection
argument to produce a taxonomy.

## Return

`array`

## Uses
- [get_terms()](https://developer.wordpress.org/reference/functions/get_terms/)  
Accepts
get_terms() arguments as well.
