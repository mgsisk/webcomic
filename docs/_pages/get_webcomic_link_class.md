---
title: get_webcomic_link_class
permalink: get_webcomic_link_class
---

> Alter the link class.

```php
apply_filters( 'get_webcomic_link_class', array $class, array $args, WP_Post $comic )
```

This filter allows hooks to alter the CSS classes assigned to the link.

## Parameters

### `array` $class
The CSS classes.

### `array` $args
Optional arguments.

### `WP_Post` $comic
The comic the link is for.
