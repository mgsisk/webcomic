---
title: webcomic_age_required_comments
permalink: webcomic_age_required_comments
---

> Alter the age restricted comments template path.

```php
apply_filters( 'webcomic_age_required_comments', string $restricted, string $template )
```

This filter allows hooks to alter the template used when a user has not
confirmed their age for an age-restricted comic.

## Parameters

### `string` $restricted
The restricted comment template.

### `string` $template
The original comment template.
