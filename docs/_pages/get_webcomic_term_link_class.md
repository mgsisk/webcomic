---
title: get_webcomic_term_link_class
permalink: get_webcomic_term_link_class
---

> Alter the link class.

```php
apply_filters( 'get_webcomic_term_link_class', array $class, array $args, WP_Term $comic_term )
```

This filter allows hooks to alter the CSS classes assigned to the link.

## Parameters

### `array` $class
The CSS classes.

### `array` $args
Optional arguments.

### `WP_Term` $comic_term
The comic term the link is for.
