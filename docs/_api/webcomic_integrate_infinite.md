---
title: webcomic_integrate_infinite
permalink: webcomic_integrate_infinite
---

> Integrate infinite comic scrolling.

```php
do_action( 'webcomic_integrate_infinite', array $args )
```

This action provides a way for hooks to add an infinitely-scrolling comic
container. It will display the Webcomic Infinite widget area for each comic
in the container when using integration. The template the action appears on
may change the default $args:

- On a standard page (e.g. `page.php`), the default value for order use the
page's comic template settings.
- On date archives (e.g. `year.php`, `archive.php`), a default date_query
argument is set based on the archive date.
- On taxonomy archives (e.g. `category.php`, `tags.php`), a default
tax_query argument is set based on the archive term.
- On author archives (e.g. `author.php`), a default author argument is set
based on the archive author.

## Parameters

### `array` $args
Optional arguments.

## Uses
- [get_webcomics()](get_webcomics())  
The fields argument is always set to `ids`.
