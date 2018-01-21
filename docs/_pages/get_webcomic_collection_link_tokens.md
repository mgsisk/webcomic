---
title: get_webcomic_collection_link_tokens
permalink: get_webcomic_collection_link_tokens
---

> Alter the link tokens.

```php
apply_filters( 'get_webcomic_collection_link_tokens', array $tokens, string $link, string $collection )
```

This filter allows hooks to alter the replaceable link text tokens and
their values.

## Core tokens

|Token        |Value                                    |Example       |
|-------------|-----------------------------------------|--------------|
|%date        |The collection's update date.            |May 1, 2099   |
|%count       |The number of comics in the collection.  |42            |
|%description |The collection's description.            |It's exciting!|
|%full        |The collection's full-size media.        |              |
|%large       |The collection's large-size media.       |              |
|%medium      |The collection's medium-size media.      |              |
|%medium_large|The collection's medium_large-size media.|              |
|%thumbnail   |The collection's thumbnail-size media.   |              |
|%time        |The collection's updated time.           |4:52 pm       |
|%title       |The collection's title.                  |Page 1        |

## Commerce tokens

|Token          |Value                                          |Example |
|---------------|-----------------------------------------------|--------|
|%print-currency|The collection's print currency.               |USD     |
|%*-print-name  |The collection's print name.                   |Domestic|
|%*-print-price |The collection's print price.                  |10      |
|%*-print-stock |The number of prints available.                |50      |

The `*` in these tokens is a placeholder for the print ID. To see a comic's
print base price for a print with an ID of `domestic`, you would use the
token `%domestic-print-price`.

## Parameters

### `array` $tokens
The token values.

### `string` $link
The link text to search for tokens.

### `string` $collection
The collection the link is for.
