---
title: webcomic_error()
permalink: webcomic_error()
---

> Trigger an error.

```php
webcomic_error( string $message, int $type = E_USER_DEPRECATED ) : bool
```

## Parameters

### `string` $message
The error message.

### `int` $type
Optional error type; one of E_USER_DEPRECATED,
E_USER_NOTICE, E_USER_WARNING, or E_USER_ERROR.

## Return

`bool`
