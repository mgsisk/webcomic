---
title: webcomic_new_collection
permalink: webcomic_new_collection
---

> Alter the default collection settings.

```php
apply_filters( 'webcomic_new_collection', array $defaults )
```

This filter allows hooks to alter the default settings of new a
collection. Any settings that remain unset will be copied from whatever
collection is being used as the $base collection.

## Parameters

### `array` $defaults
The default collection settings.
