---
title: webcomic_current_collection
permalink: webcomic_current_collection
---

> Alter the current comic collection.

```php
apply_filters( 'webcomic_current_collection', string $collection, string $url )
```

This filter allows hooks to provide additional matching features for
identifying the current collection. As a constant, this value cannot be
changed once it has been set.

## Parameters

### `string` $collection
The current collection.

### `string` $url
The requested URL.
