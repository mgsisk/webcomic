---
title: webcomic_integrate_infinite_args
permalink: webcomic_integrate_infinite_args
---

> Alter the infinite container arguments.

```php
apply_filters( 'webcomic_integrate_infinite_args', array $args )
```

This filter allows hooks to alter the infinite container arguments before
they're converted for the data-webcomic-infinite attribute and ultimately
passed to `get_webcomics()`. The fields argument is always set to 'ids'.

## Parameters

### `array` $args
Optional arguments.
