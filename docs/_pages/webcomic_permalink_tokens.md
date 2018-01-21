---
title: webcomic_permalink_tokens
permalink: webcomic_permalink_tokens
---

> Alter comic permalink tokens.

```php
apply_filters( 'webcomic_permalink_tokens', array $tokens, string $url, WP_Post $post )
```

This filter allows hooks to provide specific token => value pairs for
rewriting comic permalinks.

## Core tokens

|Token     |Value                           |Example |
|----------|--------------------------------|--------|
|%author%  |The comic author's username.    |username|
|%day%     |The publish day of the comic.   |01      |
|%hour%    |The publish hour of the comic.  |16      |
|%minute%  |The publish minute of the comic.|52      |
|%monthnum%|The publish month of the comic. |05      |
|%post_id% |The comic's post ID.            |9       |
|%second%  |The publish second of the comic.|22      |
|%year%    |The publish year of the comic.  |2099    |

## Taxonomy tokens

|Token                |Value                  |Example       |
|---------------------|-----------------------|--------------|
|%webcomic*_character%|The comic's characters.|character-slug|
|%webcomic*_storyline%|The comic's storylines.|storyline-slug|

The `*` in these tokens is a placeholder for the collection ID number, like
`webcomic1_character` or `webcomic42_storyline`.

## Parameters

### `array` $tokens
The list of token => value pairs.

### `string` $url
The URL being rewritten.

### `WP_Post` $post
The post the URL is being rewritten for.
