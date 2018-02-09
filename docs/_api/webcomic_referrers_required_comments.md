---
title: webcomic_referrers_required_comments
permalink: webcomic_referrers_required_comments
---

> Alter the referrers restricted comments template path.

```php
apply_filters( 'webcomic_referrers_required_comments', string $restricted, string $template )
```

This filter allows hooks to alter the template used when a user was not
referrerd by a valid URL to view the comic.

## Parameters

### `string` $restricted
The restricted comment template.

### `string` $template
The original comment template.
