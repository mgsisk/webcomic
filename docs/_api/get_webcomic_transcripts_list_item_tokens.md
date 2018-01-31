---
title: get_webcomic_transcripts_list_item_tokens
permalink: get_webcomic_transcripts_list_item_tokens
---

> Alter the list item tokens.

```php
apply_filters( 'get_webcomic_transcripts_list_item_tokens', array $tokens, string $link, WP_Post $transcript, array $args )
```

This filter allows hooks to alter the replaceable list item text tokens
and their values.

## Core tokens

|Token        |Value                         |Example         |
|-------------|------------------------------|----------------|
|%authors     |The transcript's authors.     |Angie, Mike     |
|%content     |The transcript's content.     |                |
|%date        |The transcript's publish date.|May 1, 2099     |
|%edit-link   |The transcript's edit link.   |[Edit this](/)  |
|%languages   |The transcript's languages.   |English, Spanish|
|%parent-link |The transcript comic's link.  |[Page 1](/)     |
|%parent-title|The transcript comic's title. |Page 1          |
|%time        |The transcript's publish time.|4:52 pm         |

## Parameters

### `array` $tokens
The token values.

### `string` $link
The list item text to search for tokens.

### `WP_Post` $transcript
The transcript the list item is for.

### `array` $args
The transcript list arguments.
