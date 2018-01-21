---
title: webcomic_integrate_landing_page_content
permalink: webcomic_integrate_landing_page_content
---

> Integrate comic content.

```php
do_action( 'webcomic_integrate_landing_page_content', array $args )
```

This action provides a way for hooks to add comic post content on landing
pages. It will display the post content as appropriate based on the
specified $args.

## Parameters

### `array` $args
Optional arguments.

- **`bool` webcomic_content**  
Wether to display comic content.
