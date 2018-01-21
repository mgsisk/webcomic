---
title: get_webcomic_term_link_tokens
permalink: get_webcomic_term_link_tokens
---

> Alter the link tokens.

```php
apply_filters( 'get_webcomic_term_link_tokens', array $tokens, string $link, WP_Term $comic_term )
```

This filter allows hooks to alter the replaceable link text tokens and
their values.

## Core tokens

|Token        |Value                                    |Example       |
|-------------|-----------------------------------------|--------------|
|%count       |The number of comics in the collection.  |42            |
|%date        |The collection's update date.            |May 1, 2099   |
|%description |The collection's description.            |It's exciting!|
|%full        |The collection's full-size media.        |              |
|%large       |The collection's large-size media.       |              |
|%medium      |The collection's medium-size media.      |              |
|%medium_large|The collection's medium_large-size media.|              |
|%thumbnail   |The collection's thumbnail-size media.   |              |
|%time        |The collection's updated time.           |4:52 pm       |
|%title       |The collection's title.                  |Chapter 1     |

## Parameters

### `array` $tokens
The token values.

### `string` $link
The link text to search for tokens.

### `WP_Term` $comic_term
The comic term the link is for.
