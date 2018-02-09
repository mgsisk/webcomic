---
title: webcomic_referrers_required_content
permalink: webcomic_referrers_required_content
---

> Alter the standard referrers restricted content.

```php
apply_filters( 'webcomic_referrers_required_content', string $content, int $id )
```

This filter allows hooks to alter the standard content returned when
a user was not referred by a valid URL to view the comic.

## Parameters

### `string` $content
The content to return in place of the normal
post content.

### `int` $id
The post ID.
