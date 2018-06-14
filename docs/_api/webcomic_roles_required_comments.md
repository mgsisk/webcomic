---
title: webcomic_roles_required_comments
permalink: webcomic_roles_required_comments
---

> Alter the roles restricted comments template path.

```php
apply_filters( 'webcomic_roles_required_comments', string $restricted, string $template )
```

This filter allows hooks to alter the template used when a user does not
have the required role or roles to view the comic.

## Parameters

### `string` $restricted
The restricted comment template.

### `string` $template
The original comment template.
