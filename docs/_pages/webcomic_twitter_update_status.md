---
title: webcomic_twitter_update_status
permalink: webcomic_twitter_update_status
---

> Alter Twitter status updating.

```php
apply_filters( 'webcomic_twitter_update_status', bool $tweet )
```

This filter allows hooks to force or disallow status updates when a comic
is published.

## Parameters

### `bool` $tweet
Wether to update status when a comic is published.
