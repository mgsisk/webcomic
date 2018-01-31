---
title: webcomic_twitter_status_tokens
permalink: webcomic_twitter_status_tokens
---

> Alter status tokens.

```php
apply_filters( 'webcomic_twitter_status_tokens', array $tokens, string $url, WP_Post $post )
```

This filter allows hooks to provide specific token => value pairs for
Twitter status updates.

## Core tokens

|Token       |Value                                    |Example         |
|------------|-----------------------------------------|----------------|
|%author     |The comic's author.                      |Angie           |
|%collection |The comic's collection.                  |Numina          |
|%#collection|The comic's collection as a hashtag.     |#numina         |
|%date       |The comic's publish date.                |May 1, 2099     |
|%field{name}|The value of the `name` custom field.    |custom value    |
|%shortlink  |The shortlinke to the comic.             |example.com?p=42|
|%site-name  |The site name.                           |Example Site    |
|%site-url   |The site URL.                            |example.com     |
|%taxonomy   |The comic's `taxonomy` terms.            |tag1, tag2      |
|%#taxonomy  |The comic's `taxonomy` terms as hashtags.|#tag1, #tag2    |
|%time       |The comic's publish time.                |4:52 pm         |
|%title      |The comic's title.                       |Page 1          |
|%url        |The permalink to the comic.              |example.com/post|

### %field{name}

When using the `%field{name}` token, `name` is the name of a custom field.
To show the value of a custom field named `special_value`, for example, you
would use `%field{special_value}`.

### %taxonomy and %#taxonomy

When using the `%taxonomy` or `$#taxonomy` tokens, `taxonomy` is the ID of
the terms you want to display. For Tags, use `post_tag`; for Categories,
use `category`. Webcomic taxonomy ID's are always `collectionID_taxonomy`
(e.g. `webcomic1_character` or `webcomic42_storyline`). To show the
characters from a comic collection with an ID of `webcomic1`, you would use
%webcomic1_character (or %#webcomic1_character to show them as hashtags).

## Parameters

### `array` $tokens
The list of token => value pairs.

### `string` $url
The URL being rewritten.

### `WP_Post` $post
The post the URL is being rewritten for.
