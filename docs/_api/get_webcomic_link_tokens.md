---
title: get_webcomic_link_tokens
permalink: get_webcomic_link_tokens
---

> Alter the link tokens.

```php
apply_filters( 'get_webcomic_link_tokens', array $tokens, string $link, WP_Post $comic )
```

This filter allows hooks to alter the replaceable link text tokens and
their values.

## Core tokens

|Token            |Value                                   |Example    |
|-----------------|----------------------------------------|-----------|
|%date            |The comic's publish date.               |May 1, 2099|
|%full            |The comic's full media.                 |           |
|%large           |The comic's large media.                |           |
|%medium          |The comic's medium media.               |           |
|%medium_large    |The comic's medium_large media.         |           |
|%thumbnail       |The comic's thumbnail media.            |           |
|%time            |The comic's publish time.               |4:52 pm    |
|%title           |The comic's title.                      |Page 1     |
|%wfi-full        |The comic's full featured image.        |           |
|%wfi-large       |The comic's large featured image.       |           |
|%wfi-medium      |The comic's medium featured image.      |           |
|%wfi-medium_large|The comic's medium_large featured image.|           |
|%wfi-thumbnail   |The comic's thumbnail featured image.   |           |

## Commerce tokens

|Token          |Value                                        |Example   |
|---------------|---------------------------------------------|----------|
|%*-print-adjust|The comic's print adjustment.                |-25       |
|%*-print-base  |The collection's print price.                |10.00     |
|%*-print-left  |The number of prints left for the comic.     |42        |
|%*-print-name  |The collection's print name.                 |Domestic  |
|%*-print-price |The comic's print price.                     |7.50      |
|%*-print-sold  |The number of prints sold for the comic.     |8         |
|%*-print-stock |The number of prints available for the comic.|50        |
|%print-currency|The collection's print currency.             |USD       |

The `*` in these tokens is a placeholder for the print ID. To see a comic's
print base price for a print with an ID of `domestic`, you would use the
token `%domestic-print-price`.

## Parameters

### `array` $tokens
The token values.

### `string` $link
The link text to search for tokens.

### `WP_Post` $comic
The comic the link is for.
