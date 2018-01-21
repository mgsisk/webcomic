---
title: webcomic_integrate_landing_page_comments
permalink: webcomic_integrate_landing_page_comments
---

> Integrate comic comments.

```php
do_action( 'webcomic_integrate_landing_page_comments', array $args )
```

This action provides a way for hooks to add comic comments on landing
pages. It will display comments as appropriate based on the specified
$args.

## Parameters

### `array` $args
Optional arguments.

- **`bool` webcomic_comments**  
Wether to display comic content.
