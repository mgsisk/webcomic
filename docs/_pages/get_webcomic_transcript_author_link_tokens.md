---
title: get_webcomic_transcript_author_link_tokens
permalink: get_webcomic_transcript_author_link_tokens
---

> Alter the link tokens.

```php
apply_filters( 'get_webcomic_transcript_author_link_tokens', array $tokens, string $link, array $author )
```

This filter allows hooks to alter the replaceable link text tokens and
their values.

## Core tokens

|Token    |Value                                     |Example    |
|---------|------------------------------------------|-----------|
|%avatar  |The author's avatar                       |           |
|%avatar-*|The author's avatar at the specified size.|           |
|%name    |The transcript author's name`.            |Angie      |
|%url     |The transcript author's URL.              |example.com|

The `*` in these tokens is a placeholder for a specific avatar pixel
size, like `%avatar-48`.

## Parameters

### `array` $tokens
The token values.

### `string` $link
The link text to search for tokens.

### `array` $author
The transcript author the link is for.
