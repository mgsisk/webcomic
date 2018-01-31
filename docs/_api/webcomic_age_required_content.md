---
title: webcomic_age_required_content
permalink: webcomic_age_required_content
---

> Alter the standard age restricted content.

```php
apply_filters( 'webcomic_age_required_content', string $content, int $id )
```

This filter allows hooks to alter the standard content returned when
a user has not confirmed their age for an age-restricted comic.

## Parameters

### `string` $content
The content to return in place of the normal
post content.

### `int` $id
The post ID.
