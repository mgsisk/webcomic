---
title: webcomic_integrate_landing_page_args
permalink: webcomic_integrate_landing_page_args
---

> Alter the landing page arguments.

```php
apply_filters( 'webcomic_integrate_landing_page_args', array $args )
```

This filter allows hooks to alter the landing page arguments before they're
passed to `get_webcomics()` to fetch comics and fire actions for the
landing page. The fields argument is always set to `ids` and the
posts_per_page argument is always set to `1`.

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
