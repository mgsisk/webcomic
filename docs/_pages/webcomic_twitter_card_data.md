---
title: webcomic_twitter_card_data
permalink: webcomic_twitter_card_data
---

> Alter card meta data.

```php
apply_filters( 'webcomic_twitter_card_data', array $rules, string $collection )
```

This filter allows hooks to provide name => content pairs for generating
Twitter Card meta data tags.

## Parameters

### `array` $rules
The list of name => content meta data pairs.

### `string` $collection
The current collection.
