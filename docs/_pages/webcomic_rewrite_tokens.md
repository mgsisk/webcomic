---
title: webcomic_rewrite_tokens
permalink: webcomic_rewrite_tokens
---

> Alter rewrite tokens.

```php
apply_filters( 'webcomic_rewrite_tokens', array $rewrite )
```

This filter allows hooks to provide token => regex pairs for handling
comic permalink rewrites.

## Parameters

### `array` $rewrite
The list of token => regex rewrites.
