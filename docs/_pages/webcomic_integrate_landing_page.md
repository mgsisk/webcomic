---
title: webcomic_integrate_landing_page
permalink: webcomic_integrate_landing_page
---

> Integrate a comic landing page.

```php
do_action( 'webcomic_integrate_landing_page', array $args )
```

This action provides a way for hooks to add a comic with media, navigation,
post content, comments, and meta data on landing pages. It will display
comic media, navigation, post content, comments, and meta data when using
integration. The template the action appears on may change the default
$args:

- On the site front page (e.g. `index.php` or `home.php`), default values
for the order, post_type, webcomic_comments, webcomic_content, and
webcomic_meta arguments use the theme's Customizer settings.
- On a standard page (e.g. `page.php`), default values for the order,
post_type, webcomic_comments, webcomic_content, and webcomic_meta
arguments use the page's comic template settings.
- On date archives (e.g. `year.php`, `archive.php`), a default date_query
argument is set based on the archive date.
- On taxonomy archives (e.g. `category.php`, `tags.php`), a default
tax_query argument is set based on the archive term.
- On author archives (e.g. `author.php`), a default author argument is set
based on the archive author.

## Parameters

### `array` $args
Optional arguments.

- **`bool` webcomic_comments**  
Wether to display comic comments.
- **`bool` webcomic_content**  
Wether to display comic content.
- **`bool` webcomic_media**  
Wether to display comic media and navigation.
- **`bool` webcomic_meta**  
Wether to display comic meta data.

## Uses
- [get_webcomics()](get_webcomics())  
The fields argument is always set to `ids` and the
posts_per_page argument is always set to `1`.
