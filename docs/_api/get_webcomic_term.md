---
title: get_webcomic_term
permalink: get_webcomic_term
---

> Alter the get_webcomic_term() term.

```php
apply_filters( 'get_webcomic_term', mixed $term, array $args )
```

This filter allows hooks to alter the get_webcomic_term() term, mostly to
allow guessing at the current term when no term has been specified.

## Parameters

### `mixed` $term
Optional term to get.

### `array` $args
Optional arguments.
