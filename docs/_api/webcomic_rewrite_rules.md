---
title: webcomic_rewrite_rules
permalink: webcomic_rewrite_rules
---

> Alter comic rewrite rules.

```php
apply_filters( 'webcomic_rewrite_rules', array $rules )
```

This filter allows hooks to provide URL => collection mappings for
identifying pages that belong to a specific Webcomic collection.

## Parameters

### `array` $rules
The list of URL => collection mappings.
