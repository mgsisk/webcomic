---
title: webcomic_roles_required_content
permalink: webcomic_roles_required_content
---

> Alter the standard roles restricted content.

```php
apply_filters( 'webcomic_roles_required_content', string $content, int $id )
```

This filter allows hooks to alter the standard content returned when
a user does not have the required role or roles to view the comic.

## Parameters

### `string` $content
The content to return in place of the normal
post content.

### `int` $id
The post ID.
